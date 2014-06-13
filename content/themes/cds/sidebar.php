<aside class="secondary sidebar">
    <?php
        // If this is a leaf page, include the CTA buttons
        global $children;
        if (isset($children) && count($children) == 0): ?>
            <div class="widget cta">
                <a href="/about/contact/?sbj=quote"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/get-a-quote.png" alt="Request a quote"></a>
            </div>
            <div class="widget cta">
                <a href="/about/contact/?sbj=demo"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/schedule-a-demo.png" alt="Request a demo"></a>
            </div>
        <?php endif;

        // See if the current page or the parent has a custom industry sidebar
        // Otherwise fall back to the default one.
        $actual_link = "$_SERVER[REQUEST_URI]";
        $parentdir = explode('/', $actual_link);

        //$sidebar = "industry-{$post->post_name}";
        $sidebar = "industry-$parentdir[2]";
        if (!is_active_sidebar($sidebar)) {
            $parent = get_post($post->post_parent);
            $sidebar = "industry-{$parent->post_name}";
            if (!is_active_sidebar($sidebar))
                $sidebar = 'cds-global-sidebar';
        }
        dynamic_sidebar($sidebar);
    ?>
</aside>
