#!/usr/bin/env bash
BINPATH=$( cd "$( dirname "$0" )" && pwd )

if [ "$(id -u)" != "0" ]; then
   echo "Use 'sudo' to run this script" 1>&2
   exit 1
fi

echo "RUN success"