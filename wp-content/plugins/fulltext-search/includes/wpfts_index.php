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

global $wpfts_context;

$wpfts_context = false;

class WPFTS_Context
{
	public $index_post = false;
	public $index_token = false;
}

class WPFTS_Index
{
	public $max_word_length = 255;
	public $error = '';
	public $lock_time = 300;	// 5 min

	protected $stops = array();
	
	protected $_log = array();
	protected $_logtime = array();
	
	protected $_islock = true;
	
	// Logging
	public $_lastt0 = 0;
	public $_is_log = 0;
	public $_is_show = 0;

	public $t_words_rows = -1;

	function __construct()
	{
		//
	}

	protected function load_stops()
	{	
		global $wpfts_core;
		
		$q = 'select `word` from `'.$wpfts_core->dbprefix().'stops`';
		$res = $wpfts_core->db->get_result($q, ARRAY_A);
		
		$z = array();
		foreach ($res as $d) {
			$z[mb_strtolower($d['word'])] = 1;
		}
		$this->stops = $z;
	}
	
	public function log($message)
	{
		$this->_log[] = $message;
	}
	
	public function clearLog()
	{
		$this->_log = array();
	}
	
	public function getLog()
	{
		return implode("\n", $this->_log);
	}
	
	public function getLogTime()
	{
		return implode("\n", $this->_logtime);
	}

	public function clearLogTime()
	{
		$this->_logtime = array();
		$this->_lastt0 = microtime(true);
	}
	
	public function reindex($index_id, $chunks, $is_force_flush = true)
	{	
		global $wpdb, $wpfts_context, $wpfts_core;
		
		if (!is_array($chunks)) {
			$this->log(__('Reindex: wrong chunks format', 'fulltext-search'));
			return false;
		}

		$log_chunks = array();
		foreach ($chunks as $kk => $dd) {
			$log_chunks[] = ''.$kk.' => '.(is_string($dd) ? mb_strlen($dd) : '');
		}

		$wpfts_context = new WPFTS_Context();
		
		$wpfts_context->index_post = $index_id;
		$wpfts_context->index_token = false;

		$pfx = $wpfts_core->dbprefix();

		$this->logtime('*** Start Reindex index_id='.$index_id);
		$this->logtime('chunks = '.implode(', ', $log_chunks));

		$t0 = microtime(true);

		$q = 'delete from `'.$pfx.'vectors`, `'.$pfx.'docs` using `'.$pfx.'vectors`
				inner join `'.$pfx.'docs`
					on `'.$pfx.'docs`.id = `'.$pfx.'vectors`.did
				where 
					`'.$pfx.'docs`.`index_id` = "'.addslashes($index_id).'"';
		$wpfts_core->db->query($q);

		$t1 = microtime(true);
		$this->logtime('delete1 = '.sprintf('%.6f', $t1 - $t0));

		foreach ($chunks as $k => $d) {

			$wpfts_context->index_token = $k;

			$q = 'select 
					`id` 
				from `'.$pfx.'docs` 
				where 
					`index_id` = "'.addslashes($index_id).'" and 
					`token` = "'.addslashes($k).'"';
			$res = $wpfts_core->db->get_results($q, ARRAY_A);
			
			if (!isset($res[0]['id'])) {

				$t0 = microtime(true);
				
				// Insert token record
				$wpfts_core->db->insert($pfx.'docs', array(
					'index_id' => $index_id,
					'token' => $k,
					'n' => 0,
				));
				$doc_id = $wpdb->insert_id;
				
				$t1 = microtime(true);
				$this->logtime('insert token record = '.sprintf('%.6f', $t1 - $t0));
			
			} else {
				$doc_id = $res[0]['id'];
			}
			
			$t0 = microtime(true);

			$r2 = $this->add(array($doc_id => $d));

			$wpfts_context->index_token = false;

			$t1 = microtime(true);
			$this->logtime('add('.$doc_id.' => '.mb_strlen($d).') = '.sprintf('%.6f', $t1 - $t0));

			if (!$r2) {
				$wpfts_context = false;
				return false;
			}
		}

		if ($is_force_flush) {
			$this->_flushTW();
		}

		$wpfts_context = false;

		return true;
	}
	
