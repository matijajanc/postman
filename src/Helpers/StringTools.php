<?php

declare(strict_types=1);

namespace Matijajanc\Postman\Helpers;

class StringTools
{
    /**
     * Convert snake_case to camelCase
     *
     * @param string $input
     * @return string
     */
    public static function camelToSnakeCase(string $input): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match === strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }

    /**
     * Convert camelCase to snake_case
     *
     * @param string $input
     * @param bool $capitalizeFirst
     * @return string
     */
    public static function snakeToCamelCase(string $input, bool $capitalizeFirst = false): string
    {
        $str = str_replace('_', '', ucwords($input, '_'));
        if (!$capitalizeFirst) {
            $str = lcfirst($str);
        }

        return $str;
    }
}
