<!DOCTYPE html>
<!--[if IE 8]> <html class="lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?>> <!--<![endif]-->
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php wp_title('|'); ?></title>
    <link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon.ico">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>">
    <link rel="stylesheet" href="/content/themes/cds/print.css" media="print">
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/javascripts/modernizr.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!--[if lt IE 9]>
    <script src="/content/themes/cds/javascripts/ie9.js"></script>
    <![endif]-->
    <?php wp_head(); ?>
    <a href="https://plus.google.com/103871200619595329856" rel="publisher"></a>
</head>
<body <?php body_class(); ?>>
    <!-- Google Tag Manager -->
        <noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-N5XJNT"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-N5XJNT');</script>
    <!-- End Google Tag Manager -->
    <?php
        //$_SESSION['utm_campaign'] = $_GET['utm_campaign'];
        //$utmCookie = "hello";
        //setcookie("utmcampaign", "hello", (time()+3600), "/"); //For 1 hour
        //echo $_COOKIE["utmcampaign"];
    ?>
    <nav>
        <?php wp_nav_menu(array(
            'theme_location' => 'primary',
            'container' => false,
            'items_wrap' => '%3$s',
        )); ?>
        <li><a href="/about/contact">Contact</a></li>
    </nav>
    <div class="container">
        <div id="tools">
            <div class="menu-button"></div>
            <div class="country dropdown">
                <select>
                    <option value="">United States</option>
                    <option value="http://www.cdsglobal.ca/">Canada</option>
                    <option value="http://www.cdsglobal.co.uk/">United Kingdom</option>
                    <option value="http://www.cdsglobal.com.au/">Australia</option>
                </select>
            </div>
            <?php get_search_form(); ?>
            <ul class="links">
                <li><a href="/about/contact">Contact CDS Global</a></li>
            </ul>
        </div>
        <header>
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
                    <li><a href="https://twitter.com/cdsglobal" target="_blank" class="twitter">Twitter</a></li>
                    <li><a href="https://www.facebook.com/CDSGlobal" target="_blank" class="facebook">Facebook</a></li>
                    <li><a href="http://www.linkedin.com/company/cds-global" target="_blank" class="linkedin">LinkedIn</a></li>
                    <li><a href="http://instagram.com/cdsglobalinc" target="_blank" class="instagram">Instagram</a></li>
                    <li><a href="http://slideshare.net/cdsglobalinc" target="_blank" class="slideshare">SlideShare</a></li>
                </ul>
            </div>
            <?php wp_nav_menu(array('theme_location' => 'primary')); ?>
        </header>
