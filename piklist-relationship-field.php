<?php
/*
Plugin Name: Piklist Relationship Field Addon
Plugin URI: 
Version: v1.0
Author: Jake Chamberlain
Description: Create a select field of options based on a post type. (Inspired by Image plugin of Jason Lane)
Plugin Type: Piklist
 */

/*  This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// -------------------------------------------------------------------------
// Prevent direct access to this file
// -------------------------------------------------------------------------
if ( ! function_exists ( 'add_action' ) ) :
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
endif;



// Set the core file path
define( 'PIKLISTRELATIONSHIPFIELD_FILE_PATH', __FILE__  );
// Define the path to the plugin folder
define( 'PIKLISTRELATIONSHIPFIELD_DIR_NAME', basename( PIKLISTRELATIONSHIPFIELD_FILE_PATH ) );
// Define the URL to the plugin folder
define( 'PIKLISTRELATIONSHIPFIELD_URL', plugins_url( PIKLISTRELATIONSHIPFIELD_DIR_NAME ) );


add_filter( 'http_request_args', array( 'PiklistRelationshipField', 'disable_auto_update' ) );
add_action( 'admin_init', array( 'PiklistRelationshipField', 'check_dependencies' ) );
add_action( 'admin_enqueue_scripts', array( 'PiklistRelationshipField', 'enqueue_styles_and_scripts' ) );


class PiklistRelationshipField {

	/**
	 * Code to exclude this plugin from auto update checks
	 * Taken from http://markjaquith.wordpress.com/2009/12/14/excluding-your-plugin-or-theme-from-update-checks/
	 */
	
	function disable_auto_update ( $r, $url = null ) {
		if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) )
			return $r; // Not a plugin update request. Bail immediately.
		$plugins = unserialize( $r['body']['plugins'] );
		unset( $plugins->plugins[ plugin_basename( __FILE__ ) ] );
		unset( $plugins->active[ array_search( plugin_basename( __FILE__ ), $plugins->active ) ] );
		$r['body']['plugins'] = serialize( $plugins );
		return $r;
	}


	function check_dependencies () {
		include_once( 'includes/class-piklist-checker.php' );
		if ( ! piklist_checker::check( __FILE__ ) ) {
			return;
		}
	}


	public function enqueue_styles_and_scripts () {
		
		
	}


	/**
	 * display_field creates the relationship select for each field
	 *
	 * @public
	 *
	 * @returns nothing (echos output) or returns HTML if $return arg is true
	 */

	public static function display_field ( $args = null ) {

		extract( $args );

		$default_options = array(
			  'statuses' => array('publish')
			 ,'type' => 'select'
		);

		$options = array_merge( $default_options, $options );

		extract( $options );

		// Query the relationship.
		$relationship = new WP_Query(array(
			 'post_type' => $post_type
			,'post_status' => $statuses
		));

		// check if choices are set
		if(! $choices)
			$choices = array();

		// loop through our realtionship and push to the choices
		while($relationship->have_posts()) { 
			$relationship->next_post();
			$id = $relationship->post->ID;
			$name = get_the_title($id);
		
			$choices[$id] = $name;
		}	

		if ( $return )
			ob_start();
		?>


		<?php if($type === 'select'): ?>

		<select 
			id="<?php echo $field_id; ?>" 
			name="<?php echo $field_name; ?>"
			<?php echo $attributes; ?>
		>
			<?php foreach ($choices as $choice_value => $choice): ?>
				<option value="<?php echo $choice_value; ?>" <?php echo $value == $choice_value ? 'selected="selected"' : ''; ?>><?php echo $choice; ?></option>
			<?php endforeach; ?>

		</select>


		<?php elseif ($type === 'radio'): ?>

		<?php if ($list): ?>
  
		<<?php echo isset($list_type) ? $list_type : 'ul'; ?> class="piklist-field-list">

		<?php endif; ?>

		<?php
		$index = 0;
		$value = is_array($value) && count($value) == 1 && !empty($value[0]) ? $value[0] : $value;
			
		foreach ($choices as $_value => $choice): 
		?>

		<?php echo $list ? '<li>' : ''; ?>

		<label class="piklist-field-list-item <?php echo isset($attributes['class']) ? implode(' ', $attributes['class']) : null; ?>">

		<input 
		type="radio"
		id="<?php echo $field_id; ?>" 
		name="<?php echo $field_name; ?>"
		value="<?php echo $_value; ?>"
		<?php echo $value == $_value ? 'checked="checked"' : ''; ?>
		<?php echo $attributes; ?>
		/>

		<span>
		<?php echo $choice; ?>
		</span>

		</label>

		<?php 
			echo $list ? '</li>' : '';
			$index++;

		endforeach; ?>

		<?php if ($list): ?>

		</<?php echo isset($list_type) ? $list_type : 'ul'; ?>>

		<?php endif; ?>


		<?php elseif ($type === 'checkbox'): ?>


		<?php if ($list): ?>
  
		<<?php echo isset($list_type) ? $list_type : 'ul'; ?> class="piklist-field-list">

		<?php endif; ?>

		  <?php 
		    $values = array_keys($choices);
		    for ($index = 0; $index < count($choices); $index++):
		  ?>

		    <?php echo $list ? '<li>' : ''; ?>
		    
		      <label class="piklist-field-list-item <?php echo isset($attributes['class']) ? implode(' ', $attributes['class']) : null; ?>">
		  
		        <input 
		          type="checkbox"
		          id="<?php echo $field_id; ?>" 
		          name="<?php echo $field_name; ?>"
		          value="<?php echo $values[$index]; ?>"
		          <?php echo (!is_array($value) && $value == $values[$index]) || (is_array($value) && in_array($values[$index], $value)) ? 'checked="checked"' : ''; ?>
		          <?php echo $attributes; ?>
		        />

		        <span>
		          <?php echo $choices[$values[$index]]; ?>
		        </span>
		    
		      </label>
		  
		    <?php echo $list ? '</li>' : ''; ?>

		  <?php endfor; ?>
		  
		<?php if ($list): ?>

		</<?php echo isset($list_type) ? $list_type : 'ul'; ?>>

		<?php endif; ?>

		<?php endif; // ends type of field outputs?>


		<?php if ( '' != $field_desc ) {
			echo '<p class="description">' .$field_desc . '</p>';
		} ?>

		<?php

        if ( $return ) {
        	$output = trim( ob_get_contents() );
			ob_end_clean();
			return $output;
		} else {
			return;
		}

	}

}


/**
 * Provide a global function for easy access
 */

function piklist_relationship_field ( $custom_args = null ) {
	return PiklistRelationshipField::display_field( $custom_args );
}