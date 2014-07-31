<?php

include 'widgets/single-post.php';
include 'widgets/latest-post.php';
include 'widgets/upcoming-events.php';
include 'widgets/industries.php';
include 'widgets/locations.php';
include 'widgets/optin.php';
include 'widgets/media-post.php';
include 'widgets/nonprofit-post.php';
include 'widgets/twitter.php';
include 'widgets/youtube.php';
include 'widgets/vimeo.php';

function cds_breadcrumbs($post_id = null) {
    if (!$post_id)
        $post_id = get_the_ID();

    $crumbs = array();
    $post = get_post($post_id);
    while($post_id = $post->post_parent) {
        $post = get_post($post_id);
        $crumbs[get_permalink($post)] = get_the_title($post);
    }
    if ($post->post_type && !is_404()) {

        if ($post->post_type == 'post') {
            if (!is_home())
                $slug = 'blog';
        } else {
            $type = get_post_type_object($post->post_type);
            $slug = $type->rewrite['slug'];
        }
        if ($slug) {
            while ($slug) {
                $list_page = get_page_by_path($slug);
                if ($list_page)
                    $crumbs[get_permalink($list_page)] = $list_page->post_title;

                $parts = explode('/', $slug);
                array_pop($parts);
                $slug = implode('/', $parts);
            }
        }
            echo $slug;
    }
    $crumbs['/'] = 'Home';

    return array_reverse($crumbs);
}

function cds_setup() {
    add_theme_support('post-thumbnails');
    add_theme_support('custom-header');
    set_post_thumbnail_size(500, 250, false);

    register_nav_menu('primary', 'Navigation Menu');
    register_nav_menu('footer', 'Footer Menu');
}
add_action('after_setup_theme', 'cds_setup');

function cds_init() {
    add_post_type_support('page', 'excerpt');

    if (class_exists('MultiPostThumbnails')) {
        new MultiPostThumbnails(array(
            'label' => 'Icon',
            'id' => 'icon',
            'post_type' => 'page',
        ));
    }

    register_post_type(
        'news',
        array(
            'labels' => cds_get_post_type_labels('News', 'News'),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-megaphone',
            'rewrite' => array('slug' => 'about/news', 'with_front' => false),
            'supports' => array(
                'title',
                'author',
                'editor',
                'excerpt',
                'thumbnail',
            ),
        )
    );

    register_post_type(
        //'event',
        'ajde_events',
        array(
            'labels' => cds_get_post_type_labels('Event', 'Events'),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'events', 'with_front' => false),
            'supports' => array(
                'title',
                'author',
                'editor',
                'thumbnail',
                'excerpt',
            ),
        )
    );

    /*register_post_type(
        'event',
        array(
            'labels' => cds_get_post_type_labels('Event2', 'Events2'),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'events2', 'with_front' => false),
            'supports' => array(
                'title',
                'editor',
                'thumbnail',
                'excerpt',
            ),
        )
    );*/

    register_post_type(
        'resource',
        array(
            'labels' => cds_get_post_type_labels('Resource', 'Resources'),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-media-text',
            'rewrite' => array('slug' => 'resources', 'with_front' => false),
            'supports' => array(
                'title',
                'author',
                'editor',
                'thumbnail',
                'excerpt',
            ),
            'taxonomies' => array('resource_type'),
            'yarpp_support' => true,
        )
    );
    register_taxonomy('resource_type', 'resource', array(
        'public' => false,
    ));
    wp_insert_term('Overview', 'resource_type', array('slug' => 'resource-overview'));
    wp_insert_term('Benchmarking Report', 'resource_type', array('slug' => 'benchmarking-report'));
    wp_insert_term('Forward Report', 'resource_type', array('slug' => 'forward-report'));
    wp_insert_term('Data Sheet', 'resource_type', array('slug' => 'data-sheet'));
    wp_insert_term('Case Study', 'resource_type', array('slug' => 'case-study'));
    wp_insert_term('Video', 'resource_type', array('slug' => 'video'));
    wp_insert_term('Webinar', 'resource_type', array('slug' => 'webinar'));
    wp_insert_term('White Paper', 'resource_type', array('slug' => 'white-paper'));
    wp_insert_term('Executive Summary', 'resource_type', array('slug' => 'executive-summary'));
    wp_insert_term('FAQ', 'resource_type', array('slug' => 'faq'));
    wp_insert_term('Demo', 'resource_type', array('slug' => 'demo'));
    wp_insert_term('Assessment Tool', 'resource_type', array('slug' => 'assessment-tool'));
    wp_insert_term('Infographic', 'resource_type', array('slug' => 'infographic'));
    wp_insert_term('Other', 'resource_type', array('slug' => 'other'));
}
add_action('init', 'cds_init');

