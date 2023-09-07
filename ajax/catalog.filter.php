<?php

//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

header('Content-Type: text/html; charset=utf-8');

ini_set('memory_limit', '-1');
//$old = ini_set('memory_limit', '8192M'); 

ini_set('max_execution_time', 0);

define("NOT_CHECK_PERMISSIONS", true);

$debug = (isset($_REQUEST['debug'])) ? $_REQUEST['debug'] : false;

if($debug) require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if($debug) CModule::IncludeModule('iblock');

define("SPHINX", true);   // Выборки через Sphinx

$opened_enums = (isset($_REQUEST['opened_enums'])) ? $_REQUEST['opened_enums'] : false;
$section_id = (isset($_REQUEST['section_id'])) ? $_REQUEST['section_id'] : 137;

$index_name = 'tiptop';

$float_fields = array('price');
$int_fields = array('width', 'depth', 'height', 'weight', 'dlina____sm');

$tovars = 0;
$allowed_brands = array();
$allowed_series = array();
$min_max_price = array();
$allowed_enums = array();
$count_enums = array();

if(SPHINX) {
	// Подключим файл с api
	require_once ("sphinxapi.php");
}

$mode = 'mysql';
$sphinx = false;
if(SPHINX) {
	$host = $_SERVER['HTTP_HOST'];
	$sphinx_ip = 'localhost';

	$sphinx = new SphinxClient();
	$res = $sphinx->SetServer( $sphinx_ip, 9312 );
	$sphinx_status = $sphinx->status();
	$mode = ($sphinx_status[0][1] > 1) ? 'sphinx' : 'mysql';
}

$time_start = microtime(1);	

$filters = array();

foreach(array_merge($_GET, $_POST) as $key => $value) {
	if($debug) echo '<br>'.$key. ' = ' . var_export($value, true);
	if(strpos($key, 'arrFilter') !== false) {
		$arr = explode('_', $key);
		if(isset($arr[2])) {
			$field_id = (is_numeric($arr[1])) ? (int) $arr[1] *2 : $arr[1];
			$value_id = $arr[2];
			$filters[$field_id][] = $value_id;
		}
	}else{
		if(strpos($key, '-from') !== false) {
			$arr = explode('-from', $key);
			$filters[$arr[0]]['from'] = $value;
		}
		if(strpos($key, '-to') !== false) {
			$arr = explode('-to', $key);
			$filters[$arr[0]]['to'] = $value;
		}
	} 
}

if($debug > 1) {print '<br>GET фильтр: <pre>'; print_r($filters); print '</pre>';}

$condition = $filters;


// Заполнить список множественных полей

$multiple_fields = array();
if($mode == 'sphinx') {  //if(SPHINX) {

	  $sphinx->ResetFilters();
	  $sphinx->resetGroupBy();

	  if($section_id) $sphinx->SetFilter('s', array($section_id));

		$sphinx->SetSelect("v");
		$sphinx->SetGroupBy('v', SPH_GROUPBY_ATTR);
		$sphinx->SetLimits(0,100000);

		$sphinx->SetArrayResult (true);
		$result = $sphinx->Query('', 'filterIndex_'.$index_name);

		if($debug > 1) {print '<br><pre>'; print_r($result); print '</pre>';}
		//print '<br><pre>'; print_r($result); print '</pre>'; die();

		if ( $result !== false ) { 

		  if ( ! empty($result["matches"]) ) { // если есть результаты поиска - обрабатываем их
			  $group_arr = array();
			  foreach ( $result["matches"] as $product => $info ) {
					//if ($debug) var_dump($info);
					$field_id = isset($info['attrs']['@groupby']) ? $info['attrs']['@groupby'] : false;
					if($field_id > 0 and !in_array($field_id, $multiple_fields)) $multiple_fields[] = $field_id;

			  }
			  if ($debug) {
				  print '<br>total: '; echo $result["total"];
				  print ' time: '; echo $result["time"];
			  }
		  }
		}else{
			if($debug) echo "<br>Query failed: " . $sphinx->GetLastError() . ".\n"; // выводим ошибку если произошла
		}
	
}

