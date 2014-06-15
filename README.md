ShotbowForumAPI
===============

Shotbow Forum API for logging in, making posts, and tracking data.

I plan on working on this a lot. I do plan on being at a "Finished State" at one point, though.

###Forums
Assign a variable a "Forum" object that can scan it and return information.
#Initialization 
new Forum(link)
Ex: $f = new Forum('http://shotbow.net/forum/forums/annihilation/');
#scan()
Scans forum page and retrieves all threads.
Will not scan a second time if already scanned a first.
#getAllThreads()
Automatically scans if you haven't already.
Return an array of ThreadView objects
#getThread(i)
Automatically scans if you haven't already.
Returns ThreadView object from specific index. Return NULL if it does not exist.

##ThreadView
#Variables
**date:** Time in Epoch
**author:** Username of creator of thread
**title:** Title of thread
**replies:** # of replies
**views:** # of views
**replyAuthor:** Author of last post
**replyDate:** Date of last post (Epoch)
**authorId:** User ID of creator of thread
**replyAuthorId:** Used ID of the last poster
**stickied:** Boolean true or false if stickied
**locked:** Boolean true or false if locked
**id:** ID of the thread, needed for the Thread class
**type:** Type of post (Ex: Guide, Other, Video, Question, etc)

###Thread
Give it an ID and it will scan all data about the given thread.
#Initialization 
new Thread(id)
Ex: $t = new Thread(32987)
#scan()
Scans ALL thread pages and stores the data.
Will not scan a second time if already scanned a first.
This goes through a "Page" class where each page scans data. The Page class is not used anywhere else
#getAllPosts()
Automatically scans if you haven't already.
Return array of Post objects.
#getPost()
Automatically scans if you haven't already.
Returns Post object from specific index. Return NULL if it does not exist.

##Post
#Variables
**date:** Date of creation
**author:** Username of creator
**authorId:** ID of creator
**message:** Post message

=====================================================
=====================================================


##THINGS TO BE DONE:
- Logging in
- Page, Thread, and Post Objects
- Sending private message
- Posting on profile page
- Posting on thread
- Getting thread data
- Getting forum data
- Setup Queue
- Read Queue

##THINGS MAYBE TO BE DONE:
- Getting post likes
- Liking a post

##NOTICES:
- Redirects are ignored
- Non-existent data does not give errors, it just is left as "null"