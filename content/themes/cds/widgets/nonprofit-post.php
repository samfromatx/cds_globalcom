<?php

class NonprofitPostWidget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'nonprofit_post_widget',
            __('Nonprofit Blog Posts', 'text_domain'),
            array('description' => __('Display the last three nonprofit blog posts', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        global $post;

        $recent = get_posts(array('numberposts' => 3, 'category' => 439));
        if (count($recent)) {
            $firstpost = $recent[0];
            setup_postdata($post);

            $image = get_the_post_thumbnail($firstpost->ID, 250, 125);
            if (!$image) {
                $image_path = get_stylesheet_directory_uri() . '/images/widget-defaults/' . $instance['default_image'];
                $image = "<img src=\"$image_path\" />";
            }

            ?>
                <div class="nonprofit widget single">
                    <h4><?php echo $instance['title']; ?></h4>
                    <a class="full" href="/blog/category/nonprofit/"><?php echo $image; ?></a>
                    <ul>
                    <?php foreach ( $recent as $post ) : setup_postdata( $post ); ?>
                    <li><a class="read" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                    <p><a href="/blog/category/nonprofit/">Read More Nonprofit Posts</a></p>
                </div>
            <?php
        }
    }

    public function form($instance) {
        if (isset($instance['title']))
            $title = $instance['title'];
        else
            $title = __('New title', 'text_domain');

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
    register_widget('NonprofitPostWidget');
});
