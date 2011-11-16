<?php
/**
 * Elgg Tasks Management
 *
 * @package ElggTasks
 */

elgg_register_event_handler('init', 'system', 'tasks_init');

/**
 * Initialize the tasks management plugin.
 *
 */
function tasks_init() {

	// register a library of helper functions
	elgg_register_library('elgg:tasks', elgg_get_plugins_path() . 'tasks/lib/tasks.php');

	$item = new ElggMenuItem('tasks', elgg_echo('tasks'), 'tasks/all');
	elgg_register_menu_item('site', $item);

	// Register a page handler, so we can have nice URLs
	elgg_register_page_handler('tasks', 'tasks_page_handler');

	// Register a url handler
	elgg_register_entity_url_handler('object', 'task', 'tasks_url');
	elgg_register_entity_url_handler('object', 'tasklist', 'tasks_url');

	// Register some actions
	$action_base = elgg_get_plugins_path() . 'tasks/actions/tasks';
	elgg_register_action("tasks/edit", "$action_base/edit.php");
	elgg_register_action("tasks/delete", "$action_base/delete.php");

	// Extend the main css view
	elgg_extend_view('css/elgg', 'tasks/css');

	// Register entity type for search
	elgg_register_entity_type('object', 'task');
	elgg_register_entity_type('object', 'tasklist');

	// Register granular notification for this type
	register_notification_object('object', 'task', elgg_echo('tasks:new'));
	register_notification_object('object', 'tasklist', elgg_echo('tasks:tasklist:new'));
	elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'tasks_notify_message');

	// add to groups
	add_group_tool_option('tasks', elgg_echo('groups:enabletasks'), true);
	elgg_extend_view('groups/tool_latest', 'tasks/group_module');

	//add a widget
	elgg_register_widget_type('tasks', elgg_echo('tasks'), elgg_echo('tasks:widget:description'));

	// Language short codes must be of the form "tasks:key"
	// where key is the array key below
	elgg_set_config('tasks', array(
		'title' => 'text',
		'description' => 'longtext',
		'tags' => 'tags',
		'access_id' => 'access',
		'write_access_id' => 'write_access',
	));

	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'tasks_owner_block_menu');

	// icon url override
	elgg_register_plugin_hook_handler('entity:icon:url', 'object', 'tasks_icon_url_override');

	// entity menu
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'tasks_entity_menu_setup');

	// register ecml views to parse
	elgg_register_plugin_hook_handler('get_views', 'ecml', 'tasks_ecml_views_hook');
}

/**
 * Dispatcher for tasks.
 * URLs take the form of
 *  All tasks:        tasks/all
 *  User's tasks:     tasks/owner/<username>
 *  Friends' tasks:   tasks/friends/<username>
 *  View task:        tasks/view/<guid>/<title>
 *  New task:         tasks/add/<guid> (container: user, group, parent)
 *  Edit task:        tasks/edit/<guid>
 *  Group tasks:      tasks/group/<guid>/all
 *
 * Title is ignored
 *
 * @param array $page
 */
function tasks_page_handler($page) {

	elgg_load_library('elgg:tasks');

	if (!isset($page[0])) {
		$page[0] = 'all';
	}

	elgg_push_breadcrumb(elgg_echo('tasks'), 'tasks/all');

	$base_dir = elgg_get_plugins_path() . 'tasks/pages/tasks';

	$page_type = $page[0];
	switch ($page_type) {
		case 'owner':
			include "$base_dir/owner.php";
			break;
		case 'friends':
			include "$base_dir/friends.php";
			break;
		case 'view':
			set_input('guid', $page[1]);
			include "$base_dir/view.php";
			break;
		case 'add':
			set_input('guid', $page[1]);
			include "$base_dir/new.php";
			break;
		case 'edit':
			set_input('guid', $page[1]);
			include "$base_dir/edit.php";
			break;
		case 'group':
			include "$base_dir/owner.php";
			break;
		case 'all':
		default:
			include "$base_dir/world.php";
			break;
	}

	return;
}

/**
 * Override the task url
 * 
 * @param ElggObject $entity task object
 * @return string
 */
function tasks_url($entity) {
	$title = elgg_get_friendly_title($entity->title);
	return "tasks/view/$entity->guid/$title";
}

/**
 * Override the default entity icon for tasks
 *
 * @return string Relative URL
 */
function tasks_icon_url_override($hook, $type, $returnvalue, $params) {
	$entity = $params['entity'];
	if (elgg_instanceof($entity, 'object', 'tasklist') ||
		elgg_instanceof($entity, 'object', 'task')) {
		switch ($params['size']) {
			case 'small':
				return 'mod/tasks/images/tasks.gif';
				break;
			case 'medium':
				return 'mod/tasks/images/tasks_lrg.gif';
				break;
		}
	}
}

/**
 * Add a menu item to the user ownerblock
 */
function tasks_owner_block_menu($hook, $type, $return, $params) {
	if (elgg_instanceof($params['entity'], 'user')) {
		$url = "tasks/owner/{$params['entity']->username}";
		$item = new ElggMenuItem('tasks', elgg_echo('tasks'), $url);
		$return[] = $item;
	} else {
		if ($params['entity']->tasks_enable != "no") {
			$url = "tasks/group/{$params['entity']->guid}/all";
			$item = new ElggMenuItem('tasks', elgg_echo('tasks:group'), $url);
			$return[] = $item;
		}
	}

	return $return;
}

/**
 * Add links/info to entity menu particular to tasks plugin
 */
function tasks_entity_menu_setup($hook, $type, $return, $params) {
	if (elgg_in_context('widgets')) {
		return $return;
	}

	$entity = $params['entity'];
	$handler = elgg_extract('handler', $params, false);
	if ($handler != 'tasks') {
		return $return;
	}

	// remove delete if not owner or admin
	if (!elgg_is_admin_logged_in() && elgg_get_logged_in_user_guid() != $entity->getOwnerGuid()) {
		foreach ($return as $index => $item) {
			if ($item->getName() == 'delete') {
				unset($return[$index]);
			}
		}
	}

	$options = array(
		'name' => 'history',
		'text' => elgg_echo('tasks:history'),
		'href' => "tasks/history/$entity->guid",
		'priority' => 150,
	);
	$return[] = ElggMenuItem::factory($options);

	return $return;
}

/**
* Returns a more meaningful message
*
* @param unknown_type $hook
* @param unknown_type $entity_type
* @param unknown_type $returnvalue
* @param unknown_type $params
*/
function tasks_notify_message($hook, $entity_type, $returnvalue, $params) {
	$entity = $params['entity'];
	$to_entity = $params['to_entity'];
	$method = $params['method'];
	if (($entity instanceof ElggEntity) && (($entity->getSubtype() == 'tasklist') || ($entity->getSubtype() == 'task'))) {
		$descr = $entity->description;
		$title = $entity->title;
		//@todo why?
		$url = elgg_get_site_url() . "view/" . $entity->guid;
		$owner = $entity->getOwnerEntity();
		return $owner->name . ' ' . elgg_echo("tasks:via") . ': ' . $title . "\n\n" . $descr . "\n\n" . $entity->getURL();
	}
	return null;
}




/**
 * Return views to parse for tasks.
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $return_value
 * @param unknown_type $params
 */
function tasks_ecml_views_hook($hook, $entity_type, $return_value, $params) {
	$return_value['object/task'] = elgg_echo('item:object:task');
	$return_value['object/tasklist'] = elgg_echo('item:object:tasklist');

	return $return_value;
}
