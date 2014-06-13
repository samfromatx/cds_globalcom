<?php get_header(); ?>

    <div class="main">
        <div class="section-nav">
            <?php get_template_part('partials/breadcrumbs'); ?>

            <h1>Not Found</h1>
        </div>
        <div class="primary">
            <p>It looks like nothing was found at this location. Maybe try a search?</p>

            <?php get_search_form(); ?>
        </div>
        <?php get_sidebar(); ?>
    </div>

<?php get_footer(); ?>
