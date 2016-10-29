start_spider.sh  开始抓取

stop_spider.sh 结束抓取

默认进程数为400，若修改请在spider_start.php中找到$total_pid_num,修改其值即可

运行前请安装mysql和redis，mysql语句运行DB.sql中的内容,请手动创建scraper数据库。并到scraper数据库中运行
修改Core\Config\mysqlconfig & redisconfig中的配置内容。

自我性能问题追踪：请查看capacity.log文件,可能对你有帮助，但是请根据自己的机器环境配置量力而行