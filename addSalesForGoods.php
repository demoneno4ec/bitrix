<?
/*Подключим нужные модули*/
CModule::IncludeModule('iblock');
CModule::IncludeModule('sale');

$element = new element;

// Добавляем товар-родитель, у которго будут торг. предложения
$IBLOCK_ID = 1; // IBLOCK товаров
$IBLOCK_ID_SALES = 2; // IBLOCK торговых предложений
$arProduct = [
	'IBLOCK_ID' => $IBLOCK_ID, 
	'NAME' => 'Товар 1',
	'ACTIVE' => 'Y',
	// Прочие параметры товара
];

if($product_id = $element->Add($arProduct)){
	/*Можно это обернуть в цикл форыч, если у нас предопределенны значения для торговых предложений.*/
	// добавляем нужное кол-во торговых предложений
	$arLoadProductArray = [
	    'IBLOCK_ID'      => $IBLOCK_ID_SALES,
	    'NAME'           => 'Торговое предложение 1',
	    'ACTIVE'         => 'Y',
	    'PROPERTY_VALUES' => [
	        'CML2_LINK' => $product_id, // Свойство типа "Привязка к товарам (SKU)", связываем торг. предложение с товаром
	    ]
	    // Прочие параметры торгового предложения
	];
	if($product_id_sales = $element->Add($arLoadProductArray)){
		// Добавляем параметры к торг. предложению
		CCatalogProduct::Add(
		    [
		        'ID' => $product_id_sales,
		        'QUANTITY' => 9999
		    ]
		);
		// Добавляем цены к торг. предложению
		CPrice::Add(
		    [
		        'CURRENCY' => 'RUB',
		        'PRICE' => 999,
		        'CATALOG_GROUP_ID' => 1,
		        'PRODUCT_ID' => $product_id_sales,
		    ]
		);
	}else{
		// проверка на ошибки
	    echo "Ошибка добавления торгового предложения: ". $element->LAST_ERROR;
	    die();
	}
}else{
    echo "Ошибка добавления товара: ". $element->LAST_ERROR;
    die();	
}
?>