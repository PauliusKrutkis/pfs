<?php
/** @var \Pfs\Filter $filter */
$filter  = $this->get('filter');
$options = $filter->getOptions();
?>

<dt><?php echo $filter->getName() ?></dt>
<dd>
    <div data-pfs-range='{
            "slug": "<?php echo $filter->getSlug() ?>",
            "activeMin": <?php echo $filter->getActiveRangeFrom(true) ?>,
            "activeMax": <?php echo $filter->getActiveRangeTo(true) ?>,
            "min": <?php echo $options[0]->getValue() ?>,
            "max": <?php echo $options[1]->getValue() ?>,
            "order": <?php echo $filter->getOrder() ?>
        }'>
        <div class="ui-slider-handle">
            <div data-pfs-range-min class="min-handle"></div>
        </div>
        <div class="ui-slider-handle">
            <div data-pfs-range-max class="max-handle"></div>
        </div>
    </div>
</dd>