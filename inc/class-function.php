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
	  add_action('admin_init', array($this,'callback_for_scripts_styles') );
	  
	  add_action( 'wp_ajax_nopriv_get_data', array($this,'get_data') );
	  add_action( 'wp_ajax_get_data', array($this,'get_data') );
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
	  <?php for($count=0 ;$count< $level ;$count++){ ?>
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
 					<td><a href="#popup" class="get_user_button" data-user="<?php echo $value; ?>"><?php echo $value; ?></a></td>
					
					
		 <?php
	  }

	  ?>
	
      

      
     
     
    </tr>
	<?php  } ?>
  </tbody>
</table>

<svg display="none" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="768" height="800" viewBox="0 0 768 800"><defs><g id="icon-close"><path class="path1" d="M31.708 25.708c-0-0-0-0-0-0l-9.708-9.708 9.708-9.708c0-0 0-0 0-0 0.105-0.105 0.18-0.227 0.229-0.357 0.133-0.356 0.057-0.771-0.229-1.057l-4.586-4.586c-0.286-0.286-0.702-0.361-1.057-0.229-0.13 0.048-0.252 0.124-0.357 0.228 0 0-0 0-0 0l-9.708 9.708-9.708-9.708c-0-0-0-0-0-0-0.105-0.104-0.227-0.18-0.357-0.228-0.356-0.133-0.771-0.057-1.057 0.229l-4.586 4.586c-0.286 0.286-0.361 0.702-0.229 1.057 0.049 0.13 0.124 0.252 0.229 0.357 0 0 0 0 0 0l9.708 9.708-9.708 9.708c-0 0-0 0-0 0-0.104 0.105-0.18 0.227-0.229 0.357-0.133 0.355-0.057 0.771 0.229 1.057l4.586 4.586c0.286 0.286 0.702 0.361 1.057 0.229 0.13-0.049 0.252-0.124 0.357-0.229 0-0 0-0 0-0l9.708-9.708 9.708 9.708c0 0 0 0 0 0 0.105 0.105 0.227 0.18 0.357 0.229 0.356 0.133 0.771 0.057 1.057-0.229l4.586-4.586c0.286-0.286 0.362-0.702 0.229-1.057-0.049-0.13-0.124-0.252-0.229-0.357z"></path></g></defs></svg>
<div class="modal">
    <div class="modal-overlay modal-toggle"></div>
    <div class="modal-wrapper modal-transition">
      <div class="modal-header">
      
        <h2 class="modal-heading">User details</h2>
        <button class="modal-close modal-toggle"><svg class="icon-close icon" viewBox="0 0 32 32"><use xlink:href="#icon-close"></use></svg></button>
      </div>
      
      <div class="modal-body">
        <div class="modal-content">
          <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Impedit eum delectus, libero, accusantium dolores inventore obcaecati placeat cum sapiente vel laboriosam similique totam id ducimus aperiam, ratione fuga blanditiis maiores.</p>
        
        </div>
      </div>
    </div>
  </div>
	<?php

    
}

public function callback_for_scripts_styles() 
{
    // wp_register_style( 'custom_style', plugins_url('css/custom.css',__FILE__ ) );
    wp_enqueue_style( 'custom_style' , plugins_url('css/custom.css',__FILE__ ) );
    wp_enqueue_script( 'custom_script', plugins_url('js/custom.js', __FILE__), array('jquery'));
	
	wp_localize_script( 'custom_script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}

public function get_data() {
	global $wpdb;
    $user_id = $_POST['user_id'];

	echo $user_id;
  die();
}

public function fetch_data()
{ ?>

<?php 
 global $wpdb, $db;
 $table_name =$wpdb->prefix.'qr_code_scans_log';
  $query="SELECT * from $table_name";
  $exec=mysqli_query($db, $query);
  if(mysqli_num_rows($exec)>0){
    $row= mysqli_fetch_all($exec, MYSQLI_ASSOC);
    return $row;  
        
  }else{
    return $row=[];
  }
}
//const $fetchData= fetch_data();
//show_data($fetchData);
public function show_data($fetchData){
 echo '<table border="1">
        <tr>
            <th>S.N</th>
            <th>Full Name</th>
            <th>Email Address</th>
            <th>City</th>
            <th>Country</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>';
 if(count($fetchData)>0){
      $sn=1;
      foreach($fetchData as $data){ 
  echo "<tr>
          <td>".$sn."</td>
          <td>".$data['fullName']."</td>
          <td>".$data['emailAddress']."</td>
          <td>".$data['city']."</td>
          <td>".$data['country']."</td>
          <td><a href='crud-form.php?edit=".$data['id']."'>Edit</a></td>
          <td><a href='crud-form.php?delete=".$data['id']."'>Delete</a></td>
   </tr>";
       
  $sn++; 
     }
}else{
     
  echo "<tr>
        <td colspan='7'>No Data Found</td>
       </tr>"; 
}
  echo "</table>";
}
}