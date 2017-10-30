<?php


namespace Pfs;


class View
{
    protected $file;
    protected $values;
    protected $dir;

    function __construct($file)
    {
        $this->dir  = Config::getPluginDir() . '/views';
        $this->file = $file;
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
        return $this->dir . '/' . $this->file . '.php';
    }

    public function setPartialDir($dir)
    {
        $this->dir = $dir;

        return $this;
    }
}