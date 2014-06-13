<!-- partials post -->
<article class="primary">
    <div class="meta">
    <?php echo get_avatar(get_the_author_meta('user_email'), 50); ?>
    <span class="author"><?php the_author_posts_link(); ?></span><span class="date"><?php the_date(); ?></span>
    </div>
    <?php the_content(); ?>
    <?php if ( comments_open() || get_comments_number() ) {
        comments_template();
    } ?>
</article>