function cds_get_post_type_labels($singular, $plural, $overrides = array()) {
    return array(
        'name' => __($plural),
        'singular_name' => __($singular),
        'add_new_item' => __("Add New $singular"),
        'edit_item' => __("Edit $singular"),
        'new_item' => __("New $singular"),
        'view_item' => __("View $singular"),
        'search_items' => __("Search $plural"),
        'not_found' => __("No ".lcfirst($plural)." found"),
        'not_found_in_trash' => __("No ".lcfirst($plural)." found in Trash"),
    ) + $overrides;
}

function cds_add_metaboxes() {
    add_meta_box('resource_priority', 'Resource Priority', 'cds_resource_priority_metabox', 'resource', 'side', 'core');
    add_meta_box('resource_type', 'Type', 'cds_resource_type_metabox', 'resource', 'side', 'core');
    add_meta_box('resource_industry', 'Industry', 'cds_resource_industry_metabox', 'resource', 'side', 'core');
    add_meta_box('resource_solution', 'Solution', 'cds_resource_solution_metabox', 'resource', 'side', 'core');
    eloqua_forms_add_support('resource');

    add_meta_box('event_date', 'Date', 'cds_event_date_metabox', 'event', 'side', 'default');
    add_meta_box('event_location', 'Location', 'cds_event_location_metabox', 'event', 'side', 'default');

    add_meta_box('resource_filter', 'Resources', 'cds_resource_filter_metabox', 'page', 'side', 'default');

    add_meta_box('twitter_cookie', 'Twitter Tailored Audience ID', 'cds_twitter_cookie_metabox', 'page', 'side', 'default');
    add_meta_box('twitter_cookie', 'Twitter Tailored Audience ID', 'cds_twitter_cookie_metabox', 'resource', 'side', 'default');
    add_meta_box('twitter_cookie', 'Twitter Tailored Audience ID', 'cds_twitter_cookie_metabox', 'ajde_events', 'side', 'default');
}
add_action('add_meta_boxes', 'cds_add_metaboxes');

function cds_resource_type_metabox($resource) {
    $types = get_terms('resource_type', array('hide_empty' => 0));

    $selected_types = get_the_terms($resource->ID, 'resource_type');
    if ($selected_types)
        $selected = wp_list_pluck($selected_types, 'term_id');
    else
        $selected = false;

    $select = '<select name="tax_input[resource_type]" class="widefat">';
    foreach ($types as $type) {
        $select .= "<option value=\"{$type->slug}\"".array_selected($selected, $type->term_id, false).">{$type->name}</option>";
    }
    $select .= '</select>';

    echo $select;
}

function cds_resource_industry_metabox($resource) {
    $industry_page = get_page_by_path('industries');
    $industries = get_pages(array('parent' => $industry_page->ID));

    $selected = get_post_meta($resource->ID, 'industry', true);

    $select = '<select name="industry[]" class="widefat" multiple>';
    foreach ($industries as $industry) {
        $select .= "<option value=\"{$industry->post_name}\"".array_selected($selected, $industry->post_name, false).">{$industry->post_title}</option>";
    }
    $select .= '<option value="other"'.array_selected($selected, 'other', false).'>Other</option>';
    $select .= '</select>';
    echo $select;
}

