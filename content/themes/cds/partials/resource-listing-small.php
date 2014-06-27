    <li <?php post_class(); ?>>
        <a class="icon ifeatured" href="<?php the_permalink(); ?>"><i class="icon ifeatured"></i></a>
        <h3>
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        <?php the_excerpt(); ?>
    </li>