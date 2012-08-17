
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `login` varchar(200) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `email` varchar(220) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `password` varchar(220) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `salt` varchar(200) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL,
  `activated_at` datetime,
  `is_banned` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `idx_search` (`email`,`login`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=10117 ;

INSERT INTO `users` (`id`, `salt`, `login`, `email`, `password`, `created_at`, `activated_at`, `is_banned`, ) VALUES
(10000, 'dd2383b9b428500be266fa6289ac5df5', 'admin', 'admin@example.com', '1f6ed8a041e616e4e0130df9c8cfced442109823750c37460', utc_timestamp(), utc_timestamp(), 0);

