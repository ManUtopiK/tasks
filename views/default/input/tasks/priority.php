<?php

$vars['options'] = array('low', 'normal', 'high');

if(!isset($vars['value'])){
	$vars['value'] = 'normal';
}

echo elgg_view('input/dropdown', $vars);
