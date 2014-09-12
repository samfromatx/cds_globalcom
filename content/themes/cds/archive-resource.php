<?php
get_header();

global $post;

$post_type = get_query_var('post_type');
$post_type_obj = get_post_type_object($post_type);

$post = get_page_by_path($post_type_obj->rewrite['slug']);
setup_postdata($post);
?>

<?php get_header(); ?>

    <div class="main">
        <div class="section-nav">
            <?php get_template_part('partials/breadcrumbs'); ?>

            <h1><?php the_title(); ?></h1>
        </div>
        <div class="banner">
            <?php the_post_thumbnail('full'); ?>
        </div>
        <div class="primary">
            <?php
                $filters = array(
                    'type' => array(
                        'label' => 'All Resource Types',
                        'items' => array_reduce(
                            get_terms('resource_type'),
                            function ($result, $item) {
                                $result[$item->slug] = $item->name;
                                return $result;
                            },
                            array()
                        ),
                    ),
                    'industry' => array(
                        'label' => 'All Industries',
                        'items' => array_reduce(
                            get_pages(array('parent' => get_page_by_path('industries')->ID)),
                            function ($result, $item) {
                                $result[$item->post_name] = $item->post_title;
                                return $result;
                            },
                            array()
                        ),
                    ),
                );

                $filter_params = array(
                    'type' => null,
                    'industry' => null,
                );
                if (isset($_GET['type']))
                    $filter_params['type'] = $_GET['type'];
                if (isset($_GET['industry']))
                    $filter_params['industry'] = $_GET['industry'];
            ?>
            <form class="resource-filter">
                <label>Filter by:</label>

                <?php foreach($filters as $name => $filter): ?>
                <div class="dropdown">
                    <label for="<?php echo $name; ?>" class="hidefromscreen"><?php if ($name == "type") {echo("Select Resource Type");} else {echo("Select Industry");} ?></label>
                    <select name="<?php echo $name; ?>" id="<?php echo $name; ?>">
                        <option value=""><?php echo $filter['label']; ?></option>
                        <?php foreach($filter['items'] as $key => $item): ?>
                            <option value="<?php echo $key; ?>" <?php selected($filter_params[$name], $key); ?>>
                                <?php echo $item; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endforeach; ?>

                <?php
                    $filter_params = array_filter($filter_params);
                    if (count($filter_params)): ?>
                        <div class="reset">
                            <a href="/resources">Reset filters</a>
                        </div>
                    <?php endif; ?>
            </form>
            <?php the_content(); ?>
            <?php
                if (have_posts()): ?>
                <ul class="listing resources">

                        <?php

                        if (isset($_GET['industry']) && isset($_GET['type'])) {
                            $query = new WP_Query( array( 'post_type' => 'resource', 'meta_query' => array(	array('key' => 'industry', 'value'   => $_GET['industry'], 'compare' => 'LIKE', ),), 'tax_query' => array( array( 'taxonomy' => 'resource_type', 'field' => 'slug', 'terms' => $_GET['type'], ), ), 'orderby' => array( 'meta_value_num' => 'ASC', 'date' => 'DESC' ), 'meta_key' => 'resource_priority' ) );
                        } elseif (isset($_GET['industry'])) {
                            $query = new WP_Query( array( 'post_type' => 'resource', 'meta_query' => array(	array('key' => 'industry', 'value'   => $_GET['industry'], 'compare' => 'LIKE', ),), 'orderby' => array( 'meta_value_num' => 'ASC', 'date' => 'DESC' ), 'meta_key' => 'resource_priority' ) );
                        } elseif (isset($_GET['type'])) {
                            $query = new WP_Query( array( 'post_type' => 'resource', 'tax_query' => array( array( 'taxonomy' => 'resource_type', 'field' => 'slug', 'terms' => $_GET['type'], ), ), 'orderby' => array( 'meta_value_num' => 'ASC', 'date' => 'DESC' ), 'meta_key' => 'resource_priority' ) );
                        } else {
                            $query = new WP_Query( array( 'post_type' => 'resource', 'orderby' => array( 'meta_value_num' => 'ASC', 'date' => 'DESC' ), 'meta_key' => 'resource_priority' ) );
                        }

                        while ( $query->have_posts() ): $query->the_post();
                            get_template_part('partials/resource', get_post_format());
                        endwhile;

                        /*while (have_posts()): the_post();
                           get_template_part('partials/resource', get_post_format());
                        endwhile;*/

                        /*global $wp_query;
                        //$args = array_merge( $wp_query->query_vars, array( 'meta_key' => 'resource_priority', 'orderby' => 'meta_value date', 'order' => 'ASC DESC') );
                        $query = new WP_Query( array( 'post_type' => 'news', 'orderby' => array( 'meta_value_num' => 'ASC', 'date' => 'DESC' ), 'meta_key' => 'resource_priority' ) );
                        //$query = new WP_Query( array ( 'post_type' => 'resource', 'orderby' => 'date', 'order' => 'ASC' ) );
                        query_posts( $args );

                        while (have_posts()): the_post();
                            get_template_part('partials/resource', get_post_format());
                        endwhile;*/

                        //wp_reset_query(); ?>
                </ul>
                <nav class="pagination">
                    <div class="previous"><?php previous_posts_link('Previous page'); ?></div>
                    <div class="next"><?php next_posts_link('Next page'); ?></div>
                    <div class="pages">Page <?php echo get_query_var('paged') ? get_query_var('paged') : 1; ?> of <?php echo $GLOBALS['wp_query']->max_num_pages; ?></div>
                </nav>
                <?php else: ?>
                    <p>Sorry, there are no resources available for your selected filters.</p>
                <?php endif;
            ?>
        </div>
        <?php get_sidebar(); ?>
    </div>

<?php get_footer(); ?>
