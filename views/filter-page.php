<?php
/** @var \Pfs\Filters $filters */
$filters = apply_filters('filters_instance', null);
$query   = $filters->getQuery();
get_header();
?>

<div class="container">

    <?php $filters->output('filters') ?>

    <?php
    while ($query->have_posts()) {
        $query->the_post();
        get_template_part('partials/article');
    }
    ?>

    <?php $filters->output('pagination') ?>

</div>

<?php get_footer(); ?>
