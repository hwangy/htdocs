#!/usr/bin/python
import MySQLdb

#Five columns ->
#0	TITLE
#1	CREATED
#2	AUTHOR
#3	LINK
#4	ID
columns = 5

def title(string) :
	arr = string.split('/')
	return arr[len(arr)-1]

db = MySQLdb.connect(host="localhost", user="root", 
		passwd="yh2ew0oa1nn4", db="wooburgh")

c  = db.cursor();

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
	i++

for val in result :
	#Get txt for link, then compare headers (tags)
	article = open(title(val[3]), 'r')
	
	while (out = article.readline()) =! "<article>" :
		out.split('\t')[1]
