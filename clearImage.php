<?
	/*Подключим нужные модули*/
	CModule::IncludeModule('iblock');

	/*Определим начальные значения*/
	$arProperties = [];
	$arFileIDS = [];

	/*Выберем все свойства инфоблоков типа файл*/
	$arSort = ['id' => 'ASC'];
	$arFilter = [
		'PROPERTY_TYPE' => 'F',
	];
	$resProperties = CIBlockProperty::GetList($arSort, $arFilter);
	while($arProperty = $resProperties->Fetch()){
		devDump($arProperty['CODE']);
		if(!in_array($arProperty['CODE'], $arProperties)){
			$arProperties[$arProperty['ID']] = $arProperty['CODE'];
		}
	}

	/*Сформируем в нужный для на вид, элементы массива с кодами свойств*/
	foreach($arProperties as &$code) {
		$code = 'PROPERTY_'.$code;
	}
	unset($code);

	/*Выберем все элементы инфоблоков*/
	$arFilter = [
		'ACTIVE' => '',
	];
	$arSelect = ['ID', 'IBLOCK_ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'];
	if(!empty($arProperties)){
		$arSelect += $arProperties;
	}
	$resElemenets = CIBlockElement::GetList($arSort, $arFilter, false,  false, $arSelect);
	while($arElement = $resElemenets->fetch()){
		/*Проверим айди превью и детальной картинки на наличие в нашем результирующем массиве*/
		if(!empty($arElement['PREVIEW_PICTURE']) and !in_array($arElement['PREVIEW_PICTURE'], $arFileIDS)){
			$arFileIDS[] = $arElement['PREVIEW_PICTURE'];
		}
		if(!empty($arElement['DETAIL_PICTURE']) and !in_array($arElement['DETAIL_PICTURE'], $arFileIDS)){
			$arFileIDS[] = $arElement['DETAIL_PICTURE'];
		}

		/*Проверим вхождение наших свойств типа файл, вложенным циклом*/
		foreach($arProperties as $property_code) {
			$property_code = $property_code.'_VALUE';
			if(!empty($arElement[$property_code]) and !in_array($arElement[$property_code], $arFileIDS)){
				$arFileIDS[] = $arElement[$property_code];
			}
		}
	}

	/*Выберем все файлы из базы данных, чтобы понять какие файлы не используются инфоблоками*/
	$arFilter = [];
	$resFiles = CFile::GetList($arSort, $arFilter);
	while($arFile = $resFiles->fetch()){
		if(!in_array($arFile['ID'], $arFileIDS)){
			CFile::Delete($arFile['ID']);
		}
	}
}
?>