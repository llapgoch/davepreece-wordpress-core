<?php
/*
Plugin Name: Dave Preece Core Classes - Forms
Plugin URI: http://carbolowdrates.com
Description: Core classes and dependencies
Version: 0.1
Author: Dave Preece
Author URI: http://www.scumonline.co.uk
License: GPL

Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : dangerous@scumonline.co.uk)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
add_action('wp', 'davepreece_core_setup');


// Autoloader
spl_autoload_register(function($class){
	if(substr($class, 0, 3) == "DP_"){
		if(file_exists(dirname(__FILE__) . '/DP/' . $class . ".php")){
			include(dirname(__FILE__) . '/DP/' . $class . ".php");
		}
	}
});

function davepreece_core_setup(){	
	wp_enqueue_script('dp-core', plugins_url('js/script.js', __FILE__), array('jquery'));
}