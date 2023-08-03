<?php

namespace ishop;

abstract class Controller
{

    protected array $data = [];
    protected array $meta = [
        'title' => '',
        'keywords' => '',
        'description' => '',
    ];
    protected false|string $layout = '';
    protected string $view = '';
    protected object $model;

    public function __construct(
        public $route = []
    )
    {
    }

    public function getModel(): void
    {
        $model =
            'app\models\\' .
            $this->route['admin_prefix'] .
            $this->route['controller'];

        if (class_exists($model)) {
            $this->model = new $model();
        }
    }

    public function getView(): void
    {
        $this->view = $this->view ?: $this->route['action'];

        (new View(
            $this->route,
            $this->layout,
            $this->view,
            $this->meta
        ))->render($this->data);
    }

    public function set(mixed $data): void
    {
        $this->data = $data;
    }

    public function setMeta($title = '', $description = '', $keywords = ''): void
    {
        $this->meta = [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
        ];
    }

    public function isAjax(): bool
    {
        return
            isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    public function loadView(string $view, array $vars = []): never
    {
        extract($vars);
        $prefix = str_replace(
            '\\',
            '/',
            $this->route['admin_prefix']
        );
        require APP . "/views/{$prefix}{$this->route['controller']}/{$view}.php";
        exit;
    }

    public function error404(
        $folder = 'Error',
        $view = 404,
        $response = 404
    ): void
    {
        http_response_code($response);
        $this->setMeta(getPhrase('tpl_error_404'));
        $this->route['controller'] = $folder;
        $this->view = $view;
    }

}