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

            <div class="primary" role="main">
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
                            <label for="formsubject" class="hidefromscreen">Subject:</label>
                            <select name="subject" required class="contact-dropdown" id="formsubject" aria-required="true">
                                <option value="">Subject</option>
                                <option value="Customer Service">Customer Service (magazine subscriptions, etc.)</option>
                                <option>Sales</option>
                                <option <?php if ($_GET['sbj'] == "demo") : echo("selected"); endif; ?>>Demo</option>
                                <option <?php if ($_GET['sbj'] == "quote") : echo("selected"); endif; ?>>Quote</option>
                                <option>Career</option>
                                <option>Other</option>
                            </select>
                            <label for="formcustomer" class="hidefromscreen">Customer:</label>
                            <select name="customer_service" class="contact-dropdown" id="formcustomer" aria-required="true">
                                <option value="">Select one</option>
                                <option>Cancel subscription</option>
                                <option>Change address</option>
                                <option>Order problem</option>
                                <option>Make a return</option>
                                <option>Issue with return</option>
                                <option>Subscription notice</option>
                            </select>
                        </div>
                            <label for="formemailAddress" class="hidefromscreen">Email Address:</label>
	                            <input type="email" name="emailAddress" placeholder="Email Address" id="formemailAddress" required aria-required="true" aria-invalid="true" />
	                        <label for="formfirstName" class="hidefromscreen">First Name:</label>
	                            <input type="text" name="firstName" placeholder="First Name" id="formfirstName" required aria-required="true" />
	                        <label for="formlastName" class="hidefromscreen">Last Name:</label>
	                            <input type="text" name="lastName" placeholder="Last Name" id="formlastName" required aria-required="true" />
	                        <label for="forminboundOriginator" class="hidefromscreen">How Did You Hear About Us:</label>
	                            <input name="inboundOriginator1" type="text" placeholder="How Did You Hear About Us?" id="forminboundOriginator" aria-required="false" />
	                        <label for="formComments" class="hidefromscreen">Message:</label>
                            	<textarea rows="6" name="comments" placeholder="Message" id="formComments"></textarea>
                        <div>
                            <input value="cds-global-contact" type="hidden" name="elqFormName" />
                            <input value="1851" type="hidden" name="elqSiteId" />
                            <input name="elqCampaignId" type="hidden" />
                            <input type="submit" value="Submit" />
                        </div>
                    </form>

<div id="thankyou" style="display: none;">
<h3 style="color: #fc4c00;">Thank you for contacting CDS Global!</h3>
<p>CDS Global is the leading provider of end-to-end business process outsourcing.</p>

<aside class="additional sidebar" role="complementary">
    <h2 style="color: #1cadf1; text-align:center;">We Power&nbsp;&nbsp;&nbsp;&nbsp;We Connect&nbsp;&nbsp;&nbsp;&nbsp;We Simplify</h2>
    <?php

        dynamic_sidebar('cds-thankyou');
    ?>
</aside>
</div>

                    <iframe name="eloqua-submit" style="display: none;"></iframe>

                </div>
            </div>

            <?php //get_sidebar(); ?>
        </div>
        <?php endif; ?>
    </div>

<?php get_footer(); ?>
