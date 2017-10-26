<?php
/** @var \Pfs\Filter $filter */
$filter = $this->get('filter');
?>

<dt><?php echo $filter->getName() ?></dt>
<dd>
    <?php if ($filter->getOptions()): ?>
        <ul>
            <?php /** @var \Pfs\Option $option */
            foreach ($filter->getOptions() as $option):
                $id = $filter->getSlug() . '-' . $option->getValue();
                ?>
                <li>
                    <input type="checkbox"
                        <?php if ($filter->isOptionActive($option))
                            echo 'checked' ?>
                           data-pfs-checkbox='{
                               "slug": "<?php echo $filter->getSlug() ?>",
                               "order": <?php echo $filter->getOrder() ?>,
                               "value": "<?php echo $option->getValue() ?>"
                           }'
                           id="<?php echo $id ?>"
                           name="<?php echo $filter->getSlug() ?>"
                           value="<?php echo $option->getValue() ?>">
                    <label for="<?php echo $id ?>"><?php echo $option->getLabel() ?></label>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</dd>