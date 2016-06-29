# Guestbook

Simple application to accept and store visitors comments.  Uses CouchDB 2.0 for storage and also offers webhooks for realtime updates of new comments via RabbitMQ.

## Installation and Setup

It's vagrant and ansible.  Type `vagrant up` and your machine will be on `192.168.121.8`.

Once it's up, there are a few things that you need to start manually from inside the VM (`byobu` is installed):

 * CouchDB itself.  Go to `/vagrant/couchdb` and run `dev/run` (it's a grunt task)
 * HAProxy to put CouchDB onto port 5984 where you were expecting it: go to `/vagrant/couchdb` and run `haproxy -f rel/haproxy.cfg`
 * PHP webserver (could set up nginx instead really): go to `/vagrant-src/public` and run `php -S 0.0.0.0:8080`

You now have a web interface http://192.168.121.8:8080/ and fauxton http://192.168.121.8:5984/_utils/#

CouchDB setup came from here http://couchdb.apache.org/developer-preview/2.0/
