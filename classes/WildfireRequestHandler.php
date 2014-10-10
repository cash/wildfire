<?php
/**
 * Request handler
 */
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
		elgg_register_title_button();

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
		elgg_register_title_button();

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
		elgg_register_title_button();

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

	protected function serveGetAdd($segments) {
		elgg_push_breadcrumb(elgg_echo('wildfire:add'));

		$title = elgg_echo('wildfire:add');

		$form_vars = array(
			'enctype' => 'multipart/form-data',
		);
		$body_vars = $this->prepareUploadFormVars();
		$content = elgg_view_form('wildfire/add', $form_vars, $body_vars);

		return array(
			'title' => $title,
			'content' => $content,
			'filter' => '',
		);
	}

	protected function prepareUploadFormVars() {
		// input names => defaults
		$values = array(
			'title' => '',
			'description' => '',
			'access_id' => ACCESS_DEFAULT,
			'tags' => '',
			'container_guid' => elgg_get_page_owner_guid(),
		);

		if (elgg_is_sticky_form('wildfire')) {
			$sticky_values = elgg_get_sticky_values('wildfire');
			foreach ($sticky_values as $key => $value) {
				$values[$key] = $value;
			}
		}

		elgg_clear_sticky_form('wildfire');

		return $values;
	}
}

