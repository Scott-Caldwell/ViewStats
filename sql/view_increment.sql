-- MySQL version of the database schema for the View Stats extension.
-- License: GNU GPL v2+
-- Author: Scott Caldwell, Steven Orvis

CREATE TABLE IF NOT EXISTS /*_*/view_increment (
  view_increment_id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary key.',
  page_id int(11) NOT NULL COMMENT 'Page that was viewed.',
  user_id int(11) NOT NULL COMMENT 'User who viewed the page.',
  update_timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp of the page view.',
  total_views int(11) NOT NULL COMMENT 'Total number of views as of the update timestamp.',
  PRIMARY KEY (view_increment_id),
  KEY page_id (page_id, update_timestamp)
)/*$wgDBTableOptions*/ COMMENT='Tracks increments in page views over time.';
