<?php
/**
 * @version                $Id: component.php $
 * @package                Joomla.Site
 * @subpackage        	   tpl_ta2ta
 * @copyright              Copyright (C) 2013 NCJFCJ. All rights reserved.
 */


defined('_JEXEC') or die;

// get rid of the stupid generator line
$this->setGenerator('');

// get the error code
$errCode = $this->error->getCode();

// template directory
$template_dir = '/templates/' . $this->template . '/';
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<meta name="viewport" content="initial-scale=1.0, width=device-width">
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<jdoc:include type="head" />
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
	<body>
		<div id="topBar" class="navbar-fixed-top">
			<div class="container">
				<div class="row">
					<a id="topLogo" href="/"><img src="<?php echo $template_dir; ?>img/logo-inverse.png" alt="TA2TA" /></a>
				</div>
			</div>
		</div>
		<div id="bodyBar">
			<div class="container">
				<header class="row hidden-xs">
					<div class="col-xs-12">
						<div class="row" id="headerTop">
							<div class="col-xs-12">								
								<nav id="topNav" class="navbar">
									<div class="navbar-inner" id="topNavInner">
										<a href="/" id="logo" class="brand">
											<img src="<?php echo $template_dir; ?>img/logo.png" alt="TA 2 TA">
										</a>
									</div>
								</nav>
							</div>
						</div>
					</div>
				</header>
				<div id="content">
					<div class="row">
						<div class="col-xs-12" style="text-align: center">
							<h2>
								<?php
								switch($errCode){
									case 400:
										echo 'Bad Request (' . $errCode . ')';
										break;
									case 401:
										echo 'Unauthorized (' . $errCode . ')';
										break;
									case 403:
										echo 'Forbidden (' . $errCode . ')';
										break;
									case 404:
										echo 'Page Not Found (' . $errCode . ')';
										break;
									case 500:
										echo 'Internal Server Error (' . $errCode . ')';
										break;
									case 503:
										echo 'Service Unavailable (' . $errCode . ')';
										break;
									case 550:
										echo 'Permission Denied (' . $errCode . ')';
										break;
									default:
										echo 'Unknown Error (' . $errCode . ')';
										break;
								}
								?>
							</h2>
							<p>The page you are looking for may have moved, the address may have been mistyped, or our server may be experiencing temporary issues.  If you continue to receive this error, please <a href="contact.html">contact us</a>. If you require immediete assistance, please email us at <a href="mailto:ta2ta@ncjfcj.org">ta2ta@ncjfcj.org</a>.</p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="bottomBar">
			<div class="container">
				<footer>
					<a href="http://ncjfcj.org" target="_blank"><img src="<?php echo $template_dir; ?>img/ncjfcj-logo.png" alt="NCJFCJ" id="ncjfcjLogo"></a>
					<div id="copyright"><p>Copyright 2012-2013 NCJFCJ. All rights reserved.</p></div>
					<div id="disclaimer"><p>This project was supported by Grant No. 2011-TA-AX-K040 awarded by the Office on Violence Against Women, U.S. Department of Justice. The opinions, findings, conclusions, and recommendations expressed in this website/publication/program/exhibition are those of the author(s) and do not necessarily reflect the views of the Office on Violence Against Women, U.S. Department of Justice or the National Council of Juvenile and Family Court Judges.</p></div>
					<div class="clr"></div>
				</footer>
			</div>
		</div>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			
			ga('create', 'UA-41570936-1', 'ta2ta.org');
			ga('send', 'pageview');		
		</script>
	</body>
</html>