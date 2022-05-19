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
QrCode::init();
 QRCodeDatas::init();
WooProductData::init();




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






function my_admin_menu() {

   add_menu_page(
   
   __( 'Product And QR Code Import/Export', 'qr-code_ex-im' ),
   
   __( 'Product And QR Code Import/Export', 'qr-code_ex-im'  ),
   
   'manage_options',
   
   'qr_code_export_import',
   
   'qr_code_ex_im_admin_page_contents',
   
   'dashicons-schedule',
   
   3
   
   );
   
   }
   
   
   
   add_action( 'admin_menu', 'my_admin_menu' );
   
   
   
   function qr_code_ex_im_admin_page_contents() {
   
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
     <th>Create Date</th>
     <th>Update Date</th>
   </tr>
   </thead>
   <tbody>
  
 
  </tbody>
</table>
   
   <?php
   
}