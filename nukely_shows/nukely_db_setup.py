import sqlite3

def create_shows_table(db_name):
	con = sqlite3.connect(db_name+".db")
	create_query = """CREATE TABLE shows(
					id int PRIMARY KEY, 
					date datetime, 
					genres string,
					headliner string,
					image_url string,
					other_acts string,
					venue string,
					video_id string,
					created_at datetime
					);"""
	con.execute(create_query)
	con.commit()
	con.close()

def create_status_table(db_name):
	con = sqlite3.connect(db_name+".db")
	create_query = """CREATE TABLE status(
					show_id int PRIMARY KEY, 
					status int,
					updated_at datetime
					);"""
	con.execute(create_query)
	con.commit()
	con.close()

def create_status_change_table(db_name):
	con = sqlite3.connect(db_name+".db")
	create_query = """CREATE TABLE status_change(
					show_id int, 
					old_status int,
					new_status int,
					updated_at datetime
					);"""
	con.execute(create_query)
	con.commit()
	con.close()

def create_alerts_table(db_name):
	con = sqlite3.connect(db_name+".db")
	create_query = """CREATE TABLE alerts(
					show_id int PRIMARY KEY, 
					email_address string,
					created_at datetime,
					deleted_at datetime
					);"""
	index_query = """CREATE INDEX alert
					on alerts (show_id, email_address);"""
	con.execute(create_query)
	con.execute(index_query)
	con.commit()
	con.close()

db_name = "nukely"
create_shows_table(db_name)
create_status_table(db_name)
create_status_change_table(db_name)
create_alerts_table(db_name)
print("tables created")