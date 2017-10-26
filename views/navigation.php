<?php /** @var Pfs\View $this */ ?>
<?php /** @var Pfs\Navigation $navigation */ ?>
<?php $navigation = $this->get('navigation'); ?>

<dl data-pfs="<?php echo get_permalink() ?>">
    <?php
    /** @var \Pfs\Filter $filter */
    foreach ($navigation->getFilters() as $filter) {
        $filter->getHtml();
    }
    ?>
</dl>
