<?php

use Bitrix\Highloadblock as HL;
//dump
function wrap($color, $text) {
    return "<span style='color:".$color.";'>".htmlspecialchars($text)."</span>";
}
function filter_tilda_keys(&$outputVariable) {
    static $level = 0;
    $tab = "    ";
    if(is_array($outputVariable) or is_object($outputVariable)){
        if($level == 0){
            echo '('.wrap('magenta', gettype($outputVariable)).') ';
            echo 'count '.count($outputVariable);
            echo "\n";
            $level++;
            if(is_object($outputVariable)){
                print_r($outputVariable);
            }else{
                filter_tilda_keys($outputVariable);
            }
        }else{
            foreach($outputVariable as $key => $value) {
                echo str_repeat($tab, $level).'('.wrap('magenta', gettype($value)).') ';
                if(is_array($value) or is_object($value)) {
                    echo 'count '.count((array)$value);
                    echo ' ['.wrap('red', $key)."]\n";
                    if(!empty($value)) {
                        $level++;
                        if(is_object($value)){
                            print_r($outputVariable);
                        }else{
                            filter_tilda_keys($value);
                        }
                    }
                }else{
                    echo '['.wrap('black', $key)."] = ";
                    checksimpleVariable($value);
                }
            }
            $level--;
            echo '';
        }
    }else{
        echo '('.wrap('magenta', gettype($outputVariable)).') ';
        checksimpleVariable($outputVariable);
    }

}
function checksimpleVariable($simpleVariable){
    if(is_null($simpleVariable)){
        echo 'null'."\n";
    }elseif(empty($simpleVariable)){
        echo "''"."\n";
    }elseif(is_string($simpleVariable)){
        $simpleVariable = htmlspecialchars($simpleVariable);
        echo (strlen($simpleVariable) < 40 ? $simpleVariable : substr($simpleVariable, 0, 40)."…")."\n";
    }else{
        echo $simpleVariable."\n";
    }
}

function devDump($arResult) {
    echo "<pre style='font-size:10px;
            position: relative;
            top: 0px;
            text-align:left;
            background: rgba(0, 120, 201, 0.15);
            border: 1px solid #888;
        '>";
    filter_tilda_keys($arResult);
    echo "</pre>";
}

//dump in file
function dumpFile($variable, $fileName = 'dump.txt'){
    $path = $_SERVER['DOCUMENT_ROOT'].'/upload/dump/';
    if(!file_exists($path)){
        mkdir($path, 0775, true);
    }
    $text = '=======================\r\n';
    $text .= print_r($variable, 1);
    $text .= '\r\n';
    $handle = fopen($path.$fileName, 'a');
    fwrite($handle, $text);
    fclose($handle);
}

//convertData
function devConvertDate($date_format, $timeStamp){
    return FormatDate($date_format, MakeTimeStamp($timeStamp));
}
// custom cache
function returnResultCache($timeSeconds, $cacheId, $callback, $arCallbackParams = '', $cacheDir = '') {
    $obCache = new CPHPCache();
    $cachePath = '/'.SITE_ID.'/'.$cacheDir.$cacheId;
    if( $obCache->InitCache($timeSeconds, $cacheId, $cachePath) ) {
        $vars = $obCache->GetVars();
        $result = $vars['result'];
    }
    elseif( $obCache->StartDataCache()  ) {
        $result = $callback($arCallbackParams);
        $obCache->EndDataCache(array('result' => $result));
    }
    return $result;
}
/**
 * Получаем элемента по названию справочника
 * Если $arXML_ID не пуст, вернет соответствующие элементы по ID's
 *
 */
