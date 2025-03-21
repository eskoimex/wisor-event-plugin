<?php if ($query->have_posts()) : ?>
    <ul class="wisor-events-list">
        <?php while ($query->have_posts()) : $query->the_post(); ?>
            <li>
                <h3><?php the_title(); ?></h3>
                <p><?php the_content(); ?></p>
                <span><?php echo get_post_meta(get_the_ID(), 'event_date', true); ?></span>
            </li>
        <?php endwhile; ?>
    </ul>
    <button id="load-more-events">Load More</button>
<?php else : ?>
    <p>No events found.</p>
<?php endif; ?>