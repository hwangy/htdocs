#!/usr/bin/python
import MySQLdb
import os
import sys

#Five columns ->
#0	TITLE
#1	CREATED
#2	AUTHOR
#3	LINK
#4	ID
columns = 5
filled = 3

updates = 0
insertions = 0

def title(string) :
	arr = string.split('/')
	return arr[len(arr)-1]

def comp(needle, haystack) :
	i = 0
	for val in haystack :
		if val == needle :
			haystack.pop(i)
			return haystack
		else :
			i += 1
			continue

	return haystack

if len(sys.argv) > 1 :
	for val in sys.argv :
		if val == "--verbose" : verbose = 1

##START SYNCING SCRIPT

db = MySQLdb.connect(host="localhost", user="root", 
		passwd="yh2ew0oa1nn4", db="wooburgh")

c  = db.cursor();

articles = []
#Get list of articles in cwd
for val in os.listdir(os.getcwd()) :
	if val.find('~') > 0 : continue
	elif len(val.split('.')) != 2 : continue
	elif val.split('.')[1] != "txt" : continue
	else :
		articles.append(val)

#Do diff
c.execute("SELECT COUNT(*) FROM articles")
(numResults,) = c.fetchone()

result = [None]*numResults
for i in range(0, numResults) :
	result[i] = [None]*columns

i = 0
c.execute("SELECT * FROM articles")
for val in c.fetchall() :
	result[i] = val
	i+=1

for val in result :
	#Get txt for link, then compare headers (tags)
	t = title(val[3])
	articles = comp(t, articles)
	article = open(t, 'r')

	while 1 :
		out = article.readline()
		if out == "<tags>\n" :
			out = article.readline()
			count = int(out.split('\t')[1])

			column = "TITLE"
			dbc = 0
			for i in range(0, count) :
				out = article.readline().replace('\n', '').split('\t')[1]
				#print out, " and ", val[dbc]
				
				if out != val[dbc] :
					#DB is out of sync
					
					updates += 1
					cmd = "UPDATE `articles` SET `" + column + "` = '" + \
						out + "' WHERE `ID` = " + str(val[4])
					
					if verbose : print "UPDATED ARTICLE : " + t + " -> UPDATED " + \
						column + " FROM:\n\t" + val[dbc] + " TO " + out

					c.execute(cmd)
					db.commit()

				if dbc == 0 : 
					dbc = 2
					column = "AUTHOR"
				else : 
					dbc += 1
					column = "LINK"
		elif out == "<article>\n" :
			break

#Add new articles
toSubmit = [None]*filled

for val in articles :
	insertions += 1

	article = open(val, 'r')
	out = article.readline()
	if out == "<tags>\n" :
		while 1 :
			out = article.readline().replace('\n', '')
			if out == "<article>" : break

			out = out.split('\t')
			if out[0].lower() == 'title' : toSubmit[0] = out[1]
			elif out[0].lower() == 'author' : toSubmit[1] = out[1]
			elif out[0].lower() == 'link' : toSubmit[2] = out[1]

	cmd = "INSERT INTO `articles` (`TITLE`, `AUTHOR`, `LINK`) VALUES ('" + toSubmit[0] + \
		"', '" + toSubmit[1] + "', '" + toSubmit[2] + "')"

	if verbose : print "INSERTED ARTICLE: " + toSubmit[0]

	c.execute(cmd)
	db.commit()
		
db.close()

print "\nINSERTIONS: " + str(insertions)
print "UPDATES:    " + str(updates) + "\n"
