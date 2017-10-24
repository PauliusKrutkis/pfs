<?php


namespace Pfs;


class Filter
{
    private $title = '';
    private $options = [];
    private $slug = '';
    private $type = '';
    private $template = '';

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

    public function getHtml()
    {
        $view = new View($this->template);
        $view->set('filter', $this);

        $view->output();
    }
}