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



