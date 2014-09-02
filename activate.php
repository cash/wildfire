<?php
/**
 * initialize plugin by registering class
 */

if (get_subtype_id('object', 'wildfire')) {
	update_subtype('object', 'wildfire', 'WildfireVideo');
} else {
	add_subtype('object', 'wildfire', 'WildfireVideo');
}

