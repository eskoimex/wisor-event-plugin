<?php
if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Widget_Base;

class Wisor_Elementor_Widget extends Widget_Base {
    public function get_name() {
        return 'wisor_events_widget';
    }

    public function get_title() {
        return __('Upcoming Events', 'wisor-events-plugin');
    }

    public function get_icon() {
        return 'eicon-calendar';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Settings', 'wisor-events-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'number_of_events',
            [
                'label' => __('Number of Events', 'wisor-events-plugin'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 5,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $args = array(
            'post_type' => 'events',
            'posts_per_page' => $settings['number_of_events'],
            'meta_key' => 'event_date',
            'orderby' => 'meta_value',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => 'event_date',
                    'value' => date('Y-m-d'),
                    'compare' => '>=',
                    'type' => 'DATE',
                ),
            ),
        );

        $query = new WP_Query($args);
        include plugin_dir_path(__FILE__) . '../templates/event-list.php';
    }
}

// Register the widget
function register_wisor_events_widget($widgets_manager) {
    $widgets_manager->register(new Wisor_Elementor_Widget());
}
add_action('elementor/widgets/register', 'register_wisor_events_widget');