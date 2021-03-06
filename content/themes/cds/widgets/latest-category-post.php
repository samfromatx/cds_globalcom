<?php

class LatestCategoryPostWidget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'latest_category_post_widget',
            __('Latest Category Post', 'text_domain'),
            array('description' => __('Display the latest category blog post', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        global $post;

        $recent = get_posts(array('numberposts' => 1, 'category' => 439));
        if (count($recent)) {
            $post = $recent[0];
            setup_postdata($post);

            $image = get_the_post_thumbnail(get_the_ID(), 250, 125);
            if (!$image) {
                $image_path = get_stylesheet_directory_uri() . '/images/widget-defaults/' . $instance['default_image'];
                $image = "<img src=\"$image_path\" />";
            }

            ?>
                <div class="link widget single">
                    <h4><?php echo $instance['title']; ?></h4>
                    <a class="full" href="<?php the_permalink(); ?>"><?php echo $image; ?></a>
                    <a class="read" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
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
    register_widget('LatestCategoryPostWidget');
});
