#!/bin/bash

ps -ef | grep spider | grep -v grep | cut -c 9-15 | xargs kill
