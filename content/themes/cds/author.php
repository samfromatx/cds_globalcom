<?php get_header(); ?>

    <div class="main">
        <div class="section-nav">
            <?php get_template_part('partials/breadcrumbs'); ?>
<?php
if(isset($_GET['author_name'])) :
$curauth = get_userdatabylogin($author_name);
else :
$curauth = get_userdata(intval($author));
endif;
?>
            <h1><?php echo $curauth->display_name; ?></h1>
        </div>
        <div class="content">
        <?php $paged = $wp_query->get('paged'); ?>

            <div class="featured">
                <div style="float:left; margin: 10px 0 0 50px;">
                    <?php echo get_avatar($curauth->id, 150); ?>
                </div>
                <div class="authorbio">
                    <dl>
                        <dt><strong>About:</strong></dt>
                            <?php if ($curauth->user_description != "") : ?>
                                <dd><?php echo $curauth->user_description; ?></dd>
                            <?php else : ?>
                                <dd><?php echo $curauth->display_name; ?> is a contributing author for CDS Global's blog. <?php echo $curauth->user_firstname; ?> contributes knowledge and expertise on topics related to CDS Global's business, products and solutions.</dd>
                            <?php endif; ?>
                    <?php if (get_the_author_meta('twitter') != "" || get_the_author_meta('googleplus') != "" || $curauth->user_url != "") : ?>
                        <dt><strong>Follow <?php echo $curauth->user_firstname; ?>:</strong></dt>
                            <dd><div class="social">
                                    <ul>
                                        <?php if (get_the_author_meta('twitter') != "") : ?><li><a href="<?php the_author_meta('twitter'); ?>" target="_blank" class="twitter">Twitter</a></li><?php endif; ?>
                                        <?php if ($curauth->user_url != "") : ?><li><a href="<?php echo $curauth->user_url; ?>" target="_blank" class="linkedin">LinkedIn</a></li><?php endif; ?>
                                        <?php if (get_the_author_meta('googleplus') != "") : ?><li><a href="<?php the_author_meta('googleplus'); ?>?rel=author" rel="publisher" target="_blank" class="gplus">Google+</a></li><?php endif; ?>
                                    </ul>
                                </div>
                            </dd>
                    </dl>
                <?php endif; ?>
                </div>
                
            </div>


        <div class="primary">
        <?php while (have_posts()): the_post(); ?>
            <?php if ($image = get_the_post_thumbnail()): ?>
            <div class="list has-image">
                <div class="image">
                    <a href="<?php the_permalink(); ?>"><?php echo $image; ?></a>
                </div>
            <?php else: ?>
            <div class="list">
            <?php endif; ?>
                <div class="post">
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <span class="author"><?php the_author_posts_link(); ?></span><span class="date"><?php the_date(); ?></span>
                    <?php the_excerpt(); ?>
                    <a class="more" href="<?php the_permalink(); ?>">Read More</a>
                </div>
            </div>
        <?php endwhile; ?>
        <nav class="pagination">
            <div class="next"><?php next_posts_link('Older posts'); ?></div>
            <div class="previous"><?php previous_posts_link('Newer posts'); ?></div>
        </nav>
        </div>
        <?php get_sidebar('blog'); ?>
        </div>
    </div>

<?php get_footer(); ?>
