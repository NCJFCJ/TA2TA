<?php

/**  
 * Copyright 2013-2023 Epsiloncool
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 ******************************************************************************
 *  I am thank you for the help by buying PRO version of this plugin 
 *  at https://fulltextsearch.org/ 
 *  It will keep me working further on this useful product.
 ******************************************************************************
 * 
 *  @copyright 2013-2023
 *  @license GPLv3
 *  @package WP Fast Total Search
 *  @author Epsiloncool <info@e-wm.org>
 */

class WPFTS_Database
{
	public function log($text)
	{
		global $wpfts_core;

		$wpfts_core->writeToLog('DB', $text);
	}

	public function createColumnQuery($col_name, $col_props)
	{
		$ai = false;
		$ss = '`'.$col_name.'` '.$col_props[0].' '.((isset($col_props[1]) && ($col_props[1] == 'NO')) ? 'NOT NULL' : 'NULL');
		if (isset($col_props[3])) {
			$ss .= ' default \''.$col_props[3].'\'';
		}
		if ((isset($col_props[4])) && ($col_props[4] == 'auto_increment')) {
			$ss .= ' auto_increment';
			$ai = true;
		}
		
		return array($ss, $ai);
	}

	/**
	 * This method creates MySQL query part to add new index
	 * 
	 * $index_props - index props in the template format ( 'key_name' => array(1, '`col1`,`col2`(50)'))
	 */
	public function createIndexQuery($kk, $dd)
	{
		$ss = '';
		if ($kk == 'PRIMARY') {
			$ss = 'PRIMARY INDEX';
		} else {
			if ($dd[0] == 0) {
				$ss = 'UNIQUE INDEX `'.$kk.'`';
			} else {
				$ss = 'INDEX `'.$kk.'`';
			}
		}
		$ws = explode(',', $dd[1]);
		$zz = array();
		foreach ($ws as $z) {
			$zz[] = trim($z);
		}
		$ss .= ' ('.implode(',', $zz).')';
		
		return $ss;
	}

	public function createColumnProps($col_props)
	{
		$def = array(
			'Type' => '',
			'Null' => '',
			'Extra' => '',
		);
		$def['Type'] = isset($col_props[0]) ? $col_props[0] : '';
		$def['Null'] = (isset($col_props[1]) && ($col_props[1] == 'NO')) ? 'NO' : 'YES';

		if (isset($col_props[3])) {
			$def['Default'] = $col_props[3];
		}

		if (isset($col_props[4]) && ($col_props[4] == 'auto_increment')) {
			$def['Extra'] = 'auto_increment';
		}

		return $def;
	}

	public function createTableQuery($table_name, $cols, $index)
	{
		global $wpdb, $wpfts_core;

		$prx = $wpfts_core->dbprefix();

		$engine_type = 'InnoDB';
	
		$s = 'CREATE TABLE `'.$prx.$table_name.'` ('."\n";
		
		$cs = array();
		$ai = false;
		foreach ($cols as $kk => $dd) {
			$t = $this->createColumnQuery($kk, $dd);
			$cs[] = $t[0];
			if ($t[1]) {
				$ai = true;
			}
		}
	
		$iz = array();
		foreach ($index as $kk => $dd) {
			$ss = '';
			if ($kk == 'PRIMARY') {
				$ss = 'PRIMARY KEY';
			} else {
				if ($dd[0] == 0) {
					$ss = 'UNIQUE KEY `'.$kk.'`';
				} else {
					$ss = 'KEY `'.$kk.'`';
				}
			}
			$ws = explode(',', $dd[1]);
			$zz = array();
			foreach ($ws as $z) {
				$zz[] = ''.$z.'';
			}
			$ss .= ' ('.implode(',', $zz).')';
			
			$iz[] = $ss;
		}
		
		$s .= implode(",\n", $cs);
		
		if (count($iz) > 0) {
			$s .= ",\n".implode(",\n", $iz);
		}
		
		$s .= "\n".') ENGINE='.$engine_type.($ai ? ' AUTO_INCREMENT=1' : ''); // .' DEFAULT CHARSET=utf8';
		
		$collate = $wpdb->get_charset_collate();
		if (strlen($collate) > 0) {
			$s .= ' '.$collate;
		}

		return $s;
	}