	public function logtime($s)
	{
		if ($this->_is_log || $this->_is_show) {

			$t0 = $this->_lastt0;
			$t1 = microtime(true);
	
			$ss = sprintf('%.6f', $t1 - $t0).' : '.$s;

			if ($this->_is_log) {
				$this->_logtime[] = $ss;
			}
			if ($this->_is_show) {
				echo $ss."\n";
			}
			$this->_lastt0 = $t1;
		}
	}

	public function _getTWCount($force = false)
	{
		global $wpfts_core;

		// Let's count rows in t_words
		// Only need to count one time (in the start of the PHP script)
		if ($force || ($this->t_words_rows < 0)) {
			$pfx = $wpfts_core->dbprefix();

			$q = 'select count(*) n from `'.$pfx.'tw`';
			$rr = $wpfts_core->db->get_results($q, ARRAY_A);
	
			$this->t_words_rows = $rr[0]['n'];

			$this->logtime('Read number of t_words rows: '.$this->t_words_rows);
		}
		
		return $this->t_words_rows;
	}

	public function _getVCNotAct($max_n = 1)
	{
		global $wpfts_core;

		$pfx = $wpfts_core->dbprefix();

		$q = 'select 
				id
			from `'.$pfx.'words`
			where
				`act` = -1
			limit '.intval($max_n).'
		';
		$rr = $wpfts_core->db->get_results($q, ARRAY_A);

		$a = array();
		foreach ($rr as $row) {
			$a[] = $row['id'];
		}

		return $a;
	}

	public function indexWordData($wid)
	{
		global $wpdb, $wpfts_core;

		$pfx = $wpfts_core->dbprefix();

		$time = current_time('timestamp');

		// We need to process this part with chunks because of possible large amount of
		// same words.
		$max_chunk = 10000;

		$c_off = 0;
		$is_finished = false;
		$tt = array();

		$q = 'select 
				v.did,
				v.wn
			from `'.$pfx.'vectors` v
			where 
				v.wid = '.intval($wid);

		while (!$is_finished) {
			$wpfts_core->db->query($q.' limit '.$c_off.','.$max_chunk);
		
			if (count($wpdb->last_result) > 0) {
				foreach ($wpdb->last_result as $row3) {
					if (isset($tt[$row3->did])) {
						$tt[$row3->did][] = $row3->wn;
					} else {
						$tt[$row3->did] = array($row3->wn);
					}
				}
			
			} else {
				$is_finished = true;
				break;
			}
		
			if (count($wpdb->last_result) < $max_chunk) {
				$is_finished = true;
				break;
			}
			$c_off += count($wpdb->last_result);
		}
		
		$q = 'delete from `'.$pfx.'vc` where `wid` = "'.addslashes($wid).'"';
		$wpfts_core->db->query($q);

		if (count($tt) <= 0) {
			// It looks like this word do not used anymore
			// @todo May be we need to remove this word

			$q = 'update `'.$pfx.'words` set `act` = 0 where `id` = "'.addslashes($wid).'"';
			$wpfts_core->db->query($q);
	
			return true;
		}

		// Okay, let's create VC

		$pk_size = 0;
		$pk = '';
		foreach ($tt as $kk => $dd) {
			$pk .= pack('l', $kk);
			$zz = count($dd) - 1;
			for ($i = 0; $i <= $zz; $i ++) {
				$pk .= pack('l', ($i == $zz) ? -$dd[$i] : $dd[$i]);
			}
			$pk_size ++;

			if (strlen($pk) > 1000 * 4) {
				// Drop this pack to the vcache
				$pk = pack('l', $pk_size).$pk;

				$q = 'insert into `'.$pfx.'vc` (`wid`,`upd_dt`,`vc`) values ("'.$wid.'", "'.date('Y-m-d H:i:s', $time).'", "'.$wpdb->_real_escape($pk).'")';
				$wpfts_core->db->query($q);

				if (strlen($wpfts_core->db->get_last_error()) < 1) {
					$this->logtime('set from '.count($tt).' doc ids ('.$pk_size.','.strlen($pk).')');
				} else {
					$this->logtime('MySQL Error: '.$wpfts_core->db->get_last_error());
					return false;
				}

				$pk = '';
				$pk_size = 0;
			}
		}

		if (strlen($pk) > 0) {
			// Drop remaining
			$pk = pack('l', $pk_size).$pk;

			$q = 'insert into `'.$pfx.'vc` (`wid`,`upd_dt`,`vc`) values ("'.$wid.'", "'.date('Y-m-d H:i:s', $time).'", "'.$wpdb->_real_escape($pk).'")';
			$wpfts_core->db->query($q);

			if (strlen($wpfts_core->db->get_last_error()) < 1) {
				$this->logtime('set remaining from '.count($tt).' doc ids ('.$pk_size.','.strlen($pk).')');
			} else {
				$this->logtime('MySQL Error: '.$wpfts_core->db->get_last_error());
				return false;
			}
		}

		$q = 'update `'.$pfx.'words` set `act` = '.count($tt).' where `id` = "'.addslashes($wid).'"';
		$wpfts_core->db->query($q);

		return true;
	}
	
