<?php

//Wrapper function for template tag use.
function wds_hero( $args = array(), $content = false ) {
	wds_hero_widget()->wds_hero( $args, $content );
}
