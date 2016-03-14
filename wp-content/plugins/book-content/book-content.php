<?php

defined( 'ABSPATH' ) OR exit(':)');

/*
Plugin Name: Book Content
Description: Creates book post type and a shortcode
Version: 1.0
Author: nikolai.koulakov@hotmail.com
*/

// Define Path to this file and plugin's directory contstants

if ( !defined('BOOK_CONTENT_FILE') ) {
		
	define('BOOK_CONTENT_FILE', __FILE__);
	
}

if ( !defined('BOOK_CONTENT_DIRECTORY') ) {
	
	define('BOOK_CONTENT_DIRECTORY', dirname(__FILE__));
	
}

class BookContent {

	public function __construct() {
	
		require( BOOK_CONTENT_DIRECTORY . '/Classes/BookPostType.class.php');
		add_shortcode('book', array($this, 'displayBook'));
		add_action("add_meta_boxes", array($this, 'add_custom_meta_box'));
		add_action('save_post', array($this, 'save_publisher_meta'), 1, 2); 
		
	}
	public function displayBook($atts) {
		$atts = shortcode_atts(
			array(
				'id' => '',
			), $atts, 'book' );
		if($atts['id'] && get_post_type($atts['id']) === BookPostType::postType()) { // check if the post with passed id exists and is in fact of book post type
			$title = get_the_title($atts['id']);
			$book_meta = get_post_meta($atts['id']);
			return $title .' published by '. $book_meta['publisher'][0];
		}	
	}

	public function publisher_meta_box_markup() {
    	global $post;
		echo '<input type="hidden" name="meta_noncename" id="meta_noncename" value="' . wp_create_nonce(BOOK_CONTENT_FILE) . '" />';
		// Get the location data if its already been entered
		$publisher = get_post_meta($post->ID, 'publisher', true);
		
		// Echo out the field
		echo '<input type="text" name="publisher" value="' . $publisher  . '"/>';
	}
	
	public function add_custom_meta_box() {
	    add_meta_box("publisher-meta-box", "Publisher", array($this, 'publisher_meta_box_markup'), "book", "side", "high", null);
	}
	
	public function save_publisher_meta($post) {
		
		// Nonce must be verified and user must be allowed to edit for save/update to take place
		if ( !wp_verify_nonce( $_POST['meta_noncename'], BOOK_CONTENT_FILE ) && $post->post_type === BookPostType::postType()) {
		    return $post->ID;	
		}
	
		if ( !current_user_can( 'edit_post', $post->ID )) {
			return $post->ID;
		}
	
		// Security check passed, let's save/update
		
		if( $post->post_type == 'revision' ) return; // Don't store custom data twice
		if(get_post_meta($post->ID,'publisher' , FALSE)) { // If the custom field already has a value
			update_post_meta($post->ID, 'publisher', $_POST['publisher']);
		} else { 
			add_post_meta($post->ID, 'publisher' , $_POST['publisher']); // If the custom field doesn't have a value
		}
		if(!$_POST['publisher']) delete_post_meta($post->ID, 'publisher'); // Delete if blank
	
	}

}

new BookContent();

?>