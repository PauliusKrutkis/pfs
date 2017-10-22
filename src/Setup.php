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
        /** @var Filters $layeredNavigation */
        $instances = $this->getFilterInstances();

        if ( ! $instances) {
            return $template;
        }

        foreach ($instances as $layeredNavigation) {
            if ( ! $layeredNavigation instanceof Filters) {
                throw new \Exception("layered_navigation hook requires Layered_Navigation object(s)");
            }

            if (is_page($layeredNavigation->getFilterPageId())) {
                $view = new \Pfs\View('filter-page');

                return $view->getFilaPath();
            }
        }

        return $template;
    }

    public function addRewrites()
    {
        /** @var Filters $filters */
        $instances = $this->getFilterInstances();

        if ( ! $instances) {
            return '';
        }

        foreach ($instances as $filters) {
            if ( ! $filters instanceof Filters) {
                throw new \Exception("layered_navigation hook requires Filters object(s)");
            }

            $pageId      = $filters->getFilterPageId();
            $pageSlug    = get_post($pageId)->post_name;
            $position    = 2;
            $rule        = sprintf('^%s', $pageSlug);
            $queryString = sprintf('index.php?page_id=%s', $pageId);

            foreach ($filters->getGroups() as $key => $options) {
                $filterSlug  = $filters->getFilterSlug($key);
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

    private function getFilterInstances()
    {
        $filtersArgs    = apply_filters('pfs_filters', null);
        $filtersObjects = [];

        foreach ($filtersArgs as $args) {
            $filters = new Filters();

            $filters->setPaged($args['paged']);
            $filters->setFilterPageId($args['page']);
            $filters->setQuery($args['base_query']);

            foreach ($args['groups'] as $id => $group) {
                switch ($group['type']) {
                    case 'taxonomy':
                        $filters->addTaxonomyFilter($id, $group);
                        break;
                    case 'meta':
                        $filters->addMetaFilter($id, [
                            'min' => $group['values'][0],
                            'max' => $group['values'][1]
                        ]);
                        break;
                }
            }

            $filtersObjects[] = $filters;
        }

        if ( ! is_array($filtersObjects)) {
            return [$filtersObjects];
        }

        return $filtersObjects;
    }

    public function addActiveFilters($localize)
    {
        $instances = $this->getFilterInstances();
        /** @var Filters $filters */

        foreach ($instances as $filters) {
            if (is_page($filters->getFilterPageId())) {
                $localize['scripts'] = [
                    'prefix'  => 'pfs',
                    'strings' => [
                        'pageUrl'       => get_post($filters->getFilterPageId())->post_name,
                        'activeFilters' => $filters->getActiveFilters()
                    ]
                ];
            }
        }

        return $localize;
    }

    public function loadFiltersInstance()
    {
        $instances = $this->getFilterInstances();

        if ( ! $instances) {
            throw new \Exception("An error ocured while loading Layered_Navigation instance");
        }

        /** @var Filters $filters */
        foreach ($instances as $filters) {
            if (is_page($filters->getFilterPageId())) {
                return $filters;
            }
        }

        return '';
    }

    private function addActions()
    {
        add_action('wp_enqueue_scripts', [$this, 'addAssets']);
        add_filter('template_include', [$this, 'setupPageTemplate'], 99);
        add_action('init', [$this, 'addRewrites']);
        add_filter('pfs_localize', [$this, 'addActiveFilters']);
        add_filter('filters_instance', [$this, 'loadFiltersInstance']);
    }
}