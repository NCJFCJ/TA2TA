<?php
/**
 * @version                $Id: component.php $
 * @package                Joomla.Site
 * @subpackage        	   tpl_ta2ta
 * @copyright              Copyright (C) 2013 NCJFCJ. All rights reserved.
 */

defined('_JEXEC') or die;
?>
<form action="<?php echo JRoute::_('index.php');?>" method="post" role="search">
	<div class="input-group">
		<input type="search" name="searchword" id="mod-search-searchword" maxlength="<?php echo $maxlength; ?>" class="search-query form-control" placeholder="Search...">
		<span class="input-group-addon dark icomoon-search" onclick="jQuery(this).closest('form').submit();"></span>
	</div>
	<input type="hidden" name="task" value="search" />
    <input type="hidden" name="option" value="com_search" />
    <input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>" />
</form>