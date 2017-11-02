<?php

namespace Pfs;


class Navigation
{
    const CHECKBOX_TEMPLATE = 'checkbox';
    const RANGE_TEMPLATE = 'range';

    const TAXONOMY_TYPE = 'taxonomy';
    const META_TYPE = 'meta';

    protected $pageId;
    protected $query;
    protected $paged;
    protected $filters = [];
    protected $ajax = false;

    private function getActiveOptions(Filter $filter)
    {
        $values = [];

        switch ($filter->getTemplate()) {
            case self::RANGE_TEMPLATE:
                if ($this->getRangeActiveOption($filter)) {
                    $values[] = $this->getRangeActiveOption($filter);
                }
                break;
            default:
                $options = array_filter($filter->getOptions(), function ($option) use ($filter) {
                    /** @var Option $option */
                    return $filter->isOptionActive($option);
                });

                $values = array_map(function ($option) {
                    /** @var Option $option */
                    return $option->getValue();
                }, array_values($options));

                break;
        }

        return $values;
    }

    private function getRangeActiveOption(Filter $filter)
    {
        $from = $filter->getActiveRangeFrom(false);
        $to   = $filter->getActiveRangeTo(false);

        if ($from && $to) {
            return $from . '-' . $to;
        } else {
            return false;
        }

    }

    public function getFiltersJson()
    {
        $filters = [];

        if ( ! $this->getFilters()) {
            return '';
        }

        /** @var Filter $filter */
        foreach ($this->getFilters() as $filter) {
            $values = $this->getActiveOptions($filter);

            $filters[] = [
                'slug'   => $filter->getSlug(),
                'order'  => $filter->getOrder(),
                'values' => $values
            ];
        }

        return json_encode($filters);
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
        $this->query['meta_query'] = $this->getMetaQueryData();
        $this->query['tax_query']  = $this->getTaxQueryData();
        $this->query['paged']      = $this->getPagedQuery();

        return new \WP_Query($this->query);
    }

    private function getPagedQuery()
    {
        return (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
    }

    private function getTaxQueryData()
    {
        $count = 0;
        $data  = [];

        $filters = array_filter($this->getFilters(), function ($filter) {
            /** @var Filter $filter */
            return $filter->getType() === self::TAXONOMY_TYPE;
        });

        foreach ($filters as $filter) {
            /** @var Filter $filter */
            if ($count > 0) {
                $data['relation'] = 'AND';
            }

            if ($this->getActiveOptions($filter)) {
                $data[] = [
                    'taxonomy' => $filter->getTaxonomy(),
                    'field'    => 'slug',
                    'terms'    => $this->getActiveOptions($filter),
                ];

                $count++;
            }
        }

        return $data;
    }

    private function getMetaQueryData()
    {
        $count = 0;
        $data  = [];

        $filters = array_filter($this->getFilters(), function ($filter) {
            /** @var Filter $filter */
            return $filter->getType() === self::META_TYPE;
        });

        foreach ($filters as $filter) {
            /** @var Filter $filter */
            if ($count > 0) {
                $data['relation'] = 'AND';
            }

            if ($filter->getTemplate() == self::RANGE_TEMPLATE) {
                $data[] = $this->getRangeQueryData($filter);
                $count++;
                continue;
            }

            if ($this->getActiveOptions($filter)) {
                $data[] = [
                    'key'     => $filter->getMeta(),
                    'value'   => $this->getActiveOptions($filter),
                    'compare' => 'IN'
                ];
                $count++;
            }

        }

        return $data;
    }

    private function getRangeQueryData(Filter $filter)
    {
        return [
            'key'     => $filter->getMeta(),
            'value'   => [$filter->getActiveRangeFrom(true), $filter->getActiveRangeTo(true)],
            'type'    => 'numeric',
            'compare' => 'BETWEEN'
        ];
    }

    public function output($template)
    {
        if ( ! $this->getPaged() && $template == 'pagination') {
            return '';
        }

        $template = new View($template);

        $template->set('navigation', $this);

        $template->output();
    }

    public function getHtml($template)
    {
        if ( ! $this->getPaged() && $template == 'pagination') {
            return '';
        }

        $template = new View($template);

        $template->set('navigation', $this);

        return $template->getHtml();
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

    /**
     * @param bool $ajax
     *
     * @return Navigation
     */
    public function setAjax($ajax)
    {
        $this->ajax = $ajax;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        return $this->ajax;
    }

}
