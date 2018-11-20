nukely_shows
============

a spyre web interface for showing what unlimited show are available on nukely

Requirements
-----
nukely.py grabs the shows from nukely and only requires python.
To launch the web app you'll need spyre which has some dependencies of it's own.

Setup
-----
This application requires nukely user credentials for someone signed up for nukely's unlimited shows account.
You'll need to supply your credentials by created a config.py file (these are included in .gitignore). config.py will requires six lines

````
username = 'yourusername'
password = 'yourpassword'

gmail_sender_address = "something@gmail.com"
gmail__sender_password = "password"
 
to_addrs = ['recipient1@gmail.com', "recipient2@gmail.com"]
error_addresses = ['recipientX@gmail.com']
````

replace yourusername with your nukely username and yourpassword with your nukely password, and the other stuff with valid email/gmail addresses/password.

Launching the web app
----
Once you've created the config.py file with your credentials, 

launch the nukely script as a background process (with screen, upstart, nohup, etc.) with:

````
import jukely
j = jukely.Jukely()
j.execute()
````

launch the web app with

````
> python jukely_app.py
````

By default the app will launch locally at 127.0.0.1:8080