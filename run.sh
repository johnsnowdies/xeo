#!/bin/bash

until mysqladmin ping -h db; do
	echo "$(date) - waiting for mysql"
	sleep 3
done

php -S 0.0.0.0:5000 -t /app/web

