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

        <h1><?php the_title(); ?></h1>
    </div>

    <?php if ($banner = get_the_post_thumbnail(get_the_ID(), 'full')): ?>
    <div class="banner">
        <?php echo $banner; ?>
    </div>
    <?php endif; ?>
    <div class="primary">
        <?php while (have_posts()): the_post(); ?>
            <div class="list">
                <div class="image">
                        <?php the_post_thumbnail(); ?>
                    </div>
                <div class="post">
                    <h3><?php the_title(); ?></h3>                    
                    <div class="meta">
                    <?php $post_meta = get_post_meta(get_the_ID());
                        if ($post_meta['event_date'][0]): ?>
                            <span class="date"><?php echo date('M j, Y', strtotime($post_meta['event_date'][0])); ?></span>
                        <?php endif;
                        if ($post_meta['event_location'][0]): ?>
                            <span class="location"><?php echo $post_meta['event_location'][0]; ?></span>
                        <?php endif; ?>
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
    <?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
