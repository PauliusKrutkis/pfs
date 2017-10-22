<?php /** @var Pfs\View $this */ ?>
<?php /** @var Pfs\Filters $filters */ ?>

<?php $filters = $this->get('filters'); ?>
<aside data-filter-groups>
    <ul>
        <?php
        foreach ($filters->getGroups() as $group => $options) {
            $filters->getTemplate($options['template'], [
                'group'   => $group,
                'options' => $options
            ]);
        }
        ?>
    </ul>
</aside>
