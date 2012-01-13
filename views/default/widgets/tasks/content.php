<?php
/**
 * Elgg tasks widget
 *
 * @package ElggTasks
 */

$num = (int) $vars['entity']->tasks_num;

// We show active first
$options = array(
	'type' => 'object',
	'subtype' => 'task',
	'owner_guid' => $vars['entity']->owner_guid,
	'metadata_name' => 'status',
	'metadata_value' => 'active',
	'limit' => $num,
	'full_view' => FALSE,
	'pagination' => FALSE,
);
$content = elgg_list_entities_from_metadata($options);

// And then the remaining assinged
$num -= elgg_get_entities_from_metadata(array_merge($options, array('count' => true)));
if ($num > 0) {
	$content .= elgg_list_entities_from_metadata(array_merge($options, array(
		'metadata_value' => 'assigned',
		'limit' => $num,
	)));
}

echo $content;

if ($content) {
	$url = "tasks/owner/" . elgg_get_page_owner_entity()->username;
	$more_link = elgg_view('output/url', array(
		'href' => $url,
		'text' => elgg_echo('tasks:more'),
		'is_trusted' => true,
	));
	echo "<span class=\"elgg-widget-more\">$more_link</span>";
} else {
	echo elgg_echo('tasks:none');
}
