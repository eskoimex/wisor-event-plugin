<?php
/*
Plugin Name: Wisor Events Plugin
Description: A custom plugin to manage and display upcoming events using Elementor and shortcodes.
Version: 1.0
Author: Your Name
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load all necessary files
require_once plugin_dir_path(__FILE__) . 'includes/class-events-post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-event-management.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-elementor-widget.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-security.php';

// Initialize the plugin
function wisor_events_plugin_init() {
    new Wisor_Events_Post_Type();
    new Wisor_Event_Management();
    new Wisor_Elementor_Widget();
    new Wisor_Shortcode();
    new Wisor_Security();
}
add_action('plugins_loaded', 'wisor_events_plugin_init');