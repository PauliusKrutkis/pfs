<?php /** @var Pfs\View $this */ ?>
<?php /** @var Pfs\Navigation $navigation */ ?>
<?php $navigation = $this->get('navigation');
?>

<dl data-pfs='{
    "permalink": "<?php echo get_permalink() ?>",
    "ajax": "<?php echo $navigation->isAjax() ?>",
    "page": <?php echo $navigation->getPageId() ?>,
    "filters": <?php echo $navigation->getFiltersJson() ?>
}'>
    <?php
    /** @var \Pfs\Filter $filter */
    foreach ($navigation->getFilters() as $filter) {
        $filter->getRawHtml();
    }
    ?>
</dl>
