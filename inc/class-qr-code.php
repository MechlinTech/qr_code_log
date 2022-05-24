<?php

class QRCodeDatas{
    static function init()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new QrCodeDatas();
        }
        return $instance;
    }
    public function __construct()
    {
     
	  $this->product_type();
	  $this->card_type();
	  $this->qr_code();
	  $this->qr_code_actions();
	  $this->qr_code_actions_history();
    }

 

 

    
public function product_type(){
	global $wpdb;
	
	$charset_collate = $wpdb->get_charset_collate();
	
	$table_name =$wpdb->prefix.'product_type';
	
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $wpdb->prefix.'product_type') {
		$sql = "CREATE TABLE `$table_name`(
			`id` BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY , 		
			`product_type` VARCHAR(255) NULL DEFAULT NULL , 
			`product_name` VARCHAR(255) NULL DEFAULT NULL ,			
			`createdate` TIMESTAMP NULL DEFAULT NULL , 
			`updatedate` TIMESTAMP NULL DEFAULT NULL
			) 
			 $charset_collate;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}



public function card_type(){
	global $wpdb;
	
	$charset_collate = $wpdb->get_charset_collate();
	
	$table_name =$wpdb->prefix.'card_type';
	
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $wpdb->prefix.'card_type') {
		$sql = "CREATE TABLE `$table_name`(
			`id` BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY , 		
			`card_type` VARCHAR(255) NULL DEFAULT NULL , 
			`card_name` VARCHAR(255) NULL DEFAULT NULL ,			
			`createdate` TIMESTAMP NULL DEFAULT NULL , 
			`updatedate` TIMESTAMP NULL DEFAULT NULL
			) 
			 $charset_collate;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}
public function qr_code(){
	global $wpdb;
	
	$charset_collate = $wpdb->get_charset_collate();
	
	$table_name =$wpdb->prefix.'qr_code';
	
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $wpdb->prefix.'qr_code') {
		$sql = "CREATE TABLE `$table_name`(
			`id` BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY , 		
			`qr_code` BIGINT NOT NULL	,	
			`product_type`  BIGINT(20) NOT NULL , 
			`card_type` BIGINT(20) NOT NULL ,			
			`createdate` TIMESTAMP NULL DEFAULT NULL , 
			`updatedate` TIMESTAMP NULL DEFAULT NULL,
			UNIQUE (`qr_code`),
			KEY `".$table_name."_product_type_foreign` (`product_type`),
				KEY `".$table_name."_card_type_foreign` (`card_type`),

				CONSTRAINT `".$table_name."_product_type_foreign` 
				FOREIGN KEY (product_type) 
				REFERENCES ".$wpdb->prefix."card_type(id) ON DELETE RESTRICT ON UPDATE RESTRICT,

				CONSTRAINT `".$table_name."_card_type_foreign` 
				FOREIGN KEY (card_type) 
				REFERENCES ".$wpdb->prefix."card_type(id) ON DELETE RESTRICT ON UPDATE RESTRICT
			) 
			 $charset_collate;";
			 require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			 dbDelta($sql);
			
         }else{
			$getColumn= $wpdb->get_row("SELECT * FROM $table_name");
//Add column if not present.
				if(!isset($getColumn->user_owner)){
				$wpdb->query("ALTER TABLE $table_name ADD `user_owner` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
				ADD`user_upline` BIGINT(20) UNSIGNED NULL DEFAULT NULL ,
				ADD KEY `".$table_name."_user_owner_foreign` (`user_owner`),
				ADD KEY `".$table_name."_user_upline_foreign` (`user_upline`),

				ADD CONSTRAINT `".$table_name."_user_owner_foreign` 
				FOREIGN KEY (user_owner) 
				REFERENCES ".$wpdb->prefix."users(ID) ON DELETE RESTRICT ON UPDATE RESTRICT,

				ADD CONSTRAINT `".$table_name."_user_upline_foreign` 
				FOREIGN KEY (user_upline) 
				REFERENCES ".$wpdb->prefix."users(ID) ON DELETE RESTRICT ON UPDATE RESTRICT
				 ");
				}
		 }
		
	}






public function qr_code_actions(){
	global $wpdb;
	
	$charset_collate = $wpdb->get_charset_collate();
	
	$table_name =$wpdb->prefix.'qr_code_actions';
	
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $wpdb->prefix.'qr_code_actions') {
		$sql = "CREATE TABLE `$table_name`(
			`id` BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY , 		
			`qr_code_action` VARCHAR(255) NULL DEFAULT NULL , 
			`qr_code_desc` VARCHAR(255) NULL DEFAULT NULL ,			
			`createdate` TIMESTAMP NULL DEFAULT NULL , 
			`updatedate` TIMESTAMP NULL DEFAULT NULL
			) 
			 $charset_collate;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}


public function qr_code_actions_history(){
	global $wpdb;
	
	$charset_collate = $wpdb->get_charset_collate();
	
	$table_name =$wpdb->prefix.'qr_code_actions_history';
	
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $wpdb->prefix.'qr_code_actions_history') {
		$sql = "CREATE TABLE `$table_name`(
			`id` BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY , 		
			`qr_code_action` BIGINT(20) NOT NULL , 
			`user_id` VARCHAR(255) NULL DEFAULT NULL ,			
			`createdate` TIMESTAMP NULL DEFAULT NULL , 
			`updatedate` TIMESTAMP NULL DEFAULT NULL,
			KEY `".$table_name."_qr_code_action_foreign` (`qr_code_action`),

				CONSTRAINT `".$table_name."_qr_code_action_foreign` 
				FOREIGN KEY (qr_code_action) 
				REFERENCES ".$wpdb->prefix."qr_code_actions(id) ON DELETE RESTRICT ON UPDATE RESTRICT
			) 
			 $charset_collate;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}






}