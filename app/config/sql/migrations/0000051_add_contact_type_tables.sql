CREATE TABLE `contact_types` (`id` int(11) unsigned NOT NULL auto_increment, `identity_id` int(11) unsigned NOT NULL, `name` varchar(255) NOT NULL,`created` datetime NOT NULL,`modified` datetime NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `contacts_contact_types` (`contact_id` int(11) unsigned NOT NULL,`contact_type_id` int(11) unsigned NOT NULL) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `noserub_contact_types` (`id` int(11) unsigned NOT NULL auto_increment, `name` varchar(255) NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `contacts_noserub_contact_types` (`contact_id` int(11) unsigned NOT NULL,`noserub_contact_type_id` int(11) unsigned NOT NULL) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
