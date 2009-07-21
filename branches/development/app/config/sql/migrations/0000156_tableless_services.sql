ALTER TABLE  `accounts` ADD  `service` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER  `service_type_id` ;
ALTER TABLE  `accounts` ADD  `is_contact` TINYINT UNSIGNED NOT NULL AFTER  `service_type_id` ;
ALTER TABLE  `accounts` ADD INDEX (  `is_contact` ) ;
UPDATE accounts SET service='Flickr' WHERE service_id=1;
UPDATE accounts SET service='Delicious' WHERE service_id=2;
UPDATE accounts SET service='Ipernity' WHERE service_id=3;
UPDATE accounts SET service='_23hq' WHERE service_id=4;
UPDATE accounts SET service='Twitter' WHERE service_id=5;
UPDATE accounts SET service='RSS-Feed' WHERE service_id=8;
UPDATE accounts SET service='Upcoming' WHERE service_id=9;
UPDATE accounts SET service='Vimeo' WHERE service_id=10;
UPDATE accounts SET service='Lastfm' WHERE service_id=11;
UPDATE accounts SET service='Qype' WHERE service_id=12;
UPDATE accounts SET service='Magnolia' WHERE service_id=13;
UPDATE accounts SET service='Stumbleupon' WHERE service_id=14;
UPDATE accounts SET service='Corkd' WHERE service_id=15;
UPDATE accounts SET service='Dailymotion' WHERE service_id=16;
UPDATE accounts SET service='Zooomr' WHERE service_id=17;
UPDATE accounts SET service='Odeo' WHERE service_id=18;
UPDATE accounts SET service='Ilike' WHERE service_id=19;
UPDATE accounts SET service='Wevent' WHERE service_id=20;
UPDATE accounts SET service='Imthere' WHERE service_id=21;
UPDATE accounts SET service='Newsvine' WHERE service_id=22;
UPDATE accounts SET service='Jabber', is_contact=1 WHERE service_id=23;
UPDATE accounts SET service='Gtalk', is_contact=1 WHERE service_id=24;
UPDATE accounts SET service='Icq', is_contact=1 WHERE service_id=25;
UPDATE accounts SET service='Yim', is_contact=1 WHERE service_id=26;
UPDATE accounts SET service='Aim', is_contact=1 WHERE service_id=27;
UPDATE accounts SET service='Skype', is_contact=1 WHERE service_id=28;
UPDATE accounts SET service='Msn', is_contact=1 WHERE service_id=29;
UPDATE accounts SET service='Facebook' WHERE service_id=30;
UPDATE accounts SET service='Secondlife', is_contact=1 WHERE service_id=31;
UPDATE accounts SET service='Linkedin' WHERE service_id=32;
UPDATE accounts SET service='Xing' WHERE service_id=33;
UPDATE accounts SET service='Slideshare' WHERE service_id=34;
UPDATE accounts SET service='Plazes' WHERE service_id=35;
UPDATE accounts SET service='Scribd' WHERE service_id=36;
UPDATE accounts SET service='Moodmill' WHERE service_id=37;
UPDATE accounts SET service='Digg' WHERE service_id=38;
UPDATE accounts SET service='Misterwong' WHERE service_id=39;
UPDATE accounts SET service='Folkd' WHERE service_id=40;
UPDATE accounts SET service='Reddit' WHERE service_id=41;
UPDATE accounts SET service='Faves' WHERE service_id=42;
UPDATE accounts SET service='Simpy' WHERE service_id=43;
UPDATE accounts SET service='Deviantart' WHERE service_id=44;
UPDATE accounts SET service='Viddler' WHERE service_id=45;
UPDATE accounts SET service='Viddyou' WHERE service_id=46;
UPDATE accounts SET service='Gadugadu', is_contact=1 WHERE service_id=47;
UPDATE accounts SET service='Dopplr' WHERE service_id=48;
UPDATE accounts SET service='Orkut' WHERE service_id=49;
UPDATE accounts SET service='Kulando' WHERE service_id=50;
UPDATE accounts SET service='Wordpresscom' WHERE service_id=51;
UPDATE accounts SET service='Bloggerde' WHERE service_id=52;
UPDATE accounts SET service='Livejournal' WHERE service_id=53;
UPDATE accounts SET service='Ormigo' WHERE service_id=54;
UPDATE accounts SET service='Youtube' WHERE service_id=55;
UPDATE accounts SET service='Psn', is_contact=1 WHERE service_id=56;
UPDATE accounts SET service='Wii', is_contact=1 WHERE service_id=57;
UPDATE accounts SET service='Xbox', is_contact=1 WHERE service_id=58;
UPDATE accounts SET service='Bliptv' WHERE service_id=59;
UPDATE accounts SET service='Glugg' WHERE service_id=60;
UPDATE accounts SET service='Hemidemi' WHERE service_id=61;
UPDATE accounts SET service='Identica' WHERE service_id=62;
UPDATE accounts SET service='Disqus' WHERE service_id=63;
UPDATE accounts SET service='Ffffound' WHERE service_id=64;
UPDATE accounts SET service='Blippr' WHERE service_id=65;
UPDATE accounts SET service='Picasa' WHERE service_id=66;
UPDATE accounts SET service='Fanfou' WHERE service_id=67;
UPDATE accounts SET service='Jaiku' WHERE service_id=68;
UPDATE accounts SET service='Qik' WHERE service_id=69;
UPDATE accounts SET service='Brightkite' WHERE service_id=70;
UPDATE accounts SET service='Blipfm' WHERE service_id=71;
UPDATE accounts SET service='Joost' WHERE service_id=72;
UPDATE accounts SET service='Zootool' WHERE service_id=73;
UPDATE accounts SET service='Mento' WHERE service_id=74;
UPDATE accounts SET service='Fotolog' WHERE service_id=75;
UPDATE accounts SET service='Seesmic' WHERE service_id=76;
UPDATE accounts SET service='Backtype' WHERE service_id=77;
UPDATE accounts SET service='Twitpic' WHERE service_id=78;
UPDATE accounts SET service='Skitch' WHERE service_id=79;
UPDATE accounts SET service='Github' WHERE service_id=80;
UPDATE accounts SET service='Googlecode' WHERE service_id=81;
UPDATE accounts SET service='Scratch' WHERE service_id=82;
UPDATE accounts SET service='Sixgroups' WHERE service_id=83;
UPDATE accounts SET service='Twelveseconds' WHERE service_id=84;
UPDATE accounts SET service='Imgly' WHERE service_id=85;
UPDATE accounts SET service='Readernaut' WHERE service_id=86;
UPDATE accounts SET service='Snipplr' WHERE service_id=87;
UPDATE accounts SET service='Wikipedia' WHERE service_id=88;
ALTER TABLE `accounts` DROP `service_id`;
ALTER TABLE  `accounts` CHANGE  `service_type_id`  `service_type` SMALLINT UNSIGNED NOT NULL;
ALTER TABLE  `entries` CHANGE  `service_type_id`  `service_type` SMALLINT UNSIGNED NOT NULL;
DROP TABLE `service_types`;
DROP TABLE `services`;