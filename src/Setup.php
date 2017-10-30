<?php


namespace Pfs;


class Setup
{
    private $styles;
    private $scripts;
    private $localize;
    private $directory;

    public function __construct($directory)
    {
        Config::load($directory);

        $this->directory = str_replace(ABSPATH, '/', $directory);
        $this->styles    = Config::get('enqueue')['styles'];
        $this->scripts   = Config::get('enqueue')['scripts'];
        $this->localize  = Config::get('enqueue')['localize'];

        $this->addActions();
    }

    public function addAssets()
    {
        foreach ($this->styles as $name => $options) {
            wp_enqueue_style($name, $this->directory . $options['src']);
        }

        foreach ($this->scripts as $name => $options) {
            wp_enqueue_script(
                $name,
                $this->directory . $options['src'],
                $options['deps'],
                $options['ver'],
                $options['in-footer']
            );
        }

        foreach ($this->localize as $name => $options) {
            wp_localize_script(
                $name,
                $options['prefix'],
                $options['strings']
            );
        }
    }

    public function setupPageTemplate($template)
    {
        /** @var Navigation $navigation */
        $instances = $this->getNavigations();

        if ( ! $instances) {
            return $template;
        }

        foreach ($instances as $navigation) {
            if (is_page($navigation->getPageId())) {
                $view = new View('page');

                return $view->getFilaPath();
            }
        }

        return $template;
    }

    public function addRewrites()
    {
        /** @var Navigation $navigations */
        $instances = $this->getNavigations();

        if ( ! $instances) {
            return '';
        }

        foreach ($instances as $navigation) {
            $pageId      = $navigation->getPageId();
            $pageSlug    = get_post($pageId)->post_name;
            $position    = 2;
            $rule        = sprintf('^%s', $pageSlug);
            $queryString = sprintf('index.php?page_id=%s', $pageId);

            /** @var Filter $filter */
            foreach ($navigation->getFilters() as $filter) {
                $filterSlug  = $filter->getSlug();
                $rule        .= sprintf('(\/%s/([^/]*))?', $filterSlug);
                $queryString .= sprintf('&%s=$matches[%s]', $filterSlug, $position);
                $position    += 2;

                add_rewrite_tag('%' . $filterSlug . '%', '([^&]+)');
            }

            $rule        .= sprintf('(\/%s\/([0-9]+))?$', 'page');
            $queryString .= sprintf('&%s=$matches[%s]', 'paged', $position);

            add_rewrite_rule($rule, $queryString, 'top');
        }
    }

    private function getNavigations()
    {
        $settings    = apply_filters('pfs_navigation', null);
        $navigations = [];

        if ( ! $settings) {
            return '';
        }

        foreach ($settings as $setting) {
            $navigation = new Navigation();
            $filters    = [];
            $order      = 0;

            $navigation->setPaged($setting['paged']);
            $navigation->setPageId($setting['page']);
            $navigation->setQuery($setting['query']);

            if (isset($setting['ajax'])) {
                $navigation->setAjax($setting['ajax']);
            }

            foreach ($setting['filters'] as $arguments) {
                $filter = new Filter($arguments['title'], $arguments['type'], $arguments['template']);
                $filter->setOrder($order);

                if (isset($arguments['slug'])) {
                    $filter->setSlug($arguments['slug']);
                }

                if (isset($arguments['taxonomy'])) {
                    $filter->setTaxonomy($arguments['taxonomy']);
                }

                if (isset($arguments['meta'])) {
                    $filter->setMeta($arguments['meta']);
                }

                if (isset($arguments['values'])) {
                    $options = [];
                    foreach ($arguments['values'] as $key => $value) {
                        $option    = new Option($key, $value);
                        $options[] = $option;
                    }

                    $filter->setOptions($options);
                }

                $filters[] = $filter;
                $order++;
            }

            $navigation->setFilters($filters);

            $navigations[] = $navigation;
        }

        if ( ! is_array($navigations)) {
            return [$navigations];
        }

        return $navigations;
    }

    public function loadNavigationInstance()
    {
        $instances = $this->getNavigations();

        if ( ! $instances) {
            throw new \Exception("An error ocured while loading Layered_Navigation instance");
        }

        /** @var Navigation $navigation */
        foreach ($instances as $navigation) {
            if (is_page($navigation->getPageId())) {
                return $navigation;
            }
        }

        return '';
    }

    public function getNavigation()
    {
        $instances  = $this->getNavigations();
        $navigation = null;
        $pageId     = $_GET['page'];
        $data       = $_GET['data'];

        foreach ($instances as $instance) {
            /** @var Navigation $instance */
            if ($instance->getPageId() == $pageId) {
                $navigation = $instance;
                break;
            }
        }

        foreach ($data as $filter) {
            $slug = $filter['slug'];

            if ($slug == 'page') {
                $slug = 'paged';
            }

            set_query_var($slug, implode(',', $filter['values']));
        }

        $response = [
            '[data-pfs-posts]'      => $navigation->getHtml('posts'),
            '[data-pfs-pagination]' => $navigation->getHtml('pagination')
        ];

        header("Content-type: application/json");
        echo json_encode($response);

        die();
    }

    private function addActions()
    {
        add_action('wp_enqueue_scripts', [$this, 'addAssets']);
        add_filter('template_include', [$this, 'setupPageTemplate'], 99);
        add_action('init', [$this, 'addRewrites']);
        add_filter('navigation_instance', [$this, 'loadNavigationInstance']);
        add_action('wp_ajax_getNavigation', [$this, 'getNavigation']);
        add_action('wp_ajax_nopriv_getNavigation', [$this, 'getNavigation']);
    }
}
