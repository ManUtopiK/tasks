<?php
/**
 * Tasks comments add form
 *
 * @package ElggTasks
 *
 * @uses ElggEntity $vars['entity'] The entity to comment on
 * @uses bool       $vars['inline'] Show a single line version of the form?
 */

elgg_load_library('elgg:tasks');

if (isset($vars['entity']) && elgg_is_logged_in()) {
	
	$inline = elgg_extract('inline', $vars, false);
	
	if ($inline) {
		echo elgg_view('input/text', array('name' => 'generic_comment'));
		echo elgg_view('input/submit', array('value' => elgg_echo('comment')));
	} else {
?>
	<div>
		<label><?php echo elgg_echo("generic_comments:add"); ?></label>
		<?php echo elgg_view('input/longtext', array('name' => 'generic_comment')); ?>
		<br /><label><?php echo elgg_echo('tasks:state:actions'); ?></label>
		<?php
		echo elgg_view('input/radio', array(
			'name' => 'state_action',
			'options' => tasks_prepare_radio_options($vars['entity']->status),
		));
		?>
	</div>
	<div class="elgg-foot">
<?php
		echo elgg_view('input/submit', array('value' => elgg_echo("tasks:comments:post")));
?>
	</div>
<?php
	}
	
	echo elgg_view('input/hidden', array(
		'name' => 'entity_guid',
		'value' => $vars['entity']->getGUID()
	));
}
