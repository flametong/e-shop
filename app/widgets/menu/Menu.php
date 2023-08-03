<?php


namespace app\widgets\menu;


use ishop\App;
use ishop\Cache;

class Menu
{

    protected array $data;
    protected array $tree;
    protected string $menuHtml;
    protected string $tpl;
    protected string $container = 'ul';
    protected string $class = 'menu';
    protected int $cache = 3600;
    protected string $cacheKey = 'ishop_menu';
    protected array $attrs = [];
    protected string $prepend = '';
    protected array $language;

    public function __construct($options = [])
    {
        $this->language = App::$app->getProperty('language');
        $this->tpl = __DIR__ . '/menu_tpl.php';
        $this->getOptions($options);
        $this->run();
    }

    protected function getOptions(array $options): void
    {
        foreach ($options as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }

    protected function getTree(): array
    {
        $tree = [];
        $data = $this->data;

        foreach ($data as $id => &$node) {
            if (!$node['parent_id']) {
                $tree[$id] = &$node;
            } else {
                $data[$node['parent_id']]['children'][$id] = &$node;
            }
        }

        return $tree;
    }

    protected function catToTemplate(
        array $category,
        string $tab,
        int $id
    ): string
    {
        ob_start();
        require $this->tpl;
        return ob_get_clean();
    }

    protected function getMenuHtml(array $tree, string $tab = ''): string
    {
        $str = '';

        foreach ($tree as $id => $category) {
            $str .= $this->catToTemplate($category, $tab, $id);
        }

        return $str;
    }

    protected function output(): void
    {
        $attrs = '';

        if (!empty($this->attrs)) {
            foreach ($this->attrs as $k => $v) {
                $attrs .= " $k='$v' ";
            }
        }

        echo "<{$this->container} class='{$this->class}' $attrs>";
        echo $this->prepend;
        echo $this->menuHtml;
        echo "</{$this->container}>";
    }

    protected function run(): void
    {
        $cache = Cache::getInstance();
        $this->menuHtml =
            $cache->get("{$this->cacheKey}_{$this->language['code']}");

        if (!$this->menuHtml) {
            $this->data = App::$app->getProperty("categories_{$this->language['code']}");

            $this->tree = $this->getTree();
            $this->menuHtml = $this->getMenuHtml($this->tree);

            if ($this->cache) {
                $cache->set(
                    "{$this->cacheKey}_{$this->language['code']}",
                    $this->menuHtml,
                    $this->cache
                );
            }
        }

        $this->output();
    }

}