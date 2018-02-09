#!/bin/bash
chgrp -R apache *
chmod -R 750 *
chmod -R 770 ./app/cache
chmod -R 770 ./app/logs

