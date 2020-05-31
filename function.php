add_action('wp_ajax_artfilter', 'art_filter_function'); 
add_action('wp_ajax_nopriv_artfilter', 'art_filter_function');
 
function art_filter_function(){
	$args = array(
		'post_type' => 'show',
		'orderby' => 'date', // we will sort posts by date
		'order'	=> $_POST['date'] // ASC or DESC
	);
 
	// for taxonomies / categories
	if( isset( $_POST['categoryfilter'] ) && $_POST['categoryfilter'] )
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'event_type',
				'field' => 'id',
				'terms' => $_POST['categoryfilter']
			)
		);

	// фильтр по дате
	if( isset( $_POST['date_start'] ) && $_POST['date_start'] || isset( $_POST['date_end'] ) && $_POST['date_end']  )
		$args['meta_query'] = array( 'relation'=>'AND' ); // AND means that all conditions of meta_query should be true
 	
	function my_posts_where( $where ) {
   $where = str_replace("meta_key = 'tickets_and_date_$", "meta_key LIKE 'tickets_and_date_%", $where);  
   return $where;
}

add_filter('posts_where', 'my_posts_where'); 
	if( isset( $_POST['date_start'] ) && $_POST['date_start'] && isset( $_POST['date_end'] ) && $_POST['date_end'] ) {
		$args['meta_query'][] = 	array(
			array(
			'key' => 'tickets_and_date_$_event_date',
			'value' => $_POST['date_start'],
		//	'value' => date("Y-m-d"), // Set today's date (note the similar format)
			'compare' => '>=', // Return the ones greater than today's date
			'type' => 'numeric' 

		),
	array(
			'key' => 'tickets_and_date_$_event_date',
			'value' => $_POST['date_end'],
			'type' => 'numeric' ,
			'compare' => '<='
		));
		;
	} else {
			if( isset( $_POST['date_start'] ) && $_POST['date_start'] )
		$args['meta_query'][] = array(
			'key' => 'tickets_and_date_$_event_date',
			'value' => $_POST['date_start'],
			'type' => 'numeric' ,
			'compare' => '>='
		);
 		if( isset( $_POST['date_end'] ) && $_POST['date_end'] )
			$args['meta_query'][] = array(
				'key' => 'tickets_and_date_$_event_date',
				'value' => $_POST['date_end'],
				'type' => 'numeric' ,
				'compare' => '<='
			);
	}

 	// Фильтр по цене
 /*if( isset( $_POST['price_min'] ) && $_POST['price_min'] || isset( $_POST['price_max'] ) && $_POST['price_max']  )
   $args['meta_query'] = array( 'relation'=>'OR' ); // AND means that all conditions of meta_query should be true


 if( isset( $_POST['price_min'] ) && $_POST['price_min'] && isset( $_POST['price_max'] ) && $_POST['price_max'] ) {
   $args['meta_query'][] =   array(
    'relation' => 'AND',
         array(
		  'key' => 'events_prices_price_min',
		  'value' => $_POST['price_min'],
		  'type' => 'numeric',
		  'compare' => '>='
	   ),
		 array(
		  'key' => 'events_prices_price_max',
		  'value' => $_POST['price_max'],
		  'type' => 'numeric',
		  'compare' => '<='
   ));
   $args['meta_query'][] = array(
    'relation' => 'AND',
         array(
		  'key' => 'events_prices_price_max',
		  'value' => $_POST['price_max'],
		  'type' => 'numeric',
		  'compare' => '<='
	   ),
		 array(
		  'key' => 'events_prices_price_max',
		  'value' => $_POST['price_min'],
		  'type' => 'numeric',
		  'compare' => '>='
   ));
    $args['meta_query'][] = array(
    'relation' => 'AND',
         array(
      'key' => 'events_prices_price_min',
      'value' => $_POST['price_min'],
      'type' => 'numeric',
      'compare' => '>='
   ),
		 array(
		  'key' => 'events_prices_price_min',
		  'value' => $_POST['price_max'],
		  'type' => 'numeric',
		  'compare' => '<='
 ));
 
$args['meta_query'][] = array(
    'relation' => 'AND',
         array(
      'key' => 'events_prices_price_min',
      'value' => $_POST['price_min'],
      'type' => 'numeric',
      'compare' => '<='
   ),
		 array(
		  'key' => 'events_prices_price_max',
		  'value' => $_POST['price_max'],
		  'type' => 'numeric',
		  'compare' => '>='
		   ));
 } else {
		 if( isset( $_POST['price_min'] ) && $_POST['price_min'] )
	 $args['meta_query'][] = array(
		 'key' => 'events_prices_price_max',
		 'value' => $_POST['price_min'],
		 'type' => 'numeric',
		 'compare' => '>='
	 );
		if( isset( $_POST['price_max'] ) && $_POST['price_max'] )
		 $args['meta_query'][] = array(
			 'key' => 'events_prices_price_max',
			 'value' => $_POST['price_max'],
			 'type' => 'numeric',
			 'compare' => '<='
		 );
 }
*/
 
	$query = new WP_Query( $args );
 
	if( $query->have_posts() ) :
		while( $query->have_posts() ): $query->the_post();
		get_template_part( 'content', 'show' );
		endwhile;
		wp_reset_postdata();
	else :
		echo 'По вашим параметрам ничего не найдено';
	endif;
 
	die();
}
