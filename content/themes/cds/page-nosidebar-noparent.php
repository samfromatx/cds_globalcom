<?php
/*
Template Name: Page with No Sidebar and No Parent
*/
?>

<?php get_header(); ?>

    <div class="main">
        <?php if (have_posts()) : the_post(); ?>
        <div class="section-nav">
            <?php get_template_part('partials/breadcrumbs'); ?>

             <h1><?php the_title(); ?></h1>
        </div>
        <div class="content">

            <div class="primary-wide" role="main">

				<?php the_content(); ?>

            </div>

        </div>
        <?php endif; ?>
    </div>

<?php get_footer(); ?>
