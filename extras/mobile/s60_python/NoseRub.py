import httplib, urllib, json, appuifw, e32, os.path, os, socket, e32dbm, graphics, socket


# ------------------------------------------------------------------------------------
#
# NoseRub Mobile client in Python for Symbian series 60.
#            Version: 0.01
#
# Created by Lance Wicks on a Nokia E90 mobile Phone.
#            lw@judocoach.com http://www.lancewicks.com
#
#  Created: September 3, 2008. Last updated: September 3, 2008
#
# NOTES: This is the initial version of this client. You will need to 
#        install the JSON module seperately. Please see http://www.mobilepythonbook.org/
#        for more information.
#
#        For more information on NoseRub visit http://noserub.com/
#
# The MIT License
#
# CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
# Copyright 2005-2007,	Cake Software Foundation, Inc.
#							1785 E. Sahara Avenue, Suite 490-204
#							Las Vegas, Nevada 89104
#
# Permission is hereby granted, free of charge, to any person obtaining a
# copy of this software and associated documentation files (the "Software"),
# to deal in the Software without restriction, including without limitation
# the rights to use, copy, modify, merge, publish, distribute, sublicense,
# and/or sell copies of the Software, and to permit persons to whom the
# Software is furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in
# all copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
# FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
# DEALINGS IN THE SOFTWARE. 
#
#
#-------------------------------------------------------------------------------------


# locations for the data files
# -----------------------------
PATH = u"E:\\Data\\NoseRub\\"
DB_FILE = u"E:\\Data\\NoseRub\\noserub.db"
CONF_FILE = u"E:\\Data\\NoseRub\\noserub.cfg"
 
 
        
def update():
    db = e32dbm.open(DB_FILE, "r")
    location = []
    loc_key = []
    for key, value in db.items():
        #print "Key", key, "Value", value
        location.append(unicode(value))
        loc_key.append(key.encode("utf-8"))
    db.close()
    #print location[2]
    #print loc_key[2]
    loc_index = appuifw.popup_menu(location, u"Select:")
    #print loc_key[loc_index]
    URL = "http://"+config["url"]+"/api/"+config["user"]+"/"+config["api"]+"/json/locations/"    
    URL = URL+"set/" + loc_key[loc_index]    
    #print URL
    res = urllib.urlopen(URL).read()
    #print res
    appuifw.note(u"Location Updated", "error")

 

def update_list():
    appuifw.note(u"Downloading...", "error")
    #print "Downloading..."
    URL = "http://"+config["url"]+"/api/"+config["user"]+"/"+config["api"]+"/json/locations/"    
    #print URL
    res = urllib.urlopen(URL).read()
    status = json.read(res)
    current =  status["data"]["Identity"]["last_location_id"]
    #print current
    #print status["data"]["Locations"][int(current)-1]["Location"]
    db = e32dbm.open(DB_FILE, "nf")
    for x in status["data"]["Locations"]:
        #print x["Location"]
        #print x["Location"]["id"]
        #print x["Location"]["name"]
        db[x["Location"]["id"]] = x["Location"]["name"]
    db.close()    
    #print "...done."
    appuifw.note(u"...done.", "error")

    
def url():
    #print "pref url"
    #print config["url"]
    config["url"] = appuifw.query(u"url of NoseRub:", "text", config["url"])
    save_config()


    
def user():
    #print "pref user"
    #print config["user"]
    config["user"] = appuifw.query(u"User id on NoseRub:", "text", config["user"])
    save_config()
    
def api():
    #print "pref API key"
    #print config["api"]
    config["api"] = appuifw.query(u"API key:", "text", config["api"])
    save_config()
                        
def ap():
    #print "set default access- point"
    #print config["ap"]
    ap_id = socket.select_access_point()
    apo = socket.access_point(ap_id)
    socket.set_default_access_point(apo)
    config["ap"] = ap_id
    save_config()
    
def quit():
    #print "NoseRub Client EXITS"
    appuifw.note(u"Exiting... Thanks.", "error")
    app_lock.signal()

def read_config():
    f = file(CONF_FILE, "r")
    for line in f:
        key, value = line.split(":")
        config[key.strip()] = value.strip()
    f.close()
    
def save_config():
    f = file(CONF_FILE, "w")
    for key, value in config.items():
        print >> f, "%s: %s" % (key, value)
    f.close()   
 

if not os.path.exists(PATH):
    os.makedirs(PATH)
    config = {"url": "identoo.com", "user": "blank", "api": "api", "ap": "ap" }
    f = file(CONF_FILE, "w")
    for key, value in config.items():
        print >> f, "%s: %s" % (key, value)
    f.close()
    appuifw.note(u"Welcome! You will need to set your preferences.", "error")
	
canvas = appuifw.Canvas()
appuifw.app.body = canvas
w, h = canvas.size
img = graphics.Image.new((w, h))
img.clear((255,255,255))
img.text((10,40), u"NoseRub Client v0.1", fill = (0,0,0))
img.text((10,70), u"www.noserub.com", fill = (0,0,0))
canvas.blit(img)
 

location = []
loc_key = []
config = {}

read_config()

# if user has set a default AP, configure it.
ap_id = socket.select_access_point()
apo = socket.access_point(ap_id)
socket.set_default_access_point(apo)

appuifw.app.exit_key_handler = quit
appuifw.app.title = u"NoseRub Client"
appuifw.app.menu = [(u"Update Location", update), (u"Download locations list",update_list), (u"Preferences", ((u"Base URL", url), (u"User name",user),(u"API Key",api)))]

print "NoseRub Client Started"
app_lock = e32.Ao_lock()
app_lock.wait()
