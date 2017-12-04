lifeapp
=========
针对特有业务做相关处理

* 新增`log_id`字段
```
alter table  details add log_id char(17) NOT NULL default '0';
ALTER TABLE `details` ADD INDEX log_id ( `log_id`);
```
