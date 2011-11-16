<?php
/**
 * Create a new task list
 *
 * @package ElggTasks
 */

gatekeeper();

$container_guid = (int) get_input('guid');
$container = get_entity($container_guid);
if (!$container) {
	$container = elgg_get_logged_in_user_entity();
}

$parent_guid = 0;
$page_owner = $container;
if (elgg_instanceof($container, 'object')) {
	$parent_guid = $container->getGUID();
	$page_owner = $container->getContainerEntity();
}

elgg_set_page_owner_guid($page_owner->getGUID());

$title = elgg_echo('tasks:lists:add');
elgg_push_breadcrumb($title);

$vars = array();
$content = elgg_view_form('tasklists/edit', array(), $vars);

$body = elgg_view_layout('content', array(
	'filter' => '',
	'content' => $content,
	'title' => $title,
));

echo elgg_view_page($title, $body);
