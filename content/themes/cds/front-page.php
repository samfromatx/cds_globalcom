<!-- front-page -->
<?php get_header(); ?>
<h1 class="hidden">CDS Global - Home</h1>
<?php the_post(); ?>
<?php the_content(); ?>
<div class="features">
    <?php dynamic_sidebar('cds-homepage'); ?>
</div>
    <?php
/*
        $parent = get_page_by_title('industries');
        $query = new WP_Query(array(
            'post_type' => 'page',
            'post_parent' => $parent->ID,
            'order' => 'ASC',
        ));

        if ($query->have_posts()):*/ ?>

        <div class="industries-banner">
            <h2>Solutions that power, connect and simplify</h2>
            <?php include(locate_template('partials/industry_hp_wide.php')); ?>
        </div>

        <?php /*endif;

        wp_reset_query();
*/
        ?>

<?php get_footer(); ?>
