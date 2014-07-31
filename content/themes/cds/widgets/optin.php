<?php

class OptinWidget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'optin_widget',
            __('Email Opt In', 'text_domain'),
            array('description' => __('Opt in widget. Links to the Opt In Stay Informed page', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        ?>
        <div class="optinwidget widget single">
            <div class="optinwidget-banner"><img src="/content/uploads/2014/07/optin_widget_banner.png" alt="Be The First To Hear!"  /></div>
            <div class="optinwidget-text">
                <p><?php echo $instance['optin_text'] ?></p>
                <p><strong><?php echo $instance['optin_text_b'] ?></strong></p>
                <a href="/stay-informed/" class="btn btn-danger optin_submit" id="optinSubmit" role="button"><?php echo $instance['optin_button_text'] ?></a>
            </div>
        </div>
        <?php
    }

    public function form($instance) {
        $optin_text = $instance['optin_text'];
        $optin_text_b = $instance['optin_text_b'];
        $optin_button_text = $instance['optin_button_text'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('optin_text'); ?>"><?php _e('Optin 1st Paragraph:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('optin_text'); ?>" name="<?php echo $this->get_field_name('optin_text'); ?>" type="text" value="<?php echo esc_attr($optin_text); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('optin_text_b'); ?>"><?php _e('Optin 2nd Paragraph (bolded):'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('optin_text_b'); ?>" name="<?php echo $this->get_field_name('optin_text_b'); ?>" type="text" value="<?php echo esc_attr($optin_text_b); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('optin_button_text'); ?>"><?php _e('Opt In Button Text:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('optin_button_text'); ?>" name="<?php echo $this->get_field_name('optin_button_text'); ?>" type="text" value="<?php echo esc_attr($optin_button_text); ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['optin_text'] = (!empty($new_instance['optin_text'])) ? strip_tags($new_instance['optin_text']) : '';
        $instance['optin_text_b'] = (!empty($new_instance['optin_text_b'])) ? strip_tags($new_instance['optin_text_b']) : '';
        $instance['optin_button_text'] = (!empty($new_instance['optin_button_text'])) ? strip_tags($new_instance['optin_button_text']) : '';

        return $instance;
    }
}
add_action('widgets_init', function() {
    register_widget('OptinWidget');
});
