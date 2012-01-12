<?php
/**
 * Create or edit a task
 *
 * @package ElggTasks
 */

$variables = elgg_get_config('tasks');
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
$task_guid = (int)get_input('task_guid');
$list_guid = (int)get_input('list_guid');

elgg_make_sticky_form('task');

if (!$input['title']) {
	register_error(elgg_echo('tasks:error:no_title'));
	forward(REFERER);
}

if ($task_guid) {
	$task = get_entity($task_guid);
	if (!$task || !$task->canEdit()) {
		register_error(elgg_echo('tasks:error:no_save'));
		forward(REFERER);
	}
	$new_task = false;
} else {
	$task = new ElggObject();
	$task->subtype = 'task';
	$task->status = 'new';
	$task->time_status_changed = time();
	$new_task = true;
}

if (sizeof($input) > 0) {
	foreach ($input as $name => $value) {
		$task->$name = $value;
	}
}

if (!$list_guid) {
	$user = elgg_get_logged_in_user_entity();
	$list_guid = $user->tasklist_guid;
	if (!get_entity($list_guid)) {
		$list = new ElggObject();
		$list->subtype = 'tasklist';
		$list->title = elgg_echo('tasks:owner', array($user->name));
		$list->container_guid = $user->getGUID();
		$list->access_id = ACCESS_PRIVATE;
		if(!$list->save()) {
			register_error(elgg_echo('tasks:error:no_save'));
			forward(REFERER);
		}
		$list_guid = $list->guid;
		$user->tasklist_guid = $list_guid;
	}
}
$task->container_guid = $list_guid;

if ($task->save()) {

	elgg_clear_sticky_form('task');

	// Now save description as an annotation
	$task->annotate('task', $task->description, $task->access_id);

	system_message(elgg_echo('tasks:saved'));

	if ($new_task) {
		add_to_river('river/object/task/create', 'create', elgg_get_logged_in_user_guid(), $task->guid);
	}

	forward($task->getURL());
} else {
	register_error(elgg_echo('tasks:error:no_save'));
	forward(REFERER);
}
