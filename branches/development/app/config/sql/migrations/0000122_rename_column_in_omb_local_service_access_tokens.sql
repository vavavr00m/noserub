TRUNCATE `omb_local_service_access_tokens`;
ALTER TABLE `omb_local_service_access_tokens` CHANGE identity_id contact_id INTEGER NOT NULL;