	public function _flushTW()
	{
		// Let's FLUSH!
		global $wpfts_core;

		$pfx = $wpfts_core->dbprefix();
		
		$t0 = microtime(true);

		// Insert new words only
		// We have taken every measure to ensure that words are not added to the dictionary again. 
		// However, due to a MySQL bug, it misses some characters from Asian languages, 
		// so we had to add "ignore" here.
		$q = 'insert ignore into `'.$pfx.'words`
				(`word`)
			select distinct 
				tw.w
			from `'.$pfx.'tw` tw
			left outer join `'.$pfx.'words` w
				on tw.w = w.word
			where 
				isnull(w.id)
			';
		$wpfts_core->db->query($q);

		$t1 = microtime(true);
		$this->logtime('Insert new words only: '.$wpfts_core->db->get_last_error().', dt = '.sprintf('%.4f', $t1 - $t0));

		$t0 = microtime(true);

		// Insert vectors
		$q = 'insert into `'.$pfx.'vectors`
					(`did`, `wid`, `wn`)
				select
					tw.did,
					w.id,
					tw.wn
				from `'.$pfx.'tw` tw
				STRAIGHT_JOIN `'.$pfx.'words` w
					on tw.w = w.word
		';
		$wpfts_core->db->query($q);

		$t1 = microtime(true);
		$this->logtime('Insert vectors: '.$wpfts_core->db->get_last_error().', dt = '.sprintf('%.4f', $t1 - $t0));

		// Touch all used words (right join is important here)
		$t0 = microtime(true);
		$q = 'update `'.$pfx.'words` w
			right join `'.$pfx.'tw` tw
				on tw.w = w.word
			set `act` = -1 
		';
		$wpfts_core->db->query($q);

		$t1 = microtime(true);
		$this->logtime('Touch words: '.$wpfts_core->db->get_last_error().', dt = '.sprintf('%.4f', $t1 - $t0));

		// Okay, clear the temp table
		$t0 = microtime(true);

		$q = 'CREATE TABLE `'.$pfx.'tw2` LIKE `'.$pfx.'tw`';
		$wpfts_core->db->query($q);

		$q = 'RENAME TABLE `'.$pfx.'tw` TO `'.$pfx.'tw0`, `'.$pfx.'tw2` TO `'.$pfx.'tw`';
		$wpfts_core->db->query($q);

		$q = 'DROP TABLE `'.$pfx.'tw0`';
		$wpfts_core->db->query($q);

		//$q = 'truncate table `'.$pfx.'tw`';
		//$wpfts_core->db->query($q);

		$t1 = microtime(true);
		$this->logtime('Truncate table: '.$wpfts_core->db->get_last_error().', dt = '.sprintf('%.4f', $t1 - $t0));

		$this->t_words_rows = 0;
	}

