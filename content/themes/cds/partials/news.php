<article class="primary">
    <?php the_content(); ?>
    <?php if ( comments_open() || get_comments_number() ) {
        comments_template();
    } ?>
</article>
