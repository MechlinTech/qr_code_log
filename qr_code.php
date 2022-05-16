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
 function create_map_user_table()
	{
		global $wpdb;
	
		$charset_collate = $wpdb->get_charset_collate();
		
		$table_name =$wpdb->prefix.'map_user_to_user';
		
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $wpdb->prefix.'map_user_to_user') {
			$sql = "CREATE TABLE `$table_name`(
				`id` BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY , 
				`active` BOOLEAN NOT NULL DEFAULT TRUE , 
				`userid` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
				`upline_userid` BIGINT(20) UNSIGNED NULL DEFAULT NULL, 
				`fname` VARCHAR(255) NULL DEFAULT NULL , 
				`lname` VARCHAR(255) NULL DEFAULT NULL , 
				`address` VARCHAR(255) NULL DEFAULT NULL , 
				`city` VARCHAR(50) NULL DEFAULT NULL, 
				`state` VARCHAR(50) NULL DEFAULT NULL , 
				`zip` VARCHAR(50) NULL DEFAULT NULL , 
				`affiliateId` BIGINT(20) UNSIGNED NULL DEFAULT NULL , 
				`createdate` TIMESTAMP NULL DEFAULT NULL , 
				`updatedate` TIMESTAMP NULL DEFAULT NULL,
				
				KEY `".$table_name."_upline_userid_foreign` (`upline_userid`),
				KEY `".$table_name."_userid_foreign` (`userid`),
				KEY `".$table_name."_affiliateId_foreign` (`affiliateId`),


				CONSTRAINT `".$table_name."_upline_userid_foreign` 
				FOREIGN KEY (upline_userid) 
				REFERENCES wp_users(ID) ON DELETE RESTRICT ON UPDATE RESTRICT,
				
				
				CONSTRAINT `".$table_name."_userid_foreign` 
				FOREIGN KEY (userid) 
				REFERENCES wp_users(ID) ON DELETE RESTRICT ON UPDATE RESTRICT,

				
				CONSTRAINT `".$table_name."_affiliateId_foreign` 
				FOREIGN KEY (affiliateId) 
				REFERENCES wp_users(ID) ON DELETE RESTRICT ON UPDATE RESTRICT
				) $charset_collate;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}

/*


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


*/
function level_data(){
	SELECT t1.name AS lev1, t2.name as lev2, t3.name as lev3, t4.name as lev4
FROM category AS t1
LEFT JOIN category AS t2 ON t2.parent = t1.category_id
LEFT JOIN category AS t3 ON t3.parent = t2.category_id
LEFT JOIN category AS t4 ON t4.parent = t3.category_id
WHERE t1.name = 'ELECTRONICS';
}



function create_table(){
	global $wpdb;
	
$charset_collate = $wpdb->get_charset_collate();
$table_name =$wpdb->prefix.'qr_code_scans_log';
if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $wpdb->prefix.'qr_code_scans_log') {
$sql = "CREATE TABLE `$table_name`(
	id INT NOT NULL AUTO_INCREMENT,
	date_time_stamp DATETIME NOT NULL,
	qr_code VARCHAR(159) NOT NULL,
	card_type VARCHAR(159) NOT NULL,
	tidny_trump_card DATETIME NOT NULL,
	PRIMARY KEY (id)
	) $charset_collate;";
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);
}

}



create_map_user_table();

create_table();


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
