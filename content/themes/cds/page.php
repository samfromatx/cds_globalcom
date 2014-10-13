<?php get_header(); ?>
<!-- page -->
    <div class="main">
        <?php if (have_posts()) : the_post(); ?>
        <div class="section-nav">
            <?php get_template_part('partials/breadcrumbs'); ?>

            <?php $children = get_pages(array('parent' => $post->ID, 'sort_column' => 'menu_order'));?>
            <?php if (count($children)): ?>
                <h1><?php the_title(); ?></h1>
            <?php else: ?>
                <h2><?php print get_the_title($post->post_parent); ?></h2>
            <?php endif; ?>
        </div>
        <div class="content">
            <?php if (count($children)):
                get_template_part('partials/page', 'listing');
            else:
                get_template_part('partials/page', 'basic');
            endif; ?>
            <?php //get_sidebar(); ?>
        </div>
        <?php endif; ?>
    </div>

<?php get_footer(); ?>
