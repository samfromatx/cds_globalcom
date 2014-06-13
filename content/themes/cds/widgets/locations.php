<?php

class LocationsWidget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'locations_widget',
            __('Locations Map', 'text_domain'),
            array('description' => __('Displays a map with location markers', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        ?>
        <div class="locations widget double">
            <h4><?php echo $instance['title']; ?></h4>
            <a href="/about/locations"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/locations.png"></a>
        </div>
        <?php
    }

    public function form($instance) {
        if (isset($instance['title']))
            $title = $instance['title'];
        else
            $title = __('Our Locations', 'text_domain');

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
    register_widget('LocationsWidget');
});
