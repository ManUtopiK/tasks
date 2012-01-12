<?php
/**
 * List a user's friends' tasks
 *
 * @package ElggTasks
 */

$owner = elgg_get_page_owner_entity();
if (!elgg_instanceof($owner, 'user')) {
	forward('tasks/all');
}

elgg_register_title_button('tasks', 'addlist');
elgg_register_title_button('tasks', 'add');

elgg_push_breadcrumb($owner->name, "tasks/owner/$owner->username");
elgg_push_breadcrumb(elgg_echo('friends'));

$title = elgg_echo('tasks:friends');

$content = list_user_friends_objects($owner->guid, 'tasklist', 10, false);
if (!$content) {
	$content = elgg_echo('tasks:none');
}

$params = array(
	'filter_context' => 'friends',
	'content' => $content,
	'title' => $title,
);

$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);
