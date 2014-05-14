<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Dance Networks EU
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php wp_head(); ?>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1ssxs7SdqghQui-UadBDVF3bHCarfsng&v=3.exp&sensor=false"></script>

</head>

<body <?php body_class(); ?> onload="initialize()">
<div id="page" class="hfeed site">
	<?php do_action( 'before' ); ?>
