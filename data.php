<?php
require_once( '../wp-load.php' );
$mode = $_REQUEST['mode'];

function filtering_ads(){
		GLOBAL $wpdb;
			$ad = $_REQUEST['type'];
			$web = $_REQUEST['web'];
			$blog = $_REQUEST['blog'];
			$social = $_REQUEST['social'];
			$apps = $_REQUEST['apps'];
			$cat = $_REQUEST['category'];
			$start = $_REQUEST['start'];
						
			if($ad == 'selling'){
			$sells = $wpdb->get_results( "SELECT post_id from {$wpdb->prefix}postmeta where meta_key ='ad' AND meta_value = 'selling'");
				
				foreach( $sells as $selling ){
						$sellings .= "'".$selling->post_id."', ";
					}				
				$selling = rtrim( $sellings, ', ');
			
			}else if($ad == 'buying'){
				
				$buys =  $wpdb->get_results( "SELECT post_id from {$wpdb->prefix}postmeta where meta_key ='ad' AND meta_value = 'buying'");
				foreach( $buys as $buying ){
						$buyings .= "'".$buying->post_id."', ";
					}				
				$buy = rtrim( $buyings, ', ');
			}else{
				$default = $wpdb->get_results( "SELECT post_id from {$wpdb->prefix}postmeta where meta_key ='ad'");
				foreach( $default as $defaults ){
						$defaultss .= "'".$defaults->post_id."', ";
					}
				$def = rtrim( $defaultss, ', ');
				
			}
			
			if(isset($selling)){
				$cond = $selling;
			}else if(isset($buy)){
				$cond = $buy;
			}else{
				$cond = $def;
			}
			$sql = "SELECT ID FROM wp_3081323q7w_posts p ";
			$from = "";
			$query = "p.post_type = 'post' AND p.post_status = 'publish'";
			$dist =  "";
	
	if( $web != "0" ){
		
			$from .= "INNER JOIN wp_3081323q7w_postmeta m1 ON ( p.ID = m1.post_id )";
			$query .= "AND (m1.meta_key = 'website_url' AND m1.meta_value != '' )"; 
		
	}
	if( $blog != "0" ){
		
		
			$from .= "INNER JOIN wp_3081323q7w_postmeta m2 ON ( p.ID = m2.post_id )";
			$query .= "AND (m2.meta_key = 'blog_url' AND m2.meta_value != '')";
		
	}
	if( $social != "0" ){
		
		
			$from .= "INNER JOIN wp_3081323q7w_postmeta m3 ON ( p.ID = m3.post_id )";
			$query .= "AND (m3.meta_key = 'social' AND m3.meta_value != '') ";
		
	}
	if( $apps != "0" ){
		
		 
			$from .= " INNER JOIN wp_3081323q7w_postmeta m4 ON ( p.ID = m4.post_id )";
			$query .= "AND (m4.meta_key = 'apps' AND m4.meta_value != '') ";
		
	}
	 if( $cat != "" ){
		
		
			$from .= " INNER JOIN wp_3081323q7w_term_relationships rl ON ( p.ID = rl.object_id )";
			
			$query .= "and rl.`term_taxonomy_id` IN($cat) ";
		
	} 
	
			$r = $wpdb->get_results($sql.' '.$dist.''.$from.' where '.$query .'AND p.`ID` IN ('.$cond.')', ARRAY_N ); 
			
			/* /* echo count($r);
			echo count($buys); 
			echo $wpdb->last_query;
			print_r($r);
			exit; */
			 
			foreach($r as $user) 
			{
				
				if( isset( $user[1] ) ){
					$states[] = $user[1]; // Grabing their state from their profile page
				}else{
					$states[] = $user[0]; // Grabing their state from their profile page
				}
			}
			if(!empty($states)){
			$states = array_unique($states);
			$results = array();
			foreach( $states as $display ){
				$post = get_post( $display );
				if( $post->post_status == 'publish' )
				{
					$ad_id = $post->ID;
					settype($ad_id, "string");
					$ad_title =  $post->post_title;
					$ad_desc = $post->post_content;
					$thumbnail_id = get_post_meta( $ad_id, '_thumbnail_id', true );
					$thumbnail = get_post_meta( $thumbnail_id, '_wp_attached_file', true );
					if($thumbnail !== false){
						$thumbnail_url = site_url().'/wp-content/uploads/'.$thumbnail;
					}else{
						$thumbnail_url ="";
					}
					$results[] = array("ad_id"=>$ad_id,"ad_title"=>$ad_title,"ad_desc"=>strip_tags($ad_desc),'image'=>$thumbnail_url);
				}
			} 
			
				if($start == ""){
				$showresults = array_slice($results, 0, 10);
					}else{
						
					$showresults = array_slice($results, $start, 10);
					}
				$json = array('status'=>1,'message'=>$showresults);
			}else{
				$json = array('status'=>0,'message'=>"No Ads found");
			}
			
		header('Content-type: application/json');
		echo json_encode($json); 	
			
	}

	if($mode == 'filtering_ads'){
		filtering_ads();
	}

	
	?>