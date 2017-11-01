<?php


namespace Pfs;


class View
{
    const THEME_TEMPLATE_DIR_NAME = 'pfs';

    protected $file;
    protected $values;
    protected $dir;
    protected $themeTemplateDir;

    function __construct($file)
    {
        $this->dir  = Config::getPluginDir() . '/views';
        $this->file = $file;

        $this->themeTemplateDir = get_template_directory() . '/' . self::THEME_TEMPLATE_DIR_NAME;
    }

    public function set($key, $value)
    {
        $this->values[$key] = $value;
    }

    public function get($key)
    {
        if ( ! array_key_exists($key, $this->values)) {
            throw new \Exception("{$key} is not set.");
        }

        return $this->values[$key];
    }

    public function output()
    {
        if ( ! file_exists($this->getFilaPath())) {
            throw new \Exception("Error loading template file {$this->file}.php.");
        }

        include $this->getFilaPath();
    }

    public function getHtml()
    {
        ob_start();

        $this->output();

        return ob_get_clean();
    }

    public function getFilaPath()
    {
        $dir = $this->dir;

        if (file_exists($this->themeTemplateDir . '/' . $this->file . '.php')) {
            $dir = $this->themeTemplateDir;
        }

        return $dir . '/' . $this->file . '.php';
    }

    public function setPartialDir($dir)
    {
        $this->dir = $dir;

        return $this;
    }
}