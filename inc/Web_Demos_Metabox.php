<?php
/*
* WordPress Web_Demos_MetaBox
* 
* Creates a Web_Demos_metabox to display for a custom post. You can overwrite any default arguments that you wish.
*
*	Hint: You can customize how top display the fields as much as you like. This example only displays a label and an input. 
*
* Example usage: Web_Demos_MetaBox::create(array(
*		'fields' => array(
*				array('name' => 'Test field', 'description' => 'Some description')
*		)
*	));
* 
* Required: Web_Demos_Utils.php
*/

class Web_Demos_MetaBox{
	
	public static function create(){
		return new Web_Demos_MetaBox();
	}

	protected $id, $title, $page, $context, $priority, $fields, $class;

	protected function __construct() {
		$args = array(
			"title" => 'Repository',
			"page" => 'demo',
			"class" => 'web_demos_metabox',
			"context" => 'side',
			"priority" => 'high',
			'fields' => array(
				array(
					'name' => 'URL',
					'type' => 'url'
				)
			)
		);

		$this->id = Web_Demos_Utils::generate_slug($args['title']);
		$this->title = $args['title'];
		$this->page = $args['page'];
		$this->context = $args['context'];
		$this->priority = $args['priority'];		
		$this->fields = $args['fields'];	
		$this->class = $args['class'];	

		add_action( 'save_post', array(&$this, 'save') ); 
		add_action( 'add_meta_boxes', array(&$this,'display') );
	}

	public function display(){
		add_meta_box( $this->id, $this->title, array(&$this, 'render'), $this->page, $this->context, $this->priority, array() );
	}

	/**
	* Save the Web_Demos_metabox
	*/
	public function save(){	
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return; 
		if( !current_user_can( 'edit_post' ) ) return;

		// you can add more allowed tags here
		$allowed_html_tags = array(
			'a' => array(
				'href' => array(),
				'title' => array()
			)
		);

		global $post; 
		foreach($this->fields as $field){
			$name = $field;
			if(is_array($field)){
				$name = $field['name'];
			}
			$id = Web_Demos_Utils::generate_slug($name);
			if( isset( $_POST[$id] ) )  {
				update_post_meta( $post->ID, $id, wp_kses( $_POST[$id], $allowed_html_tags ) );  
			}
		}
	}

	/**
	* Print the Web_Demos_metabox
	*/
	public function render($post, $args){
		echo '<div class="'.$this->class.'">';
		$values = get_post_custom( $post->ID );  
		foreach($this->fields as $field){
			$name = is_array($field) ? $field['name'] : $field;
			$desc = is_array($field) && isset($field['description']) ? '<p>'.$field['description'].'</p>' : '';
			$id = Web_Demos_Utils::generate_slug($name);
			$value = isset( $values[$id] ) ? esc_attr( $values[$id][0] ) : "";  
			$type = is_array($field) && isset($field['type']) ? $field['type'] : 'text';
			
			switch ($type) {
					case "select":
						echo "
						<label for=\"$id\">$name</label>  
						$desc
						<select name=\"$id\" id=\"$id\">
					";
					foreach ($field['options'] as $option) {
						echo "<option value=\"$option\"", $value == $option ? ' selected="selected"' : '', ">$option</option>";
					}
					echo '</select>';
						break;
					case "textarea":
							echo "
							<label for=\"$id\">$name</label>  
							$desc
							<textarea class='wp-editor-area' name=\"$id\" id=\"$id\" >$value</textarea><br>
						";
						break;
					// case text by default
					default:
						echo "
							<p><input type=\"$type\" name=\"$id\" id=\"$id\" value=\"$value\">$desc</p>
						";
						break;
			}			
		}
		echo '</div>';
	}
}
?>