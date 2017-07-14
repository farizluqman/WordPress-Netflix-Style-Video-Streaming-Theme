<?php

/**
 * Resume video time ajax
 *
 * @return bool
 * @author  @sameast
 */
function recently_watched_api_post() {

    // Run a nonce check
    if ( !wp_verify_nonce( $_REQUEST['nonce'], "recently_watched_api_nonce")) {

        echo json_encode(
            array(
                'error' => true,
                'message' => 'No naughty business please' 
            )
        );     
        die(); 

    }   

	// Get params
	$userId = get_current_user_id();

	// globally loop through post types.
	$args = array(
        'posts_per_page' => (int)get_theme_mod('streamium_global_options_homepage_desktop'),
        'post_type' => array('movie', 'tv','sport','kid','stream'),
        'meta_query' => array(
            array(
                'key' => 'recently_watched_user_id',
                'value' => get_current_user_id()
            )
        )
    );
    $loop = new WP_Query($args);
    if (is_user_logged_in() && $loop->post_count > 0) {

    	// Setup empty array
    	$data = [];

    	// Only run if user is logged in
        if ($loop->have_posts()):
            while ($loop->have_posts()) : $loop->the_post();
                if (has_post_thumbnail()) : // thumbnail check
                $image   = wp_get_attachment_image_src(get_post_thumbnail_id(), 'streamium-video-tile');
                $imageExpanded   = wp_get_attachment_image_src(get_post_thumbnail_id(), 'streamium-video-tile-expanded');
                $nonce = wp_create_nonce('streamium_likes_nonce');
                $trimexcerpt = !empty(get_the_excerpt()) ? get_the_excerpt() : get_the_content();

                	$paidTileText = false;
                	if($loop->post->premium){
                		$paidTileText = str_replace(array("_"), " ", $loop->post->plans[0]);
                	}
                	if (function_exists('is_protected_by_s2member')) {
                		$check = is_post_protected_by_s2member(get_the_ID());
                		if($check) { 
							$ccaps = get_post_meta(get_the_ID(), 's2member_ccaps_req', true);
							if(!empty($ccaps)){
								$paidTileText = implode(",", $ccaps);
							}else{
								$paidTileText = implode(",", $check);
							}
						}
                	}

                	$progressBar = false;
                	if(get_theme_mod( 'streamium_enable_premium' )) {
    					$progressBar = get_post_meta( get_the_ID(), 'user_' . $userId, true );
                	}
                	$data[] = array(
                		'id' => get_the_ID(),
                		'post' => $loop->post,
                		'tileUrl' => esc_url($image[0]),
                		'tileUrlExpanded' => esc_url($imageExpanded[0]),
                		'link' => get_the_permalink(),
                		'title' => get_the_title(),
                		'text' => wp_trim_words($trimexcerpt, $num_words = 18, $more = '...'),
                		'paidTileText' => $paidTileText,
                		'progressBar' => (int)$progressBar,
                		'nonce' => $nonce
                	);

        		endif;
            endwhile;
        endif;
        wp_reset_query();

        echo json_encode(
	    	array(
	    		'error' => false,
	    		'data' => $data,
                'count' => (int)$loop->post_count,
	    		'message' => 'User not logged in' 
	    	)
	    );

    }else{

    	// user is not logged in
    	echo json_encode(
	    	array(
	    		'error' => true,
	    		'message' => 'User not logged in' 
	    	)
	    );

    }       

    die(); 

}

add_action( "wp_ajax_recently_watched_api_post", "recently_watched_api_post" );
add_action( "wp_ajax_nopriv_recently_watched_api_post", "recently_watched_api_post" );