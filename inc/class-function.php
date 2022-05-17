<?php

class QRCode{
    static function init()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new QrCode();
        }
        return $instance;
    }
    public function __construct()
    {
      $this->create_map_user_table();
      $this->create_table();
      add_action( 'show_user_profile', array($this,'extra_user_profile_fields') );
        add_action( 'edit_user_profile', array($this,'extra_user_profile_fields') );
    }
   public function create_map_user_table()
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


    public function level_data($user_id){




        global $wpdb;
            
        $charset_collate = $wpdb->get_charset_collate();
        $table_name =$wpdb->prefix.'map_user_to_user';
        
        $distinct_user=$wpdb->get_results("SELECT DISTINCT `upline_userid` FROM `$table_name`;");
        //$distinct_user=$wpdb->get_results("SELECT * FROM `$table_name` WHERE upline_userid='$user_id';");
        
        // return $distinct_user;
        
        $temp_t1 ='';
        $temp_t1_join ='';
        $count =1;
        foreach($distinct_user as $key=>$item){
            if(count($distinct_user)!=$count ){
                $temp_t1 = $temp_t1.'t'.$count.'.userid AS lev'.$count.', ';
            }else{
                $temp_t1 = $temp_t1.'t'.$count.'.userid AS lev'.$count.' ';
            }
        
        if($count!=1){
            $temp_t1_join = $temp_t1_join. "LEFT JOIN ".$table_name." AS t".$count." ON t".$count.".upline_userid = t".($count-1).".userid ";
                
        }
        $count++;
        
        }
         
        
        // return "SELECT ".$temp_t1." FROM ".$table_name." AS t1 ".$temp_t1_join." WHERE t1.userid = '".$user_id."';";
        
        
        $results = $wpdb->get_results("SELECT ".$temp_t1." FROM ".$table_name." AS t1 ".$temp_t1_join." WHERE t1.userid = '".$user_id."';");
          return $results;
        
        }

        public function create_table(){
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


        



public function extra_user_profile_fields( $user ) { ?>
    <h3>Level User</h3>
	<?php




$data_od_level = $this->level_data($user->ID);

$level = count((array)$data_od_level[0]);
	?>
	
	<table class="table">
  <thead>
    <tr>
      <th scope="col">#</th>
	  <?php for($count=1 ;$count<= $level ;$count++){ ?>
      <th scope="col">Level-<?php echo $count; ?></th>
      
<?php  } ?>
    </tr>
  </thead>
  <tbody>
	<?php foreach($data_od_level as $key=>$item){
		$level_data = (array)$item;
		?>  
    <tr>
	
      <th scope="row"><?php echo $key; ?></th>
	  <?php
	  foreach ($level_data as $key => $value) {
		 ?>
 					<td><?php echo $value; ?></td>
		 <?php
	  }

	  ?>
      

      
     
     
    </tr>
	<?php  } ?>
  </tbody>
</table>
	
	
	
	<?php





    
}

}