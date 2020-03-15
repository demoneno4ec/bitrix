<?php

namespace cz\hlblock;


use CDBResult;
use CUserFieldEnum;
use Exception;
use RuntimeException;

class IntegrationTokens
{
    private $name = 'IntegrationTokens';
    private $hlTableName = 'integration_tokens';

    use CZHlblock;

    /**
     * @param string $aggregator
     *
     * @return string
     * @throws RuntimeException
     */
    public function getToken(string $aggregator): string
    {
        $arToken = $this->getArToken($aggregator);

        return $arToken['UF_TOKEN'] ?? '';
    }

    public function getArToken(string $aggregator):array
    {
        try {
            $hlblock = $this->getHlBlockByTableName();
            $entity_data_class = $this->getEntityDataClasss($hlblock);
            $arFilter = [
                'UF_AGGREGATOR' => $this->getAgregatorIdByCode($aggregator),
            ];

            $sTableID = 'tbl_'.$this->hlTableName;
            $rsData = $entity_data_class::getList([
                'select' => ['*'],
                'filter' => $arFilter,
                'limit'  => 1,
            ]);
            $response = (new CDBResult($rsData, $sTableID))->fetch();

            return !empty($response) ? $response : [];
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }


    protected function convertFields($fields): array
    {
        $fields['token'] = (string) $fields['token'];
        $fields['aggregator'] = (string) $fields['aggregator'];

        $convertFields = [];
        if (!empty($fields['token'])){
            $convertFields['UF_TOKEN'] = $fields['token'];
        }
        if (!empty($fields['aggregator'])){
            $convertFields['UF_AGGREGATOR'] = $this->getAgregatorIdByCode($fields['aggregator']);
        }

        return $convertFields;
    }

    private function getAgregatorIdByCode(string $code): int
    {
        $arStatus = CUserFieldEnum::GetList(
            [],
            ['XML_ID' => $code,]
        )->fetch();

        return (int) $arStatus['ID'];
    }
}