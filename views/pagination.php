<?php /** @var \Pfs\Navigation $navigation */

$navigation = $this->get('navigation');
$query      = $navigation->getQuery();

$args = array(
    'base'      => str_replace($query->max_num_pages, '%#%', esc_url(get_pagenum_link($query->max_num_pages))),
    'format'    => '?paged=%#%',
    'total'     => $query->max_num_pages,
    'current'   => max(1, get_query_var('paged')),
    'mid_size'  => 1,
    'prev_text' => __('«'),
    'next_text' => __('»'),
    'type'      => 'list',
);

?>

<div data-pfs-pagination>
    <?php echo paginate_links($args); ?>
</div>