<?php

/** @var Pfs\View $this */
/** @var Pfs\Filters $filters */

$taxonomy  = $this->get('group');
$options   = $this->get('options');
$filters   = $this->get('filters');
$count     = ( ! empty($options['count'])) ? $options['count'] : false;
$hideEmpty = ( ! empty($options['hide_empty'])) ? $options['hide_empty'] : false;
$terms     = get_terms($taxonomy, ['hide_empty' => $hideEmpty]);

if (empty($terms)) {
    return;
}

$label       = $filters->helper->getTaxLabel($taxonomy);
$taxSlug     = $filters->helper->getTaxSlug($taxonomy);
$activeTerms = $filters->getFilterValue($taxonomy);

?>

<li <?php if ($activeTerms)
    echo 'class="active"' ?>
        data-filter-group="<?php echo $taxSlug ?>">
    <span><?php echo $label ?></span>
    <ul>
        <?php foreach ($terms as $term): ?>

            <?php
            if ($activeTerms) {
                $activeParam = in_array($term->slug, explode(',', $activeTerms)) ? true : false;
            } else {
                $activeParam = false;
            }
            ?>

            <li>
                <input
                    <?php if ($activeParam) {
                        echo "checked";
                    } ?>
                        type="checkbox"
                        data-filter-param
                        id="<?php echo $term->term_id ?>"
                        data-slug="<?php echo $term->slug; ?>"
                        name="<?php echo $taxSlug ?>"/>
                <label for="<?php echo $term->term_id ?>"><?php echo $term->name ?><?php if ($count) {
                        echo ' (' . $term->count . ')';
                    } ?>
                </label>
            </li>
        <?php endforeach; ?>
    </ul>
</li>
