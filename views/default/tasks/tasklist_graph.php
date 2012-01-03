<?php

elgg_load_library('elgg:tasks');


$total = tasks_get_entities(array(
	'container_guid' => $vars['entity']->guid,
	'count' => true,
));
$closed = tasks_get_entities(array(
	'container_guid' => $vars['entity']->guid,
	'metadata_name' => 'status',
	'metadata_value' => 'closed',
	'count' => true,
));
// Closed tasks aren't contabilized in graph.
$total -= $closed;

$done = tasks_get_entities(array(
	'container_guid' => $vars['entity']->guid,
	'metadata_name' => 'status',
	'metadata_value' => 'done',
	'count' => true,
));

$remaining = $total - $done;

$assigned = tasks_get_entities(array(
	'container_guid' => $vars['entity']->guid,
	'metadata_name' => 'status',
	'metadata_value' => array('assigned', 'active'),
	'count' => true,
));

$active = tasks_get_entities(array(
	'container_guid' => $vars['entity']->guid,
	'metadata_name' => 'status',
	'metadata_value' => 'active',
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
