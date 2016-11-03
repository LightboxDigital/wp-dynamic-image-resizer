<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://lightboxdigital.co.uk
 * @since      1.0.0
 *
 * @package    Dynamic_Image_Resizer
 * @subpackage Dynamic_Image_Resizer/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Dynamic_Image_Resizer
 * @subpackage Dynamic_Image_Resizer/public
 * @author     Warren Bickley <warren@lightboxdigital.co.uk>
 */
class Dynamic_Image_Resizer_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_name       The name of the plugin.
	 * @param string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Disable responsive image widths as not yet support
	 *
	 * @since 1.0.0
	 */
	public function disable_max_srcset_image_width() {
		if ( apply_filters( 'dynamic_image_resizer_disable_response', true ) ) {
			add_filter( 'max_srcset_image_width', '__return_true' );
		}
	}

	/**
	 * Handle image downsize/resize requests.
	 *
	 * @since 1.0.0
	 *
	 * @param bool         $downsize Whether to short-circuit the image downsize. Default true.
	 * @param int          $id       Attachment ID for image.
	 * @param array|string $size     Size of image, either array or string. Default 'medium'.
	 * @param bool         $crop     Whether to crop the image.
	 */
	public function resize_image( $downsize = true, $id, $size, $crop = false ) {
		// Get metadata for attachment (generated sizes stored there).
		$meta = wp_get_attachment_metadata( $id );
		// Get all of the created image sizes (add_image_size()).
		$sizes = $this->get_sizes();

		// If size is not an array fall back onto default WP code.
		if ( ! is_array( $size ) ) {
			return false;
		}

		// If there is only one size set (width) then set to height too.
		if ( sizeof( $size ) === 1 ) {
			$size = array( $size[0], $size[0] );
		}

		// If the size array has 3 settings.
		if ( sizeof( $size ) >= 3 ) {
			// Overwrite the crop option.
			$crop = $size[2];
			// Remove the crop option.
			unset( $size[2] );
		}

		// Get the width height out of the size array.
		list( $width, $height ) = $size;

		// Build a size name for image & meta.
		$size = $width . 'x' . $height;

		// Add cropped to size.
		if ( $crop ) {
			$size .= '-cropped';
		}

		// Loop over the named sizes above (add_image_size()).
		foreach ( $sizes as $size_name => $size_atts ) {
			// If this size is a named size.
			if ( $width == $size_atts['width'] && $height == $size_atts['height'] ) {
				// Then use the sizes name instead.
				$size = $size_name;
			}
		}

		// If the meta already contains this size, fall back onto default WP
		// code to find it.
		if ( array_key_exists( $size, $meta['sizes'] ) ) {
			return false;
		}

		// This custom size doesn't exist, so generate the image.
		$intermediate = image_make_intermediate_size( get_attached_file( $id ), $width, $height, $crop );

		// If image creation failed for any reason, fall back onto default WP.
		if ( ! is_array( $intermediate ) ) {
			return false;
		}

		// Store the returned size metadata in the attachments metadata.
		$meta['sizes'][ $size ] = $intermediate;
		// Update metadata in database.
		wp_update_attachment_metadata( $id, $meta );

		// Further constrain the image if 'content_width' is narrower (media.php).
		list( $width, $height ) = image_constrain_size_for_editor( $intermediate['width'], $intermediate['height'], $size );

		// Get original attachment filename.
		$file_url = wp_get_attachment_url( $id );
		$file_base = wp_basename( $file_url );
		$src = str_replace( $file_base, $intermediate['file'], $file_url );

		return apply_filters( 'dynamic_image_resizer_output',
			// Return the expected array - 'true' is to declare this image is modified
			// (http://codex.wordpress.org/Function_Reference/wp_get_attachment_image_src).
			array( $src, $width, $height, true ),
			$downsize, $id, $size, $crop
		);
	}

	/**
	 * Get all the currently defined named image sizes
	 *
	 * @since  1.0.0
	 * @param  string $size If supplied, only return this size.
	 * @return array        Image sizes
	 */
	public function get_sizes( $size = '' ) {
		global $_wp_additional_image_sizes;
		$sizes = array();
		$get_intermediate_image_sizes = get_intermediate_image_sizes();

		// Create the full array with sizes and crop info.
		foreach ( $get_intermediate_image_sizes as $_size ) {
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
				$sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
				$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
				$sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array(
					'width'		=> $_wp_additional_image_sizes[ $_size ]['width'],
					'height'	=> $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'		=> $_wp_additional_image_sizes[ $_size ]['crop'],
				);
			}
		}

		// If $size argument is supplied, just return that size.
		if ( $size ) {
			if ( isset( $sizes[ $size ] ) ) {
				return $sizes[ $size ];
			} else {
				return false;
			}
		}

		return $sizes;
	}

}
