<?php

class QRCodeAdminMenu{
    static function init()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new QRCodeAdminMenu();
        }
        return $instance;
    }
    public function __construct()
    {
        add_action( 'admin_menu', array($this,'admin_menu_for_qr_code' ));
        add_action( 'wp_ajax_nopriv_update_qr_code_generation', array($this,'update_qr_code_generation') );
	  add_action( 'wp_ajax_update_qr_code_generation', array($this,'update_qr_code_generation') );

      add_action( 'wp_ajax_nopriv_update_user_line', array($this,'update_user_line') );
	  add_action( 'wp_ajax_update_user_line', array($this,'update_user_line') );
      

      
    }
    public function update_qr_code_generation() {
        global $wpdb;
        $user_id = $_POST['user_id'];
        $user_owner =$_POST['user_owner'];
        $id =$_POST['id'];
        $table_name =$wpdb->prefix.'qr_code';

        $dbData = array('user_upline'=>$user_id,'user_owner'=>$user_owner,'updatedate'=>time());
       

        $data = $wpdb->update($table_name , $dbData, array('id' => $id));
        echo $data;
      die();
    }






    public function update_user_line() {
        global $wpdb;
        $user_id = $_POST['user_id'];
        $selected_owner =$_POST['selectesUser'];
        $id =$_POST['id'];
        $table_name =$wpdb->prefix.'qr_code';

        $dbData = array('user_owner'=>$selected_owner,'updatedate'=>time());
       

        $data = $wpdb->update($table_name , $dbData, array('id' => $id));
        wp_send_json_success($data);
      die();
    }

  




public function admin_menu_for_qr_code() {

    add_menu_page(
    
    __( 'Product And QR Code Import/Export', 'qr-code_ex-im' ),
    
    __( 'Product And QR Code Import/Export', 'qr-code_ex-im'  ),
    
    'manage_options',
    
    'qr_code_export_import',
    
    array($this,'qr_code_ex_im_admin_page_contents'),
    
    'dashicons-schedule',
    
    3
    
    );

   
    add_submenu_page("qr_code_export_import", 
    "Relatation Qr Code", 
    "Relating Qr Code",
     0, 
     "my-submenu-slug",
      array($this,"relatingqr_code"));
    
    }
    
    public function relatingqr_code() {
        require_once(plugin_dir_path(__DIR__) . 'admin/admin-ui.php');
      

    }

    
    
   
    
    
    
    public function qr_code_ex_im_admin_page_contents() {
    
       global $wpdb;
 
       // Table name
      
       
       // Import CSV
       if(isset($_POST['butimport'])){
       
         // File extension
         $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);
       
         // If file extension is 'csv'
         if(!empty($_FILES['import_file']['name']) && $extension == 'csv'){
       
           $totalInserted = 0;
       
           // Open file in read mode
           $csvFile = fopen($_FILES['import_file']['tmp_name'], 'r');
       
           fgetcsv($csvFile); // Skipping header row
       
           // Read file
           while(($csvData = fgetcsv($csvFile)) !== FALSE){
             $csvData = array_map("utf8_encode", $csvData);
             $insert_data_array = array();
            
             // Row column length
             $dataLen = count($csvData);
             $totalInserted++;
             // get product type
             $table_name =$wpdb->prefix.'product_type';
             $sql = "SELECT * FROM `$table_name` WHERE product_type = '$csvData[1]'";
             $results = $wpdb->get_results($sql);
             $product_type = $results[0]->id;
             // get card type
             $table_name =$wpdb->prefix.'card_type';
             $sql = "SELECT * FROM `$table_name` WHERE card_type = '$csvData[2]'";
             $results = $wpdb->get_results($sql);
             $card_type = $results[0]->id;
            
 
             $tablename = $wpdb->prefix.'qr_code';
 
             $wpdb->insert( $tablename, array(
                'qr_code'=>$csvData[0]	,	
                'product_type' => $product_type  , 
                'card_type'=> $card_type,	  
                'createdate' => date_create()->format('Y-m-d H:i:s') , 
                      'updatedate' =>date_create()->format('Y-m-d H:i:s')     
             ));
             
           
       
             }
       
           }
           echo "<h3 style='color: green;'>Total record Inserted : ".$totalInserted."</h3>";
       
       
         }else{
           echo "<h3 style='color: red;'>Invalid Extension</h3>";
         }
       
       
       
       ?>
    
    <h2>All Entries</h2>
 
 <!-- Form -->
 <form method='post' action='<?= $_SERVER['REQUEST_URI']; ?>' enctype='multipart/form-data'>
   <input type="file" name="import_file" >
   <input type="submit" name="butimport" value="Import">
 </form>
 
 <!-- Record List -->
 <table width='100%' border='1' style='border-collapse: collapse;'>
    <thead>
    <tr>
      <th>ID</th>
      <th>QR Code</th>
      <th>Product Type</th>
      <th>Card Type</th>
      <th>User Upline</th>
        <th>User Owner</th>
      <th>Create Date</th>
      <th>Update Date</th>
  
    </tr>
    </thead>
    <tbody>
      <?php
 
      $page = 1;
      if(isset($_GET['page_no'])){
       $page = $_GET['page_no'];
      }
 
      $page =$page *20;
      $page_off =  $page -20;
 $table_name =$wpdb->prefix.'qr_code';




 $sql = "SELECT * FROM `$table_name`
 

  LIMIT 20 OFFSET $page_off";
 
 $results = $wpdb->get_results($sql);


 $sql_user = "SELECT * FROM `".$wpdb->prefix."users`";






 
 $result_users = $wpdb->get_results($sql_user);
 
 foreach($results as $key=>$item){
   
  
    $table_product =$wpdb->prefix.'product_type';

    $table_card =$wpdb->prefix.'card_type';




    $sql_product = "SELECT * FROM `$table_product` WHERE id= $item->product_type";
    $results_product = $wpdb->get_row($sql_product);


    $sql_card = "SELECT * FROM `$table_card` WHERE id= $item->card_type";
    $results_card = $wpdb->get_row($sql_card);
      ?>
      <tr data-id="<?php echo $item->id;  ?>">
        <th><?php echo $item->id;  ?></th>
        <th><?php echo $item->qr_code;  ?></th>
        <th><?php echo $results_product->product_type;  ?></th>
        <th><?php echo $results_card->card_type;  ?></th>
        
        <th>
         
            <?php
                foreach ($result_users as $key => $value) {
                    if($item->user_upline==$value->ID){
                        echo $value->user_login;
                    }
                  
                }

            ?>
      
       </th>
        <th>
    
       
            <?php
                foreach ($result_users as $key => $value) {
                    if($item->user_owner==$value->ID){
                      echo $value->user_login;
                    }
                  
                }

            ?>
        </select>    </th>
        <th><?php echo $item->updatedate;  ?></th>
        <th><?php echo $item->createdate;  ?></th>
     
       </tr>
   <?php
    }
 
 ?>
  
   </tbody>
 </table>
    
    <?php
     $table_name =$wpdb->prefix.'qr_code';
     $sql = "SELECT COUNT(*) as count FROM `$table_name` ";
     $results = $wpdb->get_results($sql,OBJECT);

     echo round(($results[0]->count)/20);
 }





}