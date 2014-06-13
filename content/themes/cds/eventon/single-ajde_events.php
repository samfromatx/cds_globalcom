<?php get_header(); ?>
<!-- Eventon -->
    <div class="main">
    <?php if (have_posts()) : the_post(); ?>
        <div class="section-nav">
            <?php get_template_part('partials/breadcrumbs'); ?>

            <h2><?php echo get_post_type_object($post->post_type)->labels->name; ?></h2>
        </div>
        <div class="content">
            <div <?php post_class(); ?>>
                <?php if (get_post_type() == 'resource'): ?>
                    <header class="has-icon">
                        <i class="icon"></i>
                        <?php $type = reset(get_the_terms(0, 'resource_type')); ?>
                        <h2><?php echo $type->name; ?></h2>
                    </header>
                <?php else: ?>
                    <header>
                        <h1><?php the_title(); ?></h1>
                    </header>
                <?php endif; ?>
                <article class="primary">
                	<?php eventon_se_page_content(); ?>
				</article>
            </div>
            <?php
                get_sidebar();
            ?>
        </div>
    <?php endif; ?>
    </div>

<?php get_footer(); ?>