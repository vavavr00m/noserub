TRUNCATE `omb_access_tokens`;
ALTER TABLE `omb_access_tokens` CHANGE identity_id contact_id INTEGER NOT NULL;