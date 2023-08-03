<?php

namespace app\models;

use ishop\App;
use ishop\helpers\data\constants\Properties;

class Breadcrumbs extends AppModel
{

    public static function getBreadcrumbs(int $categoryId, string $name = ''): string
    {
        $langCode = App::$app->getProperty(Properties::LANGUAGE)['code'];
        $categories = App::$app->getProperty("categories_{$langCode}");

        $breadCrumbsArray = self::getParts($categories, $categoryId);
        $breadcrumbs =
            "<li class='breadcrumb-item'>
                <a href='" . getLangCorrectBaseUrl() . "'>"
                    . getPhrase('tpl_home_breadcrumbs') . "
                </a>
             </li>";

        if ($breadCrumbsArray) {
            foreach ($breadCrumbsArray as $slug => $title) {
                $breadcrumbs .=
                    "<li class='breadcrumb-item'>
                        <a href='category/{$slug}'>{$title}</a>
                    </li>";
            }
        }

        if ($name) {
            $breadcrumbs .=
                "<li class='breadcrumb-item active'>$name</li>";
        }

        return $breadcrumbs;
    }

    public static function getParts(array $cats, int $id): array|false
    {
        if (!$id) {
            return false;
        }

        $breadcrumbs = [];

        foreach ($cats as $category) {
            if (isset($cats[$id])) {
                $breadcrumbs[$cats[$id]['slug']] = $cats[$id]['title'];
                $id = $cats[$id]['parent_id'];
            } else {
                break;
            }
        }

        return array_reverse($breadcrumbs, true);
    }

}