function GetElementsHLblock($hlTblName, $filter = []){
    $response = false;
    if(!empty($hlTblName)){
        $hlblock = HL\HighloadBlockTable::getList([
            'filter' => ['TABLE_NAME' => $hlTblName]
        ])->fetch();
        $entity_data_class = HL\HighloadBlockTable::compileEntity($hlblock)->getDataClass();
        $arFilter = [];
        if(!empty($filter)){
            $arFilter = $filter;
        }
        $sTableID = 'tbl_'.$hlTblName;
        $rsData = $entity_data_class::getList(array(
            'select' => ['*'],
            'filter' => $arFilter,
            'order' => ['ID'=>'ASC'],
        ));
        $rsData = new CDBResult($rsData, $sTableID);
        while($arRes = $rsData->Fetch()){
            if (!empty($arRes['UF_FILE'])) {
                $arRes['~UF_FILE'] = $arRes['UF_FILE'];
                $arRes['UF_FILE'] = CFile::GetPath($arRes['UF_FILE']);
            }
            $response[] = $arRes;
        }
    }
    return $response;
}
/**
 * Получаем ссылку для цепочки навигации
 *
 *
 */

function GetLinkAddress($defualUrlPath, $numberPage, $queryString){
    $bQueryIsNotEmpty = !empty($queryString);
    $response = $defualUrlPath;
    if($numberPage != 1){
        $response .= '?';
        if($bQueryIsNotEmpty){
            $response .= $queryString.'&';
        }
        $response .= 'PAGEN_1='.$numberPage;
    }elseif($bQueryIsNotEmpty){
        $response .= '?'.$queryString;
    }
    return $response;
}
/**
 * Получаем подмножество файлов одним запросом
 *
 *
 */
function GetArFiles($arIDS = []){
    $arResult = [];
    if(!empty($arIDS)){
        $strIDS = implode(', ', $arIDS);
        $res = CFile::GetList(['ID'=>'ASC'],['@ID' => $strIDS]);
        while($arRes = $res->fetch()){
            $arResult[$arRes['ID']] = $arRes;
        }
    }
    return $arResult;
}
/**
 * Рекурсивный трим.
 *
 */
function trimReqursive($trimVariable){
    // devDump($trimVariable);
    $result = [];
    if(is_array($trimVariable)){
        foreach($trimVariable as &$value){
            $value = trimReqursive($value);
        }
        $result = $trimVariable;
        unset($value);
    }elseif(is_string($trimVariable)){
        $result = trim($trimVariable);
    }else{
        $result = $trimVariable;
    }
    return $result;
}
/**
 * Очищает строку от всех символов кроме ведущего плюса и цифр.
 *
 *
 */
function clearPhone($string){
    $result = '';
    if(!is_array($string) and !is_object($string)){
        $result = preg_replace('/[^\+0-9+]/', '', $string);
    }
    return $result;
}
/**
 * Собирает разделы по уровням вложенности до определенного уровня вложенности
 *
 *
 */
function multiSections($arSections, $depth_level = 1){
    $arResult = [];
    if(is_array($arSections) and !empty($arSections)){
        foreach($arSections as $arSection){
            if($arSection['DEPTH_LEVEL'] > $depth_level){
                $bChildren = true;
                break;
            }
        }
        if($bChildren === true){
            // Пересобираем arSections
            $arResult = $arSections;
            foreach($arSections as $sectionID => &$arSection){
                foreach($arResult as $resultSecID => &$resArSection){
                    if($arSection['DEPTH_LEVEL'] < $resArSection['DEPTH_LEVEL']){
                        $arSection['PARENT'] = true;
                        break;
                    }else{
                        $arSection['PARENT'] = false;
                    }
                }
                unset($resArSection);
                if($arSection['PARENT'] == false){
                    $arResult[$arSection['IBLOCK_SECTION_ID']]['SECTIONS'][$sectionID] = $arSection;
                    unset($arResult[$sectionID]);
                }
            }
            unset($arSection);
            $depth_rec++;
            $arResult = multiSections($arResult, $depth_level);
        }else{
            $arResult = $arSections;
        }
    }
    return $arResult;
};
?>