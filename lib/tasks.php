<?php

/**
 * Prepare the add/edit form variables
 *
 * @param ElggObject $tasklist
 * @return array
 */
function tasklist_prepare_form_vars($tasklist = null, $parent_guid = 0) {

	// input names => defaults
	$values = array(
		'title' => '',
		'description' => '',
		'startdate' => '',
		'enddate' => '',
		'access_id' => ACCESS_DEFAULT,
		'write_access_id' => ACCESS_DEFAULT,
		'tags' => '',
		'container_guid' => elgg_get_page_owner_guid(),
		'guid' => null,
		'entity' => $tasklist,
		'parent_guid' => $parent_guid,
	);

	if ($tasklist) {
		foreach (array_keys($values) as $field) {
			if (isset($tasklist->$field)) {
				$values[$field] = $tasklist->$field;
			}
		}
	}

	if (elgg_is_sticky_form('tasklist')) {
		$sticky_values = elgg_get_sticky_values('tasklist');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}

	elgg_clear_sticky_form('tasklist');

	return $values;
}

/**
 * Prepare the add/edit form variables
 *
 * @param ElggObject $task
 * @return array
 */
function task_prepare_form_vars($task = null, $list_guid = 0) {

	// input names => defaults
	$values = array(
		'title' => '',
		'description' => '',
		'priority' => '',
		'elapsed_time' => '',
		'remaining_time' => '',
		'access_id' => ACCESS_DEFAULT,
		'write_access_id' => ACCESS_DEFAULT,
		'tags' => '',
		'container_guid' => elgg_get_page_owner_guid(),
		'guid' => null,
		'entity' => $task,
		'list_guid' => $list_guid,
	);

	if ($task) {
		foreach (array_keys($values) as $field) {
			if (isset($task->$field)) {
				$values[$field] = $task->$field;
			}
		}
	}

	if (elgg_is_sticky_form('task')) {
		$sticky_values = elgg_get_sticky_values('task');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}

	elgg_clear_sticky_form('task');

	return $values;
}


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
		
