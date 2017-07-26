#!/bin/bash

ps aux | grep [s]elenium-server-standalone | awk '{ print "kill -9", $2 }' | sh
ps aux | grep [X]vfb | awk '{ print "sudo kill -9", $2 }' | sh
ps aux | grep [g]eckodriver | awk '{ print "kill -9", $2 }' | sh
