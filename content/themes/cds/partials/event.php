<!-- partial event -->
<article class="primary">
    <div class="meta">
    <?php $post_meta = get_post_meta(get_the_ID());
        if ($post_meta['event_date'][0]): ?>
            <!--<span class="date"><?php echo date('M j, Y', strtotime($post_meta['event_date'][0])); ?></span>
        <?php endif;
        if ($post_meta['event_location'][0]): ?>
            <span class="location"><?php echo $post_meta['event_location'][0]; ?>Test</span>-->
        <?php endif; ?>
    </div>
    <?php the_content(); ?>
    <?php if ( comments_open() || get_comments_number() ) {
        comments_template();
    } ?>
</article>
