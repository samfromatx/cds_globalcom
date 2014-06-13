<?php

class CDSTwitterWidget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'cds_twitter_widget',
            __('CDS Twitter Feed', 'text_domain'),
            array('description' => __('Display the @CDSGlobal twitter feed', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        ?>
            <div class="widget single twitter">
                <a class="twitter-timeline" href="https://twitter.com/search?q=from%3ACDSGlobal+OR+from%3ACDSGlobalNP+OR+from%3ACDSGlobalEurope" data-widget-id="416656998772396032">Tweets</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
            </div>
        <?php
    }
}
add_action('widgets_init', function() {
    register_widget('CDSTwitterWidget');
});
