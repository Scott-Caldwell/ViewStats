-- MySQL version of the database schema for the View Stats extension.
-- License: GNU GPL v2+
-- Author: Scott Caldwell, Steven Orvis

CREATE SQL SECURITY DEFINER VIEW `hit_counter_incremental_view` AS (
select concat(`decode_namespace`(`p`.`page_namespace`),`p`.`page_title`) AS `full_title`,
       `i`.`update_timestamp`                                            AS `update_timestamp`,
       `i`.`total_hits`                                                  AS `total_hits`
from (`hit_counter_incremental` `i`
join `page` `p` on((`i`.`page_id` = `p`.`page_id`))));