function cds_resource_solution_metabox($resource) {
    $solution_page = get_page_by_path('solutions');
    $solutions = get_pages(array('parent' => $solution_page->ID));

    $selected = get_post_meta($resource->ID, 'solution', true);

    $select = '<select name="solution[]" class="widefat" multiple>';
    foreach ($solutions as $solution) {
        $select .= "<option value=\"{$solution->post_name}\"".array_selected($selected, $solution->post_name, false).">{$solution->post_title}</option>";
    }
    $select .= '</select>';
    echo $select;
}

function cds_resource_priority_metabox($resource) {

    $values = get_post_custom( $resource->ID );
    $selected = isset( $values['resource_priority'] ) ? esc_attr( $values['resource_priority'][0] ) : "3";
    ?>
    <select name="resource_priority" id="resource_priority" class="widefat">
        <option value="1" <?php selected( $selected, '1' ); ?>>Very High</option>
        <option value="2" <?php selected( $selected, '2' ); ?>>High</option>
        <option value="3" <?php selected( $selected, '3' ); ?>>Medium</option>
        <option value="3" <?php selected( $selected, '4' ); ?>>Low</option>
        <option value="3" <?php selected( $selected, '5' ); ?>>Very Low</option>
    </select>
    <?php
}

function cds_save_resource_meta($resource_id) {
    if ($_POST['post_type'] != 'resource')
        return;

    if (isset($_REQUEST['industry'])) {
        $values  = array_map('sanitize_text_field', $_REQUEST['industry']);
        update_post_meta($resource_id, 'industry', $values);
    }

    if (isset($_REQUEST['solution'])) {
        $values  = array_map('sanitize_text_field', $_REQUEST['solution']);
        update_post_meta($resource_id, 'solution', $values);
    }

     if( isset( $_POST['resource_priority'] ) )
        update_post_meta( $resource_id, 'resource_priority', esc_attr( $_POST['resource_priority'] ) );
}
add_action('save_post', 'cds_save_resource_meta');


function cds_resource_filter_metabox($page) {
    $selected = get_post_meta($page->ID, 'resource_filters', true);
    if (!$selected)
        $selected = array();

    $types = get_terms('resource_type', array('hide_empty' => 0));
    $industry_page = get_page_by_path('industries');
    $industries = get_pages(array('parent' => $industry_page->ID));
    $solution_page = get_page_by_path('solutions');
    $solutions = get_pages(array('parent' => $solution_page->ID));

    echo '<p>Include resources matching these filters</p>';

    $select = '<p><select name="resource_type" class="widefat"><option value="">Type</option>';
    foreach ($types as $type) {
        $select .= "<option value=\"{$type->slug}\"".selected($selected['type'], $type->slug, false).">{$type->name}</option>";
    }
    $select .= '</select></p>';

    echo $select;

    $select = '<p><select name="industry" class="widefat"><option value="">Industry</option>';
    foreach ($industries as $industry) {
        $select .= "<option value=\"{$industry->post_name}\"".selected($selected['industry'], $industry->post_name, false).">{$industry->post_title}</option>";
    }
    $select .= '<option value="other"'.array_selected($selected, 'other', false).'>Other</option>';
    $select .= '</select></p>';
    echo $select;

    $select = '<p><select name="solution" class="widefat"><option value="">Solution</option>';
    foreach ($solutions as $solution) {
        $select .= "<option value=\"{$solution->post_name}\"".selected($selected['solution'], $solution->post_name, false).">{$solution->post_title}</option>";
    }
    $select .= '</select></p>';
    echo $select;
}

