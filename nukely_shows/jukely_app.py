from spyre import server

import sys
import pandas as pd
import time
import sqlite3
from pandas.io import sql
import pandas as pd

from jukely import *


class JukelyApp(server.App):

	def __init__(self):
		self.j = Jukely()
		self.params = None
		self.shows = None
		

	title = "Free Jukely Shows"
	
	inputs =  [{"input_type":"checkboxgroup",
				"label": "Genres to include",
				'options': [
					{"label":"all", 'value':"all", "checked":True},
					{"label":"indie", 'value':"indi"},
					{"label":"rock", 'value':"rock"},
					{"label":"alternative", 'value':"alt"},
					{"label":"punk", 'value':"punk"},
					{"label":"electronic", 'value':"elec"},
					{"label":"folk", 'value':"folk"},
					{"label":"reggae", 'value':"reggae"},
					{"label":"metal", 'value':"metal"},
					{"label":"rap", 'value':"rap"},
					],
				"variable_name":"genres",
				"action_id":"html_id"
				},
				{"input_type":"checkboxgroup",
				"label" : "Include",
				'options': [
					{"label":"videos", 'value':'video'},
					{"label":"sold out shows", 'value':'sold_out'},
					],
				"variable_name":"incl",
				"action_id":"html_id"
				}]

	controls = [{	"control_type" : "button",
					"control_id" : "button1",
					"label" : "refresh",
				}]

	outputs = [{	"output_type" : "html",
					"output_id" : "html_id",
					"control_id": "button1"
				}]

	def getHTML(self,params):
		params.pop('output_id')
		print params
		print self.params
		if params == self.params or self.shows is None:
			shows = self.j.shows_to_df(self.j.get_new_shows())
			self.shows = shows
			self.params = params
		else:
			shows = self.shows
			self.params = params
		incl = params['incl']
		genres_to_include = params['genres']

		

		html = ""
		for idx, show in shows.iterrows():
			if show['status']==3:
				if 'sold_out' not in incl:
					continue
				status = 'Sold Out'
			elif  show['status']==2:
				status = 'Tickets Available'
			else:
				status = show['status']
			genre_list = show['genres']
			display=0
			if 'all' not in genres_to_include:
				for g in genres_to_include:
					if genre_list.find(g)>=0:
						display += 1
				if display==0:
					continue
			if 'video' in incl:
				video_id = show['video_id']
				video_embed = '<embed width="420" height="315" src="{}"><br>'.format("http://www.youtube.com/v/"+video_id)
			else:
				video_embed = ""
			html += """<div>
						<b>{}</b><br>
						{}
						date: {}<br>
						venue: {}<br>
						status: {} <br>
						genres: {}<br>
						also playing: {}<br>
						</div></br></br>""".format(
							show['headliner'], 
							# show['image_url'], 
							video_embed,
							show['date'],
							show['venue'],
							status,
							genre_list,
							show['other_acts']
							)
		return html

def main(host='local', port=8080):
	app = JukelyApp()
	app.launch(host=host, port=port)
	
if __name__ == "__main__":
	if len(sys.argv)>2:
		main(sys.argv[1], int(sys.argv[2]))
	elif len(sys.argv)==2:
		main(sys.argv[1])
	else:
		main()
