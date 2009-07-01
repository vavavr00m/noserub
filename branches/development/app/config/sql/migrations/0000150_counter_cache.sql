ALTER TABLE  `groups` ADD  `entry_count` INT( 11 ) UNSIGNED NOT NULL COMMENT  'counter cache' AFTER  `slug`;
ALTER TABLE  `entries` ADD  `comment_count` INT( 11 ) UNSIGNED NOT NULL COMMENT  'counter cache' AFTER  `longitude`;
ALTER TABLE  `entries` ADD  `favorite_count` INT( 11 ) UNSIGNED NOT NULL COMMENT  'counter cache' AFTER  `comment_count`;