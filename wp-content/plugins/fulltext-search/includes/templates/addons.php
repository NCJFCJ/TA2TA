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

require_once dirname(__FILE__).'/../wpfts_output.php';

$out = new WPFTS_Output();

?>
			<h4><?php echo __('Addons', 'fulltext-search'); ?></h4>
			<form method="post" id="wpftsi_form8">
			
				<div class="row">
					<div class="col-12">

						<div class="bd-callout bd-callout-info bg-white">
							<?php echo __('Addons are special small plugins that can significantly extend the functionality of WPFTS and increase compatibility with your theme and other plugins.', 'fulltext-search'); ?>
						</div>
					</div>
				</div>

				<div style="background: #f8f9fa;">
				<ul class="nav nav-tabs mb-3 nav-tabs-inv" id="pills-tab-addons" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="pills-addons-alllist-tab" data-toggle="pill" href="#pills-addons-alllist" role="tab" aria-controls="pills-addons-alllist" aria-selected="true"><?php echo __('All Addons', 'fulltext-search'); ?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="pills-addons-recommended-tab" data-toggle="pill" href="#pills-addons-recommended" role="tab" aria-controls="pills-addons-recommended" aria-selected="false"><?php echo __('Recommended', 'fulltext-search'); ?></a>
					</li>
				</ul>
				<div class="tab-content" id="pills-tab-addonsContent">
  					<div class="tab-pane show active p-3" id="pills-addons-alllist" role="tabpanel" aria-labelledby="pills-addons-alllist-tab">
						<?php /*echo $out->index_engine_tester(null);*/ ?>
						<div id="wpfts_addons_alllist_place"></div>
					</div>
					<div class="tab-pane p-3" id="pills-addons-recommended" role="tabpanel" aria-labelledby="pills-addons-recommended-tab">
						<?php /*echo $out->search_tester(null);*/ ?>
					</div>
				</div>
				</div>
			</form>
<?php
