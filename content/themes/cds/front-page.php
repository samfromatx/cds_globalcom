<!-- front-page -->
<?php get_header(); ?>
<h1 class="hidden">CDS Global - Home</h1>
<?php the_post(); ?>
<?php the_content(); ?>
<div class="features">
    <?php dynamic_sidebar('cds-homepage'); ?>
</div>
<?php get_footer(); ?>
