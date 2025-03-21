<?php
/**
 * Test suite for the Wisor Events Plugin.
 *
 * @package WisorEventsPlugin
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for querying events.
 */
class WisorEventsQueryTest extends TestCase {

    /**
     * Set up the test environment.
     */
    protected function setUp(): void {
        parent::setUp();

        // Register the custom post type and meta fields.
        require_once plugin_dir_path(__DIR__) . 'includes/class-events-post-type.php';
        $this->events_post_type = new Wisor_Events_Post_Type();
        $this->events_post_type->register_events_post_type();

        // Insert test events.
        $this->insert_test_events();
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void {
        parent::tearDown();

        // Delete test events.
        $this->delete_test_events();
    }

    /**
     * Insert test events into the database.
     */
    private function insert_test_events() {
        $this->event_ids = [];

        // Insert past event.
        $this->event_ids[] = wp_insert_post([
            'post_title'   => 'Past Event',
            'post_content' => 'This is a past event.',
            'post_type'    => 'events',
            'post_status'  => 'publish',
        ]);
        update_post_meta($this->event_ids[0], 'event_date', '2023-01-01');

        // Insert future event.
        $this->event_ids[] = wp_insert_post([
            'post_title'   => 'Future Event',
            'post_content' => 'This is a future event.',
            'post_type'    => 'events',
            'post_status'  => 'publish',
        ]);
        update_post_meta($this->event_ids[1], 'event_date', '2024-01-01');

        // Insert additional future events for infinite scroll testing.
        for ($i = 2; $i <= 6; $i++) {
            $this->event_ids[] = wp_insert_post([
                'post_title'   => 'Future Event ' . $i,
                'post_content' => 'This is future event ' . $i . '.',
                'post_type'    => 'events',
                'post_status'  => 'publish',
            ]);
            update_post_meta($this->event_ids[$i], 'event_date', '2024-01-0' . $i);
        }
    }

    /**
     * Delete test events from the database.
     */
    private function delete_test_events() {
        foreach ($this->event_ids as $event_id) {
            wp_delete_post($event_id, true);
        }
    }

    /**
     * Test if the custom post type is registered correctly.
     */
    public function test_custom_post_type_registered() {
        $post_type = get_post_type_object('events');
        $this->assertNotNull($post_type, 'Custom post type "events" is not registered.');
        $this->assertEquals('Events', $post_type->labels->name, 'Custom post type label is incorrect.');
    }

    /**
     * Test if the event meta field is registered correctly.
     */
    public function test_event_meta_field_registered() {
        $meta_keys = get_registered_meta_keys('post', 'events');
        $this->assertArrayHasKey('event_date', $meta_keys, 'Meta field "event_date" is not registered.');
    }

    /**
     * Test if the event query retrieves future events correctly.
     */
    public function test_event_query_future_events() {
        $args = [
            'post_type'      => 'events',
            'posts_per_page' => 10,
            'meta_key'       => 'event_date',
            'orderby'        => 'meta_value',
            'order'         => 'ASC',
            'meta_query'     => [
                [
                    'key'     => 'event_date',
                    'value'   => date('Y-m-d'),
                    'compare' => '>=',
                    'type'    => 'DATE',
                ],
            ],
        ];

        $query = new WP_Query($args);
        $this->assertTrue($query->have_posts(), 'No future events found.');
        $this->assertEquals(6, $query->post_count, 'Incorrect number of future events.');

        // Verify the event details.
        $event = $query->posts[0];
        $this->assertEquals('Future Event', $event->post_title, 'Incorrect event title.');
        $this->assertEquals('This is a future event.', $event->post_content, 'Incorrect event description.');
        $this->assertEquals('2024-01-01', get_post_meta($event->ID, 'event_date', true), 'Incorrect event date.');
    }

    /**
     * Test if the event query retrieves past events correctly.
     */
    public function test_event_query_past_events() {
        $args = [
            'post_type'      => 'events',
            'posts_per_page' => 10,
            'meta_key'       => 'event_date',
            'orderby'        => 'meta_value',
            'order'         => 'ASC',
            'meta_query'     => [
                [
                    'key'     => 'event_date',
                    'value'   => date('Y-m-d'),
                    'compare' => '<',
                    'type'    => 'DATE',
                ],
            ],
        ];

        $query = new WP_Query($args);
        $this->assertTrue($query->have_posts(), 'No past events found.');
        $this->assertEquals(1, $query->post_count, 'Incorrect number of past events.');

        // Verify the event details.
        $event = $query->posts[0];
        $this->assertEquals('Past Event', $event->post_title, 'Incorrect event title.');
        $this->assertEquals('This is a past event.', $event->post_content, 'Incorrect event description.');
        $this->assertEquals('2023-01-01', get_post_meta($event->ID, 'event_date', true), 'Incorrect event date.');
    }

    /**
     * Test if the event query retrieves all events correctly.
     */
    public function test_event_query_all_events() {
        $args = [
            'post_type'      => 'events',
            'posts_per_page' => 10,
            'meta_key'       => 'event_date',
            'orderby'        => 'meta_value',
            'order'         => 'ASC',
        ];

        $query = new WP_Query($args);
        $this->assertTrue($query->have_posts(), 'No events found.');
        $this->assertEquals(7, $query->post_count, 'Incorrect number of events.');

        // Verify the event details.
        $events = $query->posts;
        $this->assertEquals('Past Event', $events[0]->post_title, 'Incorrect event title.');
        $this->assertEquals('Future Event', $events[1]->post_title, 'Incorrect event title.');
    }

    /**
     * Test if the infinite scroll functionality works correctly.
     */
    public function test_infinite_scroll_functionality() {
        $args = [
            'post_type'      => 'events',
            'posts_per_page' => 5,
            'meta_key'       => 'event_date',
            'orderby'        => 'meta_value',
            'order'         => 'ASC',
            'paged'         => 1,
        ];

        $query = new WP_Query($args);
        $this->assertTrue($query->have_posts(), 'No events found.');
        $this->assertEquals(5, $query->post_count, 'Incorrect number of events on the first page.');

        // Simulate loading the next page.
        $args['paged'] = 2;
        $query = new WP_Query($args);
        $this->assertTrue($query->have_posts(), 'No events found on the second page.');
        $this->assertEquals(2, $query->post_count, 'Incorrect number of events on the second page.');
    }

    /**
     * Test if the date filtering functionality works correctly.
     */
    public function test_date_filtering_functionality() {
        // Test filtering for past events.
        $args = [
            'post_type'      => 'events',
            'posts_per_page' => 10,
            'meta_key'       => 'event_date',
            'orderby'        => 'meta_value',
            'order'         => 'ASC',
            'meta_query'     => [
                [
                    'key'     => 'event_date',
                    'value'   => date('Y-m-d'),
                    'compare' => '<',
                    'type'    => 'DATE',
                ],
            ],
        ];

        $query = new WP_Query($args);
        $this->assertTrue($query->have_posts(), 'No past events found.');
        $this->assertEquals(1, $query->post_count, 'Incorrect number of past events.');

        // Test filtering for future events.
        $args['meta_query'][0]['compare'] = '>=';
        $query = new WP_Query($args);
        $this->assertTrue($query->have_posts(), 'No future events found.');
        $this->assertEquals(6, $query->post_count, 'Incorrect number of future events.');
    }
}