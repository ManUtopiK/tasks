<?php
/**
 * List a user's or group's tasks
 *
 * @package ElggTasks
 */

$owner = elgg_get_page_owner_entity();
if (!$owner) {
	forward('tasks/all');
}

// access check for closed groups
group_gatekeeper();

$num_lists = elgg_get_entities(array(
	'type' => 'object',
	'subtype' => 'tasklist',
	'container_guid' => $owner->guid,
	'count' => true,
));

if ($num_lists == 1 && $list = get_entity($owner->tasklist_guid)) {
	forward($list->getURL());
}

$title = elgg_echo('tasks:lists:owner', array($owner->name));

if (elgg_instanceof($owner, 'user')) {
	elgg_push_breadcrumb($owner->name);
} else {
	elgg_push_breadcrumb($owner->title);
}

elgg_register_title_button('tasks', 'addlist');
elgg_register_title_button('tasks', 'add');

$content = elgg_list_entities(array(
	'type' => 'object',
	'subtypes' => 'tasklist',
	'container_guid' => elgg_get_page_owner_guid(),
	'full_view' => false,
));

if (!$content) {
	$content = '<p>' . elgg_echo('tasks:none') . '</p>';
}

$filter_context = '';
if (elgg_get_page_owner_guid() == elgg_get_logged_in_user_guid()) {
	$filter_context = 'mine';
}

$sidebar = elgg_view('tasks/sidebar/navigation');
$sidebar .= elgg_view('tasks/sidebar');

$params = array(
	'filter_context' => $filter_context,
	'content' => $content,
	'title' => $title,
	'sidebar' => $sidebar,
);

if (elgg_instanceof($owner, 'group')) {
	$params['filter'] = '';
}

$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);
