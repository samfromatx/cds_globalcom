        <footer role="contentinfo">
            <div class="social">
                <ul>
                    <li><a href="https://twitter.com/cdsglobal" target="_blank" class="twitter">Twitter</a></li>
                    <li><a href="https://www.facebook.com/CDSGlobal" target="_blank" class="facebook">Facebook</a></li>
                    <li><a href="http://www.linkedin.com/company/cds-global" target="_blank" class="linkedin">LinkedIn</a></li>
                    <li><a href="http://instagram.com/cdsglobalinc" target="_blank" class="instagram">Instagram</a></li>
                    <li><a href="http://slideshare.net/cdsglobalinc" target="_blank" class="slideshare">SlideShare</a></li>
                    <li><a href="http://www.youtube.com/user/CDSGlobal" target="_blank" class="youtube">YouTube</a></li>
                    <li><a href="http://vine.co/cdsglobal" target="_blank" class="vineico">Vine</a></li>
                    <li><a href="https://plus.google.com/103871200619595329856" rel="publisher" target="_blank" class="gplus">Google+</a></li>
                    <li><a href="https://vimeo.com/cdsglobal" target="_blank" class="vimeoico">Vimeo</a></li>
                    <li><a href="/feed/?post_type[]=posts&post_type[]=resource&post_type[]=news&post_type[]=ajde_events&post_type[]=page" target="_blank" class="rss">RSS</a></li>
                </ul>
            </div>
            Copyright <?php echo date('Y'); ?> CDS Global, Inc. All rights reserved.
            <?php wp_nav_menu(array('theme_location' => 'footer')); ?>
        </footer>
    </div><?php //.container ?>


    <script type="text/javascript">
        var _elqQ = _elqQ || [];
        _elqQ.push(['elqSetSiteId', '1851']);
        _elqQ.push(['elqTrackPageView']);
        _elqQ.push(['elqUseFirstPartyCookie', 'www.cds-global.com']);

        (function () {
            function async_load() {
                var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true;
                s.src = '//img.en25.com/i/elqCfg.min.js';
                var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x);
            }
            if (window.addEventListener) window.addEventListener('DOMContentLoaded', async_load, false);
            else if (window.attachEvent) window.attachEvent('onload', async_load);
        })();
    </script>

    <script src="/content/themes/cds/javascripts/site.js"></script>

    <script>
        Modernizr.load({
            test: Modernizr.formvalidation,
            nope: '<?php echo get_stylesheet_directory_uri(); ?>/javascripts/webshim/polyfiller.js',
            callback: function() {
                webshims.setOptions('waitReady', false);
                webshims.polyfill('forms');
            }
        });
    </script>

    <?php
    // Add Twitter Tailored Audience code if twitter id exist in meta data
    $twitter = get_post_meta(get_the_ID(), 'twitter_cookie', true);
    if ( $twitter ) {
    ?>
        <script src="//platform.twitter.com/oct.js" type="text/javascript"></script>
        <script type="text/javascript">
        twttr.conversion.trackPid('<?php echo $twitter; ?>');
        </script>
        <noscript>
        <img height="1" width="1" style="display:none;" alt="" src="https://analytics.twitter.com/i/adsct?txn_id=<?php echo $twitter; ?>&p_id=Twitter" />
        <img height="1" width="1" style="display:none;" alt="" src="//t.co/i/adsct?txn_id=<?php echo $twitter; ?>&p_id=Twitter" />
        </noscript>

    <?php } ?>

<?php wp_footer(); ?>
</body>
</html>
