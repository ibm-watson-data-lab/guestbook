#!/bin/bash

# set param cloudantURL to point to a database
bx wsk action update hook-endpoint --web true hook-endpoint.php