	/**
	 * This method will compare existing table with given parameters and upgrade the table
	 * according to given parameters.
	 * 
	 * ##--- Use with caution, it may delete existing data! ---##
	 * 
	 * $table_name - The table name without a prefix
	 * $cols - the list of columns with formatted properties, or "false" in case we want to delete the table
	 * $index - the list of indexes with formatted properties
	 * $is_enable_delete
	 * 
	 */
	public function checkDBTable($table_name, $cols, $index, $is_enable_delete = false)
	{
		global $wpfts_core;

		$this->log('checkDBTable() for "'.$table_name.'"');

		if (strlen($table_name) < 1) {
			// No table given
			$this->log('No table given');
			return false;
		}

		$prx = $wpfts_core->dbprefix();

		if ($cols === false) {
			// We need to delete the table
			if ($is_enable_delete) {
				$q = 'drop table if exists `'.$prx.$table_name.'`';

				$this->log('Table should be removed. Query: '.$q);

				$wpfts_core->db->query($q);

				return true;
			} else {
				// Table removing is disabled
				$this->log('Table should be removed. Table removing is disabled');
				return false;
			}
		}

		// Check if the table exists
		$q = 'show tables like "'.$prx.$table_name.'"';
		$r2 = $wpfts_core->db->get_results($q, ARRAY_A);

		if ($r2 && (count($r2) > 0)) {
			// Table exists
			$this->log('Table exists, compare columns');

			// Compare columns
			$q = 'show columns from `'.$prx.$table_name.'`';
			$res = $wpfts_core->db->get_results($q, ARRAY_A);	

			//$this->log('Existing columns data: '.print_r($res, true));

			$ex_cols = array();
			foreach ($res as $d) {
				$ex_cols[strtolower($d['Field'])] = $d;
			}

			$prev_column = false;
			foreach ($cols as $k => $d) {

				$this->log('Processing column: '.$k);

				if (isset($ex_cols[strtolower($k)])) {
					$exc = $ex_cols[strtolower($k)];

					// Exists, compare props
					$new_props = $this->createColumnProps($d);

					$is_equal = true;
					if (strtolower($new_props['Type']) != strtolower($exc['Type'])) {
						$this->log('The Type is not equal: "'.strtolower($new_props['Type']).'" vs "'.strtolower($exc['Type']).'"');
						$is_equal = false;
					}
					if ($is_equal && ($new_props['Null'] != $exc['Null'])) {
						$this->log('The Null is not equal: "'.$new_props['Null'].'" vs "'.$exc['Null'].'"');
						$is_equal = false;
					}
					if ($is_equal && ($new_props['Extra'] != $exc['Extra'])) {
						$this->log('The Extra is not equal: "'.$new_props['Extra'].'" vs "'.$exc['Extra'].'"');
						$is_equal = false;
					}
					if ($is_equal) {
						// Compare default value
						if (isset($exc['Default'])) {
							if (isset($new_props['Default'])) {
								if (preg_match('~float|double|int|bigint~', $new_props['Type'])) {
									if (abs(floatval($new_props['Default']) - floatval($exc['Default'])) > 0.000001) {
										$this->log('The Default is not equal: "'.$new_props['Default'].'" vs "'.$exc['Default'].'"');
										$is_equal = false;
									}
								} else {
									if ($new_props['Default'] != $exc['Default']) {
										$this->log('The Default is not equal: "'.$new_props['Default'].'" vs "'.$exc['Default'].'"');
										$is_equal = false;
									}
								}	
							} else {
								// The Default value is not set in $new_props
								$this->log('The Default is not equal: null vs "'.$exc['Default'].'"');
								$is_equal = false;
							}
						} else {
							if (isset($new_props['Default'])) {
								// Existing default is not set
								// We going to change default value
								$this->log('The Default is not equal: "'.$new_props['Default'].'" vs null');
								$is_equal = false;
							}
						}
					}

					if (!$is_equal) {
						// Modify column
						$this->log('Modifying the column, because existing one is not equal to template');
						$this->log('Existing column data: '.print_r($exc, true));
						$this->log('Template column data: '.print_r($new_props, true));

						$t = $this->createColumnQuery($k, $d);

						$q = 'alter table `'.$prx.$table_name.'` modify column '.$t[0];

						$this->log('Modify column: '.$q);

						$wpfts_core->db->query($q);

						$this->log('Last error: '.$wpfts_core->db->get_last_error());
					}

					unset($ex_cols[strtolower($k)]);
				} else {
					// Not exists, create column
					$t = $this->createColumnQuery($k, $d);

					$q = 'alter table `'.$prx.$table_name.'` add column '.$t[0];
					if ($prev_column) {
						$q .= ' after `'.$prev_column.'`';
					}

					$this->log('No column, create: '.$q);

					$wpfts_core->db->query($q);

					$this->log('Last error: '.$wpfts_core->db->get_last_error());
				}

				$prev_column = $k;
			}

			if (count($ex_cols) > 0) {

				$this->log('Removing extra columns: '.print_r($ex_cols, true));

				if ($is_enable_delete) {
					// Remove extra columns
					foreach ($ex_cols as $k => $d) {
						$q = 'alter table `'.$prx.$table_name.'` drop column `'.$k.'`';

						$this->log('Query: '.$q);

						$wpfts_core->db->query($q);

						$this->log('Last error: '.$wpfts_core->db->get_last_error());
					}
				} else {
					$this->log('Removing disabled');
				}
			}

			// Compare indexes
			$q = 'show index from `'.$prx.$table_name.'`';
			$res = $wpfts_core->db->get_results($q, ARRAY_A);	
			
			//$this->log('Column index data: '.print_r($res, true));

			// Pack to template format, since we going to compare in template normalized format
			$a = $res;
			// Order in Key_name asc, Seq_in_index asc
			usort($a, function($v1, $v2)
			{
				$t = strcmp($v1['Key_name'], $v2['Key_name']);
				if ($t == 0) {
					return $v1['Seq_in_index'] - $v2['Seq_in_index'];
				} else {
					return $t;
				}
			});

			$ex_inx = array();
			$uses = array();
			foreach ($a as $k => $d) {
				if (isset($uses[$k])) {
					continue;
				}

				$uses[$k] = 1;
				$non_unique = $d['Non_unique'];
				$key_name = $d['Key_name'];
				$ord = $d['Seq_in_index'];
				$tcols = array();
				if (isset($d['Sub_part']) && is_numeric($d['Sub_part'])) {
					$tcols[] = '`'.$d['Column_name'].'`('.$d['Sub_part'].')';
				} else {
					$tcols[] = '`'.$d['Column_name'].'`';
				}

				// Check if we have more columns for this key
				$ord ++;
				foreach ($a as $kk => $dd) {
					if (!isset($uses[$kk])) {
						if (($dd['Key_name'] == $key_name) && ($dd['Seq_in_index'] == $ord)) {
							if (isset($dd['Sub_part']) && is_numeric($dd['Sub_part'])) {
								$tcols[] = '`'.$dd['Column_name'].'`('.$dd['Sub_part'].')';
							} else {
								$tcols[] = '`'.$dd['Column_name'].'`';
							}
							$uses[$kk] = 1;
							$ord ++;
						}
					}
				}

				$ex_inx[$key_name] = array(intval($non_unique), implode(',', $tcols));
			}

			//$this->log('Column index data (normalized): '.print_r($ex_inx, true));

			// Normalize template data
			$new_index = array();
			foreach ($index as $k => $d) {
				$t2 = explode(',', $d[1]);
				$tcols = array();
				foreach ($t2 as $dd) {
					$v = array();
					if (preg_match('~^\`?([a-zA-Z_0-9]+)\`?\s*(\(([0-9]+)\))?$~', trim($dd), $v)) {
						if (isset($v[3])) {
							$tcols[] = '`'.$v[1].'`('.$v[3].')';
						} else {
							$tcols[] = '`'.$v[1].'`';
						}
					} else {
						$this->log('Unable to unpack index template "'.$k.'": '.$dd);
						return false;
					}	
				}
				$new_index[$k] = array(intval($d[0]), implode(',', $tcols));
			}

			if ($new_index) {
				//$this->log('Template index data: '.print_r($new_index, true));
			
				// Let's compare
				foreach ($new_index as $k => $d) {
					if (isset($ex_inx[$k])) {
						// Index exists, compare properties
						$exc = $ex_inx[$k];

						$is_equal = true;
						if ($d[0] != $exc[0]) {
							$this->log('Index NULL/NOT-NULL is not equal');
							$is_equal = false;
						}
						if ($d[1] != $exc[1]) {
							$this->log('Index column set is not equal');
							$is_equal = false;
						}

						if (!$is_equal) {
							// Rebuild the index
							$this->log('Changing index "'.$k.'"');
							$this->log('Existing index: '.print_r($exc, true));
							$this->log('Template index: '.print_r($d, true));

							$tt = $this->createIndexQuery($k, $d);

							$q = 'alter table `'.$prx.$table_name.'` drop index `'.$k.'`, add '.$tt;
	
							$this->log('Query: '.$q);
	
							$wpfts_core->db->query($q);
	
							$this->log('Last error: '.$wpfts_core->db->get_last_error());		
						}

						unset($ex_inx[$k]);
					} else {
						// We need to add the index
						$this->log('No index "'.$k.'" found, let\'s create it');

						$tt = $this->createIndexQuery($k, $d);

						$q = 'alter table `'.$prx.$table_name.'` add '.$tt;

						$this->log('Query: '.$q);

						$wpfts_core->db->query($q);

						$this->log('Last error: '.$wpfts_core->db->get_last_error());	
					}
				}

				if (count($ex_inx) > 0) {

					$this->log('Removing extra indexes: '.print_r($ex_inx, true));
	
					if ($is_enable_delete) {
						// Remove extra indexes
						foreach ($ex_inx as $k => $d) {
							$q = 'alter table `'.$prx.$table_name.'` drop index `'.$k.'`';
	
							$this->log('Query: '.$q);
	
							$wpfts_core->db->query($q);
	
							$this->log('Last error: '.$wpfts_core->db->get_last_error());
						}
					} else {
						$this->log('Removing disabled');
					}
				}

			} else {
				return false;
			}

			return true;
		} else {
			// No table exists
			// Create the table from scratch
			$q = $this->createTableQuery($table_name, $cols, $index);

			$this->log('The table does not exist, create new: '.$q);

			$wpfts_core->db->query($q);

			$this->log('Last error: '.$wpfts_core->db->get_last_error());

			return true;
		}
	}

