<?php
// See if the current page or the parent has a custom industry sidebar
// Otherwise fall back to the default one.
$cdsdomain = $_SERVER['SERVER_NAME'];
$actual_link = "$_SERVER[REQUEST_URI]";
$parentdir = explode('/', $actual_link);
if ($parentdir[1] != "stay-informed") {
?>

<aside class="secondary sidebar" role="complementary">
    <?php
        // If this is a leaf page, include the CTA buttons
        global $children;
        if (isset($children) && count($children) == 0):
            if ($cdsdomain == "www.cds-global.com" || $cdsdomain == "stage.cds-global.com" || $cdsdomain == "www.cdsglobal.ca" || $cdsdomain == "ca.cds-global.com" || $cdsdomain == "stageca.cds-global.com"): ?>
                <div class="widget cta">
                    <a href="/get-a-quote/?sbj=quote"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/get-a-quote.png" alt="Request a quote"></a>
                </div>
                <div class="widget cta">
                    <a href="/schedule-a-demo/?sbj=demo"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/schedule-a-demo.png" alt="Request a demo"></a>
                </div>
        <?php
            elseif ($cdsdomain == "uk.cds-global.com" || $cdsdomain == "stageuk.cds-global.com"): ?>
                <div class="widget cta">
                    <a href="/about/contact/#contactinfo"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/get-a-quote.png" alt="Request a quote"></a>
                </div>
        <?php
            endif;
        endif;

        //$sidebar = "industry-{$post->post_name}";
        $sidebar = "industry-$parentdir[2]";
        if (!is_active_sidebar($sidebar)) {
            $parent = get_post($post->post_parent);
            $sidebar = "industry-{$parent->post_name}";
            if (!is_active_sidebar($sidebar))
                $sidebar = 'cds-global-sidebar';
        }
        dynamic_sidebar($sidebar);
    ?>
</aside>
<?php } else { ?>
<aside class="additional sidebar" role="complementary">
    <h2 style="color: #1cadf1; text-align:center;">We Power&nbsp;&nbsp;&nbsp;&nbsp;We Connect&nbsp;&nbsp;&nbsp;&nbsp;We Simplify</h2>
    <?php

        dynamic_sidebar('cds-thankyou');
    ?>
</aside>
<?php } ?>