function cds_save_page_meta($page_id) {
    if ($_POST['post_type'] != 'page')
        return;

    $meta = array();
    if (isset($_REQUEST['resource_type']))
        $meta['type'] = $_REQUEST['resource_type'];
    if (isset($_REQUEST['industry']))
        $meta['industry'] = $_REQUEST['industry'];
    if (isset($_REQUEST['solution']))
        $meta['solution'] = $_REQUEST['solution'];

    update_post_meta($page_id, 'resource_filters', $meta);
}
add_action('save_post', 'cds_save_page_meta');

function cds_event_date_metabox($event) {
    $custom = get_post_custom($event->ID);
    $date = $custom['event_date'][0]
    //list($month, $day, $year) = unserialize($custom['event_date'][0]);
    ?>
    <input class="widefat" type="text" name="event_date" value="<?php echo $date; ?>" />
    <?php
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css');
    wp_enqueue_script('event-datepicker', get_template_directory_uri().'/javascripts/event-datepicker.js', 'jquery-ui-datepicker', false, true);
}

function cds_event_location_metabox($event) {
    $custom = get_post_custom($event->ID);
    $location = $custom['event_location'][0];
    ?>
    <input class="widefat" type="text" name="event_location" value="<?php echo $location; ?>" />
    <?php
}

function cds_save_event_meta($event_id) {
    if ($_POST['post_type'] != 'event')
        return;

    if (isset($_REQUEST['event_date'])) {
        $date = strtotime($_REQUEST['event_date']);
        $date = date('Y-m-d', $date);
        update_post_meta($event_id, 'event_date', sanitize_text_field($date));
    }
    if (isset($_REQUEST['event_location']))
        update_post_meta($event_id, 'event_location', sanitize_text_field($_REQUEST['event_location']));
}
add_action('save_post', 'cds_save_event_meta');

function cds_twitter_cookie_metabox($post) {

        $values = get_post_custom( $post->ID );
         $text = $values['twitter_cookie'][0];
    ?>
    <input class="widefat" type="text" name="twitter_cookie" value="<?php echo $text; ?>" />
    <?php
}
function cds_save_twitter_cookie_meta($page_id) {

    if (isset($_REQUEST['twitter_cookie']))
        update_post_meta($page_id, 'twitter_cookie', sanitize_text_field($_REQUEST['twitter_cookie']));

}
add_action('save_post', 'cds_save_twitter_cookie_meta');

function cds_nav_menu_args($args = array()) {
    // div is the default, override that to nav
    if ($args['container'] == 'div')
        $args['container'] = 'nav';
    return $args;
}
add_filter('wp_nav_menu_args', 'cds_nav_menu_args');

function cds_resource_post_class($classes) {
    global $post;
    $terms = get_the_terms($post->ID, 'resource_type');
    if ($terms) {
        $term = array_pop($terms);
        $classes = array('resource', $term->slug);
    }
    return $classes;
}
add_filter('post_class', 'cds_resource_post_class');

function cds_blog_body_class($classes) {
    if (is_archive() && !is_post_type_archive())
        $classes[] = 'blog';
    return $classes;
}
add_filter('body_class', 'cds_blog_body_class');

function cds_widgets_init() {
    $defaults = array(
        'before_widget' => '<div class="widget">',
        'after_widget' => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    );
    register_sidebar(array(
        'name' => 'Global Sidebar',
        'id' => 'cds-global-sidebar',
    ) + $defaults);

    register_sidebar(array(
        'name' => 'Homepage',
        'id' => 'cds-homepage',
    ) + $defaults);

    register_sidebar(array(
        'name' => 'Blog',
        'id' => 'cds-blog',
    ) + $defaults);

    // Separate widget config for each industry page
    $industry_page = get_page_by_path('industries');
    $industries = get_pages(array('parent' => $industry_page->ID));

    foreach ($industries as $industry) {
        register_sidebar(array(
            'name' => $industry->post_title,
            'id' => "industry-{$industry->post_name}",
        ));
    }
}
add_action('widgets_init', 'cds_widgets_init');

