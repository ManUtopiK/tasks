<?php
/**
 * Create a new task
 *
 * @package ElggTasks
 */

gatekeeper();

$container_guid = (int) get_input('guid');
$container = get_entity($container_guid);
if (!$container) {

}

$parent_guid = 0;
$page_owner = $container;
if (elgg_instanceof($container, 'object', 'tasklist')) {
	$parent_guid = $container->getGUID();
	$page_owner = $container->getContainerEntity();
}

elgg_set_page_owner_guid($page_owner->getGUID());

if (elgg_instanceof($container, 'user')) {
	elgg_push_breadcrumb($container->name, $container->getURL());
} else {
	elgg_push_breadcrumb($container->title, $container->getURL());
}
$title = elgg_echo('tasks:add');
elgg_push_breadcrumb($title);

$vars = task_prepare_form_vars();
$content = elgg_view_form('tasks/edit', array(), $vars);

$body = elgg_view_layout('content', array(
	'filter' => '',
	'content' => $content,
	'title' => $title,
));

echo elgg_view_page($title, $body);
