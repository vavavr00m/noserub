RENAME TABLE `omb_services` TO `omb_local_services`;
ALTER TABLE `omb_service_access_tokens` CHANGE omb_service_id omb_local_service_id INTEGER NOT NULL;