	public function updateDB()
	{
		$this->log('Called updateDB()');

		$sch = $this->getDBScheme();

		$is_ok = true;
		foreach ($sch as $k => $d) {
			if (!$this->checkDBTable($k, $d['cols'], $d['index'], false)) {
				$is_ok = false;
				break;
			}
		}

		$this->log('updateDB() result: '.($is_ok ? 'OK' : 'FAIL'));
		
		return $is_ok;
	}

	public function rebuildDBTables($only_listed = false) 
	{
		global $wpfts_core;
		
		$this->log('Rebuilding tables completely');

		$success = true;
		
		$prx = $wpfts_core->dbprefix();

		$sch = $this->getDBScheme();

		foreach ($sch as $k => $d) {
			
			if ((!is_array($only_listed)) || (is_array($only_listed) && in_array($k, $only_listed))) {
				$q = 'drop table if exists `'.$prx.$k.'`';
				$wpfts_core->db->query($q);
			
				$q = $this->createTableQuery($k, $d['cols'], $d['index']);
				$wpfts_core->db->query($q);
				if ($wpfts_core->db->get_last_error()) {
					$this->log('Can\'t create table "'.$prx.$k.'": '.$wpfts_core->db->get_last_error());
					$success = false;
				}
			}
		}
		if ($success) {
			$wpfts_core->set_option('current_db_version', WPFTS_VERSION);
		}
		
		$this->log('Rebuilding tables completed, result = '.($success ? 'OK' : 'FAIL'));

		return $success;
	}

