<?php
/**
 * Create or edit a task
 *
 * @package ElggTasks
 */

$variables = elgg_get_config('tasklists');
$input = array();
foreach ($variables as $name => $type) {
	$input[$name] = get_input($name);
	if ($name == 'title') {
		$input[$name] = strip_tags($input[$name]);
	}
	if ($type == 'tags') {
		$input[$name] = string_to_tag_array($input[$name]);
	}
}

// Get guids
$tasklist_guid = (int)get_input('tasklist_guid');
$container_guid = (int)get_input('container_guid');
$parent_guid = (int)get_input('list_guid');

elgg_make_sticky_form('tasklist');

if (!$input['title']) {
	register_error(elgg_echo('tasks:lists:error:no_title'));
	forward(REFERER);
}

if ($tasklist_guid) {
	$tasklist = get_entity($tasklist_guid);
	if (!$tasklist || !$tasklist->canEdit()) {
		register_error(elgg_echo('tasks:lists:error:no_save'));
		forward(REFERER);
	}
	$new_tasklist = false;
} else {
	$tasklist = new ElggObject();
	$tasklist->subtype = 'tasklist';
	$new_tasklist = true;
}

if (sizeof($input) > 0) {
	foreach ($input as $name => $value) {
		$tasklist->$name = $value;
	}
}

// need to add check to make sure user can write to container
$tasklist->container_guid = $container_guid;

if ($list_guid) {
	$tasklist->list_guid = $list_guid;
}

if ($tasklist->save()) {

	elgg_clear_sticky_form('tasklist');

	system_message(elgg_echo('tasks:lists:saved'));

	if ($new_tasklist) {
		add_to_river('river/object/tasklist/create', 'create', elgg_get_logged_in_user_guid(), $tasklist->guid);
	}

	forward($tasklist->getURL());
} else {
	register_error(elgg_echo('tasks:lists:error:no_save'));
	forward(REFERER);
}
