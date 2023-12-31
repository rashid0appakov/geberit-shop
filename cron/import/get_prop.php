<?php 
$_SERVER["DOCUMENT_ROOT"] = dirname(dirname(__DIR__));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
?>
<?
CModule::IncludeModule('iblock');
$IBLOCK_ID = 15;
$index = 29;

$arNames = array("Длина, мм","Производство","Диаметр воздуховода,мм","Напряжение питания 12 в","Присоединительный диаметр мм","Световой индикатор","Тип электродвигателя","Класс защиты IP","Датчик движения","Регулируемый гигростат","Регулируемый таймер","Макс. расход воздуха (м3/ч)","Уровень звукового давления,дБ(А)","Автоматический таймер","Обратный клапан","Шнуровой выключатель","Шариковые подшипники","Частота вращения,об/мин","Класс изоляции двигателя","Напряжение питания,В","Потребляемая мощность,кВт","Метод крепления","Размер упаковки","Совместим с любым подвесным унитазом","Заводская настройка смыва, л","Тип управления","Регулировка по высоте, мм","Максимальное давление (бар)","Для клавиш","Минимальное давление (бар)","Тип инсталляции","Клавиша","Ссылка на доп товары","Коллекция инсталяции","Цвет клавиши","Поворотный","Функция обогрева помещения","Ширина, см","Мощность, Вт","Напряжение, В","Стандарт подводки","Функция экономии расхода","Дополнительные функции","Вращение излива","Защита от обратного потока","Донный клапан","Ограничение температуры","Тип подводки","Высота излива, см","Длина излива, см","Фактура","Тип продукта","Технологии","Тип замка","Тип уравления","Регулировка положения дверцы","Терморегулятор","Регулировка продолжительности смыва","Стилистика дизайна","Для установки в","Автоотключение при нагреве","Регулировка глубины монтажа","Сортировка доп товаров","Способ открывания","Электровыключатель","Панель смыва в комплекте","Форма","Заполнение дверцы","Теплоноситель","Звукоизолирующая прокладка в комплекте","Конструкция дверей","Время нагрева, мин","Крепление к стене в комплекте","Материал","Регулируемые петли","Рабочее давление, бар","Диаметр слива, см","Объём, л","Скрытый","Направление подключения","Диаметр переходника для слива, см","Вид установки","Усиленный","Межосевое расстояние, см","Межосевое расстояние под крепеж. шпильки, см","Антискользящее покрытие","Противопожарный","Монтажная глубина, см","Длина, см.","Водонепроницаемость","Количество секций","Монтажная высота, см","Вариант установки","Пылеизоляция","Материал","Типоразмер ширина,см","Шумоизоляция","Режим слива воды","Намеченных отверстий для смесителя","Размер дверцы (Ш*В), мм","Назначение","Готовых отверстий для смесителя","Нагрузка на дверцу, кг","Оснащение","Подключение","Цвет","Угловая конструкция","Ширина мм","Метод установки сливного бачка","Высота мм","Тип монтажа","Подсветка кнопок индикацией","Глубина мм","Длина шланга, см","Гарантия","Направление выпуска","Показать на главной","Механизм","Область применения","Комплектом дешевле","Расход воды, л/мин","Страна производитель","Объем мл","тип","Режим слива воды","Размер розетки, мм","Управление","Подвод воды в бачок","Механизм слива","Поверхность","Объем смывн. бачка, л","Защита от водяных брызг","Ширина, см","Система антивсплеск","Высота, см","Безободковый","Глубина, см","Полочка в чаше","Фурнитура","Крепление","Высота чаши, см","Крышка-сиденье","Сиденье в комплекте","В сочетании только с SensoWash","Быстросъёмный механизм","Температура сиденья,С","Сенсор для обнаружения человека","Антибактериальные свойства материалов сиденья и ст","Душ для дам","Температура при эксплуатации , °C","Скрытый подвод воды/электропитания","Макс. мощность,Вт","Возможность полного снятия сиденья для дизинфекции","Комфортынй душ","Вес кг","Программиремые профили пользователя","Сиденье и крышка легко снимаются одной рукой","Функция ночной подсветки","Номинальное напряжение,В","Температура фена,С","Автоматический дренаж воды при длительном неисполь","Бесшумное опускание сиденья и крышки","Макс жесткость воды,ммоль/л","Пульт дистанционного управления (ПДУ)","Температура воды,C","Частота,Гц","Электроприводное сиденье и крышка","Душ для ягодиц","Напор воды,МПа","Материал корпуса","Регулируемая мощность водной струи","Монтаж","Подходит только для унитазов","Система хранения","Блокировка управления функциями с помощью ПДУ","Материал фасада","Незамедлительный подогрев воды для душа","Автоматическое очищение душевого стержня и форсунк","Режим экономии энергии","Форсунка снимается для мытья и замены","Регулируемая температура воды","Регулируемая температура сиденья","Функция массажа с пульсацией","Регулируемое положение душевого стержня","Регулируемая температура фена");

foreach($arNames as $name){

	$res = CIBlockProperty::GetList(Array(), Array("IBLOCK_ID" => $IBLOCK_ID, "NAME" => $name));
	if($res_arr = $res->Fetch()){
		//echo "$".$res_arr["CODE"]." = $"."data[".$index."];"."<br>";
		/*
		if($res_arr['PROPERTY_TYPE'] == 'L'){
			echo "__SetListPropertyValue($"."arProps, '".$res_arr["CODE"]."', ".$res_arr["ID"].", $".$res_arr["CODE"].");<br>";
		}
		if($res_arr['PROPERTY_TYPE'] == 'N' || $res_arr['PROPERTY_TYPE'] == 'S'){
			echo "$"."arProps['".$res_arr["CODE"]."'] = $".$res_arr["CODE"].";<br>";
		}
		*/
	}
	else{
		//echo $name."<br>";
		//echo $name." = $"."data[".$index."];"."<br>"; 
	}
	$index++;
}
?>