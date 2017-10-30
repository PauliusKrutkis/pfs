<?php /** @var \Pfs\Navigation $navigation */

$navigation = $this->get('navigation');
$query      = $navigation->getQuery();

$totalItems   = $query->found_posts;
$itemsPerPage = $query->query['posts_per_page'];
$currentPage  = (get_query_var('paged') == 0) ? 1 : get_query_var('paged');
$urlPattern   = '(:num)';

$pagination = new Pfs\Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
$pagination->setMaxPagesToShow(5);
echo $pagination->toHtml();
