#!/bin/bash
BINPATH=$( cd "$( dirname "$0" )" && pwd )

if [ "$(id -u)" != "0" ]; then
   echo "Use 'sudo' to run this script" 1>&2
   exit 1
fi

# =====================
# check puppet package
# =====================
puppet --version >/dev/null 2>&1 || apt-get install -y puppet;

mkdir -p /etc/puppet/modules;
(puppet module list | grep 3fs-phantomjs) || puppet module install 3fs-phantomjs

# ============================================================================
# Required puppet modules (http://forge.puppetlabs.com/) with minimal version
# ============================================================================
declare -A MODULES=(
    ["puppetlabs-postgresql"]="4.3.0"
    ["puppetlabs-nodejs"]="0.7.1"
    ["puppetlabs-apt"]="1.8.0"
    ["puppetlabs-rabbitmq"]="5.2.0"
    ["saz-memcached"]="2.7.1"
)

for MODULENAME in "${!MODULES[@]}";
do
    IS_INSTALLED=$( puppet module list | grep "$MODULENAME " | wc -l )
    if [ 1 == $IS_INSTALLED ]; then
        IS_LATEST=$( puppet module list --color=no | grep "$MODULENAME (v${MODULES[$MODULENAME]})" | wc -l )
        if [ 1 != $IS_LATEST ]; then
	        puppet module upgrade $MODULENAME --version ">=${MODULES[$MODULENAME]}"
        fi
    else
	    puppet module install $MODULENAME --version ">=${MODULES[$MODULENAME]}"
    fi
done

# ======================
# Run puppet apply
# ======================
if [ "$1" != "--only-setup" ]; then
    puppet apply $BINPATH'/../puppet/manifests/default.pp' --modulepath=$BINPATH'/../puppet/modules:/etc/puppet/modules' --hiera_config=$BINPATH'/../puppet/hiera.yaml';
fi