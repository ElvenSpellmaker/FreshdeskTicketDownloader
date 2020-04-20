#!/usr/bin/env bash

if [[ `uname -s` == *"CYGWIN"* ]]
then
	export ConEmuANSI=ON
	if php -i | grep -i "Host => .*-cygwin"; then
		COMMAND='phpmd'
	else
		COMMAND='phpmd.bat'
	fi
else
	COMMAND='phpmd'
fi

./vendor/bin/$COMMAND src/ html bin/phpmd.xml > bin/sca/phpmd.html
