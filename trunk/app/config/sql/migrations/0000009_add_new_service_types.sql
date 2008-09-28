DELETE FROM `service_types` WHERE id > 5;
UPDATE `service_types` SET `token` = 'photo', `name` = 'Photos' WHERE `id` =1;
INSERT INTO `service_types` (`id`, `token`, `name`, `created`, `modified`) VALUES (6 , 'video', 'Videos', NOW() , NOW()), (7 , 'audio', 'Audio', NOW() , NOW());
UPDATE services SET service_type_id=6 WHERE id=10;
UPDATE services SET service_type_id=7 WHERE id=11;
UPDATE accounts SET service_type_id=6 WHERE service_id=10;
UPDATE accounts SET service_type_id=7 WHERE service_id=11;