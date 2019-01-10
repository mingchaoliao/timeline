#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
echo "Project Directory: ${DIR}"

echo "Setup API directory/files permission: ${DIR}/api"
sudo chown -R www-data:www-data ${DIR}/api
sudo chgrp -R www-data ${DIR}/api
sudo chmod -R g+w ${DIR}/api

sudo find ${DIR}/api -type d -exec chmod 2775 {} \;
sudo find ${DIR}/api -type f -exec chmod 644 {} \;
sudo find ${DIR}/api -type f -exec chmod ug+rw {} \;
