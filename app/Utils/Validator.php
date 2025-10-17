<?php
namespace App\Utils;

class Validator
{
    public static function email($email)
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function phone($phone)
    {
        $normalized = preg_replace('/[()\s-]+/', '', $phone);
        return (bool) preg_match('/^\+?[0-9]{7,15}$/', $normalized);
    }

    public static function required($data, $fields)
    {
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: $field");
            }
        }
    }

    public static function inArray($value, $array)
    {
        return in_array($value, $array, true);
    }
}
