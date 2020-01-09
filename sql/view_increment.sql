-- MySQL version of the database schema for the View Stats extension.
-- License: MIT
-- Author: Scott Caldwell, 2020; Steven Orvis, 2020

CREATE TABLE IF NOT EXISTS /*_*/view_increment (
  view_increment_id integer NOT NULL AUTO_INCREMENT COMMENT 'Primary key.',
  page_id integer NOT NULL COMMENT 'Page that was viewed.',
  user_id integer NOT NULL COMMENT 'User who viewed the page.',
  user_name varchar(255) NOT NULL COMMENT 'Name or IP address of the user who viewed the page.',
  update_timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp of the page view.',
  /*update_week date NOT NULL DEFAULT (date(subdate(update_timestamp, dayofweek(update_timestamp) - 1))) COMMENT 'Week of the page view.',*/
  total_views integer NOT NULL COMMENT 'Total number of views as of the update timestamp.',
  PRIMARY KEY (view_increment_id),
  KEY /*i*/page_id (page_id),
  KEY /*i*/user_name (user_name),
  KEY /*i*/update_timestamp (update_timestamp)
)/*$wgDBTableOptions*/ COMMENT='Tracks increments in page views over time.';
