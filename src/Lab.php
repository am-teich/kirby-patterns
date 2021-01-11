<?php

namespace amteich\Patterns;

use Kirby\Cms\Asset;
use Kirby\Cms\Media;
use Kirby\Toolkit\A;
use Exception;
use Kirby\Toolkit\F;
use Kirby\Cms\Html;
use Kirby\Toolkit\File;
use Kirby\Toolkit\Obj;
use Kirby\Http\Response;
use Kirby\Http\Router;
use Kirby\Toolkit\Tpl;
use Htmlawed;

class Lab
{

    static $instance = null;
    static $mode;

    public $path;
    public $title;
    public $root;

    protected $data;

    public function __construct()
    {
        $this->kirby = kirby();
        $this->path = option('amteich.patterns.path', 'patterns');
        $this->title = option('amteich.patterns.title', 'Patterns');
        $this->root = option('amteich.patterns.directory', $this->kirby->roots()->site() . '/patterns');

        $lab = $this;
        static::$instance = $this;
    }

    public static function instance()
    {
        return static::$instance = (is_null(static::$instance) ? new Lab() : static::$instance);
    }

    public static function routes() {
        return [
            [
                'pattern' => Lab::instance()->path . '/(:all?)',
                'action' => function($path = null) {
                    return Lab::instance()->run($path);
                }
            ],
        ];
    }

    public function view($name, $data = [])
    {
        $data = array_merge($this->data(), (array)$data);
        return tpl::load(dirname(__DIR__) . '/' . 'views' . '/' . $name . '.php', $data);
    }

    public function data()
    {
        if (!is_array($this->data)) {
            $this->data = [
                'site' => $this->kirby->site(),
                'pages' => $this->kirby->site()->children(),
                'page' => $this->kirby->site()->find(option('home')),
                'lab' => $this
            ];
        }
        return $this->data;
    }

    public function path()
    {
        return $this->path;
    }

    public function title()
    {
        return $this->title;
    }

    public function root()
    {
        return $this->root;
    }

    public function url()
    {
        return url($this->path());
    }

    public function run($path = '/')
    {
        if (option('amteich.patterns.lock') && !$this->kirby->user()) {
            go(option('amteich.patterns.error'));
        }

        $router = new Router([
            [
                'pattern' => '/',
                'action' => function () {

                    $readme = Lab::instance()->root() . '/' . 'readme.md';

                    if (!is_dir(Lab::instance()->root())) {
                        $modal = Lab::instance()->view('modals/folder');
                    } else {
                        $modal = null;
                    }

                    if (is_file($readme)) {
                        $markdown = kirbytext(f::read($readme));
                    } else {
                        $markdown = null;
                    }

                    return Lab::instance()->view('layouts/main', [
                        'title' => Lab::instance()->title(),
                        'menu' => Lab::instance()->menu(),
                        'content' => Lab::instance()->view('views/dashboard', ['markdown' => $markdown]),
                        'modal' => $modal
                    ]);

                }
            ],
            [
                'pattern' => 'assets/(:any)',
                'action' => function ($file) {

                    switch ($file) {
                        case 'index.js':
                        case 'index.min.js':
                            $mime = 'text/javascript';
                            break;
                        case 'index.css':
                        case 'index.min.css':
                            $mime = 'text/css';
                            break;
                        default:
                            return new Response('Not found', 'text/html', 404);
                            break;
                    }

                    // build the root for the file
                    $file = dirname(__DIR__) . '/assets/dist/' . $file;
                    return new Response(f::read($file), $mime);

                }
            ],
            [
                'pattern' => '(:all)/preview',
                'action' => function ($path) {

                    Lab::$mode = 'preview';

                    $pattern = new Pattern($path);
                    $config = $pattern->config();

                    try {
                        $html = $pattern->render();
                    } catch (Exception $e) {
                        $html = '';
                    }

                    if ($pattern->isRawPreview()) {
                      return $html;
                    }
                    else {
                      return Lab::instance()->view('views/preview', [
                          'pattern' => $pattern,
                          'html' => $html,
                          'head' => $pattern->head(),
                          'script' => $pattern->script(),
                          'bodyattributes' => $pattern->bodyattributes(),
                          'background' => a::get($config, 'background', option('amteich.patterns.preview.background')),
                          'css' => option('amteich.patterns.preview.css'),
                          'js' => option('amteich.patterns.preview.js')
                      ]);
                    }

                }
            ],
            [
                'pattern' => '(:all)',
                'action' => function ($path) {

                    if (get('view') == 'htmlpreview') {
                        Lab::$mode = 'htmlpreview';
                    }

                    $pattern = new Pattern($path);
                    $file = null;

                    if (!$pattern->exists()) {

                        $filename = basename($path);
                        $path = dirname($path);

                        if ($path == '.') {
                            $preview = Lab::instance()->view('previews/error', [
                                'error' => 'The pattern could not be found'
                            ]);
                        } else {

                            $pattern = new Pattern($path);
                            $file = $pattern->files()->get($filename);

                            if ($file) {
                                $preview = Lab::instance()->preview($pattern, $file);
                            } else {
                                $preview = Lab::instance()->view('previews/error', [
                                    'error' => 'The file could not be found'
                                ]);
                            }
                        }

                    } else if ($file = $pattern->files()->get($pattern->name() . '.twig')) {
                        go($pattern->url() . '/' . $file->filename());
                    } else if ($file = $pattern->files()->first()) {
                        go($pattern->url() . '/' . $file->filename());
                    } else {
                        $preview = Lab::instance()->view('previews/empty');
                    }

                    if ($pattern->isHidden()) {
                        go(Lab::instance()->url());
                    }

                    return Lab::instance()->view('layouts/main', [
                        'title' => Lab::instance()->title() . ' / ' . $pattern->title(),
                        'menu' => Lab::instance()->menu(null, $path),
                        'content' => Lab::instance()->view('views/pattern', [
                            'preview' => $preview,
                            'info' => Lab::instance()->view('snippets/info', [
                                'pattern' => $pattern,
                                'currentFile' => $file,
                            ])
                        ])
                    ]);

                }
            ]
        ]);

        if ($routeContent = $router->call($path ? $path : '/')) {
            // ?? return new Response(call($route->action(), $route->arguments()));
            return new Response($routeContent);
        } else {
            go('error');
        }

    }

