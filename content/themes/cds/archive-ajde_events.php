<?php
get_header();

global $post;

$post_type = get_query_var('post_type');
$post_type_obj = get_post_type_object($post_type);

$post = get_page_by_path($post_type_obj->rewrite['slug']);
setup_postdata($post);
?>
<!-- archive-event -->
<script type="text/javascript">
    $(document).ready(function () {
        $(".eventon_list_event .no_events").parent().parent().prev().hide();
        $(".eventon_list_event .no_events").parent().parent().hide();
    }
</script>
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
        <?php
        if( function_exists('add_eventon')) {
                $args = array(
                        'show_upcoming'         => 1,
                        'number_of_months'      => 12,
                        'cal_id'    => '1',
                        'hide_empty_months'  => 1,
                );
                add_eventon($args); 
        }
        ?>
        <nav class="pagination">
            <div class="next"><?php next_posts_link('Older posts'); ?></div>
            <div class="previous"><?php previous_posts_link('Newer posts'); ?></div>
        </nav>
    </div>
    <?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
