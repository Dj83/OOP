CREATE TABLE IF NOT EXISTS `#__bulletins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `catid` int(10) unsigned NOT NULL DEFAULT '0',
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `priority` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `attachments` text NOT NULL,
  `usergroups` varchar(325) NOT NULL,
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `useridto_state` (`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

