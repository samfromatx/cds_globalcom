<?php
/*
Template Name: Contact Form
*/
?>

<?php get_header(); ?>

    <div class="main">
        <?php if (have_posts()) : the_post(); ?>
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
                        foreach ($children as $child):?>
                            <li <?php echo is_page($child->ID) ? 'class="active"' : '' ?>><a href="<?php echo get_permalink($child->ID); ?>"><?php echo get_the_title($child->ID); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
                <div class="content" style="">
                    <?php the_content(); ?>
                    <div id="contactusform"></div>
                   <!-- <form method="post" action="/content/themes/cds/partials/contact_process.php" class="contact" target="eloqua-submit">-->
                    <form method="post" action="https://s1851.t.eloqua.com/e/f2" class="contact" target="eloqua-submit">
                        <div>
                            <select name="subject" required class="contact-dropdown" id="formsubject">
                                <option value="">Subject</option>
                                <option>Customer Service</option>
                                <option>Sales</option>
                                <option <?php if ($_GET['sbj'] == "demo") : echo("selected"); endif; ?>>Demo</option>
                                <option <?php if ($_GET['sbj'] == "quote") : echo("selected"); endif; ?>>Quote</option>
                                <option>Career</option>
                                <option>Other</option>
                            </select>
                            <select name="customer_service" class="contact-dropdown" id="formcustomer">
                                <option value="">Select one</option>
                                <option>Cancel subscription</option>
                                <option>Change address</option>
                                <option>Order problem</option>
                                <option>Make a return</option>
                                <option>Issue with return</option>
                                <option>Subscription notice</option>
                            </select>
                        </div>
                            <input type="email" name="emailAddress" placeholder="Email Address" required />
                            <input type="text" name="firstName" placeholder="First Name" required />
                            <input type="text" name="lastName" placeholder="Last Name" required />
                            <input name="inboundOriginator1" type="text" placeholder="How Did You Hear About Us?" />
                            <textarea rows="6" name="comments" placeholder="Comments"></textarea>
                        <div>
                            <input value="cds-global-contact" type="hidden" name="elqFormName" />
                            <input value="1851" type="hidden" name="elqSiteId" />
                            <input name="elqCampaignId" type="hidden" />
                            <input type="submit" value="Submit" />
                        </div>
                    </form>
                    <iframe name="eloqua-submit" style="display: none;"></iframe>

                </div>
            </div>

            <?php get_sidebar(); ?>
        </div>
        <?php endif; ?>
    </div>

<?php get_footer(); ?>
