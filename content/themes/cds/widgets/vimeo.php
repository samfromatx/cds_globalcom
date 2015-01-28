<?php

class VimeoWidget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'vimeo_widget',
            __('Vimeo Embed', 'text_domain'),
            array('description' => __('Embed a vimeo video', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        $path = parse_url($instance['video_url'], PHP_URL_PATH);
        $path = array_filter(explode('/', $path));
        $video_id = reset($path);

        $embed_src = "//player.vimeo.com/video/$video_id?title=0&byline=0&portrait=0&api=1&player_id=vimeo";
        ?>
        <div class="video widget double" style="">
            <div class='vimeo-container'><iframe width="100%" height="100%" src="<?php echo $embed_src; ?>" frameborder="0" id="vimeo" name="homepage video" allowfullscreen seamless></iframe></div>
        </div>
        <?php
    }

    public function form($instance) {
        $video_url = $instance['video_url'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('video_url'); ?>"><?php _e('Video URL:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('video_url'); ?>" name="<?php echo $this->get_field_name('video_url'); ?>" type="text" value="<?php echo esc_attr($video_url); ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['video_url'] = (!empty($new_instance['video_url'])) ? strip_tags($new_instance['video_url']) : '';

        return $instance;
    }
}
add_action('widgets_init', function() {
    register_widget('VimeoWidget');
});
