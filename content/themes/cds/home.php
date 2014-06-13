<?php get_header(); ?>
<!-- home -->
    <div class="main">
        <div class="section-nav">
            <?php get_template_part('partials/breadcrumbs'); ?>

            <h1>Blog</h1>
        </div>
        <div class="content">
        <?php $paged = $wp_query->get('paged'); ?>
        <?php if ($paged < 2 && have_posts()): the_post(); ?>
            <div class="featured">
                <div class="post">
                    <span class="type">Featured</span>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <span class="author"><?php the_author_posts_link(); ?></span><span class="date"><?php the_date(); ?></span>
                    <?php the_excerpt(); ?>
                    <a class="more" href="<?php the_permalink(); ?>">Read More</a>
                </div>
                <div class="image">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail(); ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <div class="primary">
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
                    <div class="meta">
                    <span class="author"><?php the_author_posts_link(); ?></span><span class="date"><?php echo get_the_date(); ?></span>
                    </div>
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
