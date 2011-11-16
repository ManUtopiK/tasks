<?php

$vars['options_values'] = array(
	'1' => 'low',
	'2' => 'normal',
	'3' => 'high',
);

if(!isset($vars['value'])){
	$vars['value'] = 2;
}

echo elgg_view('input/dropdown', $vars);
