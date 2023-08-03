<?php

use ishop\App;

?>
<div class="dropdown d-inline-block">
    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
        <img
                src="<?= PATH ?>/assets/img/lang/<?= App::$app->getProperty('language')['code'] ?>.png"
                alt="<?= App::$app->getProperty('language')['code'] ?>"
        >
    </a>
    <ul class="dropdown-menu" id="languages">
        <?php foreach ($this->languages as $langCode => $langInfo): ?>
            <?php
            if (
                App::$app->getProperty('language')['code']
                === $langCode
            ) continue;
            ?>
            <li>
                <button
                        class="dropdown-item"
                        data-langcode="<?= $langCode ?>"
                >
                    <img
                            src="<?= PATH ?>/assets/img/lang/<?= $langCode ?>.png"
                            alt="<?= $langCode ?>"
                    >
                    <?= $langInfo['title'] ?>
                </button>
            </li>
        <?php endforeach; ?>
    </ul>
</div>