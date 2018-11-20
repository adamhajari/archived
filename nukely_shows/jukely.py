from requests import session
import json
import config
import sqlite3
from pandas.io import sql
import pandas as pd
import time
import datetime

import logging

logger = logging.getLogger()
handler = logging.StreamHandler()
formatter = logging.Formatter('%(asctime)s %(name)-12s %(levelname)-8s %(message)s')
handler.setFormatter(formatter)
logger.addHandler(handler)
logger.setLevel(logging.INFO)

import warnings
warnings.filterwarnings("ignore")

import emailer
# jukely username and password, gmail sender address and password, list of recipient addresses, and address to send error emails should be stored in config.py as:
# username ='something@somewhere.com'
# password = 'password'
# 
# gmail_sender_address = "something@gmail.com"
# gmail__sender_password = "password"
# 
# to_addrs = ['recipient1@gmail.com', "recipient2@gmail.com"]
# error_addresses = ['recipientX@gmail.com']

username = config.username
password = config.password

gmail_sender_address = config.gmail_sender_address
gmail_sender_password = config.gmail_sender_password

toaddrs = config.to_addrs
error_addresses = config.error_addresses

TIME_BETWEEN_LOOKUPS = 129  # in seconds
DB_NAME = 'nukely'
RETRY_COUNT = 15

