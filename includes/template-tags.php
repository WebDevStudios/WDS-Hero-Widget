<?php

// Exit if accessed directly
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

/**
 * Template tag wrapper for WDS_Hero_Widget::mcf_hero().
 *
 * @see WDS_Hero_Widget::mcf_hero() Argument defaults, etc.
 */
function wds_hero( $args = array(), $content = false ) {
	return wds_hero_widget()->wds_hero( $args, $content );
}
