

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
    }

  
 /**
 * Add a custom product data tab
 */

public function woo_new_product_tab( $product_data_tabs) {
	
	// Adds the new tab
	
	$product_data_tabs['tidny_card'] = array(
		'label' => __( 'Tidny Trump Card Setting', 'tidny_trump_card' ),
		'target' => 'tidny_trump_card',
        
	);
	return $product_data_tabs;

}


public function add_tidny_card_product_data_fields() {
	global $woocommerce, $post;
	?>
	<!-- id below must match target registered in above add_my_custom_product_data_tab function -->
	<div id="tidny_trump_card" class="panel woocommerce_options_panel">
		<?php
		woocommerce_wp_checkbox( array( 
			'id'            => 'tidny_qr_code', 
			'wrapper_class' => 'show_if_simple', 
			'label'         => __( 'Enable', 'tidny_trump_card' ),
			'description'   => __( 'Qr Code on Product', 'tidny_trump_card' ),
			'default'  		=> '0',
            'value'       => get_post_meta( get_the_ID(), 'tidny_qr_code', true ),
			'desc_tip'    	=> false,
		) );

      
		?>
	</div>
	<?php

}

function qr_code_save_fields( $id, $post ){
 
	if(get_post_meta( get_the_ID(), 'tidny_qr_code', true )){
        update_post_meta( $id, 'tidny_qr_code', $_POST['tidny_qr_code'] );
    }else{
        add_post_meta( $id, 'tidny_qr_code', $_POST['tidny_qr_code'] );
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
               echo plugin_dir_path(__FILE__);
           
               $dataToBeStored = array(
                'userid'=> $user_id  ,
				'qr_code'=>$time , 
				'fname'=>$data['fname']  , 
				'lname'=>$data['lname'] , 
				'address'=>$data['address'] , 
				'city'=>$data['city'], 
				'state'=>$data['state'] , 
				'zip'=>$data['zip'] , 
				'order_id'=>$order_id  , 
                'item_id'=>$item['order_item_id']  , 
				'createdate'=>$data['createdate']  , 
				'updatedate'=>$data['updatedate']          
               );
                    QRCodeDatas::create_qr_data($dataToBeStored);
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