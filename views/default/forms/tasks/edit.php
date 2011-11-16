<?php
/**
 * Task edit form body
 *
 * @package ElggTasks
 */

$variables = elgg_get_config('tasks');
foreach ($variables as $name => $type) {
?>
<div>
	<label><?php echo elgg_echo("tasks:$name") ?></label>
	<?php
		if ($type != 'longtext') {
			echo '<br />';
		}
	?>
	<?php echo elgg_view("input/$type", array(
			'name' => $name,
			'value' => $vars[$name],
		));
	?>
</div>
<?php
}

$cats = elgg_view('categories', $vars);
if (!empty($cats)) {
	echo $cats;
}


echo '<div class="elgg-foot">';
if ($vars['guid']) {
	echo elgg_view('input/hidden', array(
		'name' => 'task_guid',
		'value' => $vars['guid'],
	));
}
echo elgg_view('input/hidden', array(
	'name' => 'container_guid',
	'value' => $vars['container_guid'],
));
if ($vars['list_guid']) {
	echo elgg_view('input/hidden', array(
		'name' => 'list_guid',
		'value' => $vars['list_guid'],
	));
}

echo elgg_view('input/submit', array('value' => elgg_echo('save')));

echo '</div>';
