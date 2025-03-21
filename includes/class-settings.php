<?php
if (!defined('ABSPATH')) {
    exit;
}

class Wisor_Events_Settings {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Add the settings page to the WordPress admin menu.
     */
    public function add_settings_page() {
        add_submenu_page(
            'options-general.php', // Parent menu (Settings)
            'Wisor Events Settings', // Page title
            'Wisor Events', // Menu title
            'manage_options', // Capability
            'wisor-events-settings', // Menu slug
            array($this, 'render_settings_page') // Callback function
        );
    }

    /**
     * Register the plugin settings.
     */
    public function register_settings() {
        register_setting('wisor_events_options_group', 'wisor_events_options', array($this, 'sanitize_options'));

        add_settings_section(
            'wisor_events_main_section',
            'Global Settings',
            array($this, 'render_section_text'),
            'wisor-events-settings'
        );

        add_settings_field(
            'default_number_of_events',
            'Default Number of Events',
            array($this, 'render_number_of_events_field'),
            'wisor-events-settings',
            'wisor_events_main_section'
        );
    }

    /**
     * Sanitize the plugin options.
     */
    public function sanitize_options($input) {
        $input['default_number_of_events'] = absint($input['default_number_of_events']);
        return $input;
    }

    /**
     * Render the settings page.
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>Wisor Events Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('wisor_events_options_group');
                do_settings_sections('wisor-events-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render the section text.
     */
    public function render_section_text() {
        echo '<p>Configure global settings for the Wisor Events plugin.</p>';
    }

    /**
     * Render the "Number of Events" field.
     */
    public function render_number_of_events_field() {
        $options = get_option('wisor_events_options');
        $value = isset($options['default_number_of_events']) ? $options['default_number_of_events'] : 5;
        echo '<input type="number" name="wisor_events_options[default_number_of_events]" value="' . esc_attr($value) . '" min="1" />';
    }
}

// Initialize the settings page
new Wisor_Events_Settings();