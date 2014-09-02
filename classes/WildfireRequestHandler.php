<?php

class WildfireRequestHandler {
	public function route(array $segments = array()) {
		$page = array_shift($segments);
		if ($page) {
			$requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
			$method = "serve" . ucfirst($requestMethod) . ucfirst($page);
			if (method_exists($this, $method)) {
				$sections = $this->$method($segments);
				if ($sections) {
					$this->render($sections);
					return true;
				}
			}
		}

		return false;
	}

	public function render($sections) {
		$body = elgg_view_layout('content', $sections);
		echo elgg_view_page($sections['title'], $body);
	}

	protected function serveGetAll($segments) {

		elgg_push_breadcrumb(elgg_echo('wildfire:title'));

		$content = elgg_list_entities(array(
			'type' => 'object',
			'subtype' => 'wildfire',
			'no_results' => elgg_echo('wildfire:none'),
		));

		return array(
			'title' => elgg_echo('wildfire:title:all'),
			'content' => $content,
			'filter_context' => 'all',
		);
	}

	protected function serveGetOwner($segments) {

		$owner = elgg_get_page_owner_entity();

		elgg_push_breadcrumb(elgg_echo('wildfire:title'), 'wildfire/all');
		elgg_push_breadcrumb($owner->name);

		$content = elgg_list_entities(array(
			'type' => 'object',
			'subtype' => 'wildfire',
			'container_guid' => $owner->guid,
			'no_results' => elgg_echo('wildfire:none'),
		));

		return array(
			'title' => elgg_echo('wildfire:title:owner'),
			'content' => $content,
			'filter_context' => 'mine',
		);
	}

	protected function serveGetFriends($segments) {

		$owner = elgg_get_page_owner_entity();

		elgg_push_breadcrumb(elgg_echo('wildfire:title'), 'wildfire/all');
		elgg_push_breadcrumb($owner->name, "wildfire/owner/$owner->username");
		elgg_push_breadcrumb(elgg_echo('friends'));

		$content = elgg_list_entities(array(
			'type' => 'object',
			'subtype' => 'wildfire',
			'relationship' => 'friend',
			'relationship_guid' => $owner->guid,
			'relationship_join_on' => 'container_guid',
			'no_results' => elgg_echo('wildfire:none'),
		));

		return array(
			'title' => elgg_echo('wildfire:title:owner'),
			'content' => $content,
			'filter_context' => 'friends',
		);
	}

	protected function serveGetView($segments) {

		$guid = array_shift($segments);
		$object = get_entity($guid);
		if (!$object) {
			return false;
		}

		elgg_push_breadcrumb(elgg_echo('wildfire:title'), 'wildfire/all');

		$content = elgg_view_entity($object);
		$content .= elgg_view_comments($object);

		return array(
			'title' => $object->title,
			'content' => $content,
			'filter' => '',
		);
	}

}

