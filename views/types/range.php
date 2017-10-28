<?php
/** @var \Pfs\Filter $filter */
$filter  = $this->get('filter');
$slug    = $filter->getSlug();
$options = $filter->getOptions();
?>

<dt><?php echo $filter->getName() ?></dt>
<dd>
    <div data-pfs-range='{
            "slug": "<?php echo $filter->getSlug() ?>",
            "from": "#<?php echo $filter->getSlug() ?>-from",
            "to": "#<?php echo $filter->getSlug() ?>-to",
            "activeFrom": <?php echo $filter->getActiveRangeFrom(true) ?>,
            "activeTo": <?php echo $filter->getActiveRangeTo(true) ?>,
            "min": <?php echo $options[0]->getValue() ?>,
            "max": <?php echo $options[1]->getValue() ?>,
            "order": <?php echo $filter->getOrder() ?>
        }'></div>
    <span id="<?php echo $filter->getSlug() ?>-from"></span>
    <span id="<?php echo $filter->getSlug() ?>-to"></span>
</dd>