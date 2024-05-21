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

class WPFTS_QueryLog
{
	function __construct()
	{
		$this->add_hooks();

	}

	public function add_hooks()
	{
		add_action('wpfts_init_addons', function()
		{
			if (is_admin()) {
				add_action('wp_ajax_wpftsi_get_qlog_data', array($this, 'ajax_get_qlog_data'));
				add_action('wp_ajax_wpftsi_get_qlog_details', array($this, 'ajax_get_qlog_details'));
				add_action('wp_ajax_wpftsi_get_qlog_settings', array($this, 'ajax_get_qlog_settings'));
			}
		});

		add_action('parse_query', array($this, 'Start'), -32768, 1);	// Guarantee first call
		add_action('pre_get_posts', array($this, 'GoSearch'), 32768, 1);	// Guarantee last call before pre_get_posts exis

	}

	public function createQLogDB($query, $params = array(), $search_type = '', $preset_ident = '', $data = array())
	{
		global $wpfts_core, $wpdb;

		$pfx = $wpfts_core->dbprefix();

		$wpfts_core->db->insert($pfx.'qlog', array(
			'query' => $query,
			'query_type' => $search_type,
			'preset' => $preset_ident,
			'n_results' => isset($data['n_results']) ? intval($data['n_results']) : -1,
			'q_time' => isset($data['q_time']) ? floatval($data['q_time']) : -1,
			'max_ram' => isset($data['max_ram']) ? intval($data['max_ram']) : -1,
			'user_id' => isset($data['user_id']) ? intval($data['user_id']) : 0,
			'req_ip' => isset($data['req_ip']) ? trim($data['req_ip']) : '',
			'ref_url' => isset($data['ref_url']) ? trim($data['ref_url']) : '',
			'insert_dt' => date('Y-m-d H:i:s', current_time('timestamp')),
			'ext' => '',
			'wpq_params' => json_encode($params),
		));

		return $wpdb->insert_id;
	}
	
	public function simplifyBacktrace($full_backtrace = array())
	{
		$res = array();
		if ($full_backtrace && is_array($full_backtrace)) {
			foreach ($full_backtrace as $k => $item) {
				$r = array();
				foreach (array('file', 'line', 'function', 'class', 'object', 'type', 'args') as $t) {
					if (isset($item[$t])) {
						if ($t == 'object') {
							$trr = get_object_vars($item[$t]);
							$zz = array();
							foreach ($trr as $kk => $dd) {
								if (is_object($dd)) {
									$zz[$kk] = '*OBJECT::'.get_class($dd);
								} elseif (is_array($dd)) {
									$zz[$kk] = '*ARRAY::*';
								} else {
									$zz[$kk] = $dd;
								}
							}
							$r[$t] = $zz;
						} elseif ($t == 'args') {
							
						} else {
							$r[$t] = $item[$t];
						}
					}
				}
				$res[$k] = $r;
			}
		}

		return $res;
	}

	public function GetAllowedOrderBy()
	{
		return array(
			'datetime' => array('desc', 'insert_dt'), 
			'query' => array('asc', 'query'),
			'type' => array('asc', 'query_type'),
			'preset' => array('asc', 'preset'),
			'n_results' => array('desc', 'n_results'),
			'time_spent' => array('desc', 'q_time'),
			'max_ram' => array('desc', 'max_ram'),
			'user_id' => array('asc', 'user_id'),
			'req_ip' => array('asc', 'req_ip'),
			'ref_url' => array('asc', 'ref_url'),
			'widget_name' => array('asc', 'widget_name'),
		);
	}

