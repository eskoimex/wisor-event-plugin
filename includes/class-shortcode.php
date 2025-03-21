<?php
if (!defined('ABSPATH')) {
    exit;
}

class Wisor_Security {
    public function __construct() {
        add_action('init', array($this, 'verify_nonce'));
    }

    public function verify_nonce() {
        if (isset($_POST['submit_event'])) {
            if (!wp_verify_nonce($_POST['_wpnonce'], 'add_event')) {
                wp_die('Security check failed.');
            }
        }
    }
}