DELETE FROM `request_tokens`;
ALTER TABLE  `request_tokens` ADD  `verifier` VARCHAR(255) NOT NULL AFTER  `callback_url` ;