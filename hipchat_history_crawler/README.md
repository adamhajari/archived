# HipChat 1:1 History Crawler #
This is a modification of a script written by Eric Czech: https://gist.github.com/eczech/af9237d0945f4a418d85

Given a cookie string for a logged in session, this script will use that cookie to download 
all 1:1 chats you've had within your organization


An example to collect a chat history over a specific time frame:
```bash
python hipchat_crawler.py --cookie_file=cookie.txt --begin=20140101 --end=20141231 --outfile=chats_2014.txt
```

For this invocation, all chats for 2014 will be collected and placed into a file in the current
directory with the name "chats_2014.txt"

The "cookie_file" must contain a string taken from a browser where you've logged into
HipChat and stored just that cookie in the file.  On chrome for example, you can get the cookie
using the web developer like this: https://dl.dropboxusercontent.com/u/65158725/hipchat_cookie.png

The string shown there should be placed in it's entirety into the "cookie_file"

There is also an option to include a list of hipchat user names in order to limit the crawl to only those users. This list should be included in a text file and should be comma separated. Here's an example of what the contents of the file might look like:
````
Adam Hajar,Jane Doe,Wes Anderson
````
Notice there are no spaces before or after the names. If this file were named `include_list.txt` you could limit your crawl to these users by including a `--limit_name_file` flag like:

```bash
python hipchat_crawler.py --cookie_file=cookie.txt --begin=20140101 --end=20141231 --outfile=chats_2014.txt --limit_name_file=include_list.txt
```

## Install dependencies:
```bash
pip install httplib2
pip install html5lib
pip install beautifulsoup4
```
