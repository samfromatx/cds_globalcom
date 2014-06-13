<!-- page-listing -->
<div class="banner">
    <?php the_post_thumbnail('full'); ?>
</div>

<div class="primary">
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