	public function GetValidSettings()
	{
		global $wpfts_core;

		$sts = json_decode($wpfts_core->get_option('qlog_settings'), true);

		$allowed_orderby = $this->GetAllowedOrderBy();

		$column_list = array(
			'datetime',
			'query',
			'type',
			'preset',
			'n_results',
			'time_spent',
		);

		$sts['lines_per_page'] = (isset($sts['lines_per_page'])) ? intval($sts['lines_per_page']) : 50;
		$sts['order'] = (isset($sts['order']) && ($sts['order'] == 'asc')) ? 'asc' : 'desc';
		$sts['orderby'] = (isset($sts['orderby']) && isset($allowed_orderby[$sts['orderby']])) ? $sts['orderby'] : 'datetime';

		$sts['columns'] = $column_list;

		$sts['querylog_enabled'] = (isset($sts['querylog_enabled']) && ($sts['querylog_enabled'] == 0)) ? 0 : 1;
		$sts['nonwpfts_queries'] = (isset($sts['nonwpfts_queries']) && ($sts['nonwpfts_queries'] != 0)) ? 1 : 0;
		$sts['nontext_queries'] = (isset($sts['nontext_queries']) && ($sts['nontext_queries'] != 0)) ? 1 : 0;
		$sts['detailed_log'] = (isset($sts['detailed_log']) && ($sts['detailed_log'] != 0)) ? 1 : 0;
		

		return $sts;
	}

	public function ajax_get_qlog_data()
	{
		global $wpfts_core;

		$sts = $this->GetValidSettings();

		// Detect set_props mode
		if (isset($_POST['set_props'])) {
			$set_props = json_decode(stripslashes($_POST['set_props']), true);

			$is_updated = false;
			foreach ($set_props as $k => $d) {
				switch ($k) {
					case 'lines_per_page':
					case 'order':
					case 'orderby':
						$sts[$k] = $d;
						$is_updated = true;
						break;
				}
			}

			if ($is_updated) {
				$wpfts_core->set_option('qlog_settings', json_encode($sts));
			}

			$sts = $this->GetValidSettings();
		}

		$page_num = isset($_POST['current_page']) ? max(1, intval($_POST['current_page'])) : 1;

		$allowed_orderby = $this->GetAllowedOrderBy();

		$idx = $wpfts_core->dbprefix();

		$lines_per_page = $sts['lines_per_page'];
		
		$limit_start = ($page_num - 1) * $lines_per_page;
		$limit_length = $lines_per_page;

		// Create 'where'
		$w = '1';

		// Calculate total records
		$q = 'select count(*) n from `'.$idx.'qlog` where '.$w;
		$res2 = $wpfts_core->db->get_results($q, ARRAY_A);

		$n_total = (count($res2) > 0) ? $res2[0]['n'] : 0;

		$q = 'select 
				`id`, 
				`insert_dt`,
				`query`,
				`query_type`,
				`preset`,
				`n_results`,
				`q_time`,
				`max_ram`,
				`user_id`,
				`req_ip`,
				`ref_url`,
				`widget_name`
			from `'.$idx.'qlog'.'` where '.$w;
		if (isset($allowed_orderby[$sts['orderby']])) {
			$q .= ' order by `'.$allowed_orderby[$sts['orderby']][1].'` '.$sts['order'];
		}
		$q .= ' limit '.$limit_start.', '.$limit_length;

		$res = $wpfts_core->db->get_results($q, ARRAY_A);

		$result = array(
			'code' => 0,
			'error' => 'OK',
			'lines' => $res,
			'page_num' => $page_num,
			'n_total' => $n_total,
			'settings' => $sts,
		);

		echo json_encode($result);
		wp_die();
	}

	public function ajax_get_qlog_settings()
	{
		global $wpfts_core;

		$sts = $this->GetValidSettings();

		// Detect set_props mode
		if (isset($_POST['set_props'])) {
			$set_props = json_decode(stripslashes($_POST['set_props']), true);

			$is_updated = false;
			foreach ($set_props as $k => $d) {
				switch ($k) {
					case 'querylog_enabled':
					case 'nonwpfts_queries':
					case 'nontext_queries':
					case 'detailed_log':
						$sts[$k] = $d;
						$is_updated = true;
						break;
				}
			}

			if ($is_updated) {
				$wpfts_core->set_option('qlog_settings', json_encode($sts));
			}

			$sts = $this->GetValidSettings();
		}

		$result = array(
			'code' => 0,
			'error' => 'OK',
			'settings' => $sts,
		);

		echo json_encode($result);
		wp_die();
	}

