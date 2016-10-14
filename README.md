# Guestbook

Simple application to accept and store visitors comments.  Uses CouchDB 2.0 for storage and also offers webhooks for realtime updates of new comments via RabbitMQ.

## Installation and Setup

It's vagrant and ansible.  Type `vagrant up` and your machine will be on `192.168.121.8`.

Start some services:
  - `sudo systemctl start couchdb.service
  - `sudo systemctl start requestbin.service
  - `sudo systemctl start guestbook-web.service
  - `sudo systemctl start guestbook-worker.service
  - `sudo systemctl start couchdb-haproxy.service

You now have a web interface http://192.168.121.8:8080/ and fauxton http://192.168.121.8:5984/_utils/# and rabbit http://192.168.121.8:15672 and requestbin http://192.168.121.8:8001.

Set up two new tables in CouchDB: `comments` and `webhooks`.

CouchDB setup came from here http://couchdb.apache.org/developer-preview/2.0/
