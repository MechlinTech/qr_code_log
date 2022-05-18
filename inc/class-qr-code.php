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
      $this->qr_data_get();
    }

    public function qr_data_get(){
        global $wpdb;
	
		$charset_collate = $wpdb->get_charset_collate();
		
		$table_name =$wpdb->prefix.'qr_code_list';
		
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $wpdb->prefix.'qr_code_list') {
			$sql = "CREATE TABLE `$table_name`(
				`id` BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY , 
				`active` BOOLEAN NULL DEFAULT TRUE , 
				`userid` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
				`qr_code` VARCHAR(255) UNSIGNED NULL DEFAULT NULL, 
				`fname` VARCHAR(255) NULL DEFAULT NULL , 
				`lname` VARCHAR(255) NULL DEFAULT NULL , 
				`address` VARCHAR(255) NULL DEFAULT NULL , 
				`city` VARCHAR(50) NULL DEFAULT NULL, 
				`state` VARCHAR(50) NULL DEFAULT NULL , 
				`zip` VARCHAR(50) NULL DEFAULT NULL , 
				`order_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL , 
                `item_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL , 
				`createdate` TIMESTAMP NULL DEFAULT NULL , 
				`updatedate` TIMESTAMP NULL DEFAULT NULL
                ) 
                 $charset_collate;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
		}
    }

    public function create_qr_data($data){
        global $wpdb;
         $tablename = $wpdb->prefix.'qr_code_list';

        $wpdb->insert( $tablename, array(
                'userid'=> $data['userid'] ,
				'qr_code'=>$data['qr_code'] , 
				'fname'=>$data['fname']  , 
				'lname'=>$data['lname'] , 
				'address'=>$data['address'] , 
				'city'=>$data['city'], 
				'state'=>$data['state'] , 
				'zip'=>$data['zip'] , 
				'order_id'=>$data['order_id']  , 
                'item_id'=>$data['item_id']  , 
				'createdate'=>$data['createdate']  , 
				'updatedate'=>$data['updatedate']          
        ));
    }


  

}