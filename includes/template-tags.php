<?php

// Wrapper function for template tag use.
function wds_mcf_hero( $args = array(), $content = false ) {
	wds_hero_widget()->wds_mcf_hero( $args, $content );
}