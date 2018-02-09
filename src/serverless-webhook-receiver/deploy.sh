#!/bin/bash

# set param cloudantURL to point to a database
zip -r hook.zip index.php vendor
bx wsk action update guestbook-comment --kind php:7.1 --web raw hook.zip
