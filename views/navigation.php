<?php /** @var Pfs\View $this */ ?>
<?php /** @var Pfs\Navigation $navigation */ ?>

<?php $navigation = $this->get('navigation'); ?>
<div data-filter-groups>
    <dl>
        <?php
        /** @var \Pfs\Filter $filter */
        foreach ($navigation->getFilters() as $filter) {
            $filter->getHtml();
        }
        ?>
    </dl>
</div>
