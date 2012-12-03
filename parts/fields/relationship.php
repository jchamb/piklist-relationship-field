<?php
if ( ! is_array( $options ) ) {
	$options = array();
}
piklist_relationship_field( 
	array(
		'value' => $value,
		'field_name' => piklist_form::get_field_name($field, $scope, $index),
		'field_id' => piklist_form::get_field_id($field, $scope, $index),
		'attributes' => $attributes,
		'options' => $options,
		'choices' => $choices,
		'post_type' => $post_type
	)
);
?>