INSERT INTO `service_types` (`id`, `token`, `name`, `created`, `modified`) VALUES (NULL, 'micropublish', 'Micropublishing', NOW(), NOW());
UPDATE `services` SET `service_type_id` = '5' WHERE `services`.`id` =5;
UPDATE `services` SET `service_type_id` = '5' WHERE `services`.`id` =6;
DELETE FROM `services` WHERE `services`.`id` =7;
UPDATE `services` SET `name` = 'Any RSS-Feed' WHERE `services`.`id` =8;
ALTER TABLE `identities` ADD `address` VARCHAR( 128 ) NOT NULL AFTER `lastname` ,ADD `latitude` DOUBLE NOT NULL AFTER `address` ,ADD `longitute` DOUBLE NOT NULL AFTER `latitude` ,ADD `birthday` DATE NOT NULL AFTER `longitute` ,ADD `sex` TINYINT( 2 ) UNSIGNED NOT NULL AFTER `birthday` ;