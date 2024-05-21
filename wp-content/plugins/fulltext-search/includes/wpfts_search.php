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

class WPFTS_Search
{

	public function set_hooks()
	{
		add_action('pre_get_posts', array($this, 'index_pre_get_posts'), 32760);		// 1753
		add_filter('posts_search', array($this, 'index_sql_select'), 10, 2);			// 2100
		add_filter('posts_search_orderby', array($this, 'index_sql_orderby'), 10, 2);	// 2355
		add_filter('posts_where', array($this, 'index_sql_where'), 10, 2);				// 2537	// for mime-types only
		add_filter('posts_join', array($this, 'index_sql_joins'), 10, 2);				// 2547
		add_filter('posts_distinct', array($this, 'index_posts_distinct'), 10, 2);		// 2714	// set sql_no_cache
		add_filter('post_limits', array($this, 'index_post_limits'), 10, 2);			// 2724
		add_filter('posts_fields', array($this, 'index_posts_fields'), 10, 2);			// 2734
		add_filter('posts_clauses', array($this, 'index_posts_clauses'), 10, 2);		// 2747	// information only
		// sql request merge point														// 2894
		add_filter('posts_pre_query', array($this, 'index_posts_pre_query'), 10, 2);	// 2925	// get relev for split_mode
		add_filter('split_the_query', array($this, 'index_split_the_query'), 10, 2);	// 2985
		add_filter('the_posts', array($this, 'index_the_posts'), 10, 2);		// 3169 (almost at the end - good for cleanup)
	}

