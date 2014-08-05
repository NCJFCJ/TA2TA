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
 
// No direct access.
defined('_JEXEC') or die;

// determine the path to the directory of this template
$template_dir = 'templates/' . $this->template . '/';
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
		<meta name="viewport" content="initial-scale=1.0, width=device-width">
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<link rel="stylesheet" type="text/css" href="<?php echo $template_dir; ?>styles/css/template.css" />
		<link rel="icon" sizes="16x16" type="image/png" href="<?php echo $template_dir; ?>img/icons/16x16icon.png">
		<link rel="icon" sizes="24x24" type="image/png" href="<?php echo $template_dir; ?>img/icons/24x24icon.png">
		<link rel="icon" sizes="32x32" type="image/png" href="<?php echo $template_dir; ?>img/icons/32x32icon.png">
		<link rel="apple-touch-icon" sizes="57x57" type="image/png" href="<?php echo $template_dir; ?>img/icons/57x57icon.png">
		<link rel="apple-touch-icon" sizes="72x72" type="image/png" href="<?php echo $template_dir; ?>img/icons/72x72icon.png">
		<link rel="icon" sizes="96x96" type="image/png" href="<?php echo $template_dir; ?>img/icons/96x96icon.png">
		<link rel="apple-touch-icon" sizes="114x114" type="image/png" href="<?php echo $template_dir; ?>img/icons/114x114icon.png">
		<link rel="icon" sizes="128x128" type="image/png" href="<?php echo $template_dir; ?>img/icons/128x128icon.png">
		<link rel="icon" sizes="195x195" type="image/png" href="<?php echo $template_dir; ?>img/icons/195x195icon.png">
		<script type="text/javascript" src="<?php echo $template_dir; ?>js/min/main.min.js"></script>
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