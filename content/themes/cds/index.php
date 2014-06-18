<?php get_header(); ?>
<!-- index -->
    <div class="main">
        <div class="section-nav">
            <?php get_template_part('partials/breadcrumbs'); ?>

            <h1>Blog:
                <?php
                    if (is_category())
                        single_cat_title();
                    if (is_tag())
                        single_tag_title();
                    if (is_author()) {
                        the_post();
                        the_author();
                        rewind_posts();
                    }
                ?>
            </h1>
        </div>
        <div class="content">
        <div class="primary" role="main">
        <?php while (have_posts()): the_post(); ?>
            <?php if ($image = get_the_post_thumbnail()): ?>
            <div class="list has-image">
                <div class="image">
                    <a href="<?php the_permalink(); ?>"><?php echo $image; ?></a>
                </div>
            <?php else: ?>
            <div class="list">
            <?php endif; ?>
                <div class="post">
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <span class="author"><?php the_author_posts_link(); ?></span><span class="date"><?php the_date(); ?></span>
                    <?php the_excerpt(); ?>
                    <a class="more" href="<?php the_permalink(); ?>">Read More</a>
                </div>
            </div>
        <?php endwhile; ?>
        <nav class="pagination">
            <div class="next"><?php next_posts_link('Older posts'); ?></div>
            <div class="previous"><?php previous_posts_link('Newer posts'); ?></div>
        </nav>
        </div>
        <?php get_sidebar('blog'); ?>
        </div>
    </div>

<?php get_footer(); ?>