	public function index_pre_get_posts(&$wpq)
	{
		global $wpfts_core;

		if (is_object($wpq) && property_exists($wpq, 'wpftsi_session') && is_array($wpq->wpftsi_session)) {
			// Okay
		} else {
			// Do not process this hook
			return;
		}

		// Correct "disable" flag (it could be changed in 3rd-party "pre_get_posts" calls)
		$is_wpfts_disabled = intval($wpq->get('wpfts_disable', intval($wpfts_core->get_option('enabled')) ? 0 : 1));
		$wpq->set('wpfts_disable', $is_wpfts_disabled);
		$wpq->wpftsi_session['is_wpfts_disable'] = $is_wpfts_disabled;

		if ($is_wpfts_disabled) {
			// The WPFTS search is completely disabled by WPFTS Main Settings or WP_Query 'wpfts_disable' parameter
			$wpq->wpftsi_session['use_indexed_search'] = 0;
			return;
		}

		if (!isset($wpq->wpftsi_session['props'])) {
			$wpq->wpftsi_session['props'] = array();
		}

		$use_indexed_search = 0;	// @todo replace this by WPFTS Main Settings checkbox experimental features

		$preset_data = isset($wpq->wpftsi_session['preset_data']) ? $wpq->wpftsi_session['preset_data'] : array();

		if (isset($preset_data['use_indexed_search'])) {
			// Apply preset data
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

		$wpq->wpftsi_session['use_indexed_search'] = $use_indexed_search ? 1 : 0;

		$display_attachments = 0;	// @todo Remove later, deprecated

		$nocache = 0;

		if ($wpq->wpftsi_session['use_indexed_search']) {
			// Fill up WPFTS-specific variables only (all other preset_data will be applied below)

			// Word Logic
			$word_logic = intval($wpfts_core->get_option('deflogic') ? 'or' : 'and');	// Convert 'deflogic' to string, @todo
			$wpq_word_logic = mb_strtolower($wpq->get('word_logic', ''));
			if (strlen($wpq_word_logic) > 0) {
				$word_logic = $wpq_word_logic;
			}
			if (isset($preset_data['word_logic']) && (strlen($preset_data['word_logic']) > 0)) {
				$word_logic = $preset_data['word_logic'];
			}
			$wpq->wpftsi_session['props']['word_logic'] = $word_logic;
			$wpq->set('word_logic', $word_logic);

			// Orderby
			$orderby = $wpq->get('orderby', $wpfts_core->get_option('mainsearch_orderby'));
			if (isset($preset_data['orderby']) && 
					((is_string($preset_data['orderby']) && (strlen($preset_data['orderby']) > 0)) || 
					(is_array($preset_data['orderby']) && (count($preset_data['orderby']) > 0)))) {
				$orderby = $preset_data['orderby'];
			}
			$wpq->wpftsi_session['props']['orderby'] = $orderby;
			$wpq->set('orderby', $orderby);

			// Order
			$order = $wpq->get('order', $wpfts_core->get_option('mainsearch_order'));
			if (isset($preset_data['order']) && 
					((is_string($preset_data['order']) && (strlen($preset_data['order']) > 0)) || 
					(is_array($preset_data['order']) && (count($preset_data['order']) > 0)))) {
				$order = $preset_data['order'];
			}
			$wpq->wpftsi_session['props']['order'] = $order;			
			$wpq->set('order', $order);
			
			// Display Attachments @todo Deprecated, will be removed later
			$display_attachments = $wpq->get('display_attachments', $wpfts_core->get_option('display_attachments', 0));
			if (isset($preset_data['display_attachments']) && is_string($preset_data['display_attachments']) && (strlen($preset_data['display_attachments']) > 0)) {
				$display_attachments = $preset_data['display_attachments'];
			}
			$wpq->wpftsi_session['props']['display_attachments'] = $display_attachments;
			$wpq->set('display_attachments', intval($display_attachments));	// We can remove this line later!

			// Mime types
			$limit_mimetypes = trim($wpq->get('limit_mimetypes', $wpfts_core->get_option('limit_mimetypes')));
			if (isset($preset_data['limit_mimetypes']) && 
					((is_string($preset_data['limit_mimetypes']) && (strlen($preset_data['limit_mimetypes']) > 0)) ||
					(is_array($preset_data['limit_mimetypes']) && (count($preset_data['limit_mimetypes']) > 0)))) {
				$limit_mimetypes = trim((string)$preset_data['limit_mimetypes']);
			}
			$wpq->wpftsi_session['props']['limit_mimetypes'] = $limit_mimetypes;
			$wpq->set('limit_mimetypes', $limit_mimetypes);

			// Cluster Weights
			$cw = $wpfts_core->get_option('cluster_weights', array());
			if (!is_array($cw)) {
				$cw = array();
			}
			$t_cw = $wpq->get('cluster_weights', false);
			if ($t_cw && is_array($t_cw)) {
				// Update CW by WP_Query parameter
				foreach ($t_cw as $k => $d) {
					$cw[$k] = $d;
				}
			}
			if (isset($preset_data['cluster_weights']) && (is_array($preset_data['cluster_weights']))) {
				// Update CW by Preset data
				foreach ($preset_data['cluster_weights'] as $k => $d) {
					$cw[$k] = $d;
				}
			}
			$wpq->wpftsi_session['props']['cluster_weights'] = $cw;
			$wpq->set('cluster_weights', $cw);

			// No cache
			$nocache = $wpq->get('wpfts_nocache', 0);
			$wpq->wpftsi_session['props']['nocache'] = $nocache;

			// Deeper search
			$t_ds = 1;
			if (!isset($wpq->query_vars['deeper_search'])) {
				if ($wpfts_core->get_option('deeper_search') != 0) {
					$t_ds = 1;
				} else {
					$t_ds = 0;
				}
			} else {
				$t_ds = $wpq->get('deeper_search') ? 1 : 0;
			}
			if (isset($preset_data['deeper_search'])) {
				$t_ds = $preset_data['deeper_search'] ? 1 : 0;
			}
			$wpq->set('deeper_search', $t_ds);
			$wpq->wpftsi_session['deeper_search'] = $t_ds;
		}

		// Apply WP_Query native Preset data (even if WPFTS Indexed Search was disabled)
		// @todo


		if (isset($wpq->wpftsi_session['wdgt_data'])) {
			if (isset($wpq->wpftsi_session['wdgt_data']['results_url'])) {
				// Now we assume it's a widget-powered search
				$wpq->set('pagename', '');
				$wpq->set('name', '');
				//$wpq->set('do_not_redirect', 1);
				unset($wpq->queried_object);
				unset($wpq->queried_object_id);
				//$wpq->set( 'posts_per_page', 10 );	// @todo
				$wpq->is_search = true; // We making WP think it is Search page 
            	$wpq->is_page = false; // disable unnecessary WP condition
            	$wpq->is_singular = false; // disable unnecessary WP condition

				// Now we need to implement a redirect to 'results_url'
				// @todo
			}
			if (isset($wpq->wpftsi_session['wdgt_data']['id']) && (strlen($wpq->wpftsi_session['wdgt_data']['id']) > 0)) {
				$wdata = $wpq->wpftsi_session['wdgt_data'];
				do_action_ref_array('wpfts_pre_get_posts', array(&$wpq, $wdata));
			}
		}

		do_action_ref_array('wpfts_pre_set_file_search', array(&$wpq, $display_attachments));

		// Apply data and finish
		$cw = $wpq->get('cluster_weights', false);
		$cluster_weights = apply_filters('wpfts_cluster_weights', $cw, $wpq);
		if (!is_array($cluster_weights)) {
			$cluster_weights = array();
		}

		// In case limit_mimetypes is set, let's clear the post_mime_type
		// It's important for "where" algoritm (to prevent wp_query to fill '$where' by wrong mimetypes)
		$limit_mimetypes = trim($wpq->get('limit_mimetypes', ''));
		if (strlen($limit_mimetypes) > 0) {
			// We will use WPFTS's mimetypes, so STORE the input data
			$wpq->set('wpfts_temp_mimes', $limit_mimetypes);
		} else {
			// Save WP mimetypes
			$wp_mimes = $wpq->get('post_mime_type');
			$wpq->set('wpfts_temp_mimes', $wp_mimes);
		}
		$wpq->set('post_mime_type', '');	// Anyway clear WP mimetypes
	
		$qlog_id = $wpq->wpftsi_session['q_id'];

		$sql_parts = $this->sql_parts($wpq, $cluster_weights, $wpq->wpftsi_session['use_indexed_search'], $nocache, $qlog_id);
		$wpq->wpftsi_session['parts'] = $sql_parts;

		if ($wpq->wpftsi_session['use_indexed_search']) {
			//print_r($wpq->wpftsi_session);
		}

		$wpfts_core->_querylog->AfterPreGetPosts($wpq);
	}
	
	/**
	 * Constructing SQL search part
	 * 
	 * @param string $search Search SQL from WP
	 * @param WP_Query $wpq WP query object
	 */
	public function index_sql_select($search, $wpq)
	{
		$is_enabled = isset($wpq->wpftsi_session['use_indexed_search']) && ($wpq->wpftsi_session['use_indexed_search'] != 0);
		if ($is_enabled) {

			//$search = $search.' '.$wpq->wpftsi_session['select'];	// This way enables custom plugin's search, but it also enables standard WP Query "where" part using AND, which disables most of WPFTS functionality

			$search = $wpq->wpftsi_session['parts']['select'];
		}

		return $search;
	}
	
	public function index_sql_orderby($orderby, $wpq)
	{
		$is_enabled = isset($wpq->wpftsi_session['use_indexed_search']) && ($wpq->wpftsi_session['use_indexed_search'] != 0);
		if ($is_enabled) {
			if (strlen($wpq->wpftsi_session['parts']['orderby']) > 2) {

				// Only replace if orderby = empty or orderby = relevance
				$t = $wpq->get('orderby');
				if (((is_string($t)) && (strlen(trim($t)) < 1)) || ($t == 'relevance')) {
	
					$t2 = $wpq->get('order');
					if ($t2 != 'ASC') {
						$t2 = 'DESC';
					}
					$orderby = $wpq->wpftsi_session['parts']['orderby'].' '.$t2;
				}
			}
			
			return $orderby;
		}
		return $orderby;
	}
	
	public function wpfts_wp_post_mime_type_where($post_mime_types, $table_alias = '')
	{
		$where = '';
		$wildcards = array('', '%', '%/%');
		if (is_string($post_mime_types)) {
			$post_mime_types = array_map('trim', explode(',', $post_mime_types));
		}
	
		$wheres = array();
		foreach ((array) $post_mime_types as $mime_type) {
			$mime_type = preg_replace('/\s/', '', $mime_type);
			$slashpos = strpos($mime_type, '/');
			if ( false !== $slashpos ) {
				$mime_group = preg_replace('/[^-*.a-zA-Z0-9]/', '', substr($mime_type, 0, $slashpos));
				$mime_subgroup = preg_replace('/[^-*.+a-zA-Z0-9]/', '', substr($mime_type, $slashpos + 1));
				if ( empty($mime_subgroup) )
					$mime_subgroup = '*';
				else
					$mime_subgroup = str_replace('/', '', $mime_subgroup);
				$mime_pattern = "$mime_group/$mime_subgroup";
			} else {
				if ($mime_type == '#usual_posts#') {
					$mime_pattern = '';
				} else {
					$mime_pattern = preg_replace('/[^-*.a-zA-Z0-9]/', '', $mime_type);
					if ( false === strpos($mime_pattern, '*') )
						$mime_pattern .= '/*';
				}
			}
	
			$mime_pattern = preg_replace('/\*+/', '%', $mime_pattern);
	
			if ( in_array( $mime_type, $wildcards ) )
				return '';
	
			if ( false !== strpos($mime_pattern, '%') )
				$wheres[] = empty($table_alias) ? "post_mime_type LIKE '$mime_pattern'" : "$table_alias.post_mime_type LIKE '$mime_pattern'";
			else
				$wheres[] = empty($table_alias) ? "post_mime_type = '$mime_pattern'" : "$table_alias.post_mime_type = '$mime_pattern'";
		}
		if ( !empty($wheres) )
			$where = ' AND (' . join(' OR ', $wheres) . ') ';
		return $where;
	}

	public function index_sql_where($where, $wpq)
	{
		$is_enabled = isset($wpq->wpftsi_session['use_indexed_search']) && ($wpq->wpftsi_session['use_indexed_search'] != 0);
		if ($is_enabled) {

			$t_post_mime_types = array();

			$z = trim($wpq->get('wpfts_temp_mimes', ''));
			if (is_string($z)) {
				if (strlen($z) > 0) {
					$a3 = explode(',', $z);
					foreach ($a3 as $dd) {
						$t_post_mime_types[] = trim($dd);	
					}
				}
			} elseif (is_array($z)) {
				if (count($z) > 0) {
					foreach ($z as $dd) {
						$t_post_mime_types[] = trim($dd);
					}
				}
			}
			if (count($t_post_mime_types) > 0) {
				$t_post_mime_types[] = '#usual_posts#';
			}

			global $wpdb;

			$tt = $this->wpfts_wp_post_mime_type_where($t_post_mime_types, $wpdb->posts);

			$where .= $tt;
		}

		return $where;
	}

	public function index_sql_joins($join, $wpq)
	{
		$is_enabled = isset($wpq->wpftsi_session['use_indexed_search']) && ($wpq->wpftsi_session['use_indexed_search'] != 0);
		if ($is_enabled) {
			return $join.$wpq->wpftsi_session['parts']['join'];
		}

		return $join;
	}
	
	public function index_posts_distinct($distinct, $wpq)
	{
		// This hook will work in case WPFTS is enabled (even if indexed search is disabled)
		$is_wpfts_disable = isset($wpq->wpftsi_session['is_wpfts_disable']) && ($wpq->wpftsi_session['is_wpfts_disable'] != 0);
		if (!$is_wpfts_disable) {
			$nocache_str = (isset($wpq->wpftsi_session['wpfts_nocache']) && (intval($wpq->wpftsi_session['wpfts_nocache']) > 0)) ? ' SQL_NO_CACHE ' : '';

			return str_replace('SQL_NO_CACHE', '', $distinct).$nocache_str;
		}
		return $distinct;
	}

	public function index_post_limits($limits, $wpq) 
	{
		$is_enabled = isset($wpq->wpftsi_session['use_indexed_search']) && ($wpq->wpftsi_session['use_indexed_search'] != 0);
		if ($is_enabled) {
			// Save $limits to set up 'split_mode'
			$wpq->wpftsi_session['limits'] = $limits;
			return $limits;
		}
		return $limits;
	}
	
	public function index_posts_fields($fields, $wpq)
	{
		$is_enabled = isset($wpq->wpftsi_session['use_indexed_search']) && ($wpq->wpftsi_session['use_indexed_search'] != 0);
		if ($is_enabled) {
			global $wpdb;
		
			// Save old $fields to generate correct 'split_the_query' response
			$wpq->wpftsi_session['old_fields'] = $fields;
		
			// Decide if we make split_query or not
			$limits = $wpq->wpftsi_session['limits'];
			$ppp = isset($wpq->query_vars['posts_per_page']) ? $wpq->query_vars['posts_per_page'] : 0;
					
			$is_split_query = false;
			if ((!empty($limits)) && ($ppp < 500) && ("{$wpdb->posts}.*" == $fields)) {
				$is_split_query = true;
			}
			$wpq->wpftsi_session['is_split_query'] = $is_split_query;
		
			if ($is_split_query) {
				// Use ID, relev for the main query
				return "{$wpdb->posts}.ID".$wpq->wpftsi_session['parts']['fields'];
			}
		
			return $fields.$wpq->wpftsi_session['parts']['fields'];
		}
		return $fields;
	}
	
	public function index_posts_clauses($clauses, $wpq)
	{
		if ((!isset($GLOBALS['posts_clauses'])) || (!is_array($GLOBALS['posts_clauses']))) {
			$GLOBALS['posts_clauses'] = array();
		}
		$GLOBALS['posts_clauses'][] = $clauses;
		
		return $clauses;
	}
	
	/**
	 * Set up the amount of found posts and the number of pages (if limit clause was used)
	 * for the current query.
	 * 
	 * Notice: this method was completely copied from WP_Query(), because the original one is 'private' so
	 * we can not use them.
	 *
	 * @since 3.5.0
	 *
	 * @param array  $q      Query variables.
	 * @param string $limits LIMIT clauses of the query.
	 */
	public function wpq_set_found_posts(&$wpq, $q, $limits)
	{
		// Bail if posts is an empty array. Continue if posts is an empty string,
		// null, or false to accommodate caching plugins that fill posts later.
		if ( $q['no_found_rows'] || ( is_array( $wpq->posts ) && ! $wpq->posts ) ) {
			return;
		}

		if ( ! empty( $limits ) ) {

			global $wpfts_core;

			/**
			 * Filters the query to run for retrieving the found posts.
			 *
			 * @since 2.1.0
			 *
			 * @param string   $found_posts The query to run to find the found posts.
			 * @param WP_Query $wpq        The WP_Query instance (passed by reference).
			 */
			$wpq->found_posts = $wpfts_core->db->get_var( apply_filters_ref_array( 'found_posts_query', array( 'SELECT FOUND_ROWS()', &$wpq ) ) );
		} else {
			if ( is_array( $wpq->posts ) ) {
				$wpq->found_posts = count( $wpq->posts );
			} else {
				if ( null === $wpq->posts ) {
					$wpq->found_posts = 0;
				} else {
					$wpq->found_posts = 1;
				}
			}
		}

		/**
		 * Filters the number of found posts for the query.
		 *
		 * @since 2.1.0
		 *
		 * @param int      $found_posts The number of posts found.
		 * @param WP_Query $wpq        The WP_Query instance (passed by reference).
		 */
		$wpq->found_posts = apply_filters_ref_array( 'found_posts', array( $wpq->found_posts, &$wpq ) );

		if ( ! empty( $limits ) ) {
			$wpq->max_num_pages = ceil( $wpq->found_posts / (isset($q['posts_per_page']) ? intval($q['posts_per_page']) : 1));
		}
	}

	public function index_posts_pre_query($posts, $wpq) 
	{
		global $wpdb, $wpfts_core;

		$is_enabled = isset($wpq->wpftsi_session['use_indexed_search']) && ($wpq->wpftsi_session['use_indexed_search'] != 0);
		if ($is_enabled) {
			if (isset($wpq->wpftsi_session['is_split_query']) && $wpq->wpftsi_session['is_split_query']) {
				$post_idrev = $wpfts_core->db->get_results( $wpq->request );

				$this->wpq_set_found_posts($wpq, $wpq->query_vars, $wpq->wpftsi_session['limits']);

				$res2 = array();
				if (count($post_idrev) > 0) {
					// Retrieve full posts
					// We have to use MySQL query (not get_post()) because get_post() will cache the post without relev
					$ids = array();
					$ords = array();
					$i = 1;
					foreach ($post_idrev as $d) {
						$ids[$d->ID] = $d->relev;
						$ords[$d->ID] = $i ++;
					}

					$q = 'select * from `'.$wpdb->posts.'` where ID in ('.implode(',', array_keys($ids)).')';
					$res2 = $wpfts_core->db->get_results($q);

					// We have to reorder post to make the same order as ID list was before
					usort($res2, function($v1, $v2) use (&$ords)
					{
						$t1 = $ords[$v1->ID];
						$t2 = $ords[$v2->ID];

						return ($t1 < $t2 ? -1 : ($t1 > $t2 ? 1 : 0));
					});

					foreach ($res2 as $k => $row) {
						if (isset($row->ID) && isset($ids[$row->ID])) {
							$res2[$k]->relev = floatval($ids[$row->ID]);
						}
					}
				}
				return $res2;
			}
		}

		return $posts;
	}

	public function index_split_the_query($split_the_query, $wpq) 
	{
		$is_enabled = isset($wpq->wpftsi_session['use_indexed_search']) && ($wpq->wpftsi_session['use_indexed_search'] != 0);
		if ($is_enabled) {
			if (isset($wpq->wpftsi_session['old_fields']) && (!$split_the_query)) {
				// Check if we need to switch the split_the_query on
				$split_the_query = isset($wpq->wpftsi_session['is_split_query']) ? $wpq->wpftsi_session['is_split_query'] : false;
			}
			return $split_the_query;
		}
		return $split_the_query;
	}

	/**
	 * We are using this latest hook in WP_Query as a clean up function
	 */
	function index_the_posts($posts, $wpq)
	{
		global $wpfts_core;

		// This method works even is 'use_indexed_search' is disabled
		$is_wpfts_disable = isset($wpq->wpftsi_session['is_wpfts_disable']) && ($wpq->wpftsi_session['is_wpfts_disable'] != 0);
		if (!$is_wpfts_disable) {
		
			if (isset($wpq->wpftsi_session)) {

				// Store statistics info
				$wpq->wpftsi_session['end_ts'] = microtime(true);
				$wpq->wpftsi_session['end_ram'] = memory_get_usage();
				$wpq->wpftsi_session['end_ram_peak'] = memory_get_peak_usage();
				
				// Calculate difference
				if (isset($wpq->wpftsi_session['start_ts'])) {
					$wpq->wpftsi_session['total_time_ts'] = $wpq->wpftsi_session['end_ts'] - $wpq->wpftsi_session['start_ts'];
				}
				if (isset($wpq->wpftsi_session['start_ram'])) {
					$wpq->wpftsi_session['total_ram'] = $wpq->wpftsi_session['end_ram'] - $wpq->wpftsi_session['start_ram'];
				}
				if (isset($wpq->wpftsi_session['start_ram_peak'])) {
					$wpq->wpftsi_session['total_ram_peak'] = $wpq->wpftsi_session['end_ram_peak'] - $wpq->wpftsi_session['start_ram_peak'];
				}
				$wpq->wpftsi_session['total_n_posts'] = $wpq->found_posts; //is_array($posts) ? count($posts) : 0;

				$wpfts_core->_querylog->FinishSearch($wpq);

				$sess = $wpq->wpftsi_session;
				$parts = isset($sess['parts']) ? $sess['parts'] : array();

				if (isset($parts['is_use_ttable']) && ($parts['is_use_ttable'])) {
					// We don't need to clean up "tp" because temp table was used
				} else {
					if (isset($parts['issearch']) && ($parts['issearch'])) {
						if ((isset($sess['q_id'])) && ($sess['q_id'] > 0)) {
							// Clear the tp
							$pfx = $wpfts_core->dbprefix();
	
							$exp_time = date('Y-m-d H:i:s', time() - 3600 * 1);	// Remove occassional records after 1 hour
	
							$q = 'delete from `'.$pfx.'tp` where (`q_id` = "'.addslashes($sess['q_id']).'") or (`ts` < "'.$exp_time.'")';

							$wpfts_core->db->query($q);
						}	
					}
				}
				$wpq->wpftsi_session = null;
			}
			return $posts;
		}
		return $posts;
	}
	

	public function parse_search_terms($a, &$wpq)
	{	
		global $wpfts_core;

		$z = array();
		foreach ($a as $d) {
			$v = mb_strtolower(trim($d), 'utf-8');
			$is_quoted = (mb_strlen($v) > 0) && ($v[0] == '"');
			if ($is_quoted) {
				$v = trim($v, '"');
			}
			if (mb_strlen($v) > 0) {
				if (($wpfts_core != null) && ($wpfts_core->get_option('internal_search_terms') != 0)) {
					$vv = $wpfts_core->split_to_words($d);
					$v = implode(' ', $vv);
					if ($is_quoted) {
						$v = '"'.$v.'"';
					}
				}	
				$z[] = $v;
			}
		}
		
		return apply_filters('wpfts_search_terms', $z, $wpq);
	}
	
	public function count1s32($i)
	{
		$count = 0;
		$i = $i - (($i >> 1) & 0x55555555);
		$i = ($i & 0x33333333) + (($i >> 2) & 0x33333333);
		$i = ($i + ($i >> 4)) & 0x0f0f0f0f;
		$i = $i + ($i >> 8);
		$i = $i + ($i >> 16);
		$count += $i & 0x3f;
	
		return $count;
	}

	public function write_log($wpq, $step_ident, $data = array(), $for_detailed_only = false)
	{
		if ($wpq && is_object($wpq) && isset($wpq->wpftsi_session['qlog_enabled']) && ($wpq->wpftsi_session['qlog_enabled'] != 0)) {

			if ($for_detailed_only) {
				$is_detailed_log = isset($wpq->wpftsi_session['sysvars']['is_detailed_log']) ? $wpq->wpftsi_session['sysvars']['is_detailed_log'] : false;

				if ($is_detailed_log) {
					// Ok
				} else {
					// Disabled detailed logging
					return false;
				}
			}

			$ses = $wpq->wpftsi_session;

			$q_id = isset($ses['q_id']) ? $ses['q_id'] : 0;
			if ($q_id > 0) {
				WPFTS_QueryLog::AddLog($q_id, $step_ident, $data);
			}
		}
	}

	public function DecodeStopWords($ws = '')
	{
		// $ws can be string or array
		$a = array();
		if (is_string($ws)) {
			$a = preg_split('~[\s\x2c]+~', $ws);
		} elseif (is_array($ws)) {
			$a = $ws;
		} else {
			return array();
		}

		$b = array();
		foreach ($a as $dd) {
			$tt = trim($dd);
			if (strlen($tt) > 0) {
				$type = 0;
				if ($tt[mb_strlen($tt) - 1] == '*') {
					$type = 1;
				}
				$b[] = array($type, $tt);	
			}
		}

		return $b;
	}

	public function PutCalcd($calcd, $qlog_id, $is_use_ttable, $is_first = false)
	{
		global $wpfts_core;

		$pfx = $wpfts_core->dbprefix();

		$tname = $pfx.'trel';
		if ($is_use_ttable) {
			if ($is_first) {
				// Remove previous temporary table
				$qr = 'drop temporary table if exists `'.$tname.'`';
				$wpfts_core->db->query($qr);
	
				// Create temporary table
				$qr = 'create temporary table `'.$tname.'` (
							`did` int(11) not null, 
							`pow` int(11) not null, 
							`res` float(10,6) not null,
							key `did` (`did`) )';
				$wpfts_core->db->query($qr);
			}

			if ($calcd && is_array($calcd) && (count($calcd) > 0)) {
				$aa = array();
				foreach ($calcd as $kk => $dd) {
					$aa[] = '("'.$kk.'","'.$dd[1].'","'.$dd[0].'")';
				}
				$qr = 'insert into `'.$tname.'` (`did`,`pow`,`res`) values '.implode(',', $aa);
				$wpfts_core->db->query($qr);	
			}
	
		} else {
			// Use static table
			if ($is_first) {
				// Do nothing for this case
			}

			if ($calcd && is_array($calcd) && (count($calcd) > 0)) {
				$tname = $pfx.'tp';
				$ts = date('Y-m-d H:i:s', time());
				$aa = array();
				foreach ($calcd as $kk => $dd) {
					$aa[] = '("'.$qlog_id.'","'.$kk.'","'.$dd[1].'","'.$dd[0].'","'.$ts.'")';
				}
				$qr = 'insert into `'.$tname.'` (`q_id`,`did`,`pow`,`res`,`ts`) values '.implode(',', $aa);
				$wpfts_core->db->query($qr);
			}
		}
	}

