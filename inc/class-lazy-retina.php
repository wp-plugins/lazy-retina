<?php

final class Lazy_Retina {
	// static instance variable
	public static $instance;
	
	/*
	* Register the hook-functions
	*
	* @since 0.1
	*/
	public function __construct() {	
		// save himself in instance variable
		self::$instance = $this;
	
		/* Go home */
		/* Thanks to Sergej Müller */
		if ( is_feed() OR is_admin() OR (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) OR (defined('DOING_CRON') && DOING_CRON) OR (defined('DOING_AJAX') && DOING_AJAX) OR (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) ) {
			return;
		}

		add_action( 'init', array( $this, 'add_new_sizes' ) );
		add_action( 'wp_footer', array( $this, 'load_unveil' ) );
		add_filter( 'the_content', array( $this, 'update_images' ) );
		add_filter( 'post_thumbnail_html', array( $this, 'update_images' ) );
	}
	
	/*
	* Install hook: Register all options.
	*
	* @since 0.1
	*/ 
	public static function install() {
		add_option('lazy_retina_options', array('image_link'=>'link'), '', 'yes');
	}
	
	/*
	* Remove hook: Delete all options.
	*
	* @since 0.1
	*/
	public static function remove() {
		delete_option('lazy_retina_options');	
	}
	
	/*
	* Create an instance
	*
	* @since 0.1
	*/
	public static function create_instance()
	{
		new self();
	}
	
	/*
	* Add new image size for retina devices
	*
	* @since 0.1
	*/
	function add_new_sizes() {
		$image_sizes = get_intermediate_image_sizes();
		foreach( $image_sizes as $image_size ) {

			if ( $image_size == 'full' ) continue;
			$crop = ( $image_size == 'thumbnail' ) ? get_option('thumbnail_crop') : 0;

			if ( $image = $this->_get_image_size( $image_size ) )
				add_image_size( $image_size . '@2x', $image['width'] * 2, $image['height'] * 2, $image['crop'] );
			else 
				add_image_size( $image_size . '@2x', intval( get_option( $image_size . '_size_w' ) ) * 2, intval( get_option( $image_size . '_size_h' ) ) * 2, $crop );
		}
	}
	
	/*
	* Check if image size exist
	*
	* @since 0.1
	*/
	private function _get_image_size( $name ) {
		global $_wp_additional_image_sizes;
		return ( isset( $_wp_additional_image_sizes[ $name ] ) ) ? $_wp_additional_image_sizes[ $name ] : false;
	}
	
	/*
	* Replace images with retina lazy-versions
	*
	* @since 0.1
	*/	
	function update_images( $content ) {		
		if (in_array('no_link', get_option('lazy_retina_options'))) 
			$content = $this->attachment_image_link_remove ($content);
		
		/* no images avaible? */
		if ( strpos($content, '-image') === false ) return $content;
		
		preg_match_all("#<img(.*?)\/?>#", $content, $old_img);
		
		foreach($old_img[1] as $key=>$attr_array) {
			$attr = $this->_get_image_attributes($attr_array);
			
			$classes = explode( ' ', $attr['class'] );

			foreach ( $classes as $class_key => $value ) {
				if ( strstr( $value, 'wp-image-' ) )
					$image_id = substr($value, 9);
					
				if ( in_array( $value, array( 'size-medium', 'size-large', 'size-thumbnail') ) )
					$retina_size = str_replace('size-','',$classes[ $class_key ]) . '@2x';
			}
			
			// replace attr
			$attr['data-src'] = $attr['src'];
			
			$new_img[$key] = $this->create_image($image_id, $retina_size, $old_img[0][$key], $attr);

		}
		
		/* change old with new */
		foreach ( $new_img as $key=>$image ) $content = str_replace( $old_img[0][$key], $image, $content);
		return $content;
	}
	
	/*
	* Override an image for lazy load
	*
	* @since 0.1
	*/
	function override_image( $html, $post_thumbnail_id, $size, $extra_attr ) {
		$attr = $this->_get_image_attributes($html);
		$attr = array_merge($attr, $extra_attr);
		
		$image = wp_get_attachment_image_src($post_thumbnail_id, $size);
		$retina_image = wp_get_attachment_image_src($post_thumbnail_id, $size."@2x");
		
		// replace and do things
		$attr['data-src'] = $image[0];
		$attr['width'] = $image[1];
		$attr['height'] = $image[2];

		return $this->create_image($post_thumbnail_id, $size."@2x", $html, $attr);
	}
	
	/*
	* Create finished output image
	*
	* @since 0.1
	*/
	private function create_image($image_id, $retina_size, $old_img, $attr) {
		/* Empty .gif */
		$null = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
		
		$retina_img = wp_get_attachment_image_src($image_id, $retina_size);
		
		// replace attr
		$attr['class'] .= ' lazy_retina';
		$attr['src'] = $null;
		if ($retina_img && $retina_img[3]) $attr['data-src-retina'] = $retina_img[0];
		
		// form the image
		$image 	 = "<img";
		foreach ($attr as $attr_name => $attr_value)
		$image 	.= ' ' . $attr_name . '="'. $attr_value .'"';
		$image 	.= '/><noscript>'. $old_img .'</noscript>';
		
		return $image;
	}
	
	/*
	* Return an array of attributes of given string
	*
	* @since 0.1
	*/
	private function _get_image_attributes($attributes) {
		preg_match_all( "#(\w+)=['\"]{1}([^'\"]*)#", $attributes, $each_attr );
		$attr_array = array();
		foreach ( $each_attr[1] as $value => $name ) $attr_array[$name] = $each_attr[2][$value];
		return $attr_array;
	}

	/*
	* Load unveil.js if jQuery or Zepto is load
	*
	* @since 0.1
	*/
	function load_unveil() {
		global $wp_scripts;
		
		/* Check for jQuery or zepto */
		if ( !empty($wp_scripts) && ((bool) $wp_scripts->query('jquery') || (bool) $wp_scripts->query('zepto')) ) {
			wp_enqueue_script( 'unveil.js', plugins_url('../js/unveil.js', __FILE__),array('jquery'),'',true);
		}
	}
		
	/*
	* Remove default link from adding image setup
	*
	* @since 0.1
	*/
	function imagelink_setup() {
		$image_set = get_option( 'image_default_link_type' );
		if ($image_set !== 'none' && in_array('no_link', get_option('lazy_retina_options'))) update_option('image_default_link_type', 'none');
	}
	
	/*
	* Remove default link from images
	* @author Joe Foley
	*
	* @since 0.1
	*/
	function attachment_image_link_remove( $content ) {
		 $content = preg_replace(array('{<a(.*?)(wp-att|wp-content/uploads)[^>]*><img}','{ wp-image-[0-9]*" /></a>}'),array('<img','" />'),$content);
		 return $content;
	 }

}

/*
* Return a lazy-retina image for an image id
*
* @since 0.1
*/
function lazy_retina_image( $image_id, $size = 'thumbnail', $attr = array()) {
	if (!empty($image_id)) {
		return Lazy_Retina::$instance->override_image(wp_get_attachment_image($image_id, $size), $image_id, $size, $attr);
	}
}

?>