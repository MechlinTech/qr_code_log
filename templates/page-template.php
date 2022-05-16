<?php

 /* Template Name: Example Template */
global $wpdq;
 $table_name =$wpdb->prefix.'qr_code_scans_log';
 if(isset($_GET['qr'])){
$qr = $_GET['qr'];
//  $get_data = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE qr_code = ".$qr .""); 

// if(count($get_data)==0) {
    
    $now = new DateTime();
    $wpdb->insert($table_name, array(
        'date_time_stamp'=> $now->format('Y-m-d H:i:s'),
        'qr_code'=>$qr ,
        'card_type'=>'none',
        'tidny_trump_card'=> $now->format('Y-m-d H:i:s'),
     ));
// }else{
//     echo 'already in log';
// }


 }
