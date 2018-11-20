from gmail import gmail
import datetime

class Emailer:
	def __init__(self,username,password):
		self.username=username
		self.password=password
		self.g = gmail.login(username,password)

	def fetch_emails(self,back_secs=300,since_dt=None):
		"""fetches all subjects since back_secs (in seconds)"""
		if since_dt is None:
			now = datetime.datetime.now()
			since_dt = now - datetime.timedelta(seconds=back_secs)
		try:
			self.g.imap.select("INBOX")
			emails = self.g.mailbox("INBOX").mail(after=since_dt.date())
		except:
			self.g = gmail.login(self.username,self.password)
			self.g.imap.select("INBOX")
			emails = self.g.mailbox("INBOX").mail(after=since_dt.date())
		email_list = []
		for email in emails:
			email.fetch()
			if email.sent_at <= since_dt:
				continue  # don't return any mail before since_dt
			email_list.append( (email.subject, email.fr, email.body, email.sent_at) )
		return email_list

	def send_email(self, to_addrs, subject, body):
		try:
			self.g.send_mail( to_addrs, body, subject)
		except:
			self.g = gmail.login(self.username,self.password)
			self.g.send_mail( to_addrs, body, subject)