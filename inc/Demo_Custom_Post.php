<?php
/*
* Custom Post
* 
* Creates a custom post for a WordPress theme. You can overwrite any default arguments that you wish.
*
* Example usage: Demo_Custom_Post::create(array('name' => 'Portfolio'))
* 
* Required: Web_Demos_Utils.php
*/

class Demo_Custom_Post{
	
	static public function create($args = array('name' => 'Demo')){
		return new Demo_Custom_Post($args);
	}

	protected $name, $supports, $slug;

	protected function __construct($args) {
		$args['labels'] = array_merge(
			array(
				'name' => $args['name'],
				'singular_name' => $args['name'],
				'add_new' => 'Add New',
				'add_new_item' => 'Add New '. $args['name'],
				'edit_item' => 'Edit '. $args['name'],
				'new_item' => 'New '. $args['name'],
				'all_items' => 'All '. Web_Demos_Utils::pluralize($args['name']),
				'view_item' => 'View '. $args['name'],
				'search_items' => 'Search '. $args['name'],
				'not_found' =>  'No '. Web_Demos_Utils::pluralize($args['name']).' found',
				'not_found_in_trash' => 'No '. Web_Demos_Utils::pluralize($args['name']).' found in Trash', 
				'parent_item_colon' => '',
				'menu_name' => Web_Demos_Utils::pluralize($args['name'])
			),
			isset($args['labels']) ? $args['labels'] : array()
		);
		$args['supports'] = array_merge(
			array('title', 'editor', 'author', 'thumbnail', 'custom-fields'), 
			isset($args['supports']) ? $args['supports'] : array()
		);

		$args = array_merge( array(
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => array( 'slug' => Web_Demos_Utils::generate_slug($args['name']) ),
			'capability_type' => 'post',
			'has_archive' => Web_Demos_Utils::generate_slug($args['name']), 
			'hierarchical' => false,
			'menu_position' => null
		), $args ); 

		$this->name = $args['name'];
		$this->supports = $args['supports'];
		$this->slug = Web_Demos_Utils::generate_slug($args['name']);
		
		register_post_type( $this->slug, $args );
		
		$this->add_custom_taxonomy();

		add_action( 'right_now_content_table_end' , array(&$this, 'add_to_dashboard') );
		
		// WP 3.8 hack
		add_filter('dashboard_glance_items', array(&$this, 'add_to_dashboard_hack'));
	}
			
	function add_to_dashboard_hack($elements) {
			$post_type = get_post_type_object($this->slug);
			$num_posts = wp_count_posts( $post_type->name );
			$num = number_format_i18n( $num_posts->publish );
			$text = _n( Web_Demos_Utils::pluralize($post_type->labels->singular_name), Web_Demos_Utils::pluralize($post_type->labels->name) , intval( $num_posts->publish ) );
			if ( current_user_can( 'edit_posts' ) ) {
				$text = "<a href='edit.php?post_type=$post_type->name'>$num $text</a>";
			}
			echo '<li class="post-count">' . $text . '</li>';

	    return $elements;
	}

	public function add_custom_taxonomy(){
		$name = 'Feature';	
		$slug = 'features';
		$name_plural = 'Features';

		// Add new taxonomy, NOT hierarchical (like tags)
		$labels = array(
			'name'                       => _x( $name_plural, 'taxonomy general name' ),
			'singular_name'              => _x( $name, 'taxonomy singular name' ),
			'search_items'               => __( 'Search '.$name_plural ),
			'popular_items'              => __( 'Popular '.$name_plural ),
			'all_items'                  => __( 'All '.$name_plural ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit '.$name ),
			'update_item'                => __( 'Update '.$name ),
			'add_new_item'               => __( 'Add New '.$name ),
			'new_item_name'              => __( 'New '.$name.' Name' ),
			'separate_items_with_commas' => __( 'Separate '.strtolower($name_plural).' with commas' ),
			'add_or_remove_items'        => __( 'Add or remove '.strtolower($name_plural) ),
			'choose_from_most_used'      => __( 'Choose from the most used '.strtolower($name_plural) ),
			'not_found'                  => __( 'No '.strtolower($name_plural).' found.' ),
			'menu_name'                  => __( $name_plural ),
		);

		$args = array(
			'hierarchical'          => false,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => $slug ),
		);

		register_taxonomy( $slug, $this->slug, $args );
	}

	public function add_to_dashboard(){
		$post_type = get_post_type_object($this->slug);
		$num_posts = wp_count_posts( $post_type->name );
		$num = number_format_i18n( $num_posts->publish );
		$text = _n( Web_Demos_Utils::pluralize($post_type->labels->singular_name), Web_Demos_Utils::pluralize($post_type->labels->name) , intval( $num_posts->publish ) );
		if ( current_user_can( 'edit_posts' ) ) {
			$num = "<a href='edit.php?post_type=$post_type->name'>$num</a>";
			$text = "<a href='edit.php?post_type=$post_type->name'>$text</a>";
		}
		echo '<tr><td class="first b b-' . $post_type->name . '">' . $num . '</td>';
		echo '<td class="t ' . $post_type->name . '">' . $text . '</td></tr>';
	}
}
?>