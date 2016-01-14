<html lang="en"><head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="A front-end template that helps you build fast, modern mobile web apps.">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>bPlanner</title>

	<!-- Add to homescreen for Chrome on Android -->
	<meta name="mobile-web-app-capable" content="yes">
	<!--
	<link rel="icon" sizes="192x192" href="images/android-desktop.png">
	<meta name="apple-mobile-web-app-title" content="Material Design Lite">
	<link rel="apple-touch-icon-precomposed" href="images/ios-desktop.png">
	<meta name="msapplication-TileImage" content="images/touch/ms-touch-icon-144x144-precomposed.png">
	<link rel="shortcut icon" href="images/favicon.png">
	-->

	<!-- Add to homescreen for Safari on iOS -->
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	

	<!-- Tile icon for Win8 (144x144 + tile color) -->

	<meta name="msapplication-TileColor" content="#3372DF">

	<link rel="stylesheet" href="assets/material.min.css">
	<link rel="stylesheet" href="assets/style.css">
  </head>
  <body>
	<div class="demo-layout mdl-layout mdl-js-layout mdl-layout--fixed-header mdl-color--grey-100" style="width:auto;height:auto;">
	  <header class="demo-header mdl-layout__header mdl-color--grey-100 mdl-color-text--grey-800">
		<div class="mdl-layout__header-row">
		  <span class="mdl-layout-title">bPlanner</span>
		  <div class="mdl-layout-spacer"></div>
		</div>
	  </header>
	  <div class="demo-ribbon"></div>
	  <main class="demo-main mdl-layout__content">

		<div class="demo-container mdl-grid">
		  <div class="mdl-cell mdl-cell--2-col mdl-cell--hide-tablet mdl-cell--hide-phone"></div>
		  <div class="mdl-cell mdl-cell--8-col">
<?php if(!empty($pagemsg)){ ?>
		  	<div class="mdl-card-style-alert mdl-card mdl-shadow--2dp<?php if(!empty($pagemsg_type)) echo ' ',$pagemsg_type; ?>">
				<div class="mdl-card__supporting-text"><?php echo $pagemsg; ?></div>
			</div>
<?php } ?>