	/** Using bulk insert for vectors **/
	public function add($docs = array()) 
	{	
		//ini_set('display_errors', 1);
		//error_reporting(E_ALL);

		global $wpfts_core;
		
		// Validate
		if (!is_array($docs)) {
			$this->log(__('Add document: parameter should be an array', 'fulltext-search'));
			return false;
		}
		
		if (count($docs) < 1) {
			// Nothing to do
			return true;
		}
		
		foreach ($docs as $id => $doc) {
			if (!is_numeric($id)) {
				$this->log(sprintf(__('Add document: bad index "%s" given.', 'fulltext-search'), $id));
				return false;
			} else {
				$a_ids[] = $id;
			}
		}
		
		$pfx = $wpfts_core->dbprefix();

		$this->_lastt0 = microtime(true);

		$this->logtime('Current number of ROWS: '.$this->t_words_rows);

		$this->_getTWCount();

/*
		$q = 'create temporary table `t_words` (`w` varchar(255) not null, `wn` int(11) not null, key `w` (`w`))';
		$wpfts_core->db->query($q);

		$this->logtime('create temp table: '.$wpfts_core->db->get_last_error());
*/
/*
		// Okay, clear the temp table
		$q = 'truncate table `t_words`';
		$wpfts_core->db->query($q);

		$this->logtime('Initial truncate table: '.$wpfts_core->db->get_last_error());
*/

		$wordlist = array();
		$doclist = array();
		foreach ($docs as $id => $doc) {
			
			if (!isset($doc) || (!is_string($doc)) || (mb_strlen($doc) < 1)) {
				continue;
			}
			
			$t0 = microtime(true);

			$words = $wpfts_core->split_to_words($doc);
			$num_of_words = count($words);
			$doclist[$id] = $num_of_words;

			$wpfts_core->db->update($pfx.'docs', array('n' => $num_of_words), array('id' => $id));

			$this->logtime('break to words ('.$id.', '.count($wordlist).')');

			// Remove old vectors for this doc_id
			$q = 'delete from '.$pfx.'vectors
					where `did` = "'.$id.'"';
			$wpfts_core->db->query($q);
	
			$this->logtime('Remove old vectors: '.$wpfts_core->db->get_last_error());
		
			$ws = array_chunk($words, 1000);

			$wn = 1;
			foreach ($ws as $ws_chunk) {
				$t = array();
				foreach ($ws_chunk as $d) {
					$t[] = '("'.addslashes($d).'", "'.$id.'", "'.$wn.'")';
					$wn ++;
				}
	
				$q = 'insert into `'.$pfx.'tw` (`w`, `did`, `wn`) values '.implode(',', $t);
				$wpfts_core->db->query($q);

				$this->logtime('Insert word chunk ('.count($ws_chunk).'): '.$wpfts_core->db->get_last_error());
			}

			$this->t_words_rows += $wn - 1;

			if ($this->t_words_rows > 25000) {
				$this->_flushTW();
			}

		}
/*
		// Delete temp table (just in case)
		$q = 'drop temporary table `t_words`';
		$wpfts_core->db->query($q);

		$this->logtime('Drop temp table: '.$wpfts_core->db->get_last_error());
*/

		return true;
	}

	function is_stop_word($word)
	{
		return isset($this->stoplist[mb_strtolower($word)]);
	}

	function getRecordsToRebuild($n_max = 1)
	{	
		global $wpfts_core;
		
		$idx = $wpfts_core->dbprefix();
		
		$time = time();
		$time2 = date('Y-m-d H:i:s', $time - $this->lock_time);
		
		$qr = 'select 
					id, tid, tsrc 
			from `'.$idx.'index` 
			where 
				((force_rebuild != 0) or (build_time = 0)) and 
				((locked_dt = "1970-01-01 00:00:00") or (locked_dt < "'.$time2.'"))
			order by build_time asc, id asc 
			limit '.intval($n_max).'';
		$r = $wpfts_core->db->get_results($qr, ARRAY_A);
		
		return $r;
	}
	
	function checkAndSyncWPPosts($current_build_time)
	{	
		global $wpdb, $wpfts_core;
		
		$idx = $wpfts_core->dbprefix();
		
		// Step 1. Mark index rows contains old posts and posts with wrong date of post or build time.
		$q = 'update `'.$idx.'index` wi
				left join `'.$wpdb->posts.'` p
					on p.ID = wi.tid
				set 
					wi.force_rebuild = if(p.ID is null, 2, if ((wi.build_time = "'.addslashes($current_build_time).'") and (wi.tdt = p.post_modified), 0, 1))
				where 
					(wi.tsrc = "wp_posts") and (wi.force_rebuild = 0)';
		$wpfts_core->db->query($q);
		
		// Step 2. Find and add new posts // @todo need to be optimized!
		$q = 'insert ignore into `'.$idx.'index` 
				(`tid`, `tsrc`, `tdt`, `build_time`, `update_dt`, `force_rebuild`, `locked_dt`) 
				select 
					p.ID tid,
					"wp_posts" tsrc,
					"1970-01-01 00:00:00" tdt,
					0 build_time,
					"1970-01-01 00:00:00" update_dt,
					1 force_rebuild,
					"1970-01-01 00:00:00" locked_dt
				from `'.$wpdb->posts.'` p';
		$wpfts_core->db->query($q);
		
		// Step 3. What else?
	}
	
