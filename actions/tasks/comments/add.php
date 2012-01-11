<?php
/**
 * Tasks add comment action
 *
 * @package ElggTasks
 */

elgg_load_library('elgg:tasks');

$entity_guid = (int) get_input('entity_guid');
$comment_text = get_input('generic_comment');
$state_action = get_input('state_action', false);


// Let's see if we can get an entity with the specified GUID
$entity = get_entity($entity_guid);
if (!$entity) {
	register_error(elgg_echo("generic_comment:notfound"));
	forward(REFERER);
}

$user = elgg_get_logged_in_user_entity();

if($comment_text) {
	$annotation = create_annotation($entity->guid,
								'generic_comment',
								$comment_text,
								"",
								$user->guid,
								$entity->access_id);

	// tell user annotation posted
	if (!$annotation) {
		register_error(elgg_echo("generic_comment:failure"));
		forward(REFERER);
	}
}

$new_state = tasks_get_state_from_action($state_action);

if($new_state) {
	$entity->status = $new_state;
	$entity->time_status_changed = time();
	
	$annotation = create_annotation($entity->guid,
								'task_state_changed',
								$state_action,
								"",
								$user->guid,
								$entity->access_id);
}

// notify if poster wasn't owner
if ($entity->owner_guid != $user->guid) {

	notify_user($entity->owner_guid,
				$user->guid,
				elgg_echo('generic_comment:email:subject'),
				elgg_echo('generic_comment:email:body', array(
					$entity->title,
					$user->name,
					$comment_text,
					$entity->getURL(),
					$user->name,
					$user->getURL()
				))
			);
}

system_message(elgg_echo("generic_comment:posted"));

//add to river
add_to_river('river/annotation/generic_comment/create', 'comment', $user->guid, $entity->guid, "", 0, $annotation);

// Forward to the page the action occurred on
forward(REFERER);
