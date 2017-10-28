<?php /** @var Pfs\View $this */ ?>
<?php /** @var Pfs\Navigation $navigation */ ?>
<?php $navigation = $this->get('navigation');
?>

<dl data-pfs='{
    "permalink": "<?php echo get_permalink() ?>",
    "filters": <?php echo $navigation->getFiltersJson() ?>
}'>
    <?php
    /** @var \Pfs\Filter $filter */
    foreach ($navigation->getFilters() as $filter) {
        $filter->getHtml();
    }
    ?>
</dl>
