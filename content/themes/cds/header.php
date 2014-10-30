<!DOCTYPE html>
<!--[if IE 8]> <html class="lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?>> <!--<![endif]-->
<head>
    <?php
    $cdsdomain = $_SERVER['SERVER_NAME'];
    ?>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php wp_title('|'); ?></title>
    <link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon.ico">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>?v=<?php echo date("mdy-H:i"); ?>">
    <link rel="stylesheet" href="/content/themes/cds/print.css?v=<?php echo date("mdy-H:i"); ?>" media="print">
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/javascripts/modernizr.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!--[if lt IE 9]>
    <script src="/content/themes/cds/javascripts/ie9.js"></script>
    <![endif]-->
    <meta name="msvalidate.01" content="2DE0077177D32455C498CE9C6A6DECD5" />
    <?php wp_head(); ?>
    <a href="https://plus.google.com/103871200619595329856" rel="publisher" class="hidefromscreen">Google Plus</a>
</head>
<body <?php body_class(); ?>>
    <?php if ($cdsdomain == "www.cds-global.com" || $cdsdomain == "stage.cds-global.com") { ?>
    <!-- Google Tag Manager -->
        <noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-N5XJNT"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-N5XJNT');</script>
    <!-- End Google Tag Manager -->
    <?php } elseif ($cdsdomain == "www.cdsglobal.co.uk") { ?>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-668234-4', 'auto');
      ga('send', 'pageview');

    </script>
    <?php } ?>
    <nav role="navigation">
        <?php wp_nav_menu(array(
            'theme_location' => 'primary',
            'container' => false,
            'items_wrap' => '%3$s',
        )); ?>
        <li><a href="/about/contact">Contact</a></li>
    </nav>
    <?php
    $usdomain = "";
    $ukdomain = "";
    if ($cdsdomain == "www.cds-global.com" || $cdsdomain == "stage.cds-global.com") {
        $usdomain = "selected";
        $twitterHandle = "cdsglobal";
    } elseif ($cdsdomain == "www.cdsglobal.co.uk" || $cdsdomain == "uk.cds-global.com" || $cdsdomain == "stageuk.cds-global.com") {
        $ukdomain = "selected";
        $twitterHandle = "CDSGlobalEurope";
    } elseif ($cdsdomain == "www.cdsglobal.ca" || $cdsdomain == "ca.cds-global.com" || $cdsdomain == "stageca.cds-global.com") {
        $cadomain = "selected";
        $twitterHandle = "cdsglobal";
    }
    ?>
    <div class="container">
        <div id="tools">
            <div class="menu-button"></div>
            <div class="country dropdown">
                <label for="cdsglobal_country" class="hidefromscreen">Country:</label>
                <select id="cdsglobal_country">
                    <option <?php echo $usdomain; ?> value="http://www.cds-global.com">United States</option>
                    <option <?php echo $cadomain; ?> value="http://www.cdsglobal.ca/">Canada</option>
                    <option <?php echo $ukdomain; ?> value="http://www.cdsglobal.co.uk/">United Kingdom/Australia</option>
                </select>
            </div>
            <?php get_search_form(); ?>
            <ul class="links">
                <li><a href="/about/contact">Contact CDS Global</a></li>
            </ul>
        </div>
        <header role="banner">
            <div class="logo">
                <a href="<?php echo home_url(); ?>">
                    CDS Global
                </a>
            </div>
            <div class="printonly">
                <img src="/content/themes/cds/images/logo.png" width="151" height="75" alt="CDS Global - A Hearst Company Logo" />
            </div>
            <div class="social">
                <ul>
                    <li><a href="https://twitter.com/<?php echo $twitterHandle; ?>" target="_blank" class="twitter">Twitter</a></li>
                    <li><a href="https://www.facebook.com/CDSGlobal" target="_blank" class="facebook">Facebook</a></li>
                    <li><a href="http://www.linkedin.com/company/cds-global" target="_blank" class="linkedin">LinkedIn</a></li>
                    <li><a href="http://instagram.com/cdsglobalinc" target="_blank" class="instagram">Instagram</a></li>
                    <li><a href="http://slideshare.net/cdsglobalinc" target="_blank" class="slideshare">SlideShare</a></li>
                </ul>
            </div>
            <?php wp_nav_menu(array('theme_location' => 'primary')); ?>
        </header>
