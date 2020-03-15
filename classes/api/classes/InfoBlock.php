<?php

abstract class InfoBlock
{
    protected $arSelectFields;

    protected function getFields(_CIBElement $obItem): array
    {
        return CZ_Utils::filterArray(
            $obItem->GetFields(),
            $this->arSelectList['fields']
        );
    }

    protected function getProperties(_CIBElement $obItem): array
    {
        return CZ_Utils::filterArray($obItem->GetProperties(),
            $this->arSelectList['properties']);
    }

    protected function filterProperties($arProperties){
        foreach ($this->arSelectList['properties'] as $propertyCode) {
            if ($arProperties[$propertyCode]['PROPERTY_TYPE'] === 'L') {
                $arProperties[$propertyCode]
                    = $arProperties[$propertyCode]['VALUE_XML_ID'];
            } elseif ($arProperties[$propertyCode]['PROPERTY_TYPE'] === 'S'){
                if ($arProperties[$propertyCode]['USER_TYPE'] === 'HTML'){
                    $arProperties[$propertyCode] = $arProperties[$propertyCode]['VALUE']['TEXT'];
                }elseif($arProperties[$propertyCode]['USER_TYPE'] === 'block_text_title'){
                    $arProperties[$propertyCode] = $arProperties[$propertyCode]['VALUE'][0]['TEXT'];
                }elseif($arProperties[$propertyCode]['USER_TYPE'] === 'map_yandex'){
                    $arProperties[$propertyCode] = CZ_Utils::convertMapStringToArray($arProperties[$propertyCode]['VALUE']);
                }else{
                    $arProperties[$propertyCode]
                        = $arProperties[$propertyCode]['VALUE'];
                }
            } else{
                $arProperties[$propertyCode]
                    = $arProperties[$propertyCode]['VALUE'];
            }
        }

        return $arProperties;
    }

    protected function groupLocalizationProperties(array $properties):array
    {
        $newProperties = [];

        foreach ($properties as $property => $value){
            $newProperty = $property;

            if (mb_stripos(strrev($newProperty), 'ne_') === 0){
                $newProperty = substr($newProperty, 0, -3);
                $newProperties[$newProperty]['en'] = $value;
            }elseif (mb_stripos(strrev($newProperty), 'ur_') === 0){
                $newProperty = substr($newProperty, 0, -3);
                $newProperties[$newProperty]['ru'] = $value;
            }else{
                $newProperties[$newProperty] = $value;
            }
        }
        return $newProperties;
    }
}