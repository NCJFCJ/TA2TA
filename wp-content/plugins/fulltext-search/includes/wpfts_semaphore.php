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

class WPFTS_Semaphore
{
	public $timeout = 60;
	public $process_id;
	public $proc_name;
	public $option_name;

	function __construct($proc_name, $process_id = false)
	{
		if ($process_id === false) {
			// Create random process_id for this semaphore instance
			$process_id = md5(uniqid('random_process_id').''.time());	// Create random process ID
		}

		$this->process_id = $process_id;
		$this->proc_name = $proc_name;
		$this->option_name = 'wpfts_transient_'.$this->proc_name;
	}

	/**
	 * Check if we have privileges
	 */
	function Check()
	{
		global $wpfts_core, $wpdb;

		$time = time();

		$q = 'select `option_value` from `'.$wpdb->options.'` where `option_name` = "'.addslashes($this->option_name).'"';
		$res = $wpfts_core->db->get_results($q, ARRAY_A);

		// Check if not expired and process_id is ours
		if (count($res) > 0) {
			$json = @json_decode($res[0]['option_value'], true);
			if (isset($json['pid']) && isset($json['expts'])) {
				if (($json['pid'] == $this->process_id) && ($json['expts'] > $time)) {
					// We currently own this resources
					return true;
				}
			}
		}

		// Nobody or someone else owns this resource
		return false;
	}

	/**
	 * Reserve a semaphore (is possible)
	 */
	function Enter()
	{
		global $wpdb, $wpfts_core;

		$time = time();

		$q = 'select `option_value` from `'.$wpdb->options.'` where `option_name` = "'.addslashes($this->option_name).'"';
		$res = $wpfts_core->db->get_results($q, ARRAY_A);

		// Check if not expired and process_id is ours
		if (count($res) > 0) {
			$json = @json_decode($res[0]['option_value'], true);
			if (isset($json['pid']) && isset($json['expts'])) {
				if ($json['expts'] > $time) {
					// The semaphore is already taken (by us or by someone else - does not matter here)
					return false;
				}
			}
		}

		// We can take the semaphore (at least we can try)
		$data = array(
			'pid' => $this->process_id,
			'expts' => $time + intval($this->timeout),
		);
		
		$q = 'replace `'.$wpdb->options.'` (`option_name`, `option_value`, `autoload`) values ("'.$this->option_name.'", "'.addslashes(json_encode($data)).'", "no")';
		$wpfts_core->db->query($q);

		// Successful or not? We will check in the next Check() request
		return $this->Check();
	}

	/**
	 * Release the lock 
	 */
	function Leave()
	{
		global $wpfts_core, $wpdb;

		$q = 'delete from `'.$wpdb->options.'` where option_name = "'.$this->option_name.'"';
		$wpfts_core->db->query($q);
	}

	function Update()
	{
		global $wpfts_core, $wpdb;

		$time = time();

		// First, check if we control the resource
		if ($this->Check()) {
			// Yes, we can Update
			$data = array(
				'pid' => $this->process_id,
				'expts' => $time + intval($this->timeout),
			);

			$q = 'replace `'.$wpdb->options.'` (`option_name`, `option_value`, `autoload`) values ("'.$this->option_name.'", "'.addslashes(json_encode($data)).'", "no")';
			$wpfts_core->db->query($q);
		}
	}
}
