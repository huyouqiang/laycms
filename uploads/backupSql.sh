#/bin/bash

# 备份blog数据库
DATE=`date +%Y%m%d_%H`                #every minute
DATABASE=blog              #database name
DB_USERNAME=root                       #database username
DB_PASSWORD="123456"                    #database password
BACKUP_PATH=/www/backup/sql          #backup pathhome/backup
mysqldump blog  > /www/backup/sql/${DATABASE}_${DATE}.sql

# 备份djg数据库
DATE=`date +%Y%m%d_%H`                #every minute
DATABASE=djg              #database name
DB_USERNAME=root                       #database username
DB_PASSWORD="123456"                    #database password
BACKUP_PATH=/www/backup/sql          #backup pathhome/backup
mysqldump djg  > /www/backup/sql/${DATABASE}_${DATE}.sql

# 删除sql前三天备份
find /www/backup/sql -mtime +2 -name "*.sql" -exec rm -rf {} \;