	function get_status()
	{	
		global $wpfts_core;
		
		$time = time();

		$status_next_ts = intval($wpfts_core->get_option('status_next_ts'));
		if ($status_next_ts <= $time) {
			// It's time to refresh status
			$idx = $wpfts_core->dbprefix();

			$q = 'select 
					count(*) n_inindex, 
					sum(if ((force_rebuild = 0) and (build_time != 0), 1, 0)) n_actual
				from `'.$idx.'index` 
				where `tsrc` = "wp_posts"';
			$res = $wpfts_core->db->get_results($q, ARRAY_A);
			
			$ret = array();
			if (isset($res[0]['n_inindex'])) {
				$ret = array(
					'n_inindex' => intval($res[0]['n_inindex']),
					'n_actual' => intval($res[0]['n_actual']),
					'n_pending' => intval($res[0]['n_inindex']) - intval($res[0]['n_actual']),
				);
			} else {
				$ret = array(
					'n_inindex' => 0,
					'n_actual' => 0,
					'n_pending' => 0,
				);
			}
	
			$ret['nw_act'] = 0;
			$ret['nw_total'] = 0;
	
			$is_optimizer = intval($wpfts_core->get_option('is_optimizer'));
	
			if ($ret['n_pending'] < 1) {
	
				// Check if we have any records in TW cache
				$q = 'select count(*) n from `'.$idx.'tw`';
				$rr = $wpfts_core->db->get_results($q, ARRAY_A);
		
				$ret['n_tw'] = isset($rr[0]) ? intval($rr[0]['n']) : 0;
		
				if ($ret['n_tw'] < 1) {
					if ($is_optimizer) {
						// Get number of non-indexed words
						$q = 'select 
								sum(if(`act` = -1, 0, 1)) nw_act,
								count(id) nw_total
							from `'.$idx.'words`';
						$res = $wpfts_core->db->get_results($q, ARRAY_A);
	
						$ret['nw_act'] = intval($res[0]['nw_act']);
						$ret['nw_total'] = intval($res[0]['nw_total']);
	
					} else {
						$q = 'select 
								count(id) nw_total
							from `'.$idx.'words`';
						$res = $wpfts_core->db->get_results($q, ARRAY_A);
		
						$ret['nw_act'] = intval($res[0]['nw_total']);
						$ret['nw_total'] = intval($res[0]['nw_total']);
					}
				}
			}
			$ret['tsd'] = time();

			$wpfts_core->set_option('status_next_ts', $time + 5 * 60);
			$wpfts_core->set_option('status_cache', json_encode($ret));

			$ret['is_cached'] = false;

			return $ret;
		} else {
			$ret = json_decode($wpfts_core->get_option('status_cache'), true);

			$ret['is_cached'] = true;

			return $ret;
		}
	}
	
	function getClusters()
	{	
		global $wpfts_core;
		
		$idx = $wpfts_core->dbprefix();
		
		$z = array('post_title' => 1, 'post_content' => 1);
		
		$q = 'select distinct `token` from `'.$idx.'docs` limit 100';
		$res = $wpfts_core->db->get_results($q, ARRAY_A);
		
		$z = array();
		foreach ($res as $d) {
			if (!isset($z[$d['token']])) {
				$z[$d['token']] = 1;
			}
		}
		
		return array_keys($z);
	}

	function lockUnlockedRecord($id) {
		
		global $wpfts_core;
		
		$idx = $wpfts_core->dbprefix();
		
		$time = time();
		$time2 = date('Y-m-d H:i:s', $time - $this->lock_time);
		$new_time = date('Y-m-d H:i:s', $time);
		
		$q = 'select id, if((locked_dt = "1970-01-01 00:00:00") or (locked_dt < "'.$time2.'"), 0, 1) islocked from `'.$idx.'index` where id = "'.addslashes($id).'"';
		$res = $wpfts_core->db->get_results($q, ARRAY_A);
		
		if (isset($res[0])) {
			if ($res[0]['islocked']) {
				// Already locked
				return false;
			} else {
				// Lock it
				$wpfts_core->db->update($idx.'index', array('locked_dt' => $new_time), array('id' => $id));
				return true;
			}
		} else {
			// Record not found
			return false;
		}
	}
	
