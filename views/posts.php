<?php
/** @var \Pfs\Navigation $navigation */
$navigation = $this->get('navigation');
$query      = $navigation->getQuery();

while ($query->have_posts()) {
    $query->the_post();
    get_template_part('partials/article');
}
