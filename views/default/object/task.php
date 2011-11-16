<?php
/**
 * View for task object
 *
 * @package ElggTasks
 *
 * @uses $vars['entity']    The task object
 * @uses $vars['full_view'] Whether to display the full view
 */


$full = elgg_extract('full_view', $vars, FALSE);
$task = elgg_extract('entity', $vars, FALSE);

if (!$task) {
	return TRUE;
}

$icon = elgg_view('icon/default', array('entity' => $task, 'size' => 'tiny'));

$status = $task->status;

if(!in_array($status, array('new', 'assigned', 'unassigned', 'active', 'done', 'closed', 'reopened'))){
	$status = 'new';
}

$owner = get_entity($task->owner_guid);
$owner_link = elgg_view('output/url', array(
	'href' => "tasks/owner/$owner->username",
	'text' => $owner->name,
));

$date = elgg_view_friendly_time($task->time_status_changed);
$strapline = elgg_echo("tasks:strapline:$status", array($date, $owner_link));
$tags = elgg_view('output/tags', array('tags' => $task->tags));

$comments_count = $task->countComments();
//only display if there are commments
if ($comments_count != 0) {
	$text = elgg_echo("comments") . " ($comments_count)";
	$comments_link = elgg_view('output/url', array(
		'href' => $task->getURL() . '#task-comments',
		'text' => $text,
	));
} else {
	$comments_link = '';
}

$metadata = elgg_view_menu('entity', array(
	'entity' => $vars['entity'],
	'handler' => 'tasks',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

$subtitle = "$strapline $categories $comments_link";

// do not show the metadata and controls in widget view
if (elgg_in_context('widgets')) {
	$metadata = '';
}

if ($full) {
	$body = elgg_view('output/longtext', array('value' => $task->description));

	$params = array(
		'entity' => $page,
		'title' => false,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => $tags,
	);
	$params = $params + $vars;
	$list_body = elgg_view('object/elements/summary', $params);

	$info = elgg_view_image_block($icon, $list_body);

	echo <<<HTML
$info
$body
HTML;

} else {
	// brief view

	$excerpt = elgg_get_excerpt($task->description);

	$params = array(
		'entity' => $task,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => false,
	);
	$params = $params + $vars;
	$list_body = elgg_view('object/elements/summary', $params);

	echo elgg_view_image_block($icon, $list_body);
}