	public function ajax_get_qlog_details()
	{
		global $wpfts_core;

		$result = array(
			'code' => -1,
			'error' => 'Wrong input',
		);
		if (isset($_POST['qlog_id'])) {

			$idx = $wpfts_core->dbprefix();

			$qlog_id = isset($_POST['qlog_id']) ? intval($_POST['qlog_id']) : 0;

			$q = 'select 
					`id`, 
					`query`,
					`query_type`,
					`preset`,
					`n_results`,
					`q_time`,
					`max_ram`,
					`user_id`,
					`req_ip`,
					`ref_url`,
					`widget_name`,
					`insert_dt`,
					`wpq_params`,
					`ext`
				from `'.$idx.'qlog'.'`
				where `id` = "'.addslashes($qlog_id).'"';
			$res = $wpfts_core->db->get_results($q, ARRAY_A);

			if (count($res) > 0) {
				$row = $res[0];

				$wpq_params = array();
				try {
					$wpq_params = json_decode($row['wpq_params'], true);
				} catch (Exception $e) {
					$wpq_params = array();
				}

				$result = array(
					'code' => 0,
					'error' => 'OK',
					'details' => array(
						'id' => $row['id'],
						'query' => $row['query'],
						'query_type' => $row['query_type'],
						'preset' => $row['preset'],
						'n_results' => $row['n_results'],
						'q_time' => $row['q_time'],
						'max_ram' => $row['max_ram'],
						'user_id' => $row['user_id'],
						'req_ip' => $row['req_ip'],
						'ref_url' => $row['ref_url'],
						'widget_name' => $row['widget_name'],
						'insert_dt' => $row['insert_dt'],
						'wpq_params' => $wpq_params,
						'log' => $this->DecodeExtData($row['ext']),
					),
				);
			} else {
				$result = array(
					'code' => -2,
					'error' => 'Data row not found',
				);
			}
		}

		echo json_encode($result);
		wp_die();
	}

