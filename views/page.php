<?php
/** @var \Pfs\Navigation $navigation */
$navigation = apply_filters('navigation_instance', null);
$query      = $navigation->getQuery();
get_header();
?>

<div class="container">

    <?php $navigation->output('navigation') ?>

    <?php
    while ($query->have_posts()) {
        $query->the_post();
        get_template_part('partials/article');
    }
    ?>

    <?php $navigation->output('pagination') ?>

</div>

<?php get_footer(); ?>
