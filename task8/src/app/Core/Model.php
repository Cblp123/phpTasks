<?php

namespace App\Core;

abstract class Model
{
    // Константы для правил валидации
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_UNIQUE = 'unique';

    // Массив ошибок валидации
    public array $errors = [];

    // Загрузка данных в модель
    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    // Абстрактный метод для правил валидации
    abstract public function rules(): array;

    // Валидация данных модели
    public function validate()
    {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute};
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (!is_string($ruleName)) {
                    $ruleName = $rule[0];
                }
                if ($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addError($attribute, self::RULE_REQUIRED);
                }
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($attribute, self::RULE_EMAIL);
                }
                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addError($attribute, self::RULE_MIN, $rule);
                }
                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addError($attribute, self::RULE_MAX, $rule);
                }
                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $this->addError($attribute, self::RULE_MATCH, $rule);
                }
                if ($ruleName === self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttr = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $statement = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :attr");
                    $statement->bindValue(":attr", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if ($record) {
                        $this->addError($attribute, self::RULE_UNIQUE, ['field' => $attribute]);
                    }
                }
            }
        }
        return empty($this->errors);
    }

    // Добавление ошибки валидации
    public function addError(string $attribute, string $rule, $params = [])
    {
        $message = $this->errorMessages()[$rule] ?? '';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    // Сообщения об ошибках
    public function errorMessages()
    {
        return [
            self::RULE_REQUIRED => 'Это поле обязательное',
            self::RULE_EMAIL => 'Это поле должно быть действительным email адресом',
            self::RULE_MIN => 'Минимальная длина этого поля должна быть {min}',
            self::RULE_MAX => 'Максимальная длина этого поля должна быть {max}',
            self::RULE_MATCH => 'Это поле должно быть таким же, как {match}',
            self::RULE_UNIQUE => 'Запись с этим {field} уже существует'
        ];
    }

    // Проверка наличия ошибки по атрибуту
    public function hasError($attribute)
    {
        return $this->errors[$attribute] ?? false;
    }

    // Получение первой ошибки по атрибуту
    public function getFirstError($attribute)
    {
        return $this->errors[$attribute][0] ?? false;
    }
} 