class Jukely(object):
	def __init__(self, admin_email_address=gmail_sender_address, admin_email_password=gmail_sender_password):
		self.admin_email_address = admin_email_address
		self.admin_email_password = admin_email_password
		self.emailer = emailer.Emailer(admin_email_address,admin_email_password)
		self.retry_count = RETRY_COUNT

	def get_new_shows_from_jukely(self):
		response = ""
		try:
			response = self.load_jukely_unlimited_page()
			event_tag = 'window.events = '
			s = response.find(event_tag)+len(event_tag)  	# start index of event json
			f = response[s:].find(";\n")					# end index of event json
			shows = json.loads(response[s:(s+f)])
			return shows['events']
		except  Exception as e:
			logging.exception(e)
			logger.debug(response)
			self.emailer.send_email(error_addresses, 'Error Getting Jukely Data',response)

	def load_jukely_unlimited_page(self):
		with session() as c:
			y = c.get('https://www.jukely.com/log_in')
			auth_text = 'name="csrf-token" content="'
			s = y.text.find(auth_text)+len(auth_text)
			f = y.text[s:].find('" />')
			self.auth_token = y.text[s:(s+f)]
			logger.debug("auth_token: %s" % self.auth_token)
			payload = {'authenticity_token': self.auth_token, 
						'username': username,
						'password': password}
			c.post('https://www.jukely.com/sessions',data=payload, auth=(username, password))
			request = c.get('https://www.jukely.com/unlimited/shows', auth=(username, password))
		return request.text.encode('utf-8')

	def get_shows(self, send_emails=True):
		con = sqlite3.connect(DB_NAME+'.db')
		
		# get last checked show status
		get_old_shows_query = """select shows.id AS id, status.status AS old_status from shows
								LEFT join status
								on status.show_id=shows.id"""
		old_shows = sql.read_sql(get_old_shows_query, con, index_col='id')
		con.close()

		# get current list of shows
		shows = self.get_new_shows_from_jukely()

		# put current list of shows in a dataframe
		shows_df = self.shows_to_df(shows)

		old_and_new_shows = shows_df.drop(['created_at'], axis=1).join(old_shows, how='left')
		new_shows = old_and_new_shows.loc[old_and_new_shows['old_status'].isnull(),:].drop('old_status',axis=1)
		old_shows = old_and_new_shows.loc[old_and_new_shows['old_status'].notnull(),:]
		status_change = old_shows.loc[ (old_shows['old_status']-old_shows['status'])!=0,:]

		return (new_shows, status_change)

	def update_database(self, new_shows, status_change):
		con = sqlite3.connect(DB_NAME+'.db')
		try:
			if len(new_shows)>0:
				# update shows and status tables with any new shows
				rows = [(str(idx), row['date'].strftime('%Y-%m-%d %H:%M:%S'), row['genres'], row['headliner'], row['image_url'], row['other_acts'], row['venue'], row['video_id']) for idx, row in new_shows.iterrows()]
				con.executemany("INSERT OR IGNORE INTO shows (id, date, genres, headliner, image_url, other_acts, venue, video_id, created_at) VALUES (?,?,?,?,?,?,?,?,datetime('now'));", rows )

				rows = [(str(idx), row['status']) for idx, row in new_shows.iterrows()]
				con.executemany("INSERT OR REPLACE INTO status (show_id, status, updated_at) VALUES (?,?,datetime('now'));", rows )
			if len(status_change)>0:
				# update status and status_change table with any status changes
				rows = [(str(idx),row['old_status'],row['status']) for idx, row in status_change.iterrows()]
				con.executemany("INSERT INTO status_change (show_id, old_status, new_status, updated_at) VALUES (?,?,?,datetime('now'));", rows )

				rows = [(str(idx), row['status']) for idx, row in status_change.iterrows()]
				con.executemany("INSERT OR REPLACE INTO status (show_id, status, updated_at) VALUES (?,?,datetime('now'));", rows )
			con.commit()
		except Exception as e:
			logging.exception(e)
			self.emailer.send_email(error_addresses, 'Error Updating Jukely Database',"Error")
		con.close()

	def send_emails(self, new_shows, status_change):
		try:
			# if there are new shows, send an email to everyone on the list
			if len(new_shows)>0:
				html = "<b>New Shows: </b><br>"
				html += new_shows.reset_index().to_html(index=False, columns=['id','headliner', 'date', 'venue', 'other_acts'], col_space=0)
				html += "<br><br><br>"
				self.emailer.send_email(toaddrs, 'Jukely Concer Alert',html.encode('utf-8'))
				logger.info( "*--- new shows email sent ---*" )
			# if there is a status change for a show, send an email to everyone with an alert for that show
			if len(status_change)>0:
				con = sqlite3.connect(DB_NAME+'.db')
				get_alterts_query = """select show_id, email_address from alerts
						INNER join shows
						on alerts.show_id=shows.id
						where alerts.deleted_at IS NULL
						AND (shows.date>datetime('now'))"""
				alerts = sql.read_sql(get_alterts_query, con)
				con.close()
				alertsX = pd.merge(alerts,status_change.reset_index(),left_on='show_id',right_on='id')
				for idx,row in alertsX.iterrows():
					headliner = row['headliner'].encode('utf-8')
					availability = 'AVAILABLE' if row['status'] == 2 else 'unavailable'
					subject = "%s jukely show status change alert" % headliner
					body = "Tickets for the %s jukely show on %s at %s are now %s" % (headliner, row['date'], row['venue'], availability)
					self.emailer.send_email([row['email_address']], subject, body.encode('utf-8'))
					logger.debug("status change email email sent to: %s with subject: %s" % (row['email_address'], subject))
					logger.info("*--- status change email sent ---*")
		except Exception as e:
			logging.exception(e)
			self.emailer = emailer.Emailer(self.admin_email_address,self.admin_email_password)
			self.emailer.send_email(error_addresses, 'Error Sending Jukely email',"Error")

	def shows_to_df(self,shows):
		show_ids = []; headliners = []; venues = []; videos = []; status = []; dates = []; genres = []; other_acts = []; images = []
		for show in shows:
			headliners.append( show['headliner']['name'])
			show_ids.append( show['id'] )
			venues.append( show['venue']['name'] )
			status.append( show['status'] )
			dates.append( show['starts_at'][:19] )
			genres.append( ",".join(show['headliner']['genres']) )
			other_acts.append( ",".join(show['other_artist_names']) )
			images.append( show['image_url'] )
			try:
				videos.append( show['headliner']['video_url'].split("?v=")[1] )
			except:
				videos.append( "" )
		shows_df = pd.DataFrame({'headliner':headliners, 'date':dates, 'venue':venues, 'video_id':videos, 'status':status, 'genres':genres, 'other_acts':other_acts, 'image_url':images}, index=show_ids)
		try:
			shows_df['date'] = pd.to_datetime(shows_df.date, format='%Y-%m-%d %H:%M:%S')
		except:
			shows_df['date'] = pd.to_datetime(shows_df.date, format='%Y-%m-%dT%H:%M:%S')
		shows_df['created_at'] = datetime.datetime.now()
		shows_df.index.name = 'id'
		return shows_df

	def get_new_alerts(self,back_secs):
		"""checks email inbox for new alerts
			alerts will have a subject of format "alert: alert_id" or "alert alert_id"
			case and spaces do not matter
		"""
		alerts = []
		last_alert_datetime = self.get_last_alert_datetime()
		logger.debug("last_alert_datetime: %s" % last_alert_datetime)
		emails = self.emailer.fetch_emails(since_dt=last_alert_datetime)
		for email in emails:
			email_addr = email[1]
			subject = email[0].lower().replace(" ","")
			body = email[2].lower().replace(" ","")
			sent_dt = email[3].strftime('%Y-%m-%d %H:%M:%S')
			split = subject.split("alert:")
			if len(split)!=2:
				split = subject.split("alert")  # check subject for "alert"
				if len(split)!=2 or subject.find('alert')!=0:
					continue  # this isn't an alert email, go on to the next
			try:
				show_id = int(split[1])
			except:
				# email nuker back to let them know that their show id was unrecognizable
				self.emailer.send_email([email_addr],"Nukely Alert Not Added", "alert trigger emails should have a subject that looks like: 'alert:12345' where 12345 is a valid show id")
			alerts.append((show_id,email_addr,sent_dt))
			subject = "new alert added by %s for show %s." % (email_addr,show_id)
			self.emailer.send_email([email_addr],subject, "")
			logger.info( subject )
		return alerts

	def add_new_alerts(self,alerts):
		"""adds alerts to sql db. alerts will be a list of format:
		 [(alert1_show_id, alert1_email_address), (alert2_show_id, alert2_email_address), ...]
		 """
		con = sqlite3.connect(DB_NAME+'.db')
		con.executemany("INSERT OR IGNORE INTO alerts (show_id, email_address, created_at) VALUES (?,?,?);", alerts )
		con.commit()
		con.close()

	def remove_alerts(self,alerts):
		"""adds alerts to sql db. alerts will be a list of format:
		 [(alert1_show_id, alert1_email_address), (alert2_show_id, alert2_email_address), ...]
		 """
		con = sqlite3.connect(DB_NAME+'.db')
		update_statment = "update alerts set deleted_at=datetime('now') where show_id=%s and email_address=%s"
		for alert in alerts:
			con.execute(update_statment % (alert[0],alert[1]) )
		con.commit()
		con.close()

	def get_last_alert_datetime(self):
		con = sqlite3.connect(DB_NAME+'.db')
		get_last_altert = "select max(created_at) AS last_datetime from alerts;"
		last_alert_df = sql.read_sql(get_last_altert, con)
		con.close()
		if len(last_alert_df)>0 and last_alert_df.loc[0,'last_datetime'] is not None:
			return datetime.datetime.strptime(last_alert_df.loc[0,'last_datetime'],'%Y-%m-%d %H:%M:%S')
		else:
			return datetime.datetime.strptime("1900-01-01 00:00:00",'%Y-%m-%d %H:%M:%S')

	def sleep_between_lookups(self,sleep_seconds):
		t = time.localtime()
		current_minute = t.tm_hour*60+t.tm_min  # at 11:00 am current_minute=660
		# if we're within 2 minutes of 11, don't sleep
		if current_minute<658 or current_minute>662:
			time.sleep(sleep_seconds)

	def execute(self):
		while True:
			try:
				logger.info( "checking for new alerts" )
				alerts = self.get_new_alerts(TIME_BETWEEN_LOOKUPS+60)
				self.add_new_alerts(alerts)

				logger.info( "checking for new shows" )
				new_shows, status_change = self.get_shows()
				self.update_database(new_shows, status_change)
				self.send_emails(new_shows, status_change)
				self.sleep_between_lookups(TIME_BETWEEN_LOOKUPS)
			except Exception as e:
				logging.exception(e)
				self.retry_count -= 1
				if self.retry_count >= 0:
					try:
						self.emailer = emailer.Emailer(self.admin_email_address,self.admin_email_password)
						self.emailer.send_email(error_addresses, "JUKELY ERROR. %s RETRIES UNTIL TOTAL FAILURE." % self.retry_count, "")
					except:
						logging.info('ERROR: UNABLE TO SEND EMAIL NOTIFICATION %s RETRIES' % self.retry_count)
					self.sleep_between_lookups(10*60)
					self.execute()
					break



# j = Jukely()
# j.get_new_shows_from_jukely()