<?php

class IndustriesWideWidget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'industries_wide_widget',
            __('Industries List Full Screen', 'text_domain'),
            array('description' => __('List of industries that goes across the screen', 'text_domain'))
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
        </div>
        <div class="features" style="margin: 0 !important;">
    <div class="industries widget">
        <h2>We offer solutions that power, connect and simplify your business or nonprofit in these industries</h2>
        <ul>
            <li>
            <div><a href="http://stage.cds-global.com/industries/consumer-products/"><img width="100" height="100" src="http://stage.cds-global.com/content/uploads/2014/11/consumer-products.png" class="attachment-post-thumbnail" alt="consumer-products" /></a><br />
            <a href="http://stage.cds-global.com/industries/consumer-products/">Consumer Products</a></div>
            <p>Convert visitors into customers and customers into brand advocates with effective, turnkey, multichannel eCommerce solutions that drive results and revenues.</p>
        </li>
                        <li>
            <div><a href="http://stage.cds-global.com/industries/media/"><img width="100" height="100" src="http://stage.cds-global.com/content/uploads/2014/11/media.png" class="attachment-post-thumbnail" alt="media" /></a><br />
            <a href="http://stage.cds-global.com/industries/media/">Media</a></div>
            <p>Be a leader in the ever-changing print and digital landscape with innovative, sustainable and highly cost-effective solutions to attract, retain and interact with consumers.</p>
        </li>
                        <li>
            <div><a href="http://stage.cds-global.com/industries/nonprofit/"><img width="100" height="100" src="http://stage.cds-global.com/content/uploads/2014/11/nonprofits.png" class="attachment-post-thumbnail" alt="nonprofit" /></a><br />
            <a href="http://stage.cds-global.com/industries/nonprofit/">Nonprofit</a></div>
            <p>Strategically collect, analyze and use data to your advantage to target communications and stand out in the crowd, turning prospects into donors and donors into lifelong supporters.</p>
        </li>
                        <li>
            <div><a href="http://stage.cds-global.com/industries/utilities/"><img width="100" height="100" src="http://stage.cds-global.com/content/uploads/2014/11/utilities.png" class="attachment-post-thumbnail" alt="utilities" /></a><br />
            <a href="http://stage.cds-global.com/industries/utilities/">Utilities</a></div>
            <p>Improve customer service, decrease operating costs and boost profitability with solutions designed to meet utilities’ specific payment processing and communication needs.</p>
        </li>
                </ul>
    </div>
    <div class="features">
<!--
        </div>
        <div class="features" style="margin: 0 !important;">
        <div class="industries widget">
            <h2>We offer solutions that power, connect and simplify your business or nonprofit in these industries</h2>
            <ul>
                <?php while ($query->have_posts()): $query->the_post(); ?>
                    <li>
                        <div><?php MultiPostThumbnails::the_post_thumbnail('page', 'icon', $post->ID); ?><br />
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
                        <p><?php echo get_the_excerpt(); ?></p>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
        <div class="features">-->
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
    register_widget('IndustriesWideWidget');
});