	public function GetMaxRelevance($qlog_id, $is_use_ttable)
	{
		global $wpfts_core;

		$pfx = $wpfts_core->dbprefix();

		$tname = $pfx.'trel';
		if (!$is_use_ttable) {
			$tname = $pfx.'tp';
		}

		// Getting max relevance
		$qr = 'select 
				max(trel.`res` / LOG(tbase.n + 1)) mx_rel
			from `'.$tname.'` trel
			straight_join `'.$pfx.'docs` tbase
				on (trel.did = tbase.id)'.($is_use_ttable ? '' : ' and (trel.q_id = "'.addslashes($qlog_id).'")');
		$r5 = $wpfts_core->db->get_results($qr, ARRAY_A);

		$mxrel = (isset($r5[0]) && isset($r5[0]['mx_rel'])) ? $r5[0]['mx_rel'] : 1;
			
		return $mxrel;
	}

	public function sql_parts(&$wpq, $cw, $issearch, $nocache, $qlog_id)
	{
		global $wpdb, $wpfts_core;
		
		$pfx = $wpfts_core->dbprefix();

		$q = &$wpq->query_vars;
		
		$is_use_ttable = false;

		$join = '';
		$fields = '';
		$orderby = '';
		$where_part = '';
		$matches = array();
		$good_doc_masks = array();
		$is_use_doc_masks = false;

		if ((!empty($q['s'])) && ($issearch)) {
			
			$qs = stripslashes($q['s']);
			if ( empty( $_GET['s'] ) && $wpq->is_main_query() ) {
				$qs = urldecode( $qs );
			}

			$qs = str_replace( array( "\r", "\n" ), '', $qs );
			$q['search_terms_count'] = 1;
			if ( ! empty( $q['sentence'] ) ) {
				$q['search_terms'] = array( $qs );
			} else {
				if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $qs, $matches ) ) {
					$q['search_terms_count'] = count( $matches[0] );
					$q['search_terms'] = $this->parse_search_terms( $matches[0], $wpq );
					// if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence
					if ( empty( $q['search_terms'] ) || count( $q['search_terms'] ) > 19 ) {
						$q['search_terms'] = array( $qs );
					}
				} else {
					$q['search_terms'] = array( $qs );
				}
			}

