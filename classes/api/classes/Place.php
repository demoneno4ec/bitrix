<?php

use Slim\Http\Request;
use Slim\Http\Response;

class Place extends InfoBlock
{
    private $iblockCode = 'places';
    private $iblockType = 'content';
    private $active = 'Y';
    protected $arSelectList
        = [
            'fields'     => [
                'ID',
                'SORT',
                'NAME',
                'DETAIL_PICTURE',
                'CODE',
                'IBLOCK_SECTION_ID',
                'IN_SECTIONS',
                'TIMESTAMP_X',
            ],
            'properties' => [
                'ACCREDITED',
                'CITY',
                'TAGS',
                'EMAIL',
                'ZIP',
                'VK',
                'INSTAGRAM',
                'LINK',
                'TWITTER',
                'TELEGRAM',
                'FACEBOOK',
                'PHONE',
                'PLACES',
                'MAP',
                'IMAGE',
                'FACTOID_PICTURE',

                'TITLE_RU',
                'VERDICT_RU',
                'ADDRESS_RU',
                'DESCRIPTION_RU',
                'HISTORY_RU',
                'SERVICES_RU',
                'FACTOID_NUMBER_RU',
                'FACTOID_NAME_RU',
                'FACTOID_DESCRIPTION_RU',
                'SERVICES_RU',
                'WORKTIME_RU',
                'HOWTOGET_RU',
                'DISCOUNT_RU',

                'TITLE_EN',
                'VERDICT_EN',
                'ADDRESS_EN',
                'DESCRIPTION_EN',
                'HISTORY_EN',
                'FACTOID_NUMBER_EN',
                'FACTOID_NAME_EN',
                'FACTOID_DESCRIPTION_EN',
                'SERVICES_EN',
                'WORKTIME_EN',
                'HOWTOGET_EN',
                'DISCOUNT_EN',
            ],
        ];

    public function getList(Request $request, Response $response)
    {
        $arResult = [];

        $arOrder = ['timestamp_x' => 'desc'];
        $arFilter = [
            'IBLOCK_TYPE' => $this->iblockType,
            'IBLOCK_CODE' => $this->iblockCode,
            'ACTIVE'      => $this->active,
        ];

        $arSelect = ['*'];
        $res = CIBlockElement::GetList($arOrder, $arFilter, false, false,
            $arSelect);

        $arSectionIDs = [];
        $arMapSections = [];

        $arCityIDs = [];
        $arMapCity = [];

        $arTagIDs = [];
        $arMapTags = [];

        $arPlaceIDs = [];
        $arMapPlaces = [];

        $arImageIDs = [];
        $arMapImages = [];

        $arFactoidImageIDs = [];
        $arMapFactoidImages = [];

        $timestamp = null;

        while ($obItem = $res->GetNextElement()) {
            $arItem = $this->getFields($obItem);

            if ($timestamp === null) {
                $timestamp = MakeTimeStamp($arItem['TIMESTAMP_X']);
            }

            $arItem['SECTION'] = '';
            $arItem['properties'] = $this->getProperties($obItem);
            $arItem['properties']
                = $this->filterProperties($arItem['properties']);

            $arItem['properties']
                = $this->groupLocalizationProperties($arItem['properties']);

            $itemID = (int) $arItem['ID'];
            $arResult[$itemID] = $arItem;

            $sectionID = (int) $arItem['IBLOCK_SECTION_ID'];

            $cityID = (int) $arItem['properties']['CITY'];
            $tagIDs = $arItem['properties']['TAGS'];
            $placesIDs = $arItem['properties']['PLACES'];

            $imageIDs = $arItem['properties']['IMAGE'];
            $factoidImageID = (int) $arItem['properties']['FACTOID_PICTURE'];

            $this->addElementSingleID($itemID, $sectionID, $arSectionIDs,
                $arMapSections);

            $this->addElementSingleID($itemID, $cityID, $arCityIDs, $arMapCity);
            $this->addElementMultipleID($itemID, $tagIDs, $arTagIDs,
                $arMapTags);
            $this->addElementMultipleID($itemID, $placesIDs, $arPlaceIDs,
                $arMapPlaces);

            $this->addElementMultipleID($itemID, $imageIDs, $arImageIDs,
                $arMapImages);
            $this->addElementSingleID($itemID, $factoidImageID,
                $arFactoidImageIDs, $arMapFactoidImages);
        }

        $arSections = $this->getSections($arSectionIDs);

        $this->setSectionInfo($arSections, $arMapSections, $arResult);

        $arResult = CZ_Utils::convert_to_utf8_recursively($arResult);

        return $response->withJson([
            'status'    => 200,
            'actions'   => $arResult,
            'timestamp' => (int) $timestamp,
        ], 200, JSON_UNESCAPED_UNICODE);
    }

    private function addElementMultipleID(
        $itemID,
        $elementIDs,
        &$arIDs,
        &$arMap
    ) {
        if (!empty($elementIDs) && is_array($elementIDs)) {
            foreach ($elementIDs as $elementID) {
                $elementID = (int) $elementID;
                $this->addElementSingleID($itemID, $elementID, $arIDs,
                    $arMap);
            }
        }
    }

    private function addElementSingleID($itemID, $elementID, &$arIDs, &$arMap)
    {
        if ($elementID > 0) {
            if (!isset($arMap[$elementID])) {
                $arMap[$elementID] = [];
                $arIDs[] = $elementID;
            }

            $arMap[$elementID] [] = $itemID;
        }
    }

    private function getSections($arIDs): array
    {
        $result = [];

        if (!empty($arIDs) && is_array($arIDs)) {
            $arFilter = [
                'ID'            => $arIDs,
                'GLOBAL_ACTIVE' => 'Y',
                'IBLOCK_ACTIVE' => 'Y',
                'IBLOCK_CODE'   => $this->iblockCode,
            ];
            $arSelect = ['CODE', 'ID'];
            $res = CIBlockSection::GetList([], $arFilter, false, $arSelect);
            while ($arSection = $res->fetch()) {
                $sectiondID = (int) $arSection['ID'];
                $result[$sectiondID] = $arSection['CODE'];
            }
        }

        return $result;
    }

    private function setSectionInfo(
        array $arSections,
        array $arMapSections,
        &$arResult
    ) {
        foreach ($arMapSections as $sectionID => $arItemIDs) {
            $sectionInfo = $arSections[$sectionID];
            foreach ($arItemIDs as $itemID) {
                $arResult[$itemID]['SECTION'] = $sectionInfo;
            }
        }
    }
}