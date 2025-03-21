<?php
if (!defined('ABSPATH')) {
    exit;
}

class Wisor_Shortcode {
    public function __construct() {
        add_shortcode('upcoming_events', array($this, 'render_upcoming_events'));
    }

    public function render_upcoming_events($atts) {
        $atts = shortcode_atts(array(
            'number' => 5,
        ), $atts);
    
        $args = array(
            'post_type' => 'events',
            'posts_per_page' => $atts['number'],
            'meta_key' => 'event_date',
            'orderby' => 'meta_value',
            'order' => 'ASC',
            'paged' => get_query_var('paged', 1),
        );
    
        $query = new WP_Query($args);
        ob_start();
        include plugin_dir_path(__FILE__) . '../templates/event-list.php';
        return ob_get_clean();
    }
}