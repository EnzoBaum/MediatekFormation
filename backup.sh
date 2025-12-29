#!/bin/sh
DATE=`date -I`
find /home/if0_40738878/savebdd/bdd* -mtime -1 -exec rm {} \;
mysqldump -u if0_40738878 -pbszH5pPlaU --databases if0_40738878_mediatekformation --single-
transaction | gzip > /home/if0_40738878/savebdd/bddbackup_${DATE}.sql.gz
