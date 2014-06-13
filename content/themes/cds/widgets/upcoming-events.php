<?php

class UpcomingEventsWidget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'upcoming_events_widget',
            __('Upcoming Events', 'text_domain'),
            array('description' => __('Display a number of upcoming events', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        $query = new WP_Query(array(
            'post_type' => 'event',
            'posts_per_page' => $instance['num'],
            'no_found_rows' => true,
            'post_status' => 'publish',
            'ignore_sticky_posts' => true,
            'meta_query' => array(
                array(
                    'key' => 'event_date',
                    // current_time includes the timestamp, which we don't want
                    // so we can include events that happen today.
                    // We can't use date() because wordpress sets the timezone to UTC
                    // and the converts in current_time
                    'value' => reset(explode(' ', current_time('mysql'))),
                    'compare' => '>=',
                    'type' => 'DATETIME',
                )
            ),
            'meta_key' => 'event_date',
            'orderby' => 'meta_value',
            'order' => 'ASC',
        ));

        if ($query->have_posts()) : ?>
            <div class="events widget single">
                <h4><?php echo $instance['title'] ?></h4>
                <ul>
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <li>
                            <div class="date">
                                <?php $date = strtotime(get_post_meta(get_the_ID(), 'event_date', true)); ?>
                                <span class="month"><?php echo date('M', $date); ?></span>
                                <span class="day"><?php echo date('j', $date); ?></span>
                            </div>
                            <div class="content">
                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <p><?php echo get_post_meta(get_the_ID(), 'event_location', true); ?></p>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        <?php endif;

        wp_reset_query();
    }

    public function form($instance) {
        if (isset($instance['title']))
            $title = $instance['title'];
        else
            $title = __('Upcoming Events', 'text_domain');

        if (isset($instance['num']))
            $num = $instance['num'];
        else
            $num = 3;

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('num'); ?>"<?php _e('Number of events:'); ?></label>
            <input type="number" min="1" class="widefat" id="<?php echo $this->get_field_id('num'); ?>" name="<?php echo $this->get_field_name('num'); ?>" value="<?php echo esc_attr($num); ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['num'] = (int)$new_instance['num'];

        return $instance;
    }
}
add_action('widgets_init', function() {
    register_widget('UpcomingEventsWidget');
});
