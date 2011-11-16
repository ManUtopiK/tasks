<?php
/**
 * View for task object
 *
 * @package ElggTasks
 *
 * @uses $vars['entity']    The task list object
 * @uses $vars['full_view'] Whether to display the full view
 */


$full = elgg_extract('full_view', $vars, FALSE);
$tasklist = elgg_extract('entity', $vars, FALSE);

if (!$tasklist) {
	return TRUE;
}

$icon = elgg_view('icon/default', array('entity' => $tasklist, 'size' => 'small'));

$owner = get_entity($tasklist->owner_guid);
$owner_link = elgg_view('output/url', array(
	'href' => "tasks/owner/$owner->username",
	'text' => $owner->name,
));

$date = elgg_view_friendly_time($tasklist->time_created);
$strapline = elgg_echo("tasks:lists:strapline", array($date, $owner_link));
$tags = elgg_view('output/tags', array('tags' => $tasklist->tags));

$comments_count = $tasklist->countComments();
//only display if there are commments
if ($comments_count != 0) {
	$text = elgg_echo("comments") . " ($comments_count)";
	$comments_link = elgg_view('output/url', array(
		'href' => $tasklist->getURL() . '#tasklist-comments',
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
	$body = elgg_view('output/longtext', array('value' => $tasklist->description));

	$params = array(
		'entity' => $tasklist,
		'title' => false,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => $tags,
	);
	$params = $params + $vars;
	$list_body = elgg_view('object/elements/summary', $params);
	
	$list_body .= elgg_view('tasks/tasklist_graph', array(
		'entity' => $tasklist,
	));

	$info = elgg_view_image_block($icon, $list_body);

	echo <<<HTML
$info
$body
HTML;

} else {
	// brief view

	$content = elgg_view('tasks/tasklist_graph', array(
		'entity' => $tasklist,
	));

	$params = array(
		'entity' => $tasklist,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => false,
		'content' => $content,
	);
	$params = $params + $vars;
	$list_body = elgg_view('object/elements/summary', $params);

	echo elgg_view_image_block($icon, $list_body);
}
