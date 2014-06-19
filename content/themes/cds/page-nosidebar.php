<?php
/*
Template Name: Page with No Sidebar
*/
?>

<?php get_header(); ?>

    <div class="main">
        <?php if (have_posts()) : the_post(); ?>
        <div class="section-nav">
            <?php get_template_part('partials/breadcrumbs'); ?>

            <h2><?php print get_the_title($post->post_parent); ?></h2>
        </div>
        <div class="content">
            <?php $top_level = get_page($post->post_parent); ?>
            <?php if ($top_level->ID == get_the_ID()): ?>
                <div class="banner">
                    <?php the_post_thumbnail('full'); ?>
                </div>
            <?php else: ?>
                <header>
                    <h1><?php the_title(); ?></h1>
                </header>
            <?php endif; ?>

            <div class="primary-wide" role="main">

				<?php the_content(); ?>

            </div>

        </div>
        <?php endif; ?>
    </div>

<?php get_footer(); ?>
