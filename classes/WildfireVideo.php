<?php
/**
 * Video class
 */

class WildfireVideo extends ElggObject {

	protected function initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes['subtype'] = "wildfire";
	}

	public function getURL() {
		return elgg_normalize_url('wildfire/view/' . $this->guid);
	}
}

