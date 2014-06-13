<?php
get_header();

global $post;

$post_type = get_query_var('post_type');
$post_type_obj = get_post_type_object($post_type);

$post = get_page_by_path($post_type_obj->rewrite['slug']);
setup_postdata($post);
?>

<div class="main">
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
        <div class="primary">
            <nav>
                <ul>
                    <?php $children = get_pages(array('parent' => $top_level->ID, 'sort_column' => 'menu_order'));
                    foreach ($children as $child): ?>
                        <li <?php echo is_page($child->ID) ? 'class="active"' : '' ?>><a href="<?php echo get_permalink($child->ID); ?>"><?php echo get_the_title($child->ID); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <div class="content">
                <?php while (have_posts()): the_post(); ?>
                    <div class="list">                        
                        <div class="post">
                            <h3><?php the_title(); ?></h3>
                            <div class="image">
                                <?php the_post_thumbnail(); ?>
                            </div>
                            <?php the_excerpt(); ?>
                            <a href="<?php the_permalink(); ?>">Read More</a>
                        </div>
                    </div>
                <?php endwhile; ?>
                <nav class="pagination">
                    <div class="next"><?php next_posts_link('Older posts'); ?></div>
                    <div class="previous"><?php previous_posts_link('Newer posts'); ?></div>
                </nav>
            </div>
        </div>
        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>
