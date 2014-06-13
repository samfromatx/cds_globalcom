<?php if (is_single()): ?>
    <article class="primary" style="width:95%;">
        <h1><?php the_title(); ?></h1>
        <?php the_content(); ?>
        <?php eloqua_form(); ?>
        <?php related_posts(); ?>
    </article>
<?php else: ?>
    <li <?php post_class(); ?>>
        <a class="icon" href="<?php the_permalink(); ?>"><i class="icon"></i></a>
        <h3>
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        <?php the_excerpt(); ?>
        <a class="more" href="<?php echo the_permalink(); ?>">Learn more</a>
    </li>
<?php endif; ?>