    public function menu($patterns = null, $path = '')
    {
        if (is_null($patterns)) {
            $pattern = new Pattern();
            $patterns = $pattern->children();
        }

        if (!$patterns->count()) return null;

        $html = ['<ul class="nav">'];

        foreach ($patterns as $pattern) {

            if ($pattern->isHidden()) continue;

            $html[] = '<li>';

            if ($pattern->status()) {
              $patternstate = '<div title="' . $pattern->statusTitle() . '" class="patternstatus patternstatus--' . $pattern->status() . '"></div>';
            }
            else {
              $patternstate = '';
            }

            $html[] = Html::a($pattern->url(), ['<span>', $pattern->title(), '</span>', $patternstate], ['class' => $path == $pattern->path() ? 'active' : null]);

            if ($pattern->isOpen($path)) {
                $html[] = $this->menu($pattern->children(), $path);
            }

            $html[] = '</li>';

        }

        $html[] = '</ul>';

        return implode(array_filter($html));
    }

    /**
     * @param Pattern $pattern
     * @param File $file
     * @return string
     */
    public function preview($pattern, $file)
    {
        $data = [
            'pattern' => $pattern,
            'file' => $file,
        ];

        if (get('raw') == 'true') {
            $this->raw($pattern, $file);
        }
        else if ($file->filename() == $pattern->name() . '.html.php' or 
                 $file->filename() == $pattern->name() . '.twig') {

            $views = ['preview', 'htmlpreview', 'twig'];
            $snippet = 'html';

            // pass the mode to the template
            $data['view'] = in_array(get('view'), $views) ? get('view') : option('amteich.patterns.preview.mode', 'preview');

            switch ($data['view']) {
                case 'preview':
                    try {
                        lab::$mode = 'preview';
                        //$pattern->render();
                        $data['content'] = '<iframe src="' . $pattern->url() . '/preview"></iframe>';
                    } catch (Exception $e) {
                        $data['content'] = $this->error($e);
                    }
                    break;
                case 'twig':
                    $data['content'] = $this->codeblock($file);
                    break;
                case 'htmlpreview':
                    $data['content'] = $this->codeblock($pattern);
                    break;
            }

        } else if (in_array(strtolower($file->extension()), ['gif', 'jpg', 'jpeg', 'svg', 'png'])) {

            $snippet = 'image';
            $url = $pattern->url() . '/' . $file->filename() . '?raw=true';
            $data['url'] = $url;

        } else if (in_array(strtolower($file->extension()), ['md', 'mdown'])) {

            $snippet = 'markdown';
            $data['content'] = kirbytext($file->read());

        } else {

            $ext = $file->extension();
            $code = ['twig', 'html', 'js', 'css', 'scss', 'sass', 'less', 'json', 'txt'];

            if (in_array($ext, $code)) {
                $snippet = 'code';
                $data['content'] = $this->codeblock($file);
            } else {
                $snippet = 'empty';
            }

        }

        return $this->view('previews/' . $snippet, $data);

    }

    /**
     * @param Pattern $pattern
     * @param File $file
     * @return \Kirby\Cms\Response
     */
    public function raw($pattern, $file)
    {
        die(new \Kirby\Cms\Response($file->read(), $file->mime()));
    }

    public function codeblock($object, $lang = 'markup')
    {
        $langs = [
            'css' => 'css',
            'twig' => 'twig',
            'js' => 'js',
            'scss' => 'sass',
            'sass' => 'sass',
            'md' => 'markdown',
            'mdown' => 'markdown',
        ];

        try {

            if (is_a($object, 'Kirby\\Toolkit\\File')) {
                $code = $object->read();
                $lang = a::get($langs, $object->extension(), 'markup');
            } else if (is_a($object, 'Kirby\\Patterns\\Pattern')) {
                $code = Htmlawed::filter($object->render(), ['tidy' => 1]);
                $lang = 'php';
            } else if (is_string($object)) {
                $code = $object;
            } else {
                $code = '';
            }

        } catch (Exception $e) {
            return $this->error($e);
        }

        if (strlen($code) > 20000) {
            $lang = 'none';
        }

        return '<pre><code class="language-' . $lang . '">' . htmlspecialchars(trim($code)) . '</code></pre>';

    }

    public function error($e)
    {
        return '<div class="error">There\'s an error in your pattern: <strong>' . $e->getMessage() . '</strong></div>';
    }

    public function theme()
    {

        $assets = $this->kirby->roots()->index() . '/' . 'assets' . '/' . 'patterns';

        $theme = new Obj;
        $theme->css = file_exists($assets . '/' . 'index.css') ? 'assets/patterns/index.css' : $this->url() . '/assets/index.min.css';
        $theme->js = file_exists($assets . '/' . 'index.js') ? 'assets/patterns/index.js' : $this->url() . '/assets/index.min.js';

        return $theme;

    }

}
