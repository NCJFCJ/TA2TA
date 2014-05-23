<?php
/**
 * @version                $Id: component.php $
 * @package                Joomla.Site
 * @subpackage        	   tpl_ta2ta
 * @copyright              Copyright (C) 2013 NCJFCJ. All rights reserved.
 * 
 * This file is used by the Joomla! system to render the site without any headers or footers, 
 * such as when the user tries to print something or 
 */

 // National Geographic "brain games"
 
// No direct access.
defined('_JEXEC') or die;
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<jdoc:include type="head" />
		<link type="text/css" rel="stylesheet" href="/templates/ta2ta/css/main.css" media="all">
		<link rel="shortcut icon" href="/templates/ta2ta/favicon.ico" type="image/x-icon">
		<link rel="icon" href="/templates/ta2ta/favicon.ico" type="image/x-icon">
	</head>
	<body class="contentpane">
		<div id="bodyBar">
			<div class="container">
				<jdoc:include type="message" />
				<jdoc:include type="component" />
			</div>
		</div>
	</body>
</html>