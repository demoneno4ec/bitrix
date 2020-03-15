<?php


namespace cz\iblock;


use Bitrix\Main\Localization\Loc;
use CIBlockElement;
use CIBlockResult;
use CModule;
use RuntimeException;

trait CZ_Element
{
    private $order = [];
    private $filter = [];
    private $navParams;
    private $groupBy;
    private $select = [];

    public function __construct()
    {
        if (!CModule::IncludeModule('iblock')) {
            throw new RuntimeException(Loc::getMessage('ERROR_IBLOCK_NOT_INCLUDED'));
        }
    }

    private $customFilter = [];

    /**
     * @param  array  $additionalFilter
     * @param  array  $arSelect
     *
     * @return integer|CIBlockResult
     */
    public function getList(array $additionalFilter = [], array $arSelect = ['*'])
    {
        $this->setOrder();
        $this->setFilter($additionalFilter);
        $this->setNavParams();
        $this->setGroupBy();
        $this->setSelect($arSelect);

        return CIBlockElement::GetList($this->order, $this->filter, $this->navParams,
            $this->groupBy, $this->select);
    }

    private function setSelect(array $arSelect = ['*'])
    {
        $this->select = $arSelect;
    }

    private function setGroupBy()
    {
        $this->groupBy = false;
    }

    private function setNavParams()
    {
        $this->navParams = false;
    }

    private function setFilter(array $additionalFilter)
    {
        $defaultFilterRequired = [
            'IBLOCK_TYPE' => $this->iblockType,
        ];

        if ($this->iblockCode !== '') {
            $defaultFilterRequired['IBLOCK_CODDE'] = $this->iblockCode;
        } else {
            $defaultFilterRequired['IBLOCK_ID'] = $this->iblockId;
        }

        /** @noinspection AdditionOperationOnArraysInspection */
        $resultFilter = $defaultFilterRequired + $additionalFilter;

        $defaultFilterAdditional = [
            'ACTIVE' => 'Y',
        ];

        /** @noinspection AdditionOperationOnArraysInspection */
        $this->filter =  $resultFilter + $defaultFilterAdditional;
    }

    private function setOrder()
    {
        $this->order = ['ID' => 'ASC'];
    }
}