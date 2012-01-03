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
	
	
	$assigned_tasks = elgg_list_entities_from_metadata(array(
		'container_guid' => $tasklist->guid,
		'metadata_name' => 'status',
		'metadata_values' => array('assigned', 'active'),
		'full_view' => false,
	));
	if($assigned_tasks) {
		$assigned_tasks = elgg_view_module('info', elgg_echo('tasks:assigned'), $assigned_tasks);
	}
	
	$unassigned_tasks = elgg_list_entities_from_metadata(array(
		'container_guid' => $tasklist->guid,
		'metadata_name' => 'status',
		'metadata_values' => array('new', 'unassigned', 'reopened'),
		'full_view' => false,
	));
	if($unassigned_tasks) {
		$unassigned_tasks = elgg_view_module('info', elgg_echo('tasks:unassigned'), $unassigned_tasks);
	}
	
	$closed_tasks = elgg_list_entities_from_metadata(array(
		'container_guid' => $tasklist->guid,
		'metadata_name' => 'status',
		'metadata_values' => array('done', 'closed'),
		'full_view' => false,
	));
	if($closed_tasks) {
		$closed_tasks = elgg_view_module('info', elgg_echo('tasks:closed'),	$closed_tasks);
	}
		

	echo <<<HTML
$info
$body
<div class="mtl">
$assigned_tasks
$unassigned_tasks
$closed_tasks
</div>
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