function array_selected($selected, $current, $echo = true) {
    if (is_array($selected)) {
        foreach ($selected as $item) {
            if ($s = selected($item, $current, $echo))
                return $s;
        }
    }
}

function strip_download_text_from_excerpt($excerpt) {
    return str_ireplace('Download now', '', $excerpt);
}
add_filter('get_the_excerpt', 'strip_download_text_from_excerpt');

function cds_homepage_gallery($output, $attr) {
    if (!is_front_page())
        return $output;

    $_attachments = get_posts( array('include' => $attr['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'post__in') );

    $attachments = array();
    foreach ( $_attachments as $key => $val ) {
        $attachments[$val->ID] = $_attachments[$key];
    }

    $output = '<div class="banner owl-carousel owl-theme">';
    foreach ($attachments as $id => $attachment) {
        $output .= '<div class="item" data-content-width="1400">';
        $output .= wp_get_attachment_image($id, 'full');
        $output .= '</div>';
    }
    $output .= '</div>';

    return $output;
}
add_filter('post_gallery', 'cds_homepage_gallery', 10, 2);

function resources_get_posts($query) {
    if (!(is_post_type_archive('resource') && $query->is_main_query()))
        return;

    $query->set('posts_per_page', 20);

    if ($_GET['type'])
        $query->set('resource_type', $_GET['type']);
    if ($_GET['industry']) {
        $query->set('meta_query', array(array(
            'key' => 'industry',
            'value' => $_GET['industry'],
            'compare' => 'LIKE',
        )));
    }
}
add_action('pre_get_posts', 'resources_get_posts');

function add_sticky_to_custom_posts_archive($posts) {
    global $wp_query;

    $sticky_posts = get_option('sticky_posts');

    if (is_post_type_archive() && is_main_query() && $wp_query->get('paged') <= 1 && !empty($sticky_posts)) {
        $num_posts = count($posts);
        $sticky_offset = 0;

        for ($i = 0; $i < $num_posts; $i++) {
            if (in_array($posts[$i]->ID, $sticky_posts)) {
                $sticky_post = $posts[$i];
                array_splice($posts, $i, 1);
                array_splice($posts, $sticky_offset, 0, array($sticky_post));
                $sticky_offset++;

                $offset = array_search($sticky_post->ID, $sticky_posts);
                unset($sticky_posts[$offset]);
            }
        }

        if (!empty($sticky_posts)) {
            $stickies = get_posts(array(
                'post__in' => $sticky_posts,
                'post_type' => $wp_query->query_vars['post_type'],
                'post_staus' => 'publish',
                'nopaging' => true,
            ));

            foreach ($stickies as $sticky_post) {
                array_splice($posts, $sticky_offset, 0, array($sticky_post));
                $sticky_offset++;
            }
        }
    }

    return $posts;
}
add_action('the_posts', 'add_sticky_to_custom_posts_archive');

function reorder_events($query) {
    if (!(is_post_type_archive('event') && $query->is_main_query()))
        return;

    $query->set('meta_key', 'event_date');
    $query->set('orderby', 'meta_value');
    $query->set('order', 'ASC');
}
add_action('pre_get_posts', 'reorder_events');

add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
    //if (!current_user_can('administrator') && !is_admin()) {
      show_admin_bar(false);
    //}
}

add_action( 'admin_init', 'wps_cpt_support' );
function wps_cpt_support() {
    remove_post_type_support( 'ajde_events', 'comments' );
}

add_action( 'wp_enqueue_scripts', 'load_dashicons_front_end' );
function load_dashicons_front_end() {
    wp_enqueue_style( 'dashicons' );
}

apply_filters( 'wpseo_sitemap_page-sitemap_change_freq', 'daily', 'http://www.cds-global.com/' );

add_action('init', 'setUTMCookie');
function setUTMCookie() {
    if (!$_COOKIE["utmcampaign"] || $_GET['utm_campaign']) {
        setcookie('utmcampaign', $_GET['utm_campaign'], time()+3600*1, COOKIEPATH, COOKIE_DOMAIN); //For 1 hours
    }
    if (!$_COOKIE["utmmedium"] || $_GET['utm_medium']) {
        setcookie('utmmedium', $_GET['utm_medium'], time()+3600*1, COOKIEPATH, COOKIE_DOMAIN); //For 1 hours
    }
    if (!$_COOKIE["utmsource"] || $_GET['utm_source']) {
        setcookie('utmsource', $_GET['utm_source'], time()+3600*1, COOKIEPATH, COOKIE_DOMAIN); //For 1 hours
    }
    if (!$_COOKIE["utmcontent"] || $_GET['utm_content']) {
        setcookie('utmcontent', $_GET['utm_content'], time()+3600*1, COOKIEPATH, COOKIE_DOMAIN); //For 1 hours
    }
}

add_action( 'restrict_manage_posts', 'priority_admin_posts_filter_restrict_manage_posts' );
/**
 * First create the dropdown
 * make sure to change POST_TYPE to the name of your custom post type
 *
 * @author Ohad Raz
 *
 * @return void
 */
function priority_admin_posts_filter_restrict_manage_posts(){
    $type = 'resource';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    //only add filter to post type you want
    if ('resource' == $type){
        //change this to the list of values you want to show
        //in 'label' => 'value' format
        $values = array(
            'Very High' => '1',
            'High' => '2',
            'Medium' => '3',
            'Low' => '4',
            'Very Low' => '5',
        );
        ?>
        <select name="priority_filter">
        <option value=""><?php _e('All Priorities ', 'priority'); ?></option>
        <?php
            $current_v = isset($_GET['priority_filter'])? $_GET['priority_filter']:'';
            foreach ($values as $label => $value) {
                printf
                    (
                        '<option value="%s"%s>%s</option>',
                        $value,
                        $value == $current_v? ' selected="selected"':'',
                        $label
                    );
                }
        ?>
        </select>
        <?php
    }
}


add_filter( 'parse_query', 'resource_priority_posts_filter' );
/**
 * if submitted filter by post meta
 *
 * make sure to change META_KEY to the actual meta key
 * and POST_TYPE to the name of your custom post type
 * @author Ohad Raz
 * @param  (wp_query object) $query
 *
 * @return Void
 */
function resource_priority_posts_filter( $query ){
    global $pagenow;
    $type = 'resource';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }
    if ( 'resource' == $_GET['post_type'] && is_admin() && $pagenow=='edit.php' && isset($_GET['priority_filter']) && $_GET['priority_filter'] != '') {
        $query->query_vars['meta_key'] = 'resource_priority';
        $query->query_vars['meta_value'] = $_GET['priority_filter'];
    }
}

add_filter('manage_resource_posts_columns', 'resource_table_head');
function resource_table_head( $defaults ) {
    $defaults['resource_priority']  = 'Priority';
    //$defaults['ticket_status']    = 'Ticket Status';
    //$defaults['venue']   = 'Venue';
    //$defaults['author'] = 'Added By';
    return $defaults;
}

add_action( 'manage_resource_posts_custom_column', 'resource_table_content', 10, 2 );

function resource_table_content( $column_name, $post_id ) {
    if ($column_name == 'resource_priority') {
    $resource_priority = get_post_meta( $post_id, 'resource_priority', true );
        $values = array(
            'Very High' => '1',
            'High' => '2',
            'Medium' => '3',
            'Low' => '4',
            'Very Low' => '5',
        );
        foreach ($values as $label => $value) {
                if ($resource_priority == $value) {
                    echo $label;
                }
            }
    }
    /*if ($column_name == 'ticket_status') {
    $status = get_post_meta( $post_id, '_bs_meta_event_ticket_status', true );
    echo $status;
    }

    if ($column_name == 'venue') {
    echo get_post_meta( $post_id, '_bs_meta_event_venue', true );
    }*/

}