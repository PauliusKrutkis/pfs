<?php
/** @var \Pfs\Filter $filter */
$filter = $this->get('filter');
?>

<dt><?php echo $filter->getName() ?></dt>
<dd data-filter-group="<?php echo $filter->getSlug() ?>">
    <?php /** @var \Pfs\Option $option */
    foreach ($filter->getOptions() as $option):
        $id = $filter->getSlug() . '-' . $option->getValue();
        ?>
        <!--TODO is checked -->
        <input type="checkbox"
               data-filter-param
               data-slug="<?php echo $option->getValue(); ?>"
               id="<?php echo $id ?>"
               name="<?php echo $filter->getSlug() ?>"
               value="<?php echo $option->getValue() ?>">
        <label for="<?php echo $id ?>"><?php echo $option->getLabel() ?></label>
    <?php endforeach; ?>
</dd>