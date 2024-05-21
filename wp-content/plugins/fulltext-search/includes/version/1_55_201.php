<?php

return array(
	'callback' => function()
	{
		/*
		global $wpfts_core;

		$dbscheme = array(
			'qlog' => array(
				'cols' => array(
					'id' => array('int(11)', 'NO', 'PRI', null, 'auto_increment'),
					'query' => array('longtext', 'YES_NULL'),
					'query_type' => array('varchar(255)', 'NO', '', ''),
					'preset' => array('varchar(255)', 'NO', '', ''),
					'n_results' => array('int(11)', 'NO', '', '0'),
					'q_time' => array('float(10,6)', 'NO', '', '0'),
					'max_ram' => array('bigint(20)', 'NO', '', '0'),
					'user_id' => array('bigint(20)', 'NO', '', '0'),
					'req_ip' => array('varchar(40)', 'NO', '', ''),
					'ref_url' => array('text', 'YES_NULL'),
					'insert_dt' => array('datetime', 'NO', '', '1970-01-01 00:00:00'),
					'wpq_params' => array('longtext'),
					'ext' => array('longtext'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'query_type' => array(1, '`query_type`'),
					'preset' => array(1, '`preset`'),
					'req_ip' => array(1, '`req_ip`'),
					'user_id' => array(1, '`user_id`'),
				),
				'create' => "CREATE TABLE `wpftsi_qlog` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`query` longtext COLLATE utf8mb4_unicode_520_ci,
					`query_type` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
					`preset` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
					`n_results` int(11) NOT NULL DEFAULT '0',
					`q_time` float NOT NULL DEFAULT '0',
					`max_ram` bigint(20) NOT NULL DEFAULT '-1',
					`user_id` bigint(20) NOT NULL DEFAULT '0',
					`req_ip` varchar(40) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
					`ref_url` longtext COLLATE utf8mb4_unicode_520_ci,
					`insert_dt` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
					`wpq_params` longtext COLLATE utf8mb4_unicode_520_ci,
					`ext` longtext COLLATE utf8mb4_unicode_520_ci,
					PRIMARY KEY (`id`),
					KEY `query_type` (`query_type`),
					KEY `preset` (`preset`),
					KEY `req_ip` (`req_ip`),
					KEY `user_id` (`user_id`)
				  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci",
			),
			'rawcache' => array(
				'cols' => array(
					'id' => array('int(10) unsigned', 'NO', 'PRI', null, 'auto_increment'),
					'object_id' => array('bigint(20)', 'NO', '', 0),
					'object_type' => array('varchar(150)', 'YES'),
					'cached_dt' => array('datetime', 'YES'),
					'insert_dt' => array('datetime', 'YES'),
					'method_id' => array('varchar(150)', 'YES', '', ''),
					'data' => array('longtext', 'YES'),
					'error' => array('text', 'YES'),
					'filename' => array('text', 'YES'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'object_id_and_type' => array(0, '`object_id`,`object_type`'),
				),
				'create' => "CREATE TABLE `wpftsi_rawcache` (
  					`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  					`object_id` bigint(20) NOT NULL DEFAULT '0',
  					`object_type` varchar(255) DEFAULT NULL,
  					`cached_dt` datetime DEFAULT NULL,
  					`insert_dt` datetime DEFAULT NULL,
  					`method_id` varchar(255) DEFAULT '',
  					`data` longtext,
  					`error` text,
  					`filename` text,
  					PRIMARY KEY (`id`),
  					UNIQUE KEY `object_id_and_type` (`object_id`,`object_type`) USING BTREE
				  ) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8",
			),
		);

		$index = $wpfts_core->GetIndex();

		$qr = $index->CreateMySQLQuery($dbscheme);

		$success = true;
		foreach ($qr as $k => $d) {
			
			$q = 'drop table if exists `'.$index->dbprefix().$k.'`';
			$wpfts_core->db->query($q);
			
			$wpfts_core->db->query($d['create2']);
			if ($wpfts_core->db->get_last_error()) {
				$index->log('Can\'t create table "'.$index->dbprefix().$k.'": '.$wpfts_core->db->get_last_error());
				$success = false;
			}	
		}

		return $success;
		*/

		return true;
	},
);
