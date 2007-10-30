ALTER TABLE `identities` ADD `openid` VARCHAR(255) AFTER `password` ;
ALTER TABLE `identities` MODIFY `password` VARCHAR(128);