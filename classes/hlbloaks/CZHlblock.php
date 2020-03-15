<?php

namespace cz\hlblock;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Highloadblock as HL;
use Exception;
use RuntimeException;

trait CZHlblock
{
    private function checkModule()
    {
        if (!Loader::IncludeModule('highloadblock')) {
            throw new RuntimeException(Loc::getMessage('ERROR_HLBLOCK_NOT_INCLUDED'));
        }
    }

    /**
     * @param $fields
     *
     * @return int
     * @throws RuntimeException
     */
    public function add($fields): int
    {
        $this->checkModule();
        $fields = $this->convertFields($fields);

        // Айди нашего хайлоад блока
        try {
            $hlblock = $this->getHlBlockByTableName();
            $entity_data_class = $this->getEntityDataClasss($hlblock);
            $result = $entity_data_class::add($fields);

            if ($result->isSuccess()) {
                return $result->getId();
            }

            throw new RuntimeException($result->getErrors());
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    abstract protected function convertFields($fields):array;

    /**
     * @param  int  $id
     * @param  array  $fields
     *
     * @return int
     * @throws RuntimeException
     *
     */
    public function update(int $id, array $fields):int
    {
        $this->checkModule();
        $fields = $this->convertFields($fields);

        try {
            $hlblock = $this->getHlBlockByTableName();
            $entity_data_class = $this->getEntityDataClasss($hlblock);
            $result = $entity_data_class::update($id, $fields);

            if ($result->isSuccess()) {
                return (int) $result->getId();
            }

            throw new RuntimeException($result->getErrors());
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), 501);
        }
    }

    public function delete(int $id)
    {
        $this->checkModule();

        try {
            $hlblock = $this->getHlBlockByTableName();
            $entity_data_class = $this->getEntityDataClasss($hlblock);
            $result = $entity_data_class::delete($id);
            if (!$result->isSuccess()) {
                throw new RuntimeException($result->getErrors());
            }
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), 501);
        }
    }


    /**
     * @param $hlblock
     * @return DataManager
     * @throws RuntimeException
     */
    private function getEntityDataClasss($hlblock){
        try {
            return HL\HighloadBlockTable::compileEntity($hlblock)
                ->getDataClass();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function getHlBlockByTableName(){
        $this->checkModule();
        try {
            return HL\HighloadBlockTable::getList([
                'filter' => ['TABLE_NAME' => $this->hlTableName]
            ])->fetch();
        } catch (ArgumentException $e) {
            throw new ArgumentException($e->getMessage());
        }
    }
}