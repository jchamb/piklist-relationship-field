# Description
Piklist addon for creating a select/radio/checkbox field of options based on a post type. Lists show name and use id as the value. (Inspired by Image plugin of Jason Lane)

# Installation
Install [piklist](http://piklist.com). Add to plugins folder and activate normally.

# Usage
Use like normal piklist field
```php
piklist('field', array(
  'type' => 'relationship' //set fieldtype to relationship
  ,'scope' => 'post_meta'
  ,'field' => 'field_name'
  ,'label' => 'field_label'
  ,'description' => 'your description'
  ,'post_type' => 'post_type' // The post type you want to pull in for relationship
  ,'value' => '0'
  ,'choices' => array( // optional: add choices array to add additional values
    '0' => '-- Select --'
  )
  ,'options' => array( 
     'type' => 'select|radio|checkbox' // type of field defaults to select
    ,'list' => true // optional: will turn checkbox and radio into list
  )
  ,'attributes' => array( //standard piklist usage
      'class' => 'text'
  )
));
```