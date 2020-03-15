<?php

class CZ_Utils
{
    public static function filterArray($arFieldsDefault, $arFieldsNeed): array
    {
        return array_intersect_key($arFieldsDefault, array_flip($arFieldsNeed));
    }

    /**
     * @param  array|string  $dat
     *
     * @return array|string
     */
    public static function convert_to_utf8_recursively($dat)
    {
        if (is_string($dat)) {
            return mb_convert_encoding($dat, 'UTF-8', 'UTF-8');
        }

        if (is_array($dat)) {
            $ret = [];
            foreach ($dat as $i => $d) {
                $ret[$i] = self::convert_to_utf8_recursively($d);
            }
            return $ret;
        }

        return $dat;
    }

    public static function convertMapStringToArray(string $coords):array {
        $arCoords = explode(',', $coords);

        if (count($arCoords) === 2){
            return [
                'long' => (float) strip_tags($arCoords[1]),
                'lat' => (float) strip_tags($arCoords[0])
            ];
        }

        return [];
    }
}