<?php
/**
 * @version                $Id: component.php $
 * @package                Joomla.Site
 * @subpackage        	   tpl_ta2ta
 * @copyright              Copyright (C) 2013 NCJFCJ. All rights reserved.
 */

// No direct access
defined( '_JEXEC' ) or die;

// get rid of the stupid generator line
$this->setGenerator('');

// determine the path to the directory of this template
$template_dir = 'templates/' . $this->template . '/';

// positions
$showContentBottom	= $this->countModules( 'content-bottom' );
$showLeftMenu		= $this->countModules( 'left-menu' );

// stop Bootstrap from loading (we include it manually in our main js file)
unset($this->_scripts[JURI::root(true).'/media/jui/js/bootstrap.min.js']);
unset($this->_scripts[JURI::root(true).'/media/jui/js/jquery.min.js']);
unset($this->_scripts[JURI::root(true).'/media/jui/js/jquery-noconflict.js']);
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
		<jdoc:include type="head" />
	</head>
	<body>
		<div id="topBar" class="navbar-fixed-top">
			<div class="container">
				<div class="row">
					<div class="col-xs-12">
						<a id="topLogo" href="/"><img src="<?php echo $template_dir; ?>img/logo-inverse.png" alt="TA2TA" /></a>
						<div id="siteSearch" class="dropdown">
							<button type="button" class="dropdown-toggle dark btn" data-toggle="dropdown">
									<span class="icomoon-search"></span><span class="sr-only">Search</span>
							</button>
						    <ul class="dropdown-menu pull-right">
							    <li>
							    	<jdoc:include type="modules" name="search" />
								</li>
						    </ul>
					 	</div>
						<nav class="navbar" role="navigation">
							<div class="navbar-header">
								<button type="button" class="dark navbar-toggle" data-toggle="collapse" data-target="#topNavCollapse">
									<span class="icomoon-list"></span><span class="sr-only">Menu</span>
								</button>
							</div>
							<div class="collapse navbar-collapse" id="topNavCollapse">
								<jdoc:include type="modules" name="small-top-nav" style="none" />
							</div>
						</nav>
					</div>
				</div>
			</div>
		</div>
		<div id="bodyBar">
			<div class="container">
				<header class="row hidden-xs">
					<div class="col-xs-12">
						<div class="row" id="headerTop">
							<div class="col-xs-12">								
								<nav id="topNav" class="navbar" role="navigation">
									<div class="navbar-inner" id="topNavInner">
										<a href="/" id="logo" class="brand">
											<img src="<?php echo $template_dir; ?>img/logo.png" alt="TA2TA">
										</a>
										<jdoc:include type="modules" name="main-nav" style="none" />
									</div>
								</nav>
							</div>
						</div>
					</div>
				</header>
				<div class="row hidden-xs">
					<div class="col-xs-12">
						<?php
							// load the banner module on homepage only, show replacement on others
							$app = JFactory::getApplication();
							$menu = $app->getMenu();
							if($menu->getActive() == $menu->getDefault()):
						?>
						<jdoc:include type="modules" name="banner" />
						<?php else: ?>
						<img src="<?php echo $template_dir; ?>/img/top-banner.jpg" alt="" />
						<?php endif; ?>
					</div>
				</div>
				<div id="content">
					<div class="row">
						<?php if ( $showLeftMenu ) : ?>
						<div class="col-sm-2">
							<jdoc:include type="modules" name="left-menu" />
						</div>
						<?php endif; ?>
						<div class="col-sm-<?php echo ( $showLeftMenu ? '10' : '12'); ?>">
							<jdoc:include type="message" />
							<jdoc:include type="component" />
						</div>
					</div>
					<?php if ( $showContentBottom ) : ?>
					<div class="row">
						<div class="col-xs-12">
							<jdoc:include type="modules" name="content-bottom" />
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div id="bottomBar">
			<div class="container">
				<footer class="row">
					<div class="col-sm-9">
						<div class="row">
							<div class="col-xs-12">
								<div id="copyright" class="pull-left"><jdoc:include type="modules" name="copyright" /></div>
								<nav id="legalMenu" class="pull-left"><jdoc:include type="modules" name="legal-menu" /></nav>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<div id="disclaimer"><jdoc:include type="modules" name="disclaimer" /></div>
							</div>
						</div>
					</div>
					<div class="col-xs-6 col-xs-offset-3 col-sm-3 col-sm-offset-0">
						<a href="http://ncjfcj.org" target="_blank"><img src="<?php echo $template_dir; ?>img/ncjfcj-logo.png" alt="NCJFCJ"></a>
					</div>
				</footer>
			</div>
		</div>
	</body>
</html>