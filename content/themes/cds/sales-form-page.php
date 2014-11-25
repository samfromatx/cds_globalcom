<?php
/*
Template Name: Sales/Demo Form
*/
?>

<?php get_header(); ?>
<style>
    .primary {
        width: 50%;
    }

    .primary .content {
        margin-left: 15%;
        width: 95%;
    }

    .primary .content .form {
        width: 60%;
        float: left;
    }

    form input[type=text], form input[type=search], form input[type=email], form input[type=tel], form textarea, form input[type=submit], form select, form .contact-dropdown {
        width: 95%;
    }

    .content .formright {
        width: 30%;
        float: right;
    }

    @media (max-width: 1023px) {
       .primary .content {
            margin-left: 5%;
            width: 95%;
        }

        .content .formright {
            margin-left: 5%;
            width: 95%;
            float: left;
        }
    }

    @media (max-width: 768px) {
       .primary .content {
            margin-left: 0;
            width: 100%;
        }

        .content .formright {
            margin: 0 3%;
            width: 95%;
            float: left;
        }
    }

</style>

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
                <div class="content" style="">
                    <?php the_content(); ?>
                    <div id="contactusform"></div>
                    <form method="post" action="https://s1851.t.eloqua.com/e/f2" class="contact" target="eloqua-submit">
                        <div>
                            <label for="formsubject" class="hidefromscreen">Subject:</label>
                            <select name="subject" required class="contact-dropdown" id="formsubject" aria-required="true">
                                <option value="">Subject</option>
                                <option>Sales</option>
                                <?php if ($_GET['sbj'] == "demo") { ?>
                                <option <?php if ($_GET['sbj'] == "demo") : echo("selected"); endif; ?>>Demo</option>
                                <?php } elseif ($_GET['sbj'] == "quote") { ?>
                                <option <?php if ($_GET['sbj'] == "quote") : echo("selected"); endif; ?>>Quote</option>
                                <?php } else { ?>
                                <option <?php if ($_GET['sbj'] == "demo") : echo("selected"); endif; ?>>Demo</option>
                                <option <?php if ($_GET['sbj'] == "quote") : echo("selected"); endif; ?>>Quote</option>
                                <?php } ?>
                            </select>
                        </div>
                            <label for="formemailAddress" class="hidefromscreen">Email Address:</label>
	                            <input type="email" name="emailAddress" placeholder="Email Address" id="formemailAddress" required aria-required="true" aria-invalid="true" />
	                        <label for="formfirstName" class="hidefromscreen">First Name:</label>
	                            <input type="text" name="firstName" placeholder="First Name" id="formfirstName" required aria-required="true" />
	                        <label for="formlastName" class="hidefromscreen">Last Name:</label>
	                            <input type="text" name="lastName" placeholder="Last Name" id="formlastName" required aria-required="true" />
	                        <label for="forminboundOriginator2" class="hidefromscreen">How Did You Hear About Us:</label>
	                            <input name="inboundOriginator1" type="text" placeholder="How Did You Hear About Us?" id="forminboundOriginator2" aria-required="false" />
	                        <label for="formComments" class="hidefromscreen">Message:</label>
                            	<textarea rows="6" name="comments" placeholder="Message" id="formComments"></textarea>
                        <div>
                            <input value="cds-global-contact" type="hidden" name="elqFormName" />
                            <input value="1851" type="hidden" name="elqSiteId" />
                            <input name="elqCampaignId" type="hidden" />
                            <?php if ($_GET['sbj'] == "demo") {
                                    $cta = "SCHEDULE A DEMO!";
                                } elseif ($_GET['sbj'] == "quote") {
                                    $cta = "GET A QUOTE!";
                                } else {
                                    $cta = "SUBMIT";
                                }

                                ?>
                            <div class="form-actions">
                                <button class="btn btn-danger optin_submit" id="" type="submit"><?php echo $cta ?></button>
                            </div>
                        </div>
                    </form>


<div id="thankyou" style="display: none;">
<h3 style="color: #fc4c00;">Thank you for contacting CDS Global<br />We will reach out to you with more information</h3>
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
            <div class="formright">
                <h4>You can also reach us by phone:</h4>
                <p>US: 1.866.897.7987<br>
                Canada: 1.844.237.7456<br>
                UK: +44 (0) 1858 468811<br>
                Australia: +61 (2) 8296 54001</p>
            </div>
            <?php //get_sidebar(); ?>
        </div>
        <?php endif; ?>
    </div>

<?php get_footer(); ?>
