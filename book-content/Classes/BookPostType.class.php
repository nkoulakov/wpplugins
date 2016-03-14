<?php

defined( 'ABSPATH' ) OR exit(':)'); //no direct access

include( BOOK_CONTENT_DIRECTORY . '/WPHelper/PostType.class.php'); // include parent class

class BookPostType extends PostType {

	public static function postType() {
		return 'book';
	}
	
	public static function args() {
		$labels = array(
		    'name'               => 'Books',
		    'singular_name'      => 'Book',
		    'add_new'            => 'Add New',
		    'add_new_item'       => 'Add New Book',
		    'edit_item'          => 'Edit Book',
		    'new_item'           => 'New Book',
		    'all_items'          => 'All Books',
		    'view_item'          => 'View Book',
		    'search_items'       => 'Search Books',
		    'not_found'          => 'No Books found',
		    'not_found_in_trash' => 'No Books found in Trash',
		    'parent_item_colon'  => '',
		    'menu_name'          => 'Books'
		  );
		
		  $args = array(
		    'labels'             => $labels,
		    'public'             => true,
		    'publicly_queryable' => true,
		    'show_ui'            => true,
		    'show_in_menu'       => true,
		    'query_var'          => true,
		    'rewrite'            => array( 'slug' => 'book' ),
		    'capability_type'    => 'post',
		    'has_archive'        => true,
		    'hierarchical'       => false,
		    'menu_position'      => null,
		    'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'tag' ),
		  );

		return $args;
	}
	
	
}

new BookPostType();

?>