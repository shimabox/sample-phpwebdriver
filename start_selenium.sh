#!/bin/bash

##################################################
# Xvfb(仮想ディスプレイ), selenium の起動
# デフォルトのサイズは 1366x768x24 です
##################################################

# Xvfb(仮想ディスプレイ)の起動 サイズ(横x高さx深度)は変えたかったら変えてください
sudo Xvfb :1 -screen 0 1366x768x24 &

# :1 のディスプレイですよ
export DISPLAY=:1

# selenium
java -Dwebdriver.gecko.driver=/usr/local/bin/geckodriver -jar selenium-server-standalone-3.8.1.jar -enablePassThrough false &
