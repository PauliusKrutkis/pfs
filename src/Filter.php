<?php


namespace Pfs;


class Filter
{
    private $title = '';
    private $options = [];
    private $slug = '';
    private $type = '';
    private $template = '';
    private $taxonomy = '';
    private $order = 0;
    private $meta = '';
    private $dynamicValues = false;

    public function __construct($name, $type, $template)
    {
        $this->title    = $name;
        $this->type     = $type;
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        if ($this->slug) {
            return $this->slug;
        } else {
            return Helper::slugify($this->title);
        }
    }

    /**
     * @param array $options
     *
     * @return Filter
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $slug
     *
     * @return Filter
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    public function getRawHtml()
    {
        $view = new View('types/' . $this->template);
        $view->set('filter', $this);

        $view->output();
    }

    /**
     * @param string $taxonomy
     *
     * @return Filter
     */
    public function setTaxonomy($taxonomy)
    {
        $this->taxonomy = $taxonomy;
        $terms          = get_terms($taxonomy);
        $options        = [];

        foreach ($terms as $term) {
            $options[] = new Option($term->name, $term->slug);
        }

        $this->setOptions($options);

        return $this;
    }

    /**
     * @return string
     */
    public function getTaxonomy()
    {
        return $this->taxonomy;
    }

    /**
     * @param int $order
     *
     * @return Filter
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    public function isOptionActive(Option $option)
    {
        $queryVar = get_query_var($this->getSlug());

        if ($queryVar == '') {
            return false;
        }


        $values = explode(',', $queryVar);

        return in_array($option->getValue(), $values);
    }

    public function getActiveRangeFrom($default)
    {
        $queryVar = get_query_var($this->getSlug());
        $values   = explode('-', $queryVar);

        if ($values[0] != '') {
            return $values[0];
        }

        if ($default) {
            return $this->getOptions()[0]->getValue();
        } else {
            return false;
        }
    }

    public function getActiveRangeTo($default)
    {
        $queryVar = get_query_var($this->getSlug());
        $values   = explode('-', $queryVar);

        if ($values[0] != '') {
            return $values[1];
        }

        if ($default) {
            return $this->getOptions()[1]->getValue();
        } else {
            return false;
        }
    }

    /**
     * @param string $meta
     *
     * @return Filter
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @return string
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param bool $dynamicValues
     *
     * @return Filter
     */
    public function setDynamicValues($dynamicValues)
    {
        $this->dynamicValues = $dynamicValues;

        if ($dynamicValues) {
            $this->options[0] = new Option('min', $this->getMetaMinValue());
            $this->options[1] = new Option('max', $this->getMetaMaxValue());
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isDynamicValues()
    {
        return $this->dynamicValues;
    }

    private function getMetaMinValue()
    {
        global $wpdb;

        $min = $wpdb->get_var("SELECT min(cast(meta_value as unsigned)) FROM wp_postmeta WHERE meta_key='" . $this->getMeta() . "'");

        return $min;
    }

    private function getMetaMaxValue()
    {
        global $wpdb;

        $max = $wpdb->get_var("SELECT max(cast(meta_value as unsigned)) FROM wp_postmeta WHERE meta_key='" . $this->getMeta() . "'");

        return $max;
    }
}