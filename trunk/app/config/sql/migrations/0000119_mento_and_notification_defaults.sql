INSERT INTO `services` (`id`, `internal_name`, `name`, `url`, `service_type_id`, `help`, `icon`, `has_feed`, `is_contact`, `minutes_between_updates`) VALUES (74, 'Mento', 'Mento', 'http://www.mento.info', 2, 'Please enter your Mento Username. ', 'mento.gif', 1, 0, 30);
UPDATE `identities` SET notify_contact=1, notify_comment=1, notify_favorite=1 WHERE `allow_emails`=2;