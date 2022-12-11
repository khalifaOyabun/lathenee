CREATE TABLE IF NOT EXISTS `website` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(120) NOT NULL,
  `ssl` tinyint(4) NOT NULL,
  `alias` longtext,
  `status` varchar(20) NOT NULL DEFAULT 'CREATE',
  `directory` varchar(180) DEFAULT NULL,
  `errors` longtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain` (`domain`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;