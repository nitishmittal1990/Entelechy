<div id="sidebar">

<?php if (get_option('swt_fcats') == 'Hide') { ?>
<?php { echo ''; } ?>
<?php } else { include(TEMPLATEPATH . '/includes/featured-cats.php'); } ?>

<?php if (get_option('swt_banners') == 'Hide') { ?>
<?php { echo ''; } ?>
<?php } else { include(TEMPLATEPATH . '/includes/banners.php'); } ?>

    <?php if (!function_exists('dynamic_sidebar')
	|| !dynamic_sidebar()) : ?>

    <div class="side-widget">
    <h3>Pages</h3>
    <ul><?php wp_list_pages('title_li=&depth=1' ); ?></ul>
    </div>

    <div class="side-widget">
    <h3>Search</h3>
    <?php get_search_form(); ?>
    </div>

    <?php endif; ?>

    <?php if (get_option('swt_social') == 'Hide') { ?>
    <?php { echo ''; } ?>
    <?php } else { include(TEMPLATEPATH . '/includes/social.php'); } ?>

</div>

