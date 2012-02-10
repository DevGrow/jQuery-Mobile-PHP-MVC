<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<title><?php $template->page_title(); ?></title>

<!-- Stylesheets + Favicon: -->
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0.1/jquery.mobile-1.0.1.min.css" />
<link rel="stylesheet" href="/static/css/main.css" type="text/css" media="screen" />
<link rel="shortcut icon" href="/static/images/favicon.ico" /> 
<!-- /Stylesheets + Favicon: -->

<!-- JavaScript: -->
<script src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
<script src="http://code.jquery.com/mobile/1.0.1/jquery.mobile-1.0.1.min.js"></script>
<!-- /JavaScript -->

<!-- SEO: -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?php echo APP_DESCRIPTION; ?>" /> 
<meta name="keywords" content="<?php echo APP_KEYWORDS; ?>" /> 
<!-- /SEO -->

</head>

<body>
<div data-role="page" data-theme="c" data-add-back-btn="true"">
	<div data-role="header" data-theme="a">
		<h1><?php echo APP_NAME; ?></h1>
	</div>
	<div data-role="content" role="main">
		<?php $template->get_msg(); ?>