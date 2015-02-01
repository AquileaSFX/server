#!/bin/bash - 

#set -o nounset                              # Treat unset variables as an error

DULL=0
BRIGHT=1
FG_BLACK=30
FG_RED=31
FG_GREEN=32
FG_YELLOW=33
FG_BLUE=34
FG_VIOLET=35
FG_CYAN=36
FG_WHITE=37

FG_NULL=00


BRIGHT_BLACK="\033[${BRIGHT};${FG_BLACK}m"
BRIGHT_RED="\033[${BRIGHT};${FG_RED}m"
BRIGHT_GREEN="\033[${BRIGHT};${FG_GREEN}m"
BRIGHT_YELLOW="\033[${BRIGHT};${FG_YELLOW}m"
BRIGHT_BLUE="\033[${BRIGHT};${FG_BLUE}m"
BRIGHT_VIOLET="\\${ESC}[${BRIGHT};${FG_VIOLET}m"
BRIGHT_CYAN="\033[${BRIGHT};${FG_CYAN}m"
BRIGHT_WHITE="\033[${BRIGHT};${FG_WHITE}m"

ESC="\033"
NORMAL="\033[m"

