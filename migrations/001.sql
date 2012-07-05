
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `md5_id` varchar(200) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `login` varchar(200) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `email` varchar(220) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `password` varchar(220) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL,
  `users_ip` varchar(200) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `is_activated` int(1) NOT NULL DEFAULT '0',
  `activation_code` int(10) NOT NULL DEFAULT '0',
  `banned` int(1) NOT NULL DEFAULT '0',
  `ckey` varchar(220) COLLATE latin1_general_ci DEFAULT '',
  `ctime` varchar(220) COLLATE latin1_general_ci DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_email` (`email`),
  FULLTEXT KEY `idx_search` (`email`,`login`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=10117 ;


INSERT INTO `users` (`id`, `md5_id`, `login`, `email`, `access_level`, `pwd`, `created_at`, `users_ip`, `approved`, `activation_code`, `banned`, `ckey`, `ctime`) VALUES
(10000, 'dd2383b9b428500be266fa6289ac5df5', 'admin', 'admin@example.com', 5, '1f6ed8a041e616e4e0130df9c8cfced442109823750c37460', '2012-03-20 20:28:48', '71.93.69.9', 1, 4159, 0, 'sov9wdh', '1333206333');
