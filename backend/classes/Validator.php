<?php
declare(strict_types=1);

final class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function value(string $field, bool $trim = true): string
    {
        $v = (string) ($this->data[$field] ?? '');
        return $trim ? trim($v) : $v;
    }

    public function required(string $field, string $label = ''): self
    {
        if ($this->value($field) === '') {
            $this->addError($field, ($label ?: $field) . ' — обязательное поле');
        }
        return $this;
    }

    public function login(string $field): self
    {
        $v = $this->value($field);
        if ($v !== '' && !preg_match('/^[A-Za-z0-9]{6,}$/', $v)) {
            $this->addError($field, 'Логин: латиница и цифры, не менее 6 символов');
        }
        return $this;
    }

    public function password(string $field): self
    {
        $v = $this->value($field, false); 
        if ($v !== '') {
            if (mb_strlen($v) < 8) {
                $this->addError($field, 'Пароль: не менее 8 символов');
            } elseif (!preg_match('/\d/', $v)) {
                $this->addError($field, 'Пароль: минимум одна цифра');
            }
        }
        return $this;
    }

    public function fullName(string $field): self
    {
        $v = $this->value($field);
        if ($v !== '' && !preg_match('/^[А-Яа-яЁё\s]+$/u', $v)) {
            $this->addError($field, 'ФИО: только кириллица и пробелы');
        }
        return $this;
    }

    public function phone(string $field): self
    {
        $v = $this->value($field);
        if ($v !== '' && !preg_match('/^8\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $v)) {
            $this->addError($field, 'Телефон в формате 8(XXX)XXX-XX-XX');
        }
        return $this;
    }

    public function email(string $field): self
    {
        $v = $this->value($field);
        if ($v !== '' && filter_var($v, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($field, 'Некорректный e-mail');
        }
        return $this;
    }

    public function dateDmy(string $field): self
    {
        $v = $this->value($field);
        if ($v === '') {
            return $this;
        }
        if (!preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $v)) {
            $this->addError($field, 'Дата в формате ДД.ММ.ГГГГ');
            return $this;
        }
        $dt = DateTime::createFromFormat('d.m.Y', $v);
        if (!$dt || $dt->format('d.m.Y') !== $v) {
            $this->addError($field, 'Некорректная дата');
        }
        return $this;
    }

    public function timeHm(string $field): self
    {
        $v = $this->value($field);
        if ($v !== '' && !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $v)) {
            $this->addError($field, 'Время в формате ЧЧ:ММ');
        }
        return $this;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function errors(): array
    {
        return $this->errors;
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    public static function flashErrors(array $errors, array $old): void
    {
        $_SESSION['errors'] = $errors;
        unset($old['password'], $old['password_confirm']);
        $_SESSION['old'] = $old;
    }
}