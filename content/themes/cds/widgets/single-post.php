<?php

class SinglePostWidget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'single_post_widget',
            __('Single Post', 'text_domain'),
            array('description' => __('Display a specific post', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        $query = new WP_Query( array(
            'p' => $instance['post_id'],
             'post_type' => 'any',
        ));
        if (!$query->have_posts())            
            return;

        $query->the_post();

        $image = get_the_post_thumbnail(get_the_ID(), 250, 125);
        if (!$image) {
            $image_path = get_stylesheet_directory_uri() . '/images/widget-defaults/' . $instance['default_image'];
            $image = "<img src=\"$image_path\" />";
        }

        if ($instance['post_utm']) {
            $post_utm = $instance['post_utm'];
        } else {
            $post_utm = "";
        }

        ?>
        <div class="link widget single">
        <?php if ($args['id'] == 'cds-homepage'): ?>
            <h4><?php echo $instance['title']; ?></h4>
            <a href="<?php the_permalink(); echo $post_utm; ?>"><?php the_title(); ?></a>
            <p><?php echo get_the_excerpt(); ?> <a href="<?php the_permalink(); echo $post_utm; ?>">Learn&nbsp;more</a></p>
            <a class="full" href="<?php the_permalink(); echo $post_utm; ?>"><?php echo $image; ?></a>
        <?php else: ?>
            <h4><?php echo $instance['title']; ?></h4>
            <a class="full" href="<?php the_permalink(); echo $post_utm; ?>"><?php echo $image; ?></a>
            <a class="read" href="<?php the_permalink(); echo $post_utm; ?>"><?php the_title(); ?></a>
        <?php endif; ?>
        </div>
        <?php
    }

    public function form($instance) {
        if (isset($instance['title']))
            $title = $instance['title'];
        else
            $title = __('New title', 'text_domain');

        $post_id = (isset($instance['post_id'])) ? $instance['post_id'] : '';
        $post_utm = (isset($instance['post_utm'])) ? $instance['post_utm'] : '';
        $default_image = $instance['default_image'];

        $image_options = array(
            'noteworthy.jpg' => 'New &amp; Noteworthy',
            'connect.jpg' => 'Connect',
            'learn.jpg' => 'Learn',
        );

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
            <?php _e('Post ID: '); ?>
            <input class="widefat" id="<?php echo $this->get_field_id('post_id'); ?>" name="<?php echo $this->get_field_name('post_id'); ?>" type="text" value="<?php echo esc_attr($post_id); ?>" />
            <?php _e('Custom Campaign parameters (UTA tracking): '); ?>
            <input class="widefat" id="<?php echo $this->get_field_id('post_utm'); ?>" name="<?php echo $this->get_field_name('post_utm'); ?>" type="text" value="<?php echo esc_attr($post_utm); ?>" />
            <label for="<?php echo $this->get_field_id('default_image'); ?>">Default image if post does not have one</label>
            <select class="widefat" id="<?php echo $this->get_field_id('default_image'); ?>" name="<?php echo $this->get_field_name('default_image'); ?>">
                <?php foreach ($image_options as $value => $name): ?>
                    <option value="<?php echo $value; ?>" <?php selected($value, $default_image); ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['post_id'] = (int)$new_instance['post_id'];
        $instance['post_utm'] = $new_instance['post_utm'];
        $instance['default_image'] = $new_instance['default_image'];

        return $instance;
    }
}
add_action('widgets_init', function() {
    register_widget('SinglePostWidget');
});

