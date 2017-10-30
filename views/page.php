<?php
/** @var \Pfs\Navigation $navigation */
$navigation = apply_filters('navigation_instance', null);
get_header();
?>

    <div class="container">

        <div data-pfs-navigation>
            <?php $navigation->output('navigation') ?>
        </div>

        <div data-pfs-posts>
            <?php $navigation->output('posts') ?>
        </div>

        <div data-pfs-pagination>
            <?php $navigation->output('pagination') ?>
        </div>

    </div>

<?php get_footer(); ?>