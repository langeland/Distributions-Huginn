#!/bin/bash

# This file is started from the Procfile; and is executed from the FLOW_PATH_ROOT.
touch Data/Logs/System.log
touch Data/Logs/Security.log
touch Data/Logs/Query.log

# TODO explain why we do this here and not on build
FLOW_CONTEXT=Production/Heroku ./flow resource:publish
FLOW_CONTEXT=Production/Heroku ./flow doctrine:migrate

bin/heroku-php-nginx \
	-F Packages/Application/Langeland.Heroku/Resources/Private/Heroku/fpm_custom.conf \
	-C Packages/Application/Langeland.Heroku/Resources/Private/Heroku/nginx.inc.conf \
	-l Data/Logs/System.log \
	-l Data/Logs/Security.log \
	-l Data/Logs/Query.log \
	Web/