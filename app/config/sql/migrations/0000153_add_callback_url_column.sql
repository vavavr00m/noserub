ALTER TABLE  `request_tokens` ADD  `callback_url` VARCHAR(255) NOT NULL AFTER  `token_secret` ;