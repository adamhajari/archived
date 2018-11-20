import os
import sys
import json
import getopt
import httplib2
import html5lib
import traceback
from bs4 import BeautifulSoup
from datetime import datetime, timedelta
import datetime as dt
 
HTTP = httplib2.Http()
HC = 'hc-chat-'
ATTRS = [ 'from', 'time', 'msg' ]
USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36'
 
def get_soup(url, cookie_string):
    """ Return BeautifulSoup object containing chat history page """
    _, content = HTTP.request(url, 'GET', headers={'Cookie':cookie_string})
    return BeautifulSoup(content.decode('utf-8'), 'html5')
 
def get_members(limit_name_string):
    """ Use HipChat API to get a list of NBS user ids on hipchat for user past AND present
    (The api token in use here may need to be changed in the future). """
 
    url = 'https://api.hipchat.com/v1/users/list?auth_token=7806cb5743b804b7dc8d553272c0fa&include_deleted=1'
    print 'Parsing HipChat member list url {}'.format(url)
    _, content = HTTP.request(url, 'GET', headers={'User-Agent': USER_AGENT})
    users = json.loads(content)
    if limit_name_string is not None:
        include_users = limit_name_string.split(',')
    members = {}
    for o in users['users']:
        if limit_name_string is None or o['name'] in include_users:
            members[o['user_id']] = o['name']
    return members
 
def get_history(member_id, member_name, date, page, cookie_string):
    """ Return document containing all chats from this particular member on this particular date """
 
    url = 'https://nextbigsound.hipchat.com/history/member/{}/{}/{:02}/{:02}?q=&p={}'
    url = url.format(member_id, date.year, date.month, date.day, page)
    print u'Parsing url {} (chats w/ {})'.format(url, member_name)
    return get_soup(url, cookie_string)
 
def parse_history(soup):
    """ Parse chat descriptions out of html page and return those chats in a condensed form """
    docs = []
 
    # Extract documents from message divs with class hc-chat-row (ie
    # this is the primary message container)
    for msg in soup.findAll('div', attrs={'class' : HC + 'row'}):
        # Extract the values for specific divs within the message for things like who
        # it came from, what time it was sent, and what the content of the message was
        doc = dict( [ (k, msg.find(attrs={'class' : HC + k})) for k in ATTRS] ) 
    
        # Light transformations for brevity (currently only extracting time string from 'a' tag)
        if doc['time'] and doc['time'].find('a'):
            doc['time'] = doc['time'].find('a')
    
        # Extract the inner content of each tag found above (any html content within a
        # field, like that commonly in message text, will be unchanged)
        doc = dict( [ (k, ''.join([ ''.join(str(i if i else '').strip()) for i in v.contents ])) for (k, v) in doc.iteritems() if v ] )
        if 'msg' in doc:
            doc['msg'] = doc['msg'].replace('<div>\n      ','').replace('    </div>','')
    
        # At this point, each 'doc' will have the form:
        #     {'msg': '<html content>', 'from': '<sender>', 'time': '<time of day>'}
        docs.append(doc)
 
    return docs

def crawl_dates(cookie_string, dates, member_id, member_name=""):
    """ Collects 1:1 chat histories for the person logged in to HipChat with a browser cookie
    in the given 'cookie_file'.  Chats with the given member will be collected only for the dates given."""
 
    print 'Beginning chat history crawl for %s' % member_name
 
    chats = {}
    # for member_id in members.keys():
    for date in dates:
        errors = 0
        page = 0
        while errors < 3:
            page += 1
            try:
                docs = parse_history(get_history(member_id, member_name, date, page, cookie_string))
                if not docs:
                    break
                chats[date.strftime('%Y-%m-%d')] = docs
            except KeyboardInterrupt:
                return None
            except:
                print "Error occurred crawling chats for member {}, date = {}, page = {}"\
                    .format(member_id, date.strftime('%Y%m%d'), page)
                traceback.print_exc()
                errors += 1
    return chats

def crawl(cookie_file, begin, end, limit_name_file=None):
    """ like crawl method, but groups by person rather than date"""
    all_chats = []
 
    # get dates to crawl
    numdays = end-begin
    dates = [begin + dt.timedelta(days=x) for x in range(0, numdays.days+1)]
 
    # Load the cookie string from the given file (crawls have 
    # to be made using an authenticated session)
    cookie_string = None
    with open(cookie_file, 'rb') as f:
        cookie_string = f.read().strip()
    if not cookie_string:
        raise Exception('Failed to load cookie string from file {}'.format(cookie_file))

    limit_name_string = None
    if limit_name_file is not None:
        with open(limit_name_file, 'rb') as f:
            limit_name_string = f.read().strip()
        if not cookie_string:
            raise Exception('Failed to load cookie string from file {}'.format(cookie_file))

    # Get list of ALL NBS member ids
    members = get_members(limit_name_string)

        

    # Loop through the members collecting chat histories for each date
    for member_id in members.keys():
        chats = crawl_dates(cookie_string, dates, member_id, members[member_id])
        if len(chats) > 0:
            print '{} chats found for {}'.format(len(chats), members[member_id])
            chats_dict = {'member_id':member_id, 'member_name':members[member_id], 'chats':chats}
            all_chats.append(chats_dict)
    return all_chats
            
        
 
if __name__ == '__main__':
 
    # Location of local file contain cookie created for browser session
    cookie_file = None
 
    # Date range to collect chats for
    begin = end = None

    # files used to limit crawls by hipchat names
    limit_name_file = None
 
    # Output file name
    outname = 'chats.txt'
 
    # Parse options and ensure all are set
    usage = 'hipchat_crawl.py --cookie_file=<path_to_cookie_file> --begin=<yyyyMMdd> --end=<yyyyMMdd> --outfile=<path_to_output_file>  --limit_name_file=<path_to_limit_name_file>'
    try:
        opts, args = getopt.getopt(sys.argv[1:], "hcbeo::", ["cookie_file=","begin=","end=","outfile=","limit_name_file="])
    except getopt.GetoptError:
        print usage
        sys.exit(2) 
    for opt, arg in opts:
        if opt == '-h':
            print usage
            sys.exit()
        elif opt in ("-c", "--cookie_file"):
            cookie_file = arg
        elif opt in ("-o", "--outfile"):
            outname = arg
        elif opt in ("-b", "--begin"):
            begin = datetime.strptime(arg, '%Y%m%d')
        elif opt in ("-e", "--end"):
            end = datetime.strptime(arg, '%Y%m%d')
        elif opt in ("-l", "--limit_name_file"):
            limit_name_file = arg
 
    if any(i is None for i in [ cookie_file, begin, end, outname ]):
        print usage
        sys.exit(2)
    
    with open(outname, 'w') as outfile:
        all_chats = crawl(cookie_file, begin, end, limit_name_file)
        outfile.write('['+','.join([json.dumps(j) for j in all_chats])+']')
 
    print 'Chat history crawl complete'
