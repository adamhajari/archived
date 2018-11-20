import urllib2
from BeautifulSoup import BeautifulSoup
#the text of news stories are usually hidden in a mess of html
#for ads, comments, and related stories.  The trick here is to find
#a unique marker for where a story begins, and then grab all of the 
#stories text and images (without any extra junk).

def NPR_news_parser(url):
	"""rips story text and images from NPR news stories"""
	
	story_text = " "
	response = urllib2.urlopen(url) 
	html = response.read()
	soup = BeautifulSoup(html)
	
	#sensibily, NPR marks their stories with a "storytext" id
	txt = soup.findAll(id='storytext')
	imgs = txt[0].findAll('img')

	for item in txt[0].contents:
	#the actual text is always in between either paragraph tags or blockquoates
		if getattr(item, 'name', None)=='p' or getattr(item, 'name', None)=='blockquote':
			story_text = story_text + unicode(item.prettify(), "utf-8")

	i = 0
	#thow any images at the bottom of the purified html
	for item in imgs:
		if i%2==0:
			story_text = story_text + unicode(item.prettify(), "utf-8")
		i = i+1
		
	return story_text
	
def reuters_news_parser(url):
	"""rips story text and images from NPR news stories"""
	
	story_text = " "
	response = urllib2.urlopen(url) 
	html = response.read()
	soup = BeautifulSoup(html)
	
	#Reuters, less sensible than NPR
	txt = soup.findAll(attrs={"class" : "column2 gridPanel grid8"})
	imgs = soup.findAll(id='articleImage')
	print len(txt[0])

	for item in txt[0].contents:
		
		if getattr(item, 'name', None)=='p' or getattr(item, 'name', None)=='blockquote':
			story_text = story_text + unicode(item.prettify(), "utf-8")
			print ""
			print ""
			print ""

	i = 0
	print story_text
	for item in imgs:
		if i%2==0:
			story_text = story_text + unicode(item.prettify(), "utf-8")
		i = i+1
		
	return story_text