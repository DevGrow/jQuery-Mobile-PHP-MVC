<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<title><?php $template->page_title(); ?></title>

<base href='<?php echo BASE_URL; ?>' /> 

<!-- Stylesheets + Favicon: -->
<link rel="stylesheet" href="./static/css/main.css" type="text/css" media="screen" />
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0a2/jquery.mobile-1.0a2.min.css" />
<link rel="shortcut icon" href="./static/images/favicon.ico" /> 
<!-- /Stylesheets + Favicon: -->

<!-- JavaScript: -->
<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/mobile/1.0a2/jquery.mobile-1.0a2.min.js"></script>
<script type="text/javascript" src="./static/js/functions.js"></script>
<!-- /JavaScript -->

<!-- SEO: -->
<meta name="description" content="<?php echo APP_DESCRIPTION; ?>" /> 
<meta name="keywords" content="<?php echo APP_KEYWORDS; ?>" /> 
<link rel="canonical" href="<?php echo BASE_URL; ?>" /> 
<!-- /SEO -->

</head>

<body>
<div data-role="page" data-theme="c">
	<div data-role="header" data-theme="a">
		<h1><?php echo APP_NAME; ?></h1>
	</div>
	<div data-role="content" role="main">
		<?php $template->get_msg(); ?>