<?php
/** @var \Pfs\Navigation $navigation */
$navigation = apply_filters('navigation_instance', null);
get_header();
?>

    <div class="pfs-page">

        <div class="pfs-page__navigation" data-pfs-navigation='{
            "permalink": "<?php echo get_permalink() ?>",
            "ajax": "<?php echo $navigation->isAjax() ?>",
            "page": <?php echo $navigation->getPageId() ?>,
            "filters": <?php echo $navigation->getFiltersJson() ?>
        }'>
            <?php $navigation->output('navigation') ?>
        </div>

        <div class="pfs-page__posts" data-pfs-posts>
            <?php $navigation->output('posts') ?>
        </div>

        <div class="pfs-page__pagination" data-pfs-pagination>
            <?php $navigation->output('pagination') ?>
        </div>

    </div>

<?php get_footer(); ?>