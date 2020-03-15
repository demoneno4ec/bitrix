<?php

use Slim\Http\Request;
use Slim\Http\Response;

class Event extends InfoBlock
{
    private $iblockCode = 'events';
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
                'TIMESTAMP_X',
            ],
            'properties' => [
                'HIDE_DATES',
                'PLACE',
                'CITY',
                'FACEBOOK',
                'TWITTER',
                'YOUTUBE',
                'VK',
                'EMAIL',
                'INSTAGRAM',
                'LINK',
                'BUY',
                'PHONE',
                'MAP',

                'TITLE_RU',
                'ADDRESS_RU',
                'DESCRIPTION_RU',
                'PROGRAM_RU',
                'DISCOUNT_RU',

                'TITLE_EN',
                'ADDRESS_EN',
                'DESCRIPTION_EN',
                'PROGRAM_EN',
                'DISCOUNT_EN',
            ],
        ];

    public function getList(Request $request, Response $response): Response
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

        $timestamp = null;

        while ($obItem = $res->GetNextElement()) {
            $arItem = $this->getFields($obItem);

            if ($timestamp === null) {
                $timestamp = MakeTimeStamp($arItem['TIMESTAMP_X']);
            }

            $arItem['properties'] = $this->getProperties($obItem);
            $arItem['properties']
                = $this->filterProperties($arItem['properties']);

            $arItem['properties']
                = $this->groupLocalizationProperties($arItem['properties']);

            $itemID = (int) $arItem['ID'];
            $arResult[$itemID] = $arItem;
        }

        $arResult = CZ_Utils::convert_to_utf8_recursively($arResult);

        return $response->withJson([
            'status'    => 200,
            'actions'   => $arResult,
            'timestamp' => $timestamp,
        ], 200, JSON_UNESCAPED_UNICODE);
    }
}