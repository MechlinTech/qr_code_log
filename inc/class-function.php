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
	  $this->get_data();
      add_action( 'show_user_profile', array($this,'extra_user_profile_fields') );
      add_action( 'edit_user_profile', array($this,'extra_user_profile_fields') );
	  add_action('wp_enqueue_scripts', array($this,'callback_for_scripts_styles') );
	  
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
 					<td><a href="#popup1" id="button"><?php echo $value; ?></a></td>
					
					
		 <?php
	  }

	  ?>
	  <style>
	  .box {
  width: 40%;
  margin: 0 auto;
  background: rgba(255,255,255,0.2);
  padding: 35px;
  border: 2px solid #fff;
  border-radius: 20px/50px;
  background-clip: padding-box;
  text-align: center;
}

.button {
  font-size: 1em;
  padding: 10px;
  color: #fff;
  border: 2px solid #06D85F;
  border-radius: 20px/50px;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.3s ease-out;
}
.button:hover {
  background: #06D85F;
}

.overlay {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.7);
  transition: opacity 500ms;
  visibility: hidden;
  opacity: 0;
}
.overlay:target {
  visibility: visible;
  opacity: 1;
}

.popup {
  margin: 70px auto;
  padding: 20px;
  background: #fff;
  border-radius: 5px;
  width: 30%;
  position: relative;
  transition: all 5s ease-in-out;
}

.popup h2 {
  margin-top: 0;
  color: #333;
  font-family: Tahoma, Arial, sans-serif;
}
.popup .close {
  position: absolute;
  top: 20px;
  right: 30px;
  transition: all 200ms;
  font-size: 30px;
  font-weight: bold;
  text-decoration: none;
  color: #333;
}
.popup .close:hover {
  color: #06D85F;
}
.popup .content {
  max-height: 30%;
  overflow: auto;
}

@media screen and (max-width: 700px){
  .box{
    width: 70%;
  }
  .popup{
    width: 70%;
  }
}
</style>
      
<div id="popup1" class="overlay">
	<div class="popup">
		<h2>Here i am</h2>
		<a class="close" href="#">&times;</a>
		<div class="content">
			
			<?php 
			global $wpdb;
			
			$id= $_GET['value'];
			$table_name =$wpdb->prefix.'map_user_to_user';
	$results = $wpdb->get_results( "SELECT * FROM $table_name"); // Query to fetch data from database table and storing in $results
if(!empty($results))                        // Checking if $results have some values or not
{    
         
    foreach($results as $row){   
    
    echo "ID  : " . $row->id . " <br>";
    echo "userid  : " . $row->userid . " <br>";
    echo "upline_userid : " . $row->upline_userid . " <br>";
    echo "fname : " . $row->fname . " <br>";
    
    }
   
}
	?>
		</div>
	</div>
</div>
      
     
     
    </tr>
	<?php  } ?>
  </tbody>
</table>
	
	
	
	<?php

    
}

public function callback_for_scripts_styles() 
{
    wp_register_style( 'custom_style', plugins_url('css/custom.css',__FILE__ ) );
    wp_enqueue_style( 'custom_style' , plugins_url('css/custom.css',__FILE__ ) );
    wp_enqueue_script( 'custom_script', plugins_url('js/custom.js', __FILE__) );
	
	wp_localize_script( 'ajax-script', 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}

public function get_data() {
	global $wpdb;
    $abc = '1';
    //$result = $wpdb->get_results("SELECT * FROM ".$wpdb->options ." WHERE option_name LIKE '_transient_%'");
    //echo  $result; //returning this value but still shows 0
    //wp_die();
	?>
	
	<?php 
	
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