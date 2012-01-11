<?php
/**
 * Edit a task
 *
 * @package ElggTasks
 */

gatekeeper();

$task_guid = (int)get_input('guid');
$task = get_entity($task_guid);
if (!$task) {
	
}

$container = $task->getContainerEntity();
if (!$container) {
	
}

if(!elgg_instanceof($container, 'user') && !elgg_instanceof($container, 'group')) {
	$list = $container;
	$container = $list->getContainerEntity();
}

elgg_set_page_owner_guid($container->getGUID());

if (elgg_instanceof($container, 'user')) {
	elgg_push_breadcrumb($container->name, "tasks/owner/$container->guid/");
} elseif (elgg_instanceof($container, 'group')) {
	elgg_push_breadcrumb($container->name, "tasks/group/$container->guid/all");
}
if($list) {
	elgg_push_breadcrumb($list->title, "tasks/view/$list->guid/$list->title");
}
elgg_push_breadcrumb($task->title, $task->getURL());
elgg_push_breadcrumb(elgg_echo('edit'));

$title = elgg_echo("tasks:edit");

if ($task->canEdit()) {
	$vars = array(
		'guid' => $task_guid,
		'container_guid' => $container_guid,
	);
	
	foreach(array_keys(elgg_get_config('tasks')) as $variable){
		$vars[$variable] = $task->$variable;
	}
	
	$content = elgg_view_form('tasks/edit', array(), $vars);
} else {
	$content = elgg_echo("tasks:noaccess");
}

$body = elgg_view_layout('content', array(
	'filter' => '',
	'content' => $content,
	'title' => $title,
));

echo elgg_view_page($title, $body);
