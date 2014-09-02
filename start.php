<?php

elgg_register_event_handler('init', 'system', 'wildfire_init');

function wildfire_init() {
	elgg_register_page_handler('wildfire', 'wildfire_page_handler');

	$item = new ElggMenuItem('wildfire', elgg_echo('wildfire:title'), 'wildfire/all');
	elgg_register_menu_item('site', $item);

	elgg_register_entity_type('object', 'wildfire');

	$actions_base = elgg_get_plugins_path() . 'wildfire/actions/wildfire';
	elgg_register_action('wildfire/delete', "$actions_base/delete.php");
}

function wildfire_page_handler($segments) {
	$handler = new WildfireRequestHandler();
	return $handler->route($segments);
}

