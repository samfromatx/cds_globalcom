<!-- page-listing -->
<div class="banner">
    <?php the_post_thumbnail('full'); ?>
</div>

<div class="primary" style="width:95%;">
    <?php the_content(); ?>
    <ul class="listing">
        <?php global $children; foreach ($children as $child): ?>
            <li>
                <a class="icon" href="<?php print get_permalink($child->ID); ?>"><?php MultiPostThumbnails::the_post_thumbnail('page', 'icon', $child->ID); ?></a>
                <h3>
                    <a href="<?php print get_permalink($child->ID); ?>">
                        <?php print $child->post_title; ?>
                    </a>
                </h3>
                <p><?php print $child->post_excerpt ?></p>
                <a class="more" href="<?php print get_permalink($child->ID); ?>">Learn more</a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<aside class="sidebarListing sidebar" role="complementary">
    <?php
$cdsdomain = $_SERVER['SERVER_NAME'];
$actual_link = "$_SERVER[REQUEST_URI]";
$parentdir = explode('/', $actual_link);

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
