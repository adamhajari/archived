import feedparser
import MySQLdb
import html_parser
import date_parser

#feedparser.parse returns the title, url, description and date of each element of a feed
d = feedparser.parse('http://www.npr.org/rss/rss.php?id=1001')

#all story info is stored in a MySQL database
db = MySQLdb.connect("localhost","news_parse_admin","password","cnn_newsfeed")
cursor = db.cursor()
cursor.execute('select * from stories order by id desc')
last_entry = cursor.fetchone()
last_date = date_parser.parse_date(last_entry[0])

for item in reversed(d.entries):
	date = item.date
	parsed_date = date_parser.parse_date(date)
	
	#if any items from the feed are more recent than the most recent item in the MySQL
	#database, add them to the database as well
	if date_parser.dateIsAfter(parsed_date, last_date):
		title = item.title
		url = item.link
		description = item.description
		story_id = item.id
		
		#the story text is ripped from the stories html by a python method custom 
		#built for the specific news source
		story_text = html_parser.NPR_news_parser(url)
		cursor.execute("insert into stories(date, title, link, description, story_id, story) values (%s, %s, %s, %s, %s, %s)", (date, title.encode('utf-8'), url, description.encode('utf-8'), story_id, story_text.encode('utf-8')))
		db.commit()
	print parsed_date, ": ", date_parser.dateIsAfter(parsed_date, last_date)
	

db.close()

