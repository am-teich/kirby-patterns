<?php

namespace Kirby\Patterns;

use Kirby\Cms\File;
use Kirby\Cms\Media;
use Kirby\Image\Image;
use Kirby\Cms\Collection;
use Kirby\Cms\Dir;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\A;
use Exception;
use Kirby\Toolkit\Tpl;

class Pattern
{

    public $lab;
    public $name;
    public $path;
    public $data;
    public $root;
    public $config = null;

    public function __construct($path = '', $data = [])
    {
        $this->lab = Lab::instance();
        $this->name = basename($path);
        $this->path = trim($path, '/');
        $this->root = $this->lab->root() . '/' . $path;
        $this->data = $data;
    }

    public function file($ext)
    {
        return $this->root . '/' . $this->name . '.' . $ext;
    }

    public function defaults()
    {
        return (array)a::get($this->config(), 'defaults', []);
    }

    public function url()
    {
        return $this->lab->url() . '/' . $this->path;
    }

    public function isHidden()
    {
        return a::get($this->config(), 'hide', false);
    }

    public function data()
    {

        $data = $this->data;
        $defaults = $this->defaults();

        if (in_array(Lab::$mode, ['preview', 'htmlpreview'])) {
            $callback = a::get($this->config(), 'preview');
            $previewData = is_callable(($callback)) ? $callback->call($this, []) : [];
            $defaults = array_merge($defaults, $previewData);
        }

        foreach ($defaults as $key => $value) {
            if (!isset($this->data[$key]) and !isset($this->data[$key])) {
                if (is_a($value, 'Closure')) {
                    $data[$key] = call($value, [$this]);
                } else {
                    $data[$key] = $value;
                }
            } else if (isset($this->data[$key])) {
                $data[$key] = $this->data[$key];
            } else {
                //$data[$key] = tpl::$data[$key];
                $data[$key] = Lab::instance()->data()[$key];
            }
        }

        return $data;

    }

    public function template()
    {
        return tpl::load($this->file('html.php'), $this->data());
    }

    public function render()
    {
        return $this->template();
    }

    public function path()
    {
        return $this->path;
    }

    public function name()
    {
        return $this->name;
    }

    public function title()
    {
        return a::get($this->config(), 'title', $this->name());
    }

    public function files()
    {

        $files = new Collection;

        foreach (dir::read($this->root) as $file) {
            if (is_dir($this->root . '/' . $file)) continue;
            $url = $this->url() . '/' . $file . '?raw=true';
            //$media = new \Kirby\Toolkit\File($this->root . '/' . $file, $url);
            $media = new \Kirby\Toolkit\File($this->root . '/' . $file);
            $files->append($media->filename(), $media);
        }

        return $files;

    }

    public function isOpen($path)
    {
        if ($path == $this->path) {
            return true;
        } else if (str::startsWith($path, $this->path)) {
            return true;
        }
    }

    public function children()
    {

        $children = new Collection;

        foreach (dir::read($this->root) as $dir) {
            if (!is_dir($this->root . '/' . $dir)) continue;
            $pattern = new Pattern($this->path . '/' . $dir);
            $children->append($pattern->path(), $pattern);
        }

        return $children;

    }

    public function config()
    {

        if (!is_null($this->config)) return $this->config;

        $config = $this->file('config.php');

        if (file_exists($config)) {
            return $this->config = (array)require($config);
        } else {
            return $this->config = [];
        }

    }

    public function exists()
    {
        return is_dir($this->root);
    }

    public function __toString()
    {
        try {
            return (string)$this->render();
        } catch (Exception $e) {
            return '';
        }
    }

}
