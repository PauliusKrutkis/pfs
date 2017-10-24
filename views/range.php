<?php

/** @var Pfs\View $this */
/** @var Pfs\Navigation $filters */

$meta    = $this->get('group');
$options = $this->get('options');
$filters = $this->get('filters');
$value   = $filters->getFilterValue($meta);
$active  = ($value !== '') ? explode('-', $value) : [$options['min'], $options['max']];
?>

<li data-filter-group="<?php echo $meta ?>">
    <span><?php echo ucfirst($meta) ?></span>
    <div class="range-slider">
        <div data-no-ui-slider='{
            "meta": "<?php echo $meta ?>",
            "from": "#<?php echo $meta ?>-from",
            "to": "#<?php echo $meta ?>-to",
            "activeFrom": <?php echo $active[0] ?>,
            "activeTo": <?php echo $active[1] ?>,
            "min": <?php echo $options['min'] ?>,
            "max": <?php echo $options['max'] ?>
        }'></div>
        <span id="<?php echo $meta ?>-from"></span>
        <span id="<?php echo $meta ?>-to"></span>
    </div>
</li>
