<?php
/*
Template Name: Optin Page
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

				<?php the_content(); ?>

				<?php get_template_part('partials/optin-form'); ?>

        </div>
        <?php endif; ?>
    </div>

<?php get_footer(); ?>
