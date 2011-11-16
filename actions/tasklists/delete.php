<?php
/**
 * Remove a task list (moving its tasks to "other tasks" list)
 *
 * @package ElggTasks
 */

$guid = get_input('guid');
$tasklist = get_entity($guid);
if ($tasklist) {
	if ($tasklist->canEdit()) {
		$container = get_entity($tasklist->container_guid);
		
		// Bring all child elements forward
		$children = elgg_get_entities_from_metadata(array(
			'metadata_name' => 'list_guid',
			'metadata_value' => $tasklist->getGUID()
		));
		if ($children) {
			foreach ($children as $child) {
				$child->list_guid = 0;
			}
		}
		
		if ($tasklist->delete()) {
			system_message(elgg_echo('tasks:lists:delete:success'));
			
			if (elgg_instanceof($container, 'group')) {
				forward("tasks/group/$container->guid/all");
			} else {
				forward("tasks/owner/$container->username");
			}
		}
	}
}

register_error(elgg_echo('tasks:lists:delete:failure'));
forward(REFERER);
