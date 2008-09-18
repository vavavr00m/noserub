TRUNCATE `nonces`;
ALTER TABLE `nonces` DROP COLUMN `id`;
ALTER TABLE `nonces` DROP COLUMN `consumer_id`;
ALTER TABLE `nonces` ADD `consumer` VARCHAR(255) NOT NULL FIRST;
ALTER TABLE `nonces` ADD `created` DATETIME NOT NULL AFTER `nonce` ;
ALTER TABLE `nonces` ADD PRIMARY KEY (`consumer`, `token`, `nonce`) ;