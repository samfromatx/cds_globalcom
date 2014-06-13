<?php
/**
 * Plugin Name: CDS Blog Import
 * Description: Does cleanup of imported blog posts
 * Author: Alex Luke
 */

function cds_blog_import_post_terms($post_terms, $post_id, $post) {
    $new_terms = array();
    foreach ($post_terms as $term) {
        if ($term['domain'] == 'post_tag')
            $new_terms[] = $term;
    }

    return $new_terms;
}
add_filter('wp_import_post_terms', 'cds_blog_import_post_terms', 10, 3);

function cds_blog_import_posts($posts) {
    return array_filter($posts, function ($post) {
        return $post['post_type'] != 'page';
    });
}
add_filter('wp_import_posts', 'cds_blog_import_posts');
