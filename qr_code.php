<?php
/**
 * @package Akismet
 */
/*
Plugin Name: QR Code Log
Plugin URI: https://mechlin.com
Description:Keep record of Qr Code
Version: 0.0.1
Author: Mechlin
Author URI: https://mechlin.com
License: GPLv2 or later
Text Domain: Mechlin
*/


include(plugin_dir_path(__FILE__) . 'inc/class-function.php');

include(plugin_dir_path(__FILE__) . 'inc/class-qr-code.php');

include(plugin_dir_path(__FILE__) . 'inc/class-woo-product.php');

include(plugin_dir_path(__FILE__) . 'inc/class-admin.php');

QrCode::init();
 QRCodeDatas::init();
WooProductData::init();
QRCodeAdminMenu::init();



/*

Example we have to achieve


	+-------------+----------------------+--------+
| category_id | name                 | parent |
+-------------+----------------------+--------+
|           1 | ELECTRONICS          |   NULL |
|           2 | TELEVISIONS          |      1 |
|           3 | TUBE                 |      2 |
|           4 | LCD                  |      2 |
|           5 | PLASMA               |      2 |
|           6 | PORTABLE ELECTRONICS |      1 |
|           7 | MP3 PLAYERS          |      6 |
|           8 | FLASH                |      7 |
|           9 | CD PLAYERS           |      6 |
|          10 | 2 WAY RADIOS         |      6 |
+-------------+----------------------+--------+



+-------------+----------------------+--------------+-------+
| lev1        | lev2                 | lev3         | lev4  |
+-------------+----------------------+--------------+-------+
| ELECTRONICS | TELEVISIONS          | TUBE         | NULL  |
| ELECTRONICS | TELEVISIONS          | LCD          | NULL  |
| ELECTRONICS | TELEVISIONS          | PLASMA       | NULL  |
| ELECTRONICS | PORTABLE ELECTRONICS | MP3 PLAYERS  | FLASH |
| ELECTRONICS | PORTABLE ELECTRONICS | CD PLAYERS   | NULL  |
| ELECTRONICS | PORTABLE ELECTRONICS | 2 WAY RADIOS | NULL  |
+-------------+----------------------+--------------+-------+



// select * from wp_map where user_id=$user_id AND Active = true
// select * from wp_map where upline_userid=$user_id AND Active = true
// 	SELECT t1.name AS lev1, t2.name as lev2, t3.name as lev3, t4.name as lev4
// FROM category AS t1
// LEFT JOIN category AS t2 ON t2.parent = t1.category_id
// LEFT JOIN category AS t3 ON t3.parent = t2.category_id
// LEFT JOIN category AS t4 ON t4.parent = t3.category_id
// WHERE t1.name = 'ELECTRONICS';

*/







function qr_template(){
	$templates['page-template.php'] = 'Page Template From Plugin';

	return $templates;
}

add_filter('theme_page_templates','qr_code_template',10,3); 

function qr_code_template($page_templates,$theme,$post){
	$templates['page-template.php'] = 'Page Template From Plugin';

   return $templates;
}




function qr_code_page_template($template)
{
   global $post,$wp_query,$wpdb;
   $page_temp_slug=get_page_template_slug( $post->ID );
   $templates = qr_template();
  
if(isset($templates[$page_temp_slug])){
	$template=plugin_dir_path(__FILE__).'templates/'.$page_temp_slug;
}
    return $template;
}
add_filter( 'template_include', 'qr_code_page_template', 99 );



