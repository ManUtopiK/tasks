<?php
/**
 * List all tasks
 *
 * @package ElggTasks
 */

$title = elgg_echo('tasks:all');

elgg_pop_breadcrumb();
elgg_push_breadcrumb(elgg_echo('tasks'));

$lists = elgg_get_entities(array(
	'type' => 'object',
	'subtype' => 'tasklist',
	'count' => true,
));

elgg_register_title_button('tasks', 'addlist');
elgg_register_title_button('tasks', 'add');


$content = elgg_list_entities(array(
	'type' => 'object',
	'subtype' => $lists ? 'tasklist' : 'task',
	'full_view' => false,
));
if (!$content) {
	$content = '<p>' . elgg_echo('tasks:none') . '</p>';
}

$body = elgg_view_layout('content', array(
	'filter_context' => 'all',
	'content' => $content,
	'title' => $title,
	'sidebar' => elgg_view('tasks/sidebar'),
));

echo elgg_view_page($title, $body);
