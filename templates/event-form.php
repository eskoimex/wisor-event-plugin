<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<form method="post" action="">
    <?php wp_nonce_field('add_event', '_wpnonce'); ?>
    <label for="event_name">Event Name:</label>
    <input type="text" name="event_name" required><br>

    <label for="event_date">Event Date:</label>
    <input type="date" name="event_date" required><br>

    <label for="event_description">Event Description:</label>
    <textarea name="event_description" required></textarea><br>

    <input type="submit" name="submit_event" value="Add Event">
</form>