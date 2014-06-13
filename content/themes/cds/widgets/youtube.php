<?php

class YoutubeWidget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'youtube_widget',
            __('Youtube Embed', 'text_domain'),
            array('description' => __('Embed a youtube video', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        $querystring = parse_url($instance['video_url'], PHP_URL_QUERY);
        $queryargs = wp_parse_args($querystring);
        $video_id = $queryargs['v'];
        $embed_src = "//www.youtube.com/embed/$video_id?rel=0&html5=1"
        ?>
        <div class="video widget double">
            <iframe width="100%" height="450px" src="<?php echo $embed_src; ?>" frameborder="0" allowfullscreen seamless></iframe>
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
    register_widget('YoutubeWidget');
});