	function unlockRecord($id) {
		
		global $wpfts_core;
		
		$idx = $wpfts_core->dbprefix();
		
		$wpfts_core->db->update($idx.'index', array('locked_dt' => '1970-01-01 00:00:00'), array('id' => $id));
	}
	
	function updateRecordData($id, $data = array()) {
		
		global $wpfts_core;
		
		$idx = $wpfts_core->dbprefix();
		
		$a = array();
		foreach ($data as $k => $d) {
			if (in_array($k, array('tdt', 'build_time', 'update_dt', 'force_rebuild', 'locked_dt'))) {
				$a[$k] = $d;
			}
		}
		$wpfts_core->db->update($idx.'index', $a, array('id' => $id));
	}
	
	function insertRecordData($data = array()) {
		
		global $wpdb, $wpfts_core;
		
		$idx = $wpfts_core->dbprefix();
		
		$a = array();
		foreach ($data as $k => $d) {
			if (in_array($k, array('tdt', 'build_time', 'update_dt', 'force_rebuild', 'locked_dt', 'tid', 'tsrc'))) {
				$a[$k] = $d;
			}
		}
		$wpfts_core->db->insert($idx.'index', $a);
		
		return $wpdb->insert_id;
	}
	
	function updateIndexRecordForPost($post_id, $modt, $build_time, $time = false, $force_rebuild = 0)
	{	
		global $wpfts_core;
		
		if ($time === false) {
			$time = time();
		}
		
		$q = 'select * from `'.$wpfts_core->dbprefix().'index` where (`tid` = "'.$post_id.'") and (`tsrc` = "wp_posts")';
		$res = $wpfts_core->db->get_results($q, ARRAY_A);
		
		if (isset($res[0])) {

			// Update existing record
			$this->updateRecordData(
					$res[0]['id'], 
					array(
						'tdt' => $modt,
						'build_time' => $build_time,
						'update_dt' => date('Y-m-d H:i:s', $time),
						'force_rebuild' => $force_rebuild,
						'locked_dt' => '1970-01-01 00:00:00',
						)
			);
			
			return $res[0]['id'];
		} else {
			// Insert new record
			$insert_id = $this->insertRecordData(
					array(
						'tid' => $post_id,
						'tsrc' => 'wp_posts',
						'tdt' => $modt,
						'build_time' => $build_time,
						'update_dt' => date('Y-m-d H:i:s', $time),
						'force_rebuild' => $force_rebuild,
						'locked_dt' => '1970-01-01 00:00:00',
						)
			);
			
			return $insert_id;
		}
	}
	
	function getColumn($a, $col) {
		$r = array();
		foreach ($a as $d) {
			if (isset($d[$col])) {
				$r[] = $d[$col];
			}
		}
		return $r;
	}
	
	function removeIndexRecordForPost($post_id) {
		
		global $wpfts_core;
		
		$idx = $wpfts_core->dbprefix();
		
		$q = 'select `id` from `'.$idx.'index` where (`tid` = "'.addslashes($post_id).'") and (`tsrc` = "wp_posts")';
		$res_index = $wpfts_core->db->get_results($q, ARRAY_A);
		
		if (isset($res_index[0])) {
			$q = 'select `id` from `'.$idx.'docs` where `index_id` in ('.implode(',', $this->getColumn($res_index, 'id')).')';
			$res_docs = $wpfts_core->db->get_results($q, ARRAY_A);
			
			if (isset($res_docs[0])) {
				$q = 'delete from `'.$idx.'vectors` where `did` in ('.implode(',', $this->getColumn($res_docs, 'id')).')';
				$wpfts_core->db->query($q);
				
				$q = 'delete from `'.$idx.'docs` where `index_id` in ('.implode(',', $this->getColumn($res_index, 'id')).')';
				$wpfts_core->db->query($q);
			}
			
			$q = 'delete from `'.$idx.'index` where (`tid` = "'.addslashes($post_id).'") and (`tsrc` = "wp_posts")';
			$wpfts_core->db->query($q);
		}
		
		return true;
	}
}