	public function getDBScheme()
	{
		$dbscheme = array(
			'docs' => array(
				'cols' => array(
					// name => type, isnull, keys, default, extra
					'id' => array('int(11)', 'NO', 'PRI', null, 'auto_increment'),
					'index_id' => array('int(11)', 'NO', 'MUL', '0'),
					'token' => array('varchar(255)', 'NO', 'MUL'),
					'n' => array('int(10) unsigned', 'NO', '', '0'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'token' => array(1, '`token`(190)'),
					'index_id' => array(1, '`index_id`'),
				),
				'create' => "CREATE TABLE `wpftsi_docs` (
								`id` int(11) NOT NULL auto_increment,
								`index_id` int(11) NOT NULL,
								`token` varchar(255) NOT NULL,
								`n` int(10) unsigned NOT NULL,
								PRIMARY KEY  (`id`),
								KEY `token` (`token`),
								KEY `index_id` USING BTREE (`index_id`)
							) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8",
			),
			'index' => array(
				'cols' => array(
					'id' => array('int(10) unsigned', 'NO', 'PRI', null, 'auto_increment'),
					'tid' => array('bigint(10) unsigned', 'NO', 'MUL'),		
					'tsrc' => array('varchar(255)', 'NO', 'MUL'),		
					'tdt' => array('datetime', 'NO', '', '1970-01-01 00:00:00'),
					'build_time' => array('int(11)', 'NO', 'MUL', '0'),	
					'update_dt' => array('datetime', 'NO', '', '1970-01-01 00:00:00'),
					'force_rebuild' => array('tinyint(4)', 'NO', 'MUL', '0'),
					'locked_dt' => array('datetime', 'NO', 'MUL', '1970-01-01 00:00:00'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'tid_tsrc_unique' => array(0, '`tid`,`tsrc`(100)'),
					'tid' => array(1, '`tid`'),
					'build_time' => array(1, '`build_time`'),
					'force_rebuild' => array(1, '`force_rebuild`'),
					'locked_dt' => array(1, '`locked_dt`'),
					'tsrc' => array(1, '`tsrc`(100)'),
				),
				'create' => "CREATE TABLE `wpftsi_index` (
								`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
								`tid` bigint(10) unsigned NOT NULL,
								`tsrc` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
								`tdt` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
								`build_time` int(11) NOT NULL DEFAULT '0',
								`update_dt` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
								`force_rebuild` tinyint(4) NOT NULL DEFAULT '0',
								`locked_dt` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
								PRIMARY KEY (`id`),
								UNIQUE KEY `tid_tsrc_unique` (`tid`,`tsrc`(100)) USING BTREE,
								KEY `tid` (`tid`),
								KEY `build_time` (`build_time`),
								KEY `force_rebuild` (`force_rebuild`),
								KEY `locked_dt` (`locked_dt`),
								KEY `tsrc` (`tsrc`(100)) USING BTREE
				  			) ENGINE=InnoDB AUTO_INCREMENT=45645 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci",
				),
			'stops' => array(
				'cols' => array(
					'id' => array('int(10) unsigned', 'NO', 'PRI', null, 'auto_increment'),
					'word' => array('varchar(255)', 'NO', 'UNI'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'word' => array(0, '`word`(190)'),
				),
				'create' => 'CREATE TABLE `wpftsi_stops` (
								`id` int(10) unsigned NOT NULL auto_increment,
								`word` varchar(32) character set utf8 collate utf8_bin NOT NULL,
								PRIMARY KEY  (`id`),
								UNIQUE KEY `word` (`word`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8',
			),
			'vectors' => array(
				'cols' => array(
					'wid' => array('int(11)', 'NO', 'PRI'),
					'did' => array('int(11)', 'NO', 'PRI'),
					'wn' => array('int(11)', 'NO', '', '0'),
				),
				'index' => array(
					'did_wn' => array(0, '`did`,`wn`'),
					'wid' => array(1, '`wid`'),
					'did' => array(1, '`did`'),
				),
				'create' => 'CREATE TABLE `wpftsi_vectors` (
								`wid` int(11) NOT NULL,
								`did` int(11) NOT NULL,
								`wn` int(11) NOT NULL,
								UNIQUE KEY `wid` (`wid`,`did`,`wn`),
								KEY `wid_2` (`wid`),
								KEY `did` (`did`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8',
			),
			'words' => array(
				'cols' => array(
					'id' => array('int(11)', 'NO', 'PRI', null, 'auto_increment'),
					'word' => array('varchar(255)', 'NO', 'UNI'),
					'act' => array('int(11)', 'NO', '', '-1'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'word' => array(0, '`word`(190)'),
					'act' => array(1, '`act`'),
				),
				'create' => 'CREATE TABLE `wpftsi_words` (
								`id` int(11) NOT NULL auto_increment,
								`word` varchar(255) character set utf8 collate utf8_bin NOT NULL,
								PRIMARY KEY  (`id`),
								UNIQUE KEY `word` (`word`)
							) ENGINE=MyISAM AUTO_INCREMENT=173320 DEFAULT CHARSET=utf8',
			),
			'tw' => array(
				'cols' => array(
					'id' => array('int(11)', 'NO', 'PRI', null, 'auto_increment'),
					'w' => array('varchar(255)', 'NO'),
					'did' => array('int(11)', 'NO', '', '0'),
					'wn' => array('int(11)', 'NO', '', '0'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'w' => array(1, '`w`(190)'),
				),
				'create' => "CREATE TABLE `wpfts_tw` (
								`w` varchar(255) NOT NULL,
								`did` int(11) NOT NULL DEFAULT '0',
								`wn` int(11) NOT NULL DEFAULT '0',
								KEY `w` (`w`)
				  			) ENGINE=InnoDB DEFAULT CHARSET=utf8",
			),
			'vc' => array(
				'cols' => array(
					'id' => array('int(11)', 'NO', 'PRI', null, 'auto_increment'),
					'wid' => array('int(11)', 'NO', '', '0'),
					'upd_dt' => array('datetime', 'NO', '', '1970-01-01 00:00:00'),
					'vc' => array('longblob'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'wid' => array(1, '`wid`'),
				),
				'create' => "CREATE TABLE `wpftsi_vc` (
  								`id` int(11) NOT NULL AUTO_INCREMENT,
  								`wid` int(11) NOT NULL DEFAULT '0',
  								`upd_dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  								`vc` longblob,
  								PRIMARY KEY (`id`),
  								KEY `wid` (`wid`)
							) ENGINE=InnoDB AUTO_INCREMENT=456101 DEFAULT CHARSET=utf8",
			),
			'tp' => array(
				'cols' => array(
					'id' => array('bigint(20) unsigned', 'NO', 'PRI', null, 'auto_increment'),
					'q_id' => array('int(11)', 'NO', '', '0'),
					'did' => array('int(11)', 'NO', '', '0'),
					'pow' => array('int(11)', 'NO', '', '0'),
					'res' => array('float(10,6)', 'NO', '', '0'),
					'ts' => array('timestamp', 'NO', '', '1970-01-02 00:00:00'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'did' => array(1, '`did`'),
					'q_id' => array(1, '`q_id`'),
				),
				'create' => "CREATE TABLE `wpftsi_tp` (
								`q_id` int(11) NOT NULL,
								`did` int(11) NOT NULL,
								`pow` int(11) NOT NULL,
								`res` float(10,6) NOT NULL,
								`ts` timestamp NOT NULL DEFAULT '1970-01-02 00:00:00',
								KEY `did` (`did`),
								KEY `q_id` (`q_id`)
				  			) ENGINE=InnoDB DEFAULT CHARSET=utf8",
			),
			'qlog' => array(
				'cols' => array(
					'id' => array('int(11)', 'NO', 'PRI', null, 'auto_increment'),
					'query' => array('longtext', 'YES_NULL'),
					'query_type' => array('varchar(255)', 'NO', '', ''),
					'preset' => array('varchar(255)', 'NO', '', ''),
					'widget_name' => array('varchar(255)', 'NO', '', ''),
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
					'widget_name' => array(1, '`widget_name`'),
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
			'map' => array(
				'cols' => array(
					'id' => array('bigint(20) unsigned', 'NO', 'PRI', null, 'auto_increment'),
					'post_id' => array('bigint(20) unsigned', 'NO', '', '0'),
					'obj_id' => array('bigint(20) unsigned', 'NO', '', '0'),
					'obj_type' => array('varchar(50)', 'NO', '', ''),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'post_id' => array(0, '`post_id`'),
					'obj_id' => array(0, '`obj_id`,`obj_type`'),
				),
				'create' => "CREATE TABLE `wpftsi_map` (
								`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
								`post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
								`obj_id` bigint(20) unsigned NOT NULL DEFAULT '0',
								`obj_type` varchar(50) NOT NULL DEFAULT '',
								PRIMARY KEY (`id`),
								UNIQUE KEY `post_id` (`post_id`),
								UNIQUE KEY `obj_id` (`obj_id`,`obj_type`)
				  			) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4",
			),
			'queue' => array(
				'cols' => array(
					'id' => array('bigint(20) unsigned', 'NO', 'PRI', null, 'autoincrement'),
					'post_id' => array('bigint(20) unsigned', 'NO', '', '0'),
					'remark' => array('varchar(255)', 'NO', '', ''),
					'insert_dt' => array('datetime', 'NO', '', '1970-01-01 00:00:00'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'post_id' => array(1, '`post_id`'),
				),
				'create' => "CREATE TABLE `wpftsi_queue` (
								`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
								`post_id` int(11) NOT NULL DEFAULT '0',
								`remark` varchar(255) NOT NULL DEFAULT '',
								`insert_dt` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
								PRIMARY KEY (`id`),
								KEY `post_id` (`post_id`)
				  			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
			),
			'rawcache' => array(
				'cols' => array(
					'id' => array('int(10) unsigned', 'NO', 'PRI', null, 'auto_increment'),
					'object_id' => array('bigint(20)', 'NO', 'MUL', 0),
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
  								`object_id` int(11) NOT NULL DEFAULT '0',
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
			'ilog' => array(
				'cols' => array(
					'id' => array('int(10) unsigned', 'NO', 'PRI', null, 'auto_increment'),
					'index_id' => array('int(10) unsigned', 'NO', '', '0'),
					'start_ts' => array('double(18,6)', 'NO', '', '0.000000'),
					'getpost_ts' => array('double(18,6)', 'NO', '', '0.000000'),
					'clusters_ts' => array('double(18,6)', 'NO', '', '0.000000'),
					'cluster_stats' => array('longtext', 'YES'),
					'reindex_ts' => array('double(18,6)', 'NO', '', '0.000000'),
					'status' => array('int(11)', 'NO', '', '0'),
					'error' => array('longtext', 'YES'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'index_id' => array(0, '`index_id`'),
					'status' => array(1, '`status`'),
				),
				'create' => "CREATE TABLE `wpftsi_ilog` (
								`index_id` int(10) unsigned NOT NULL,
								`start_ts` double(18,6) NOT NULL DEFAULT '0.000000',
								`getpost_ts` double(18,6) NOT NULL DEFAULT '0.000000',
								`clusters_ts` double(18,6) NOT NULL DEFAULT '0.000000',
								`cluster_stats` longtext,
								`reindex_ts` double(18,6) NOT NULL,
								`status` int(11) NOT NULL DEFAULT '0',
								`error` longtext,
								UNIQUE KEY `index_id` (`index_id`),
								KEY `status` (`status`)
				  			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
			),
		);

		return $dbscheme;
	}
}