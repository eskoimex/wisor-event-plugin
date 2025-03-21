<?php
if (!defined('ABSPATH')) {
    exit;
}

class Wisor_Events_Post_Type {
    public function __construct() {
        add_action('init', array($this, 'register_events_post_type'));
    }

    public function register_events_post_type() {
        register_post_type('events',
            array(
                'labels' => array(
                    'name' => __('Events'),
                    'singular_name' => __('Event'),
                ),
                'public' => true,
                'has_archive' => true,
                'supports' => array('title', 'editor', 'custom-fields'),
                'show_in_rest' => true,
            )
        );

        register_meta('post', 'event_date', array(
            'type' => 'string',
            'description' => 'The date of the event',
            'single' => true,
            'show_in_rest' => true,
        ));
    }
}