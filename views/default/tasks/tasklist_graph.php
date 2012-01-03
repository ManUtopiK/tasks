<?php

elgg_load_library('elgg:tasks');


$ee = elgg_get_entities(array(
	'list_guid' => $vars['entity']->guid,
	'type' => 'object',
		'subtype' => 'task',
	));
foreach ($ee as $e) {
	//delete_entity($e->guid);
	$aa[] = $e->status;
}

global $fb; $fb->info($aa);
	
$total = tasks_get_entities(array(
	'list_guid' => $vars['entity']->guid,
	'count' => true,
));

$closed = tasks_get_entities(array(
	'list_guid' => $vars['entity']->guid,
	'status' => 'closed',
	'count' => true,
));
$fb->info($closed);
// Closed tasks aren't contabilized in graph.
$total -= $closed;

$done = tasks_get_entities(array(
	'list_guid' => $vars['entity']->guid,
	'status' => 'done',
	'count' => true,
));

$remaining = $total - $done;

$assigned = tasks_get_entities(array(
	'list_guid' => $vars['entity']->guid,
	'status' => array('assigned', 'active'),
	'count' => true,
));

$active = tasks_get_entities(array(
	'list_guid' => $vars['entity']->guid,
	'status' => 'active',
	'count' => true,
));

if ($total == 0) {
	$percent = 0;
} else {
	$percent = $done / $total * 100;
}

?>

<div class="tasklist-graph">
	<div style="width:<?php echo $percent; ?>%">&nbsp;</div>
</div>

<?php

echo '<a href="">' . elgg_echo('tasks:lists:graph:total', array($total)) . '</a>, ';
echo '<a href="">' . elgg_echo('tasks:lists:graph:remaining', array($remaining)) . '</a>, ';
echo '<a href="">' . elgg_echo('tasks:lists:graph:assigned', array($assigned)) . '</a>, ';
echo '<a href="">' . elgg_echo('tasks:lists:graph:active', array($active)) . '</a>';
