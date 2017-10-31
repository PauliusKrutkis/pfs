<?php /** @var Pfs\View $this */ ?>
<?php /** @var Pfs\Navigation $navigation */ ?>
<?php $navigation = $this->get('navigation');
?>

<dl>
    <?php
    /** @var \Pfs\Filter $filter */
    foreach ($navigation->getFilters() as $filter) {
        $filter->getRawHtml();
    }
    ?>
</dl>
