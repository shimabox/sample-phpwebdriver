#!/bin/bash

ps aux | grep [s]elenium-server-standalone | awk '{ print "kill -9", $2 }' | sh
