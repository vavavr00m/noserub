TRUNCATE `nonces`;
ALTER TABLE `nonces` DROP COLUMN `id`;
ALTER TABLE `nonces` DROP COLUMN `consumer_id`;
ALTER TABLE `nonces` MODIFY COLUMN `nonce` VARCHAR(32) NOT NULL;
ALTER TABLE `nonces` MODIFY COLUMN `token` VARCHAR(32) NOT NULL;
ALTER TABLE `nonces` ADD `consumer` VARCHAR(32) NOT NULL FIRST;
ALTER TABLE `nonces` ADD `created` DATETIME NOT NULL AFTER `nonce` ;
ALTER TABLE `nonces` ADD PRIMARY KEY (`consumer`, `token`, `nonce`) ;