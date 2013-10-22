<?php
/*
Plugin Name: Web Demos
Plugin URI: http://vilmosioo.co.uk/demos
Description: A plugin that helps developers showcase demos/documentation for their work
Version: 0.1
Author: Vilmos Ioo
Author URI: http://vilmosioo.co.uk
Author Email: ioo.vilmos@gmail.com
License: GPL2

	Copyright 2013 Vilmos Ioo  (email : ioo.vilmos@gmail.com)

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

// Define constants
define('VI_DEMOS_DIR', plugin_dir_path(__FILE__));
define('VI_DEMOS_URL', plugin_dir_url(__FILE__));

require_once 'inc/Web_Demos_Utils.php';
require_once 'inc/Demo_Custom_Post.php';
require_once 'inc/Web_Demos_Metabox.php';

class Web_Demos {
	 
	static function init(){
		return new Web_Demos();
	}

	const ID = 'Web_Demos';

	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	private function __construct() {
		register_activation_hook(__FILE__, array( &$this, 'activate' ) );
		register_deactivation_hook(__FILE__, array( &$this, 'deactivate' ) );

		Demo_Custom_Post::create();
		Web_Demos_MetaBox::create();
	} 

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
	 */
	public function activate( $network_wide ) {
		do_action('Web_Demos_Activated');
	} 
	
	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
	 */
	public function deactivate( $network_wide ) {
		do_action('WP_Github_Tools_Deactivated');
	} 
} // end class

add_action( 'init', array( 'Web_Demos', 'init' ) );
?>