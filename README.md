start_spider.sh  开始抓取

stop_spider.sh 结束抓取

php spider_flush 清空所有抓取的数据，包括redis中的数据

修改并发数量从 spider_start.php中修改pid_nums的数值

运行前请安装mysql和redis，mysql语句运行DB.sql中的内容

自我性能问题追踪：请查看capacity.log文件,可能对你有帮助，但是请根据自己的机器环境配置量力而行

