<?php

class IndustriesWidget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'industries_widget',
            __('Industries List', 'text_domain'),
            array('description' => __('An expandable list of industries', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        $parent = get_page_by_title('industries');
        $query = new WP_Query(array(
            'post_type' => 'page',
            'post_parent' => $parent->ID,
            'order' => 'ASC',
        ));

        if ($query->have_posts()): ?>
        <div class="industries widget single">
            <h4><?php echo $instance['title']; ?></h4>
            <dl>
                <?php while ($query->have_posts()): $query->the_post(); ?>
                    <dt><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></dt>
                    <dd>
                        <?php echo get_the_excerpt(); ?>
                        <a href="<?php the_permalink(); ?>">Learn more</a>
                    </dd>
                <?php endwhile; ?>
            </dl>
        </div>
        <?php endif;

        wp_reset_query();
    }

    public function form($instance) {
        if (isset($instance['title']))
            $title = $instance['title'];
        else
            $title = __('Industries', 'text_domain');

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

        return $instance;
    }
}
add_action('widgets_init', function() {
    register_widget('IndustriesWidget');
});
