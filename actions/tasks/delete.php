<?php
/**
 * Remove a task
 *
 * @package ElggTasks
 */

$guid = get_input('guid');
$task = get_entity($guid);
if ($task) {
	if ($task->canEdit()) {
		$container = get_entity($task->container_guid);
		$list = $task->list_guid;
		
		if ($task->delete()) {
			system_message(elgg_echo('tasks:delete:success'));
			if ($list) {
				if ($list = get_entity($list)) {
					forward($list->getURL());
				}
			}
			if (elgg_instanceof($container, 'group')) {
				forward("tasks/group/$container->guid/all");
			} else {
				forward("tasks/owner/$container->username");
			}
		}
	}
}

register_error(elgg_echo('tasks:delete:failure'));
forward(REFERER);
