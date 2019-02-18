#!/bin/bash

php artisan migrate:fresh
php artisan elasticsearch:fresh
php artisan timeline:generate