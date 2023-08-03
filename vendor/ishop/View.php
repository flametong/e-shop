<?php

namespace ishop;

use RedBeanPHP\R;

class View
{

    public string $content = '';

    public function __construct(
        public $route,
        public $layout = '',
        public $view = '',
        public $meta = []
    ){
        $this->layout = $this->layout ?: LAYOUT;
    }

    public function render($data): void
    {

        if (is_array($data)) {
            extract($data);
        }

        $prefix = str_replace(
            '\\',
            '/',
            $this->route['admin_prefix']
        );
        $view_file = APP . "/views/{$prefix}{$this->route['controller']}/{$this->view}.php";

        if (!is_file($view_file)) {
            throw new \Exception("Не найден вид {$view_file}", 500);
        }

        ob_start();
        require_once $view_file;
        $this->content = ob_get_clean();

        if ($this->layout) {
            $layout_file = APP . "/views/layouts/{$this->layout}.php";

            if (!is_file($layout_file)) {
                throw new \Exception("Не найден шаблон {$layout_file}", 500);
            }

            require_once $layout_file;
        }

    }

    public function getMeta(): string
    {
        $description = $this->meta['description'] ?: '';
        $keywords = $this->meta['keywords'] ?: '';

        $out =
            '<title>'
            . App::$app->getProperty('site_name')
            . ' :: '
            . h($this->meta['title'])
            . '</title>'
            . PHP_EOL;
        $out .=
            '<meta name="description" content="'
            . h($description)
            . '">'
            . PHP_EOL;
        $out .=
            '<meta name="keywords" content="'
            . h($keywords)
            . '">'
            . PHP_EOL;

        return $out;
    }

    public function getDbLogs(): void
    {
        if (DEBUG) {
            $logs = R::getDatabaseAdapter()
                ->getDatabase()
                ->getLogger();
            $logs = array_merge(
                $logs->grep('SELECT'),
                $logs->grep('INSERT'),
                $logs->grep('UPDATE'),
                $logs->grep('DELETE'),
            );
        }
    }

    public function getPart(string $file, $data = null): void
    {
        if (is_array($data)) {
            extract($data);
        }

        $file = APP . "/views/{$file}.php";

        if (is_file($file)) {
            require $file;
        } else {
            echo "File {$file} not found...";
        }
    }

}