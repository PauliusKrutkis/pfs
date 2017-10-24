<?php

namespace Pfs;


class Navigation
{
    const TAXONOMY_TEMPLATE = 'checkbox';
    const META_TEMPLATE = 'range';

    const TAXONOMY_TYPE = 'taxonomy';
    const META_TYPE = 'meta';

    protected $filterGroups;
    protected $pageId;
    protected $urlRewrite = true;
    protected $query;
    protected $paged;
    protected $filters = [];

    public $helper;

    function __construct()
    {
        $this->helper = new Helper();

        return $this;
    }

    public function getActiveFilters()
    {
        $activeFilters = [];
// TODO active filters
//        foreach ($this->getGroups() as $name => $options) {
//            $slug = $this->getFilterSlug($name);
//            if (get_query_var($slug) !== '') {
//                $activeFilters[$slug] = explode(',', get_query_var($slug));
//            };
//        }
//
//        if (get_query_var('paged') != 0) {
//            $activeFilters['page'] = get_query_var('paged');
//        }

        return $activeFilters;
    }

    public function getFilterValue($key)
    {
        $slug = $this->getFilterSlug($key);

        return get_query_var($slug);
    }

    public function getFilterSlug($key)
    {
        $slug        = '';
        $filterGroup = $this->getGroups()[$key];

        switch ($filterGroup['type']) {
            case self::META_TYPE:
                $slug = $key;
                break;

            case self::TAXONOMY_TYPE:
                $slug = $this->helper->getTaxLabel($key);
                break;
        }

        return strtolower($slug);
    }

    public function getUrlRewrite()
    {
        return $this->urlRewrite;
    }

    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    public function setPageId($id)
    {
        $this->pageId = $id;

        return $this;
    }

    public function getPageId()
    {
        return $this->pageId;
    }

    public function getQuery()
    {
//        TODO query
//        $this->query['tax_query']  = $this->getTaxQueryArgs();
//        $this->query['meta_query'] = $this->getMetaQueryArgs();
        $this->query['paged'] = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;

        return new \WP_Query($this->query);
    }

    public function addTaxonomyFilter($taxonomy, $args = null)
    {
        $filterArgs = [
            'template' => self::TAXONOMY_TEMPLATE,
            'type'     => self::TAXONOMY_TYPE,
        ];

        if ($args) {
            $filterArgs = $filterArgs + $args;
        }

        $this->filterGroups[$taxonomy] = $filterArgs;

        return $this;
    }

    public function addMetaFilter($meta, $args)
    {
        $filterArgs = [
            'template' => self::META_TEMPLATE,
            'type'     => self::META_TYPE,
        ];

        if ($args) {
            $filterArgs = $filterArgs + $args;
        }

        $this->filterGroups[$meta] = $filterArgs;

        return $this;
    }


    public function getGroups()
    {
        return $this->filterGroups;
    }

    public function output($template)
    {
        if ( ! $this->getPaged() && $template == 'pagination') {
            return '';
        }

        $this->getTemplate($template);
    }

    public function getTemplate($template, $data = null)
    {
        $template = new View($template);

        if ($data) {
            foreach ($data as $name => $value) {
                $template->set($name, $value);
            }
        }

        $template->set('navigation', $this);

        $template->output();
    }

    private function getMetaQueryArgs()
    {
        $activeCount = 0;
        $query       = [];
        $metaGroups  = array_filter($this->filterGroups, function ($group) {
            return $group['type'] === self::META_TYPE;
        });

        foreach ($metaGroups as $meta => $options) {
            $slug  = $this->getFilterSlug($meta);
            $value = $this->getFilterValue($slug);

            if (isset($value) && $value != '') {
                if ($activeCount > 0) {
                    $query['relation'] = 'AND';
                }

                switch ($options['template']) {
                    case self::META_TEMPLATE:
                        $query[] = [
                            'key'     => $meta,
                            'value'   => explode('-', $value),
                            'type'    => 'numeric',
                            'compare' => 'BETWEEN'
                        ];
                        break;
                }

                $activeCount++;
            } else if ($options['template'] == self::META_TEMPLATE) {
                if ($activeCount > 0) {
                    $query['relation'] = 'AND';
                }

                $query[] = [
                    'key'     => $meta,
                    'value'   => [$options['min'], $options['max']],
                    'type'    => 'numeric',
                    'compare' => 'BETWEEN'
                ];

                $activeCount++;
            }
        }

        if (isset($query)) {
            $args['meta_query'] = $query;
        }

        return $query;
    }

    private function getTaxQueryArgs()
    {
        $activeCount = 0;
        $query       = [];
        $taxGroups   = array_filter($this->filterGroups, function ($group) {
            return $group['type'] === self::TAXONOMY_TYPE;
        });

        foreach ($taxGroups as $taxonomy => $options) {
            $value = $this->getFilterValue($taxonomy);

            if (isset($value) && $value != '') {

                if ($activeCount > 0) {
                    $query['relation'] = 'AND';
                }

                $query[] = [
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => explode(',', $value),
                ];

                $activeCount++;
            }
        }

        if (isset($query)) {
            $args['tax_query'] = $query;
        }

        return $query;
    }

    public function getPaged()
    {
        return $this->paged;
    }

    public function setPaged($paged)
    {
        $this->paged = $paged;

        return $this;
    }

    /**
     * @param array $filters
     *
     * @return Navigation
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

}