// подсчёт кол-ва товаров

		$sphinx->ResetFilters();
		$sphinx->resetGroupBy();

		// --------- filter -----------------
		//$sphinx->SetFilter('price', array(0), true);

		if(count($condition) > 0) {
			$v_arr = array();
			foreach($condition as $fname=>$v) {
				$fieldname_float = (is_numeric($fname)) ? false : true;
				if($fieldname_float) {
					$min = (isset($v['from'])) ? (float) $v['from'] : (float) 0;
					$max = (isset($v['to'])) ? (float) $v['to'] : (float) 10000000;

					if(in_array(strtolower($fname), array('width', 'depth', 'height', 'weight'))) {$fname .= '2';} // костыль
					if(strpos($fname, 'price') !== false) $fname = 'price';

					//echo $fname; var_dump($min); var_dump($max);
					$sphinx->setFilterFloatRange ($fname, $min, $max);
				}else{
					$v_arr[] = $v;
					if(is_array($v)) $sphinx->SetFilter('v', $v); else $sphinx->SetFilter('v', array($v));
				}
			}
			if($debug) echo '<br>' . var_export($v_arr, true);
			//if(count($v_arr) > 0)	$sphinx->SetFilter('v', $v_arr);
		}

		if($section_id) $sphinx->SetFilter('s', array($section_id));

		$sphinx->SetSelect("id");
		$sphinx->SetGroupBy('id', SPH_GROUPBY_ATTR);

		$sphinx->SetLimits(0,100000,100000);

		$sphinx->SetArrayResult (true);
		$result = $sphinx->Query('', 'filterIndex_'.$index_name);

		if($debug > 1) {print '<br><pre>'; print_r($result); print '</pre>';}
		if ( ! empty($result["matches"]) ) { // если есть результаты поиска - обрабатываем их

			  $id_arr = array();
			  foreach ( $result["matches"] as $product => $info ) {
					//if ($debug) var_dump($info);
					$v = isset($info['attrs']['@groupby']) ? $info['attrs']['@groupby'] : false;
					$cnt = isset($info['attrs']['@count']) ? $info['attrs']['@count'] : false;

			  }
			  $tovars = $result["total"];
			  if ($debug) {
				  print '<br>total: '; echo $result["total"];
				  print ' time: '; echo $result["time"];
				  var_dump($id_arr);
			  }

		}else{
			if($debug) echo "<br>".$fieldname." - Query failed: " . $sphinx->GetLastError() . ".\n"; // выводим ошибку если произошла
		}

		if($debug) echo '<br><b>Найдено товаров:' . $tovars.'</b>';

	// min max price

	$min_max_price = array(); 
	$fieldname = 'price';

	if($mode == 'sphinx') {  // if(SPHINX) {
		//$min_max_price = $this->getSphinxMinMax($category_id, $filters, $fieldname, $filteredFieldsNames, false);
	}

	// min max vozrast

	$min_max_vozrast = array(); 

	if($mode == 'sphinx') {  // if(SPHINX) {
		$fieldname = 'vozrast_ot';
		//$vozrast_ot = $this->getSphinxMinMax($category_id, $filters, $fieldname, $filteredFieldsNames, false);
		//var_dump($vozrast_ot);

		$fieldname = 'vozrast_do';
		//$vozrast_do = $this->getSphinxMinMax($category_id, $filters, $fieldname, $filteredFieldsNames, false);
		//var_dump($vozrast_do);

		//$min_max_vozrast = array((isset($vozrast_do[0]) ? $vozrast_ot[0] : 0), (isset($vozrast_do[1]) ? $vozrast_do[1] : 0)); 
	}

  // подсчёт актуальных cnt для множественных значений

	// сначала для всех enums полей с общими условиями текущего фильтра

	// --- multi fields -----------

		$condition_field = $condition;

		$sphinx->ResetFilters();
		$sphinx->resetGroupBy();

		// --------- filter -----------------
		//$sphinx->SetFilter('price', array(0), true);

		if(count($condition_field) > 0) {
			$v_arr = array();
			foreach($condition_field as $fname=>$v) {
				$fieldname_float = (is_numeric($fname)) ? false : true;
				if($fieldname_float) {
					$min = (isset($v['from'])) ? (float) $v['from'] : (float) 0;
					$max = (isset($v['to'])) ? (float) $v['to'] : (float) 10000000;

					if(in_array(strtolower($fname), array('width', 'depth', 'height', 'weight'))) {$fname .= '2';} // костыль
					if(strpos($fname, 'price') !== false) $fname = 'price';

					//echo $fname; var_dump($min); var_dump($max);
					$sphinx->setFilterFloatRange ($fname, $min, $max);
				}else{
					$v_arr[] = $v;
					if(is_array($v)) $sphinx->SetFilter('v', $v); else $sphinx->SetFilter('v', array($v));
				}
			}
			if($debug) echo '<br>поиск всех enums по условиям: ' . var_export($v_arr, true);
			//if(count($v_arr) > 0)	$sphinx->SetFilter('v', $v_arr);
		}

		//$sphinx->SetFilter('FACET_ID', $fieldname);

		//if($this->SECTION_ID) $sphinx->SetFilter('SECTION_ID', $this->SECTION_ID);
		//$sphinx->SetSelect("FACET_ID, v");

		//$sphinx->SetFilter('f', array($fieldname));
		if($section_id) $sphinx->SetFilter('s', array($section_id));

		$sphinx->SetSelect("v");
		$sphinx->SetGroupBy('v', SPH_GROUPBY_ATTR);

		$sphinx->SetLimits(0,100000);

		$sphinx->SetArrayResult (true);
		$result = $sphinx->Query('', 'filterIndex_'.$index_name);

		if($debug > 1) {print '<br><pre>'; print_r($result); print '</pre>';}
		if ( ! empty($result["matches"]) ) { // если есть результаты поиска - обрабатываем их

			  $group_arr = array();
			  foreach ( $result["matches"] as $product => $info ) {
					//if ($debug) var_dump($info);
					$v = isset($info['attrs']['@groupby']) ? $info['attrs']['@groupby'] : false;
					$cnt = isset($info['attrs']['@count']) ? $info['attrs']['@count'] : false;

					if($v > 0) {
						$values[$v] = $v;
						$group_arr[$v]['cnt_base'] = $cnt;
						if($debug) {
							$prop =  CIBlockPropertyEnum::GetByID($v);
							if($debug) echo '<br>'.$v;
							$field_id = false;
							if($prop) {
								//var_dump($prop);
								$field_id = $prop["PROPERTY_ID"];
								$name = $prop['VALUE'];
								if($debug) echo ' "' . $name . '" (' . $cnt . ')';
							}else{
								$res =  CIBlockElement::GetByID($v);
								if($ar_res = $res->GetNext()) {
									if($debug) echo ' ' . $ar_res['NAME']. ' ' . $ar_res["IBLOCK_CODE"] . ' ' . $ar_res["IBLOCK_ID"] . ' ';
									//var_dump($ar_res);
									//$db_props = CIBlockElement::GetProperty($ar_res["IBLOCK_ID"], $v, "sort", "asc", Array("CODE"=>$ar_res["IBLOCK_CODE"]));
									if ($ar_res["IBLOCK_CODE"] == 'vendors_s1') {
										//var_dump($prop);
										//die();
									}
									if(isset($ar_res['NAME'])) {
										if ($ar_res["IBLOCK_CODE"] == 'vendors_s1') {
											$field_id = 56;
											$name = $ar_res['NAME'];
										}else{
											//$field_id = $ar_res["ID"];
											//$name = $ar_res['NAME'];
										}
									}
								}
							}
						}
						//if($field_id) $arResult["ITEMS"][$field_id]["VALUES"][] = array('VALUE'=>$name, "ELEMENT_COUNT"=>$cnt, "CONTROL_ID"=>"arrFilter_".$field_id."_".$v, "CONTROL_NAME"=>"arrFilter_".$field_id."_".$v, 'HTML_VALUE'=>$name);
						//if($field_id) {
							$allowed_enums[] = $v;
							$count_enums[$v] = $cnt;
						//}
					}

			  }
			  if ($debug) {
				  print '<br>total: '; echo $result["total"];
				  print ' time: '; echo $result["time"];
				  if ($debug > 1) { print '<br><pre>'; print_r($group_arr); print '</pre>';}
			  }

		}else{
			if($debug) echo "<br>".$fieldname." - Query failed: " . $sphinx->GetLastError() . ".\n"; // выводим ошибку если произошла
		}

	// скорректировать для участвующих в фильтрации полей


  $opened_enums_arr = explode(',', $opened_enums);

  $filteredNames = array_keys($filters);  // получить имена полей, участвующих в текущем фильтре

  //if (count($opened_enums_arr) > 0) {
  if (count($filteredNames) > 0) {

	// для этого сначала получить все id для каждого из полей

	if ($debug) echo '<br><br>Собрать все id для каждого из полей';

	$multiple_fields_arr = array();

		$sphinx->ResetFilters();
		$sphinx->resetGroupBy();

		// --------- filter -----------------
		//$sphinx->SetFilter('price', array(0), true);

		if($section_id) $sphinx->SetFilter('s', array($section_id));

		//$sphinx->SetSelect("v");
		$sphinx->SetSelect("multi_value");
		//$sphinx->SetGroupBy('f', SPH_GROUPBY_ATTR);

		$sphinx->SetLimits(0,1000000);

		$sphinx->SetArrayResult (true);
		$result = $sphinx->Query('', 'multivalueIndex_'.$index_name);

		if($debug > 1) {print '<br><pre>'; print_r($result); print '</pre>';}
		//print '<br><pre>'; print_r($result); print '</pre>';
		if ( ! empty($result["matches"]) ) { // если есть результаты поиска - обрабатываем их

			  foreach ( $result["matches"] as $product => $info ) {
					//if ($debug) var_dump($info);
					//$v = isset($info['attrs']['f']) ? $info['attrs']['f'] : false;
					$v = isset($info['id']) ? $info['id'] : false;
					//$v_arr = $info['attrs']['v'];
					$v_arr = $info['attrs']['multi_value'];
					if($v > 0) $multiple_fields_arr[$v] = $v_arr;

			  }
			  if ($debug) {
				  print '<br>total: '; echo $result["total"];
				  print ' time: '; echo $result["time"];
				  print '<br>Список id значений для каждого поля:<br><pre>'; print_r($multiple_fields_arr); print '</pre>';
			  }

		}else{
			if($debug) echo "<br>".$fieldname." - Query failed: " . $sphinx->GetLastError() . ".\n"; // выводим ошибку если произошла
		}

		//print '<br><pre>'; print_r($result); print '</pre>';
		//print '<br><pre>'; print_r($multiple_fields_arr); print '</pre>';
		//die();

	if($debug) echo '<br>Найти enums для участвующих в фильтрации полей';

	//foreach($opened_enums_arr as $fieldname) {
	foreach($filteredNames as $fieldname) {
		if (strlen($fieldname) > 0) {
			if($debug) echo '<br>Поиск для facet_id: '.$fieldname . ', field_id: ' . ($fieldname / 2);

			if (in_array($fieldname, $filteredNames)) // если поле участвует в фильтрации
				$condition_field = getSphinxCondition($filters, $fieldname, array(), false);
			else
				$condition_field = $condition;

			if($debug) echo ', условия: ' . var_export($condition_field, true);

			$sphinx->ResetFilters();
			$sphinx->resetGroupBy();

			// --------- filter -----------------
			//$sphinx->SetFilter('price', array(0), true);

			if(count($condition_field) > 0) {
				$v_arr = array();
				foreach($condition_field as $fname=>$v) {
					$fieldname_float = (is_numeric($fname)) ? false : true;
					if($fieldname_float) {
						$min = (isset($v['from'])) ? (float) $v['from'] : (float) 0;
						$max = (isset($v['to'])) ? (float) $v['to'] : (float) 10000000;

						if(in_array(strtolower($fname), array('width', 'depth', 'height', 'weight'))) {$fname .= '2';} // костыль
						if(strpos($fname, 'price') !== false) $fname = 'price';

						//echo $fname; var_dump($min); var_dump($max);
						$sphinx->setFilterFloatRange ($fname, $min, $max);
					}else{
						$v_arr[] = $v;
						if(is_array($v)) $sphinx->SetFilter('multi_value', $v); else $sphinx->SetFilter('multi_value', array($v));
					}
				}
				if($debug) echo '<br>---' . var_export($v_arr, true);
				//if(count($v_arr) > 0)	$sphinx->SetFilter('v', $v_arr);
			}

			//$sphinx->SetFilter('FACET_ID', $fieldname);

			//if($this->SECTION_ID) $sphinx->SetFilter('SECTION_ID', $this->SECTION_ID);
			//$sphinx->SetSelect("FACET_ID, v");

			//$sphinx->SetFilter('f', array($fieldname));
			if($section_id) $sphinx->SetFilter('s', array($section_id));

			//$sphinx->SetSelect("v");
			//$sphinx->SetGroupBy('v', SPH_GROUPBY_ATTR);
			$sphinx->SetSelect("multi_value");
			$sphinx->SetGroupBy('multi_value', SPH_GROUPBY_ATTR);

			$sphinx->SetLimits(0,100000,100000);

			$sphinx->SetArrayResult (true);
			$result = $sphinx->Query('', 'filterIndex_'.$index_name);

			if($debug > 1) {print '<br><pre>'; print_r($result); print '</pre>';}
			if ( ! empty($result["matches"]) ) { // если есть результаты поиска - обрабатываем их

				  $group_arr = array();
				  foreach ( $result["matches"] as $product => $info ) {
						//if ($debug) var_dump($info);
						$v = isset($info['attrs']['@groupby']) ? $info['attrs']['@groupby'] : false;
						$cnt = isset($info['attrs']['@count']) ? $info['attrs']['@count'] : false;

						if($v > 0) {
							//if($field_id) {
								//$facet_id = (int) $field_id * 2;
								//if($debug) echo ', facet_id:'.$facet_id. ' == ' . $fieldname . ', ' . var_export(in_array($v, $multiple_fields_arr[$fieldname]), true); 
								//if(in_array($v, $multiple_fields_arr[$fieldname])) {
								if(is_array($multiple_fields_arr[$fieldname]) and in_array($v, $multiple_fields_arr[$fieldname])) {
									$allowed_enums[] = $v;
									$count_enums[$v] = $cnt;
									if($debug) echo ' - добавлено в выдачу (' . $v . ')';
								}
							//}
						}

				  }
				  if ($debug) {
					  print '<br>total: '; echo $result["total"];
					  print ' time: '; echo $result["time"];
				  }

			}else{
				if($debug) echo "<br>".$fieldname." - Query failed: " . $sphinx->GetLastError() . ".\n"; // выводим ошибку если произошла
			}

		}
	}

  }

  if($debug) echo '<br><br>';

		$result = 'Ok';

		$result = array(
			'object_total' => $tovars,
			'allowed_brands' => $allowed_brands,
			'allowed_series' => $allowed_series,
			'min_max_price' => $min_max_price,
			'allowed_enums' => $allowed_enums,
			'count_enums' => $count_enums,
			'tovars' => tovars($tovars),
			'mode' => $mode,
			'timing' => round(round(microtime(1)-$time_start,9)*1000),
			'result' => $result
		);

		echo json_encode($result);
		exit();



$output = array('result'=>'Error');

echo json_encode($output);

exit();

function getSphinxCondition($filters, $fieldname, $filteredFieldsNames = array(), $checkfield = true) {
	//$filteredFieldsNames[] = 'type_id';
	if (!is_array($fieldname)) $fieldname = array($fieldname);
	$fields_arr = array();
	if (count($filters) > 0) {
		foreach ($filters as $key => $value) {
			//echo ' ('.$key.') ';
			if (in_array($key, $fieldname)) continue;
			if ($checkfield and !in_array($key, $filteredFieldsNames)) {
				//echo ' delete:'.$key;
				unset($filters[$key]);
				continue;
			}
			$fields_arr[$key] = (is_array($value)) ? $value : array($value);
		}
	}
	return $fields_arr;
}

function NumberEnd($number, $titles) {
	$cases = array (2, 0, 1, 1, 1, 2);
	return $titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
}

function tovars($a) {
	return number_format($a, 0, '.', ' ').' товар'.NumberEnd($a, array('','а','ов'));
}


?>