			// Decode terms
			$ts = array();
			foreach ($q['search_terms'] as $t) {
				$f = !empty($q['exact']) ? 1 : 0;
				if (!empty($q['sentence'])) {
					$ts[] = array($f, trim($t));
				} else {
					if (mb_substr($t, 0, 1, 'utf-8') == '"') {
						$t2 = explode(' ', trim($t, '"'));
						$f = 1;
					} else {
						$t2 = explode(' ', trim($t));
					}
					if (is_array($t2)) {
						foreach ($t2 as $tt) {
							$ts[] = array($f, mb_strtolower(trim($tt)));
						}
					}
				}
			}
			$q['search_terms'] = $ts;
			$q['search_terms_count'] = count($ts);
			
			$is_deeper_search = $wpq->get('deeper_search', false);

			$i = 1;
			if (count($ts) > 0) {
				// Ok, let's create mysql pieces
				$masks = array();
			
				$st_msk_a = array();
				$st_q = array(0);
				$st_msk_bit = 1;
				$full_mask = 0;
				foreach ($ts as $ts_item) {
					$word = $ts_item[1];
					$f = $ts_item[0];
					if (mb_strlen($word) >= 3) {
						if ($f) {
							$st_q[] = '(w.word = "'.$wpdb->esc_like($word).'")';
							$st_msk_a[] = 'if(w.word = "'.$wpdb->esc_like($word).'", '.$st_msk_bit.', 0)';
						} else {
							$st_q[] = '(w.word like "'.($is_deeper_search ? '%' : '').$wpdb->esc_like($word).'%")';
							$st_msk_a[] = 'if(w.word like "'.($is_deeper_search ? '%' : '').$wpdb->esc_like($word).'%", '.$st_msk_bit.', 0)';							
						}
					} else {
						if ((mb_strlen($word) <= 2) && (mb_strlen($word) >= 1)) {
							$tt = '';
							if ($f) {
								$tt = 'w.word = "'.$wpdb->esc_like($word).'"';
							} else {
								$maxn = 5 * mb_strlen($word);
								$tt = '(w.word like "'.($is_deeper_search ? '%' : '').$wpdb->esc_like($word).'%") and (char_length(w.word) <= "'.$maxn.'")';
							}
							$st_q[] = '('.$tt.')';
							$st_msk_a[] = 'if('.$tt.', '.$st_msk_bit.', 0)';
						}
					}
					$masks[$st_msk_bit] = array($word, mb_strlen($word));
			
					$full_mask |= $st_msk_bit;

					$st_msk_bit = $st_msk_bit << 1;
				}
			
				if ($wpq->get('word_logic') == 'and') {
					// Require full mask
					$is_use_doc_masks = true;
					$good_doc_masks = array(
						't'.$full_mask => 1,
					);
				}
			
				$n_words = count($ts);
			
				// Retrieve and decode stop words
				$stop_words = $this->DecodeStopWords($wpq->get('stop_words', ''));

				// We going to use a little bit different algorithm for x86-based machines
				// Because 'Q'-packing only supported on x64.
				$is_x64 = (PHP_INT_SIZE == 8);	// Simple check for x64 support

				$is_optimizer = intval($wpfts_core->get_option('is_optimizer'));

				$memory_limit = $wpfts_core->get_memory_limit();

				$is_request_chunking = true;
				if ($memory_limit < 128 * 1024 * 1024) {
					// Disable request chunking due to low memory (or it's set to -1)
					$is_request_chunking = false;
				}

				$bq_ram = memory_get_usage();

				$this->write_log($wpq, 'BF word selection', array(
					'search_terms' => $q['search_terms'],
					'stop_words' => $stop_words,
					'is_x64' => $is_x64,
					'is_optimizer' => $is_optimizer,
					'memory_limit' => $memory_limit,
					'is_request_chunking' => $is_request_chunking ? 1 : 0,
					'ts' => microtime(true),
					'ram' => $bq_ram,
					'ram_peak' => memory_get_peak_usage(),					
				), true);

				$vecdata = array();
				$cntrs = array();
				$wordz1 = array();

				// Exclude some words
				$exc_words = array();
				foreach ($stop_words as $tt) {
					if ($tt[0] == 0) {
						$exc_words[] = '(`word` = "'.addslashes($tt[1]).'")';
					} else {
						$exc_words[] = '(`word` like "'.addslashes(str_replace('*', '', $tt[1])).'%")';
					}
				}

				if ($is_optimizer) {
					// Full algorithm
					$qr = '
						SELECT
							w.id,
							char_length(w.word) len,
							('.implode(' | ', $st_msk_a).') mask,
							w.act,
							vc.vc
						FROM
							`'.$pfx.'words` w
						left join `'.$pfx.'vc` vc
							on vc.wid = w.id
						WHERE
							('.implode(' or ', $st_q).') '.(count($exc_words) > 0 ? 
								' and (w.`id` not in (select `id` from `'.$pfx.'words` where '.implode(' or ', $exc_words).'))' 
								: '').'
						ORDER BY
							NULL
					';
				} else {
					// Short algorithm
					$qr = '
						SELECT
							w.id,
							char_length(w.word) len,
							('.implode(' | ', $st_msk_a).') mask,
							w.act
						FROM
							`'.$pfx.'words` w
						WHERE
							('.implode(' or ', $st_q).') '.(count($exc_words) > 0 ? 
								' and (w.`id` not in (select `id` from `'.$pfx.'words` where '.implode(' or ', $exc_words).'))' 
								: '').'
						ORDER BY
							NULL
					';
				}
				// Query for word data
				$res1 = $wpfts_core->db->get_results($qr, OBJECT);
			
				$wz1_ram = memory_get_usage();

				// Let's check if we have enough RAM to keep all document vectors
				$free_ram_2 = $is_request_chunking ? ($memory_limit - $wz1_ram) / 2 : $memory_limit;
				$max_vchunk = $is_request_chunking ? intval($free_ram_2 * 10000 / (25 * 1024 * 1024)) : 10000000;	// Approx 25 Mb for 10000 chunk

				$this->write_log($wpq, 'BF wordz step1', array(
					'n_subwords' => count($res1),	// Found number of subwords (with masks and lengths)
					'free_ram_2' => $free_ram_2,	// Free RAM for vchunks
					'max_vchunk' => $max_vchunk,	// Number of vchunks to avoid memory overflow
					'ts' => microtime(true),
					'ram' => $wz1_ram,
					'ram_peak' => memory_get_peak_usage(),					
				), true);

				/*
				// Let's calculate the amount of vectors
				// This block was used for debug purposes only

				$wordlist_ids = array();
				foreach ($res1 as $dd) {
					$wordlist_ids[] = intval($dd->id);
				}
				$qr = 'select count(*) n from `wpftsi_vectors` v where v.wid in ('.implode(',', $wordlist_ids).')';
				$tt_res = $wpfts_core->db->get_results($qr, ARRAY_A);

				$n_found_vectors = $tt_res[0]['n'];

				$wpdb->flush();

				echo 'found vectors = '.$n_found_vectors."\n";

				$start_ram = memory_get_usage();
				
				echo 'start_ram = '.$start_ram."\n";
				*/

				// Okay, get the vcache records
				// key = doc_id
				// value:
				// -- 0 = total mask
				// -- 1 = num_words
				// -- 2 = relevance1
				$nacts = array();
				foreach ($res1 as $dd) {
					if (!isset($wordz1[$dd->id])) {
						if (!isset($masks[$dd->mask])) {
							// Calculate custom mask (combined from different words)
							$tt_max_len = 0;
							foreach ($masks as $kk => $tt) {
								if (($kk & $dd->mask) != 0) {
									$tt_max_len = max($tt_max_len, $tt[1]);
								}
							}
							$masks[$dd->mask] = array(
								'---',
								$tt_max_len,
							);
						}
						$wordz1[$dd->id] = array(
							$dd->len, 
							$dd->mask, 
							$masks[$dd->mask][1] / $dd->len,	// Relative length
						);
					}

					if (($dd->act >= 0) && (isset($dd->vc)) && (strlen($dd->vc) > 0) && ($is_optimizer)) {
						// Valid word cache
						$ns = unpack('l*', $dd->vc);
					
						$zz = 2;
						while ($zz <= count($ns)) {
							$did = $ns[$zz++];
							// Read positions
							while ($zz <= count($ns)) {
								$ofs = $ns[$zz++];
								if (!isset($vecdata[$did])) {
									$vecdata[$did] = '';
									$cntrs[$did] = 0;
								}
			
								$vecdata[$did] .= $is_x64 ? pack('Q', (abs($ofs) << 32) + $dd->id) : pack('ll', abs($ofs), $dd->id);
								$cntrs[$did] ++;
				
								if ($ofs < 0) {
									break;
								}
							}
						}
					} else {
						// Non-valid word cache, use alternative algorithm
						$nacts[] = $dd->id;
					}
				}

				$this->write_log($wpq, 'BF nacts step2', array(
					'n_nacts' => count($nacts),
					'ts' => microtime(true),
					'ram' => memory_get_usage(),
					'ram_peak' => memory_get_peak_usage(),					
				), true);

				$v_max_memory_usage = 0;

				$v_chunks_stat = array();
				$v_planned_requests = 0;
				$v_requests = 0;

				if (count($nacts) > 0) {
					// Get vecdata for nacts
					//echo 'Found '.count($nacts).' nacts'."\n";

					// In order to lower memory consumption we HAVE to process data by chunks
					$nacts_ch = array_chunk($nacts, 1000);

					$v_planned_requests = count($nacts_ch);	// vcache process has been split to this number of parts

					foreach ($nacts_ch as $n_ch) {

						$start_limit = 0;
						$is_limit_finished = false;
	
						while (!$is_limit_finished) {
							$v_requests ++;
							$qr = 'select 
									v.did,
									v.wn,
									v.wid
								from `'.$pfx.'vectors` v
								where 
									v.wid in ('.implode(',', $n_ch).') 
									limit '.$start_limit.', '.$max_vchunk;
							
							$tt0 = microtime(true);
							$wpfts_core->db->query($qr);
							$tt1 = microtime(true);
	
							$ch_read_size = count($wpdb->last_result);

							$end_ram_t = memory_get_usage();

							$tt = array();
							foreach ($wpdb->last_result as $row6) {
								$did = $row6->did;
								if (!isset($vecdata[$did])) {
									$vecdata[$did] = '';
									$cntrs[$did] = 0;
								}
			
								$vecdata[$did] .= $is_x64 ? pack('Q', (intval($row6->wn) << 32) + $row6->wid) : pack('ll', intval($row6->wn), $row6->wid);
								$cntrs[$did] ++;
							}

							$end_ram_vecd = memory_get_usage();

							// Before flush
							$v_max_memory_usage = max($v_max_memory_usage, $end_ram_t);

							$v_chunks_stat[] = array(
								'start_limit' => $start_limit,
								'max_v_chunk' => $max_vchunk,
								'query_time' => $tt1 - $tt0,
								'chunk_size' => $ch_read_size,
								'calculated_ram' => $ch_read_size * 730.23 + 17829159,
								'end_ram_t' => $end_ram_t,
								'end_ram_vecd' => $end_ram_vecd,
							);

							// If we taken exactly $max_vchunk records, it's most possible we have some more data on this
							// subword chunk
							if (count($wpdb->last_result) < $max_vchunk) {
								$is_limit_finished = true;
							} else {
								$start_limit += count($wpdb->last_result);
							}

							//unset($r6);
							$wpdb->flush();			
						}
					}
				}
				// We don't need for this query result anymore
				unset($res1);

				// This coefficient can be used later to show the "Slow down by RAM optimization" message
				$wpq->wpftsi_session['v_ram_optimization'] = ($v_planned_requests > 0) ? $v_requests / $v_planned_requests : 1;

				$this->write_log($wpq, 'BF calcd step3', array(
					'v_max_memory_usage' => $v_max_memory_usage,	// Peak memory usage by vecdata algoritm
					'v_ram_optimization' => $wpq->wpftsi_session['v_ram_optimization'],	// Used RAM optimization
					'v_vecdata_found' => count($vecdata),
					'v_chunks_num' => $v_planned_requests,
					'v_chunks_stat' => $v_chunks_stat,
					'ts' => microtime(true),
					'ram' => memory_get_usage(),
					'ram_peak' => memory_get_peak_usage(),					
				), true);

				$end_ram2 = memory_get_usage();

				// Let's calculate the raw relevance for each document
				$calcd_max_memory_usage = 0;
				$n_total_calcd = 0;
				$is_calcd_first = false;
				$calcd = array();
				foreach ($vecdata as $kv => $dv_str) {
					if ($is_x64) {
						$dv = unpack('Q*', $dv_str);
					} else {
						$t4 = unpack('l*', $dv_str);
						$dv = array();
						$i = 1;
						$t4_l = count($t4);
						while ($i < $t4_l) {
							$dv[] = ($t4[$i++] << 32) + $t4[$i++];
						}
					}
					usort($dv, function($v1, $v2)
					{
						return ($v1 < $v2) ? -1 : (($v1 > $v2) ? 1 : 0);
					});
					
					// Iterate over all found words of this document
					$rown = 0;
					$cmask = 0;
					$lastwn = -100;
			//		$totalrows = count($dv);
					$doc_sum = 0;
					$sent_pow = 10;
					$sent_len = 0;
					$max_sent_len = 0;
					$doc_mask = 0;	// Total mask of the document
					$sent_num = 0;	// Number of sentences (do we need it?)
					foreach ($dv as $w) {

						$wn = $w >> 32;	// offset
						$w32 = $w & 0xffffffff;
						$mask = $wordz1[$w32][1];	// word mask
						$kword = $wordz1[$w32][2];	// word kword
			
						$rown ++;
						$doc_mask = $doc_mask | $mask;
			
						$brk = ((($cmask & $mask) != 0) || ($wn >= $lastwn + 5)) ? 1 : 0;
			
						if ($brk) {
							// The sentence is over
							$max_sent_len = max($max_sent_len, $sent_len);
							$sent_num ++;
							$doc_sum = $doc_sum + $sent_pow;
			
							// Reset sentence counters
							$cmask = $mask;
							$sent_pow = 10 * $kword;
							$sent_len = 1;
						} else {
							// Still the same sentence
							$cmask = $cmask | $mask;
							$sent_pow = ($sent_pow * 10 * $kword * (12.7 + 2.7 * ($lastwn - $wn)) );
							$sent_len ++;
						}
			
						$lastwn = $wn;
					}

					// Finish last sentence
					$max_sent_len = max($max_sent_len, $sent_len);
					$doc_sum = $doc_sum + $sent_pow;
			
					if ((!$is_use_doc_masks) || (isset($good_doc_masks['t'.$doc_mask]))) {

						$n_total_calcd ++;

						$calcd[$kv] = array(
							$doc_sum, // res
							($this->count1s32($doc_mask) - 1) * $n_words + $max_sent_len // pow
						);

						if (count($calcd) >= 10000) {
							// Let's flush data to free some memory
							$calcd_max_memory_usage = max($calcd_max_memory_usage, memory_get_usage());

							$this->PutCalcd($calcd, $qlog_id, $is_use_ttable, $is_calcd_first);
							$is_calcd_first = false;
							$calcd = array();
						}
					}
				}

				if (count($calcd) >= 0) {
					// Let's flush remaining data
					$calcd_max_memory_usage = max($calcd_max_memory_usage, memory_get_usage());

					$this->PutCalcd($calcd, $qlog_id, $is_use_ttable, $is_calcd_first);
					$is_calcd_first = false;
					$calcd = array();
				}

				$mxrel_t0 = microtime(true);
				$mxrel = $this->GetMaxRelevance($qlog_id, $is_use_ttable);
				$mxrel_dt = microtime(true) - $mxrel_t0;

				// After relevance calculation
				$this->write_log($wpq, 'FN calcd step4', array(
					'calcd_max_memory_usage' => $calcd_max_memory_usage,
					'calcd_used_memory' => $calcd_max_memory_usage - $end_ram2,	// Max memory used by calcd algorithm
					'max_relevance' => $mxrel,
					'mxrel_calc_time' => $mxrel_dt,
					'n_docs_found' => $n_total_calcd,	// Number of documents found
					'ts' => microtime(true),
					'ram' => memory_get_usage(),
					'ram_peak' => memory_get_peak_usage(),					
				), true);

				$rcv = 1;
				if (count($cw) > 1) {
					$x = array();
					foreach ($cw as $k => $d) {
						//if(t1.token = "post_title", 100, 50)
						$x[] = ' when "'.$k.'" then '.str_replace(',', '.', floatval($d));
					}
					$rcv = ' (case tbase.token '.implode('', $x).' else 1 end)';
				}

				// Okay, now we can execute the main query :)
				$tname = $pfx.'trel';
				if (!$is_use_ttable) {
					$tname = $pfx.'tp';
				}
		
				$join = '
					inner join ( 
						select 
							fi.tid, 
							t_end.relev 
						from `'.$pfx.'index` fi 
						straight_join ( 
							select 
								tbase.index_id, 
								sum((trel.`pow` + (trel.`res` / LOG(tbase.n + 1)) / '.$mxrel.') * '.$rcv.' / ('.($n_words * $n_words + 1).')) relev
							from `'.$tname.'` trel
							straight_join `'.$pfx.'docs` tbase
								on (trel.did = tbase.id)'.($is_use_ttable ? '' : ' and (trel.q_id = "'.addslashes($qlog_id).'")').'
							group by tbase.index_id 
							order by NULL 
						) t_end 
							on t_end.index_id = fi.id 
						order by null
					) '.$pfx.'t 
						on '.$pfx.'t.tid = '.$wpdb->posts.'.ID 
					';
				
					$fields = ', '.$pfx.'t.relev ';
					$orderby = ' ('.$pfx.'t.relev)';
					$where_part = ' and ('.$pfx.'t.relev > 0)';
				}

		} else {
			$issearch = 0;
		}

		$parts = array(
				'token' => md5(time().'|'.uniqid('session')),
				'issearch' => $issearch,
				'nocache' => $nocache,
				'join' => $join.' ',
				'select' => ' and (((1)))'.$where_part,
				'orderby' => $orderby,
				'fields' => $fields,
				'sql_no_cache' => $nocache ? ' SQL_NO_CACHE' : '',
				'is_use_ttable' => $is_use_ttable,
			);
		
		return $parts;
	}


}