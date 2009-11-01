ALTER TABLE  `twitter_accounts` ADD  `access_token_key` VARCHAR(255) NOT NULL AFTER  `identity_id` ;
ALTER TABLE  `twitter_accounts` ADD  `access_token_secret` VARCHAR(255) NOT NULL AFTER  `access_token_key` ;
