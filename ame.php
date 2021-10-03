<?php
/*
Plugin Name: Easy Ame
Plugin URI: https://ame.im/
Description: Плагин для упрощенной интеграции чат бота Ame на ваш сайт. 
Version: 0.1
Author: MrMarL
Author URI: https://vk.com/mrmarl
*/
function ame_main()
{
    if(strpos($_SERVER['REQUEST_URI'],"options.php"))
        header("Location: admin.php?page=Ame_parameters.php");
    $all_options = get_option('ame_key');
    if((is_admin() && !$all_options['my_checkbox']) || !is_admin()){
	    echo "<script src='https://i.ame.im/users_widget/base/ame.js'></script>
        <script>ameChatSiteObject.init('".$all_options['my_text']."');</script>";
    }
}
add_action('after_setup_theme','ame_main');

$true_page = 'Ame_parameters.php'; // это часть URL страницы
/*
 * Функция, добавляющая страницу в пункт меню
 */
function ame_key() {
	global $true_page;
	add_menu_page( 'Ame', 'Ame', 'manage_options', $true_page, 'true_option_page');  
}
add_action('admin_menu', 'ame_key');
 
/**
 * Возвратная функция (Callback)
 */ 
function true_option_page(){
	global $true_page;
	?><div class="wrap">
		<h2>Параметры плагина <a href="https://ame.im">Ame</a></h2>
		<form method="post" enctype="multipart/form-data" action="options.php">
			<?php 
			settings_fields('ame_key'); // меняем под себя только здесь (название настроек)
			do_settings_sections($true_page);
			?>
			<p class="submit">  
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />  
			</p>
		</form>
	</div><?php
}
 
/*
 * Регистрируем настройки
 * Мои настройки будут храниться в базе под названием ame_key
 */
function true_option_settings() {
	global $true_page;
	// Присваиваем функцию валидации ( true_validate_settings() ). Вы найдете её ниже
	register_setting( 'ame_key', 'ame_key', 'true_validate_settings' ); // ame_key
 
	// Добавляем секцию
	add_settings_section( 'true_section_1', 'Настройки ключа(key) виджета:', '', $true_page );
 
	// Текстовое поле в первой секции
	$true_field_params = array(
		'type'      => 'text', // тип
		'id'        => 'my_text',
		'desc'      => 'Например: e7cc578a9c0466dda8325a88b4b59822', // описание
		'label_for' => 'my_text'
	);
	add_settings_field( 'my_text_field', 'Ваш key виджета тут', 'true_option_display_settings', $true_page, 'true_section_1', $true_field_params );
	
	// Добавляем вторую секцию настроек
	add_settings_section( 'true_section_2', 'Настройки отображения:', '', $true_page );
 
	// Создадим чекбокс
	$true_field_params = array(
		'type'      => 'checkbox',
		'id'        => 'my_checkbox',
		'desc'      => 'Не отображать в панели администрирования.'
	);
	add_settings_field( 'my_checkbox_field', 'Виджет', 'true_option_display_settings', $true_page, 'true_section_2', $true_field_params );
}
add_action( 'admin_init', 'true_option_settings' );
 
/*
 * Функция отображения полей ввода
 * Здесь задаётся HTML и PHP, выводящий поля
 */
function true_option_display_settings($args) {
	extract( $args );
 
	$option_name = 'ame_key';
	$o = get_option( $option_name );
 
	switch ( $type ) {  
		case 'text':  
			$o[$id] = esc_attr( stripslashes($o[$id]) );
			echo "<input class='regular-text' type='text' id='$id' name='" . $option_name . "[$id]' value='$o[$id]' />";  
			echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
		break;
		case 'textarea':  
			$o[$id] = esc_attr( stripslashes($o[$id]) );
			echo "<textarea class='code large-text' cols='50' rows='10' type='text' id='$id' name='" . $option_name . "[$id]'>$o[$id]</textarea>";  
			echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
		break;
		case 'checkbox':
			$checked = ($o[$id] == 'on') ? " checked='checked'" :  '';  
			echo "<label><input type='checkbox' id='$id' name='" . $option_name . "[$id]' $checked /> ";  
			echo ($desc != '') ? $desc : "";
			echo "</label>";  
		break;
		case 'select':
			echo "<select id='$id' name='" . $option_name . "[$id]'>";
			foreach($vals as $v=>$l){
				$selected = ($o[$id] == $v) ? "selected='selected'" : '';  
				echo "<option value='$v' $selected>$l</option>";
			}
			echo ($desc != '') ? $desc : "";
			echo "</select>";  
		break;
		case 'radio':
			echo "<fieldset>";
			foreach($vals as $v=>$l){
				$checked = ($o[$id] == $v) ? "checked='checked'" : '';  
				echo "<label><input type='radio' name='" . $option_name . "[$id]' value='$v' $checked />$l</label><br />";
			}
			echo "</fieldset>";  
		break; 
	}
}
 
/*
 * Функция проверки правильности вводимых полей
 */
function true_validate_settings($input) {
	foreach($input as $k => $v) {
		$valid_input[$k] = trim($v);
 
		/* Вы можете включить в эту функцию различные проверки значений, например
		if(! задаем условие ) { // если не выполняется
			$valid_input[$k] = ''; // тогда присваиваем значению пустую строку
		}
		*/
	}
	return $valid_input;
}
