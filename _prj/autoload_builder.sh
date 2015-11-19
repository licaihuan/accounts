#!/bin/sh
## build system required files : className => FilePath

PHP=php
USERHOME=`pwd`
AUTOLOAD_PATH="$USERHOME/application/components/rdb"

# create project autoload files

$PHP $USERHOME/_prj/build_admin_includes.php $AUTOLOAD_PATH $USERHOME/application/components/rdb/auto_load.php   "$USERHOME:autoload:admin"
