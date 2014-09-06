<?php
	
final class Settings {
	
	/*
	* Add settings to media settings page
	*
	* @since 0.1
	*/
	public static function lazy_retina_admin_init(){
		register_setting(
			'media', 										// settings page
			'lazy_retina_options',							// option name
			array('Settings','lazy_retina_validate_options')// validation callback
		);
		
		add_settings_field(
			'lazy_retina_notify_lazy_retina_setting',		// id
			'Lazy Retina',									// setting title
			array('Settings','lazy_retina_setting_input'),	// display callback
			'media',										// settings page
			'default'										// settings section
		);
	
	}
	
	/*
	* Setting form
	*
	* @since 0.1
	*/
	public static function lazy_retina_setting_input() {
		$options = get_option( 'lazy_retina_options' );
		$value = $options['image_link'];
		?>
	 <p>
     	<label for="image_link_opt_1">
            <input id='image_link_opt_1' name='lazy_retina_options[image_link]'
            type='radio'<?php checked( $options['image_link'], 'no_link' ); ?> value='no_link' /> 
            Remove <u>only</u> default image link
        </label>
     </p>
      
     <p>
     	<label for="image_link_opt_2">
            <input id='image_link_opt_2' name='lazy_retina_options[image_link]'
            type='radio' <?php checked( $options['image_link'], 'link' ); ?> value='link' /> 
            Add default image link
        </label></p>
		<?php
	}
	
	/*
	* Setting validation
	*
	* @since 0.1
	*/
	public static function lazy_retina_validate_options( $input ) {
		$valid = array();
		$valid['image_link'] = sanitize_text_field( $input['image_link'] );
		
		if( $valid['image_link'] != $input['image_link'] ) {
			add_settings_error(
				'lazy_retina_image_link',			// setting title
				'lazy_retina_texterror',			// error ID
				'Es ist ein Fehler aufgetreten.',	// error message
				'error'								// type of message
			);		
		}
		
		return $valid;
	}
	
}
?>