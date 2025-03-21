<?php
if (!defined('ABSPATH')) {
    exit;
}

class Wisor_Event_Management {
    public function __construct() {
        add_shortcode('event_management_form', array($this, 'render_event_management_form'));
    }

    public function render_event_management_form() {
        if (!is_user_logged_in()) {
            return '<p>You must be logged in to manage events.</p>';
        }

        ob_start();
        ?>
        <form method="post" action="">
            <label for="event_name">Event Name:</label>
            <input type="text" name="event_name" required><br>

            <label for="event_date">Event Date:</label>
            <input type="date" name="event_date" required><br>

            <label for="event_description">Event Description:</label>
            <textarea name="event_description" required></textarea><br>

            <input type="submit" name="submit_event" value="Add Event">
        </form>
        <?php

        if (isset($_POST['submit_event'])) {
            $event_name = sanitize_text_field($_POST['event_name']);
            $event_date = sanitize_text_field($_POST['event_date']);
            $event_description = sanitize_textarea_field($_POST['event_description']);

            $post_id = wp_insert_post(array(
                'post_title' => $event_name,
                'post_content' => $event_description,
                'post_type' => 'events',
                'post_status' => 'publish',
            ));

            if ($post_id) {
                update_post_meta($post_id, 'event_date', $event_date);
                echo '<p>Event added successfully!</p>';
            }
        }

        return ob_get_clean();
    }
}