<?php

function tasks_get_entities($options) {
	$default = array(
		'type' => 'object',
		'subtype' => 'task',
	);
	
	$options = array_merge($default, $options);
	
	return elgg_get_entities($options);
}

function tasks_get_actions_from_state($state){
	switch($state) {
		
		case 'new':
		case 'unassigned':
		case 'reopened':
			$actions = array(
				'assign',
				'assign_and_activate',
				'mark_as_done',
				'close',
			);
			break;
			
		case 'assigned':
			$actions = array(
				'activate',
				'leave',
				'mark_as_done',
				'close',
			);
			break;
			
		case 'active':
			$actions = array(
				'deactivate',
				'leave',
				'mark_as_done',
				'close',
			);
			break;
			
		case 'done':
		case 'closed':
			$actions = array(
				'reopen',
			);
			break;
			
	}
	
	return $actions;
}

function tasks_prepare_radio_options($state) {
	
	$actions = tasks_get_actions_from_state($state);
	
	$actions_labels = array(
		elgg_echo("tasks:state:action:noaction", array($state)) => '',
	);
	
	foreach($actions as $action) {
		$actions_labels[elgg_echo("tasks:state:action:$action")] = $action;
	}
	
	return $actions_labels;
}
				
function tasks_get_state_from_action($action){
	$actions_states = array(
		'assign' => 'assigned',
		'leave' => 'unassigned',
		'activate' => 'active',
		'deactivate' => 'assigned',
		'assign_and_activate' => 'active',
		'mark_as_done' => 'done',
		'close' => 'closed',
		'reopen' => 'reopened',
	);
	return $actions_states[$action];
}
		
