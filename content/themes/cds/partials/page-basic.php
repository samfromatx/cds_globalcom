<!-- page-basic -->
<header class="has-icon">
    <div class="icon"><?php MultiPostThumbnails::the_post_thumbnail('page', 'icon'); ?></div>
    <h1><?php the_title(); ?></h1>
</header>
<article class="primary">
    <?php the_content(); ?>
    <?php
    $solutiontitle = the_title();
    $featured = get_post_meta(get_the_ID(), 'featured_resource', true);
    $featuredarray = explode(',', $featured);

    $temp = $post;
    $featuredquery = new WP_Query( array( 'post_type' => 'resource', 'post__in' => $featuredarray, 'orderby' => 'menu_order title', 'order' => 'ASC' ) );
    if ($featuredquery->have_posts()): ?>
        <ul class="listing featured resources">
            <?php while ($featuredquery->have_posts()) {
                $featuredquery->the_post(); ?>
               <li <?php post_class(); ?>>
                    <a class="icon ifeatured" href="<?php the_permalink(); ?>"><i class="icon ifeatured"></i></a>
                    <h3>
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <?php the_excerpt(); ?>
                </li>
            <?php } ?>
        </ul>
    <?php endif;
    wp_reset_postdata();
    $post = $temp;
    ?>

    <?php
    $filters = get_post_meta(get_the_ID(), 'resource_filters', true);
    if ($filters && $filters = array_filter($filters)):
        $resource_params = array(
            'post_type' => 'resource',
            'nopaging' => true,
            // excluded posts that are in featured section
            'post__not_in' => explode(',', $featured),
        );
        if ($filters['type'])
            $resource_params['resource_type'] = $filters['type'];
        if ($filters['industry'])
            $resource_params['meta_query'][] = array(
                'key' => 'industry',
                'value' => $filters['industry'],
                'compare' => 'LIKE',
            );
        if ($filters['solution'])
            $resource_params['meta_query'][] = array(
                'key' => 'solution',
                'value' => $filters['solution'],
                'compare' => 'LIKE',
            );

        $query = new WP_Query($resource_params);
        if ($query->have_posts()): ?>
            <h3>Other Resources</h3>
            <form class="resource-filter inline">
                <label>Filter by:</label>
                <div class="dropdown">
                    <label for="formresourceTypes" class="hidefromscreen">Select Resource Type:</label>
                    <select name="type" id="formresourceTypes">
                        <option value="">All Resource Types</option>
                        <?php foreach (get_terms('resource_type') as $term): ?>
                            <option value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
            <ul class="listing resources">
                    <?php while ($query->have_posts()) {
                        $query->the_post();
                        // Don't show more than 5 posts in this loop, we'll hide them in a separate container below
                        if ($query->current_post > 4) break;
                        get_template_part('partials/resource', get_post_format());
                    } ?>
            </ul>
            <div class="pagination">
                <div class="next">
                    <a href="#more">Show more resources</a>
                </div>
            </div>
            <ul class="listing resources more">
                    <?php while ($query->have_posts()) {
                        $query->the_post();
                        get_template_part('partials/resource', get_post_format());
                    } ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
</article>
