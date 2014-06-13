<?php get_header(); ?>

<div class="main">
    <div class="section-nav">
        <?php get_template_part('partials/breadcrumbs'); ?>

        <h2>Search</h2>
    </div>

    <div class="content">
        <header>
            <h1><?php printf('Results for "%s"', get_search_query()); ?></h1>
        </header>
    <div class="primary">
        <?php while (have_posts()): the_post(); ?>
            <div class="list">
                <div class="post">
                    <h3><?php the_title(); ?></h3>
                    <?php the_excerpt(); ?>
                    <a href="<?php the_permalink(); ?>">Read More</a>
                </div>
            </div>
        <?php endwhile; ?>
        <nav>
            <div class="next"><?php next_posts_link('More Results'); ?></div>
            <div class="previous"><?php previous_posts_link('Previous'); ?></div>
        </nav>
    </div>
    <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>
