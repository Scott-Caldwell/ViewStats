-- MySQL version of the database schema for the View Stats extension.
-- License: GNU GPL v2+
-- Author: Scott Caldwell, Steven Orvis

CREATE SQL SECURITY DEFINER VIEW `hit_counter_incremental_daily` AS
select concat(`decode_namespace`(`p`.`page_namespace`),`p`.`page_title`) AS `full_title`,
       cast(`i`.`update_timestamp` as date)                              AS `date`,
       sum(`i`.`increment`)                                              AS `hits`
from (`hit_counter_incremental` `i`
join `page` `p` on((`i`.`page_id` = `p`.`page_id`)))
group by cast(`i`.`update_timestamp` as date),
         concat(`decode_namespace`(`p`.`page_namespace`),`p`.`page_title`);
