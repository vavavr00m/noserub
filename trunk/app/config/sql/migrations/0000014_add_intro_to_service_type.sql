ALTER TABLE `service_types` ADD `intro` VARCHAR( 64 ) NOT NULL AFTER `name`;
UPDATE `noserub`.`service_types` SET `intro` = '@user@ took a photo called @item@' WHERE `service_types`.`id` =1;
UPDATE `noserub`.`service_types` SET `intro` = '@user@ bookmarked @item@' WHERE `service_types`.`id` =2 ;
UPDATE `noserub`.`service_types` SET `intro` = '@user@ wrote a text about @item@' WHERE `service_types`.`id` =3;
UPDATE `noserub`.`service_types` SET `intro` = '@user@ plans to attend @item@' WHERE `service_types`.`id` =4;
UPDATE `noserub`.`service_types` SET `intro` = '@user@ says @item@' WHERE `service_types`.`id` =5;
UPDATE `noserub`.`service_types` SET `intro` = '@user@ made a video called @item@' WHERE `service_types`.`id` =6;
UPDATE `noserub`.`service_types` SET `intro` = '@user@ listens to @item@' WHERE `service_types`.`id` =7;