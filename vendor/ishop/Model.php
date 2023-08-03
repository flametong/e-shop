<?php

namespace ishop;

use RedBeanPHP\R;
use Valitron\Validator;

abstract class Model
{

    public array $attributes = [];
    public array $errors = [];
    public array $rules = [];
    public array $labels = [];

    public function __construct()
    {
        Db::getInstance();
    }

    public function load($post = true): void
    {
        $data = $post ? $_POST : $_GET;

        foreach ($this->attributes as $key => $value) {
            if (isset($data[$key])) {
                $this->attributes[$key] = $data[$key];
            }
        }
    }

    public function validate(array $data): bool
    {
        Validator::langDir(APP . '/languages/validator');
        $langCode = App::$app->getProperty('language')['code'];
        Validator::lang($langCode);

        $validator = new Validator($data);
        $validator->rules($this->rules);
        $validator->labels($this->getLabels());

        if ($validator->validate()) {
            return true;
        } else {
            $this->errors = $validator->errors();
            return false;
        }
    }

    public function setErrors(): void
    {
        $errors = '<ul>';

        foreach ($this->errors as $error) {
            foreach ($error as $item) {
                $errors .= "<li>$item</li>";
            }
        }

        $errors .= '</ul>';

        $_SESSION['errors'] = $errors;
    }

    public function getLabels(): array
    {
        $labels = [];

        foreach ($this->labels as $key => $value) {
            $labels[$key] = getPhrase($value);
        }

        return $labels;
    }

    public function save(string $table): int|string
    {
        $tbl = R::dispense($table);

        foreach ($this->attributes as $key => $value) {
            if ($value !== '') {
                $tbl->$key = $value;
            }
        }

        return R::store($tbl);
    }

    public function update(string $table, int $id): int|string
    {
        $tbl = R::load($table, $id);

        foreach ($this->attributes as $key => $value) {
            if ($value !== '') {
                $tbl->$key = $value;
            }
        }

        return R::store($tbl);
    }

    public function hashAttribute(string $attr): void
    {
        if (!isset($this->attributes[$attr])) {
            return;
        }

        $this->attributes[$attr] = password_hash(
            $this->attributes[$attr],
            PASSWORD_DEFAULT
        );
    }

}