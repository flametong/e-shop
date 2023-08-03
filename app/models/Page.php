<?php

namespace app\models;

use RedBeanPHP\R;

class Page extends AppModel
{

    public function getPage(string $slug, int $langId): array
    {
        return R::getRow(
            "SELECT p.*, pd.*
                 FROM page p
                 JOIN page_description pd 
                     ON p.id = pd.page_id
                 WHERE p.slug = ? AND pd.language_id = ?",
            [$slug, $langId]
        );
    }

}