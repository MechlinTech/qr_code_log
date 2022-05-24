

<?php



class WooProductData{
    static function init()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new WooProductData();
        }
        return $instance;
    }
    public function __construct()
    {
        add_action( 'woocommerce_thankyou', array($this,'add_data_for_qr_code'), 10, 1);
       // add_action( 'woocommerce_product_options_general_product_data', array($this,'misha_option_group') );
        add_filter( 'woocommerce_product_data_tabs',  array($this,'woo_new_product_tab') );
        add_action( 'woocommerce_product_data_panels',  array($this,'add_tidny_card_product_data_fields') );
        add_action( 'woocommerce_process_product_meta', array($this,'qr_code_save_fields'), 10, 2 );
        $this->woo_product_type_and_card_type();

        add_action( 'wp_ajax_nopriv_add_update_product_type_and_card_type', array($this,'add_update_product_type_and_card_type') );
        add_action( 'wp_ajax_add_update_product_type_and_card_type', array($this,'add_update_product_type_and_card_type') );
        
    }

  
 /**
 * Add a custom product data tab
 */

public function add_update_product_type_and_card_type(){
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
	$product_type = $_POST['product_type'];
    $card_type = $_POST['card_type'];
    $product_id =$_POST['product_id'];
    $map_id  =$_POST['map_id'];
	$table_name =$wpdb->prefix.'product_type_and_card_type';
    $sql_card = "SELECT * FROM `$table_name`;";
    $remove_map_id = $_POST['remove_id'];
 

    foreach($remove_map_id as $item){
        $wpdb->delete( $table_name, array( 'id' => $item ) );
    }
  
   
 if (count($product_type)==count($card_type)){
     for ($i=0;$i<count($card_type);$i++) {
         
        if($card_type[$i]!="NULL" && $product_type[$i] !="NULL"){
        
     $reData =$wpdb->get_row("SELECT * FROM `$table_name` WHERE `product_id`=$product_id AND `card_type`=$card_type[$i] AND `product_type`=$product_type[$i];");

    
       if(!isset($reData)){
       
       if($map_id[$i]=="NULL"){
           $wpdb->insert( 
                    $table_name, array(
                'product_type'=>$product_type[$i],
                'card_type'=> $card_type[$i],
                'product_id'=>$product_id,
                'createdate'=> date_create()->format('Y-m-d H:i:s'),
                'updatedate'=> date_create()->format('Y-m-d H:i:s')
                )
            );
       }   
       }else {
        $dbData = array('product_type'=>$product_type[$i] , 'card_type'=>$card_type[$i],'updatedate'=>date_create()->format('Y-m-d H:i:s'));
        $data = $wpdb->update($table_name , $dbData, array('id' => $map_id[$i]));
       }
     }

 }

 wp_send_json_success( array('data'=>'$reDatadsfsdf') );
 }


    wp_send_json_success( array('data'=>$data) );


}






 public function woo_product_type_and_card_type()
 {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
	
	$table_name =$wpdb->prefix.'product_type_and_card_type';
	
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $wpdb->prefix.'product_type_and_card_type') {
		$sql = "CREATE TABLE `$table_name`(
			`id` BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY , 		
			`product_type` BIGINT(20)  NULL , 
            `card_type` BIGINT(20)  NULL ,	
            `product_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL ,
            `quantity` BIGINT(20)  NULL DEFAULT 1 ,
			`createdate` TIMESTAMP NULL DEFAULT NULL , 
			`updatedate` TIMESTAMP NULL DEFAULT NULL,
            
             KEY `" . $table_name . "_product_type_foreign` (`product_type`),
				KEY `" . $table_name . "_card_type_foreign` (`card_type`),
				KEY `" . $table_name . "_product_id_foreign` (`product_id`),


                CONSTRAINT `".$table_name."_product_type_action_foreign` 
				FOREIGN KEY (product_type) 
				REFERENCES ".$wpdb->prefix."product_type(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
				
				
				CONSTRAINT `" . $table_name . "_card_type_foreign` 
				FOREIGN KEY (card_type) 
				REFERENCES ".$wpdb->prefix."card_type(id) ON DELETE RESTRICT ON UPDATE RESTRICT,

				
				CONSTRAINT `" . $table_name . "_product_id_foreign` 
				FOREIGN KEY (product_id) 
				REFERENCES ".$wpdb->prefix."posts(ID) ON DELETE RESTRICT ON UPDATE RESTRICT
			) 
			 $charset_collate;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
 }

public function woo_new_product_tab( $product_data_tabs) {
	
	// Adds the new tab
	
	$product_data_tabs['card_setting'] = array(
		'label' => __( 'Card Settings', 'card_setting' ),
		'target' => 'card_setting',
        
	);
	return $product_data_tabs;

}


public function add_tidny_card_product_data_fields() {
	global $woocommerce, $post,$wpdb;
	?>
	<!-- id below must match target registered in above add_my_custom_product_data_tab function -->
	<div id="card_setting" class="panel woocommerce_options_panel" data-product_id="<?php echo get_the_ID(); ?>">
		<?php
		woocommerce_wp_checkbox( array( 
			'id'            => 'tidny_qr_code', 
			'wrapper_class' => 'show_if_simple', 
			'label'         => __( 'Enable', 'card_setting' ),
			'description'   => __( 'Qr Code on Product', 'card_setting' ),
			'default'  		=> '0',
            'value'       => get_post_meta( get_the_ID(), 'card_setting', true ),
			'desc_tip'    	=> false,
		) );
        $product_id =get_the_ID();
        $table_name =$wpdb->prefix.'product_type_and_card_type';

        $table_product =$wpdb->prefix.'product_type';
        $sql_product = "SELECT * FROM `$table_product`;";
        $results_product = $wpdb->get_results($sql_product);


        $table_card =$wpdb->prefix.'card_type';
        $sql_card = "SELECT * FROM `$table_card`;";
        $results_card = $wpdb->get_results($sql_card);
		?>
<form class="submit_data">
<div class="card_list">
<div class="type_records_dynamic">
<!-- left over element of array and loop this -->

<?php  $reData =    $wpdb->get_results("SELECT * FROM `$table_name` WHERE `product_id`=$product_id;");

foreach($reData as $key=>$map_data){


?>
<div class="remove" data-map_id="<?php echo $map_data->id; ?>">
       <div>
           <select class="product_type_relation" name="product_type[]">
      <option value="NULL">
          please select product type
      </option>
      <?php
      foreach($results_product as $key=>$item){
      if($map_data->product_type==$item->id){
        echo '<option value="'.$item->id.'" selected>'.$item->product_type.'</option>';
      }else{
            echo '<option value="'.$item->id.'" >'.$item->product_type.'</option>';
      }
        
      }
      ?>
  </select>
      </div>
 <div>
    <select class="card_type_relation"  name="card_type[]">
      <option value="NULL">
          please select card type
      </option>
      <?php
      foreach($results_card as $key=>$item){
        if($map_data->card_type==$item->id){
            echo '<option value="'.$item->id.'" selected>'.$item->card_type.'</option>';
          }else{
            echo '<option value="'.$item->id.'" >'.$item->card_type.'</option>';
          }
         
      }
      ?>
  </select> 
 </div>
 <div>
     <input class="quantity_relation"  name="product_quantity[]" value="<?php echo $map_data->quantity;  ?>" />
 </div>
    
  <a href="#" class="remove-field btn-remove-type button button-primary">Remove </a> 

</div>


<?php  } ?>

</div>




  <div class="type_records">
      <!-- array first element -->
      <div>
           <select class="product_type_relation" name="product_type[]">
      <option value="NULL">
          please select product type
      </option>
      <?php
      foreach($results_product as $key=>$item){
          echo '<option value="'.$item->id.'" >'.$item->product_type.'</option>';
      }
      ?>
  </select>
      </div>
 <div>
    <select class="card_type_relation"  name="card_type[]">
      <option value="NULL">
          please select card type
      </option>
      <?php
      foreach($results_card as $key=>$item){
          echo '<option value="'.$item->id.'" >'.$item->card_type.'</option>';
      }
      ?>
  </select> 
 </div>
 <div>
     <input class="quantity_relation"  name="product_quantity[]" value="" />
 </div>
  
    <a class="extra-fields-type button button-primary" href="#">Add </a>
  </div>

  

</div>
<button class="button button-primary update-type-product" data-remove_id="[]">Update</button>

</form>
	</div>
	<?php

}

function qr_code_save_fields( $id, $post ){
 
	if(get_post_meta( get_the_ID(), 'card_setting', true )){
        update_post_meta( $id, 'card_setting', $_POST['card_setting'] );
    }else{
        add_post_meta( $id, 'card_setting', $_POST['card_setting'] );
    }
		
	
 
}


    public function add_data_for_qr_code( $order_id ) {
      
        if ( ! $order_id )
            return;
    
        // Getting an instance of the order object
        $order = wc_get_order( $order_id );

        echo json_encode( $order );
        $user_id   = $order->get_user_id();
        if($order->is_paid())
            $paid = 'yes';
        else
            $paid = 'no';

       
        foreach ( $order->get_items() as $item_id => $item ) {
                $time = time();
            if( $item['variation_id'] > 0 ){
                $product_id = $item['variation_id']; // variable product
            } else {
                $product_id = $item['product_id']; // simple product
            }
    
            // Get the product object
            $product = wc_get_product( $product_id );
            
           $qr_code = get_post_meta( $product_id , 'tidny_qr_code', true ) ;
           if($qr_code=="yes"){
               if(empty(wc_get_order_item_meta( $item_id, 'qr_code_data', true ))){
               $data[$item_id] = wc_add_order_item_meta($item_id,'qr_code_data',$time);
            //    echo plugin_dir_path(__FILE__);
           
               
                    
               }else{
                $data[$item_id] = wc_get_order_item_meta($item_id,'qr_code_data',true); 
               }
             
           }
        }




    
        // Ouptput some data
        echo json_encode( $data );
         echo '<p>Order ID: '. $order_id . ' — Order Status: ' . $order->get_status() . ' — Order is paid: ' . $paid . '</p>';

         
    }
}