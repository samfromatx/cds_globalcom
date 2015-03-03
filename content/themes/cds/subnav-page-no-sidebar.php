<?php
/*
Template Name: Page with Sub-Navigation menu and No Sidebar
*/
?>

<?php get_header(); ?>

    <div class="main">
        <?php if (have_posts()) : the_post(); ?>
        <div class="section-nav">
            <?php get_template_part('partials/breadcrumbs'); ?>
            <?php $top_level = get_page($post->post_parent); ?>
            <?php if ($top_level->ID == get_the_ID()): ?>
                <h1><?php print get_the_title($post->post_parent); ?></h1>
            <?php else: ?>
                <h2><?php print get_the_title($post->post_parent); ?></h2>
            <?php endif; ?>
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

            <div class="primary-wide">
                <nav>
                    <ul>
                        <?php $children = get_pages(array('parent' => $top_level->ID, 'sort_column' => 'menu_order'));
                        foreach ($children as $child): ?>
                            <li <?php echo is_page($child->ID) ? 'class="active"' : '' ?>><a href="<?php echo get_permalink($child->ID); ?>"><?php echo get_the_title($child->ID); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>

                <div class="content">
                    <?php the_content(); ?>
                </div>
            </div>

        </div>
        <?php endif; ?>
    </div>

<?php get_footer(); ?>
