<?php


namespace cz\hlblock;


use CDBResult;
use CUserFieldEnum;
use Exception;
use RuntimeException;

class RequestPrototyre
{
    private $name = 'PrototyreRequests';
    private $hlTableName = 'hl_prototyre_request';

    use CZHlblock;


    protected function convertFields($fields): array
    {
        $status = $fields['status'];
        if ($status !== 'executed') {
            $status = 'processing';
        }

        $fields = [
            'UF_CREATE_TIMESTAMP' => (int) $fields['timestamp'],
            'UF_REQUEST_ID'       => (int) $fields['request_id'],
            'UF_CITY'             => (string) $fields['city'],
        ];

        $arStatus = CUserFieldEnum::GetList(
            [],
            ['XML_ID' => $status,]
        )->fetch();

        $fields['UF_REQUEST_STATUS'] = (int) $arStatus['ID'];

        return $fields;
    }

    /**
     * @return array
     */
    public function getLastRequest(string $cityCode): array
    {
        try {
            $hlblock = $this->getHlBlockByTableName();
            $entity_data_class = $this->getEntityDataClasss($hlblock);
            $arFilter = [
                'UF_CITY' => $cityCode,
            ];

            $sTableID = 'tbl_'.$this->hlTableName;
            $rsData = $entity_data_class::getList([
                'select' => ['*'],
                'filter' => $arFilter,
                'order'  => ['UF_CREATE_TIMESTAMP' => 'DESC'],
                'limit'  => 1,
            ]);
            $response = (new CDBResult($rsData, $sTableID))->fetch();

            $response = $this->convertResponse($response);
            return $response;
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }


    private function convertResponse($response): array
    {
        if ($response === false) {
            return [];
        }

        $arStatus = CUserFieldEnum::GetList(
            [],
            ['ID' => $response['UF_REQUEST_STATUS'],]
        )->fetch();

        $response = [
            'request_id' => (int) $response['UF_REQUEST_ID'],
            'timestamp'  => (int) $response['UF_CREATE_TIMESTAMP'],
            'status'     => (string) $arStatus['XML_ID'],
        ];

        return $response;
    }
}