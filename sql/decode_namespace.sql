-- MySQL version of the database schema for the View Stats extension.
-- License: GNU GPL v2+
-- Author: Scott Caldwell, Steven Orvis

CREATE FUNCTION `decode_namespace`(`ns_id` INT) RETURNS varchar(50)
    NO SQL
    DETERMINISTIC
begin

	if (ns_id = 0) then
		return '';
	end if;

	
	if (ns_id = 1) then
		return 'Talk:';
	end if;

	
	if (ns_id = 2) then
		return 'User:';
	end if;

	
	if (ns_id = 3) then
		return 'User_talk:';
	end if;

	
	if (ns_id = 4) then
		return 'Project:';
	end if;

	
	if (ns_id = 5) then
		return 'Project_talk:';
	end if;

	
	if (ns_id = 6) then
		return 'File:';
	end if;

	
	if (ns_id = 7) then
		return 'File_talk:';
	end if;

	
	if (ns_id = 8) then
		return 'MediaWiki:';
	end if;

	
	if (ns_id = 9) then
		return 'MediaWiki_talk:';
	end if;

	
	if (ns_id = 10) then
		return 'Template:';
	end if;

	
	if (ns_id = 11) then
		return 'Template_talk:';
	end if;

	
	if (ns_id = 12) then
		return 'Help:';
	end if;

	
	if (ns_id = 13) then
		return 'Help_talk:';
	end if;

	
	if (ns_id = 14) then
		return 'Category:';
	end if;

	
	if (ns_id = 15) then
		return 'Category_talk:';
	end if;

	
	if (ns_id = -1) then
		return 'Special:';
	end if;

	
	if (ns_id = -2) then
		return 'Media:';
	end if;

	
	if (ns_id = 3000) then
		return 'Monitoring:';
	end if;

	if (ns_id = 3001) then
		return 'Monitoring_talk:';
	end if;

	return '';
end
