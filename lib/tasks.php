<?php

function tasks_get_entities($options) {
	$default = array(
		'type' => 'object',
		'subtype' => 'task',
	);
	
	$options = array_merge($default, $options);
	
	return elgg_get_entities($options);
}