	public function GetRemoteIP()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		return $ip;
	}

	/**
	 * Start QueryLog functionality and prepare main data
	 */
	public function Start($wpq)
	{
		global $wpfts_core;

		if ($wpq && is_object($wpq) && isset($wpq->query_vars)) {
			// Valid WP_Query object
			if (!isset($wpq->wpftsi_session['qlog_token'])) {
				// Start the new qlog session
				$qlog_token = sha1(uniqid('qlog_token_').'|'.time());
				$wpq->wpftsi_session = array(
					'qlog_token' => $qlog_token,
					'qlog_enabled' => 0,	// To be changed below
				);
				
				$qlog_settings = $this->GetValidSettings();

				$is_qlog_enabled = $qlog_settings['querylog_enabled'];
				// Override by wp_query
				if (isset($wpq->query_vars['wpfts_querylog_enabled'])) {
					$is_qlog_enabled = ($wpq->query_vars['wpfts_querylog_enabled'] == 0) ? 0 : 1;
				}

				$is_detailed_log = $qlog_settings['detailed_log'];
				// Override by wp_query
				if (isset($wpq->query_vars['wpfts_detailed_log'])) {
					$is_detailed_log = ($wpq->query_vars['wpfts_detailed_log'] == 0) ? 0 : 1;
				}

				$is_text_search = (isset($wpq->query_vars['s']) && (strlen($wpq->query_vars['s']) > 0)) ? 1 : 0;
				// Check if we need to reset is_qlog_enabled
				if ((!$is_text_search) && ($qlog_settings['nontext_queries'] == 0)) {
					$is_qlog_enabled = 0;	// Disable, because we don't allow to log non-text queries
				}

				$is_admin = is_admin();
				$is_main_query = $wpq->is_main_query();

				$full_bktr = debug_backtrace();
				$bktr = $this->simplifyBacktrace($full_bktr);
				$wpq->wpftsi_session['start_bktrace'] = $full_bktr;

				$sysvars = array(
					'qlog_settings' => $qlog_settings,
					'is_detailed_log' => $is_detailed_log,
					'is_qlog_enabled' => $is_qlog_enabled,
					'is_main_query' => $is_main_query,
					'is_admin' => $is_admin,
					'is_text_search' => $is_text_search,
				);

				$sysvars = apply_filters('wpfts_start_sysvars', $sysvars, $wpq);

				$wpq->wpftsi_session['sysvars'] = $sysvars;

				// Detect integrated search types
				$search_type = '';	// Not detected
				if (isset($sysvars['is_text_search']) && ($sysvars['is_text_search'])) {
					if (isset($sysvars['is_main_query']) && ($sysvars['is_main_query'])) {
						// It could be admin or not admin WP main query
						if (isset($sysvars['is_admin']) && ($sysvars['is_admin'])) {
							// Admin mode
							$search_type = 'wpmainsearch_admin';
						} else {
							// Not admin
							$search_type = 'wpmainsearch_frontend';
						}
					} else {
						// Not main query
						// Check bktrace to detect Gutenberg block calls
						$bk = $wpq->wpftsi_session['start_bktrace'];
						$is_maybe1 = false;
						foreach ($bk as $bk_item) {
							if ($is_maybe1) {
								// Next record
								if (preg_match('~[\\\/]wp\-includes[\\\/]class\-wp\-block\.php$~', $bk_item['file']) && isset($bk_item['function']) && ($bk_item['function'] === 'render_block_core_post_template')) {
									// Okay, it looks like a Gutenberg block call
									$search_type = 'wpblockquery';
									break;
								} else {
									// Wrong try
									$is_maybe1 = false;
								}
							}
							if ((preg_match('~[\\\/]wp\-includes[\\\/]blocks[\\\/]post\-template\.php$~', $bk_item['file'])) && isset($bk_item['class']) && ($bk_item['class'] === 'WP_Query')) {
								$is_maybe1 = true;
							}
						}

						if (strlen($search_type) < 1) {
							// Not detected
						}
					}
				} else {
					// Not text search
					$search_type = '';
				}

				$search_type = apply_filters('wpfts_start_search_type', $search_type, $wpq);

				// The search_type can be overriden by query_vars
				if (isset($wpq->query_vars['wpfts_search_type'])) {
					$search_type = $wpq->query_vars['wpfts_search_type'];
				}
				$wpq->wpftsi_session['search_type'] = $search_type;

				// Get preset based on search_type
				$preset_ident = '';
				$preset_setby = '';
				if (isset($wpq->query_vars['wpfts_preset']) && (strlen($wpq->query_vars['wpfts_preset']) > 0)) {
					$preset_setby = 'wp_query';
					$preset_ident = trim($wpq->query_vars['wpfts_preset']);
				} else {
					$preset_setby = 'wpfts_settings';
					$preset_ident = $wpfts_core->GetPresetBySearchType($search_type);
				}

				$wgt_ident = $wpq->get('wpfts_wdgt', isset($_GET['wpfts_wdgt']) ? $_GET['wpfts_wdgt'] : '');
				$wpq->wpftsi_session['wpfts_wdgt'] = $wgt_ident;
				$ws = $wpfts_core->GetWidgetPresets();
				if ((strlen($wgt_ident) > 0) && (isset($ws[$wgt_ident]))) {
					
					// We going to check results_url here @todo
					$wdata = $ws[$wgt_ident];
					$wdata['id'] = $wgt_ident;

					$wpq->wpftsi_session['wdgt_data'] = $wdata;

					if (isset($wdata['preset']) && (strlen($wdata['preset']) > 0)) {
						$preset_setby = 'wpfts_wdgt';
						$preset_ident = $wdata['preset'];
					}
				}
				// A chance to change preset
				$old_v = $preset_ident;
				$preset_ident = apply_filters('wpfts_preset_detected', $preset_ident, $wpq);

				if ($preset_ident != $old_v) {
					$preset_setby = 'post_filter';
				}

				$wpq->wpftsi_session['preset_ident'] = $preset_ident;
				$wpq->wpftsi_session['preset_setby'] = $preset_setby;

				// Get preset data (only for defined presets)
				$preset_data = array();
				if (strlen($preset_ident) > 0) {
					$preset_data = $wpfts_core->GetPresetData($preset_ident);
				}

				$wpq->wpftsi_session['preset_data'] = $preset_data;

				// In case this preset does not support WPFTS search, check if we need to disable logging

				$use_indexed_search = 0;	// @todo get this value from WPFTS Main Settings experimental feature

				$is_wpfts_disabled = intval($wpq->get('wpfts_disable', intval($wpfts_core->get_option('enabled')) ? 0 : 1));

				if ($is_wpfts_disabled) {
					// WPFTS search is completely disabled by WPFTS Main Settings or WP_Query parameter 'wpfts_disable'
					$use_indexed_search = 0;
				} else {
					if (isset($preset_data['use_indexed_search'])) {
						$use_indexed_search = ($preset_data['use_indexed_search'] != 0) ? 1 : 0;
					} else {
						// In case the preset does not handle use_indexed_search, check for wpfts_is_force
						// We have a chance to enable WPFTS search anyway, using wpfts_is_force in WP_Query parameters
						if (isset($wpq->query_vars['wpfts_is_force']) && ($wpq->query_vars['wpfts_is_force'] != 0)) {
							$use_indexed_search = 1;
						}
						if (isset($wpq->query_vars['use_indexed_search'])) {
							if ($wpq->query_vars['use_indexed_search'] == 0) {
								$use_indexed_search = 0;
							}
						}
					}
				}

				$is_nonwpfts_queries = $qlog_settings['nonwpfts_queries'];

				if ((!$use_indexed_search) && (!$is_nonwpfts_queries)) {
					$is_qlog_enabled = 0;	// Disable query logging, because we do not allow non-WPFTS queries to be logged
					$sysvars['is_qlog_enabled'] = $is_qlog_enabled;
				}

				$wpq->wpftsi_session['start_ts'] = microtime(true);
				$wpq->wpftsi_session['start_ram'] = memory_get_usage();
				$wpq->wpftsi_session['start_ram_peak'] = memory_get_peak_usage();
				
				if ($is_qlog_enabled) {
					// Okay, we are enabled to use qlog
					$wpq->wpftsi_session['qlog_enabled'] = 1;

					$t_ref = wp_get_raw_referer();
					$init_data = array(
						'user_id' => get_current_user_id(),
						'req_ip' => $this->GetRemoteIP(),
						'ref_url' => $t_ref ? $t_ref : '',	
					);

					$qlog_id = $this->createQLogDB($wpq->query_vars['s'], $wpq->query_vars, $search_type, $preset_ident, $init_data);

					if ($qlog_id > 0) {
						// Let's log start variables
						$start_data = array(
							'token' => $wpq->wpftsi_session['qlog_token'],
							'ts' => $wpq->wpftsi_session['start_ts'],
							'ram' => $wpq->wpftsi_session['start_ram'],
							'ram_peak' => $wpq->wpftsi_session['start_ram_peak'],
							'query' => (Array)$wpq->query,
							'query_vars' => (Array)$wpq->query_vars,
							'bktr' => $bktr,
							'sysvars' => $sysvars,
							'preset' => $preset_ident,
							'preset_data' => $preset_data,
							'preset_setby' => $preset_setby,
						);
						self::AddLog($qlog_id, 'wpfts_qlog_start', $start_data);



					} else {
						// Failed to create qlog record in DB!
						$qlog_id = rand(95000000, 95999999);
					}

				} else {
					// QueryLog not used, but we still need for unique q_id
					$qlog_id = rand(99000000, 99999999);
				}
				$wpq->wpftsi_session['q_id'] = $qlog_id;

			} else {
				// The wpfts session was already started for this WP_Query object, do nothing
			}
		}
	}

	public function GoSearch($wpq)
	{
		if ($wpq && is_object($wpq) && isset($wpq->query_vars)) {
			$ses = $wpq->wpftsi_session;

			if (isset($ses['qlog_enabled']) && ($ses['qlog_enabled'] == 1)) {
				$q_id = isset($ses['q_id']) ? $ses['q_id'] : 0;
				if ($q_id > 0) {
					// Log current data
					$data = array(
						'start_ts' => $ses['start_ts'],
						'start_ram' => $ses['start_ram'],
						'start_ram_peak' => $ses['start_ram_peak'],
						'query' => (Array)$wpq->query,
						'query_vars' => (Array)$wpq->query_vars,
					);
					self::AddLog($q_id, 'wpfts_qlog_gosearch', $data);
				}
			}
		}
	}

	public function AfterPreGetPosts($wpq)
	{
		if ($wpq && is_object($wpq) && isset($wpq->wpftsi_session['qlog_enabled']) && ($wpq->wpftsi_session['qlog_enabled'] != 0)) {
			$ses = $wpq->wpftsi_session;

			$q_id = isset($ses['q_id']) ? $ses['q_id'] : 0;
			if ($q_id > 0) {
				// Log current data
				// Here we need to record all session data (except some really BIG fields and some already recorded)
				$exclude_keys = array(
					'start_bktrace' => 1, 
					'start_ts' => 1, 
					'start_ram' => 1, 
					'start_ram_peak' => 1,
				);
				$sess_filtered = array();
				foreach ($ses as $k => $d) {
					if (!isset($exclude_keys[$k])) {
						$sess_filtered[$k] = $d;
					}
				}

				$data = array(
					'ts' => microtime(true),
					'ram' => memory_get_usage(),
					'ram_peak' => memory_get_peak_usage(),
					'query' => (Array)$wpq->query,
					'query_vars' => (Array)$wpq->query_vars,
					'session' => $sess_filtered,
				);
				self::AddLog($q_id, 'wpfts_qlog_afterpregetposts', $data);
			}
		}
	}

	public function FinishSearch($wpq)
	{
		global $wpfts_core;

		if ($wpq && is_object($wpq) && isset($wpq->wpftsi_session['qlog_enabled']) && ($wpq->wpftsi_session['qlog_enabled'] != 0)) {
			$ses = $wpq->wpftsi_session;

			$q_id = isset($ses['q_id']) ? $ses['q_id'] : 0;
			if ($q_id > 0) {
				// Log current data
				$data = array(
					'end_ts' => $ses['end_ts'],
					'end_ram' => $ses['end_ram'],
					'end_ram_peak' => $ses['end_ram_peak'],
					'total_time_ts' => $ses['total_time_ts'],
					'total_ram' => $ses['total_ram'],
					'total_ram_peak' => $ses['total_ram_peak'],
					'total_n_posts' => $ses['total_n_posts'],
					'query' => (Array)$wpq->query,
					'query_vars' => (Array)$wpq->query_vars,
				);
				self::AddLog($q_id, 'wpfts_qlog_finishsearch', $data);

				// Update statistics
				$data = array(
					'q_time' => $ses['total_time_ts'],
					'n_results' => $ses['total_n_posts'],
					'max_ram' => max($ses['total_ram'], $ses['total_ram_peak']),
				);
				$pfx = $wpfts_core->dbprefix();
				$wpfts_core->db->update($pfx.'qlog', $data, array('id' => $q_id));
			}
		}
	}

	public function DecodeExtData($ext_data = '')
	{
		$t = explode('%$%', $ext_data);

		$ret = array();
		foreach ($t as $d) {
			if (strlen($d) > 0) {
				list ($len, $key, $ts, $json) = explode('|', $d, 4);
				$key = str_replace('\x25\x24\x25', '%$%', $key);
				if (strlen($key) > 0) {
					$data = array();
					try {
						$data = json_decode($json, true);
					} catch (Exception $e) {
						$data = false;
					}
					if ($data !== false) {
						$ret[$key] = array(
							'ts' => $ts,
							'data' => $data,
						);
					}
				}
			}
		}

		return $ret;
	}

	/**
	 * This method adds some log data to the 'ext' column of the wpftsi_qlog table
	 * It can be any data, basically, and they will be decoded in the Query Log viewer
	 * 
	 */
	public static function AddLog($qlog_id, $step_ident, $data = array())
	{
		if (($qlog_id < 1) && ($qlog_id > 90000000)) {
			// $qlog_id is none or virtual, so do not record data to DB!
			return false;
		}

		$step_ident = trim(str_replace('%$%', '\x25\x24\x25', $step_ident));

		if (strlen($step_ident) < 1) {
			// We require step_ident to be valid non-empty string
			return false;
		}

		global $wpfts_core;
		$idx = $wpfts_core->dbprefix();

		$timestamp = microtime(true);
		$json1 = '|'.$step_ident.'|'.$timestamp.'|'.json_encode($data);
		
		$q = 'update `'.$idx.'qlog` set `ext` = concat(`ext`, "%$%", "'.strlen($json1).'", "'.addslashes($json1).'") where `id` = "'.addslashes($qlog_id).'"';
		$wpfts_core->db->query($q);

		return true;
	}
}