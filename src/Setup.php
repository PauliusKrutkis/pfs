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
        $styles   = apply_filters('pfs_styles', $this->styles);
        $scripts  = apply_filters('pfs_scripts', $this->scripts);
        $localize = apply_filters('pfs_localize', $this->localize);

        foreach ($styles as $name => $options) {
            wp_enqueue_style($name, $this->directory . $options['src']);
        }

        foreach ($scripts as $name => $options) {
            wp_enqueue_script(
                $name,
                $this->directory . $options['src'],
                $options['deps'],
                $options['ver'],
                $options['in-footer']
            );
        }

        foreach ($localize as $name => $options) {
            wp_localize_script(
                $name,
                $options['prefix'],
                $options['strings']
            );
        }
    }

    public function setupPageTemplate($template)
    {
        /** @var Navigation $layeredNavigation */
        $instances = $this->getNavigations();

        if ( ! $instances) {
            return $template;
        }

        foreach ($instances as $layeredNavigation) {
            if ( ! $layeredNavigation instanceof Navigation) {
                throw new \Exception("layered_navigation hook requires Layered_Navigation object(s)");
            }

            if (is_page($layeredNavigation->getPageId())) {
                $view = new View('page');

                return $view->getFilaPath();
            }
        }

        return $template;
    }

    public function addRewrites()
    {
        /** @var Navigation $navigations */
        $navigations = $this->getNavigations();

        if ( ! $navigations) {
            return '';
        }

        foreach ($navigations as $navigation) {
            if ( ! $navigation instanceof Navigation) {
                // TODO change message
                throw new \Exception("layered_navigation hook requires Filters object(s)");
            }

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

        foreach ($settings as $setting) {
            $navigation = new Navigation();
            $filters    = [];
            $order      = 0;

            $navigation->setPaged($setting['paged']);
            $navigation->setPageId($setting['page']);
            $navigation->setQuery($setting['query']);

            foreach ($setting['filters'] as $arguments) {
                $filter = new Filter($arguments['title'], $arguments['type'], $arguments['template']);
                $filter->setOrder($order);

                if (isset($arguments['slug'])) {
                    $filter->setSlug($arguments['slug']);
                }

                if (isset($arguments['taxonomy'])) {
                    $filter->setTaxonomy($arguments['taxonomy']);
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

    public
    function addActiveFilters(
        $localize
    ) {
        $instances = $this->getNavigations();
        /** @var Navigation $filters */

        foreach ($instances as $filters) {
            if (is_page($filters->getPageId())) {
                $localize['scripts'] = [
                    'prefix'  => 'pfs',
                    'strings' => [
                        'pageUrl'       => get_post($filters->getPageId())->post_name,
                        'activeFilters' => $filters->getActiveFilters()
                    ]
                ];
            }
        }

        return $localize;
    }

    public
    function loadNavigationInstance()
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

    private
    function addActions()
    {
        add_action('wp_enqueue_scripts', [$this, 'addAssets']);
        add_filter('template_include', [$this, 'setupPageTemplate'], 99);
        add_action('init', [$this, 'addRewrites']);
        add_filter('pfs_localize', [$this, 'addActiveFilters']);
        add_filter('navigation_instance', [$this, 'loadNavigationInstance']);
    }
}