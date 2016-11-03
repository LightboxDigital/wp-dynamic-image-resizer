<?php
/**
 * Rsize testing class.
 *
 * @since             1.0.0
 * @package           Dynamic_Image_Resizer
 */

/**
 * Main resize testing class.
 */
class ResizeTest extends WP_UnitTestCase {

	/**
	 * Setup WordPress unit testing factories.
	 */
	function __construct() {
		$this->post = new WP_UnitTest_Factory_For_Post( $this );
		$this->attachment = new WP_UnitTest_Factory_For_Attachment( $this );
	}

	/**
	 * 800px by 800px cropped.
	 */
	function test_dynamic_resize_800_800_1() {
		$resize = array( 800, 800, 1 );
		$expected = array( 800, 800 );
		$sizes = $this->dynamic_resize_helper( 'test-image-large.png', $resize );

		$this->assertEquals( $expected[0], $sizes['src'][1] );
		$this->assertEquals( $expected[1], $sizes['src'][2] );

		$this->assertEquals( $expected[0], $sizes['file'][0] );
		$this->assertEquals( $expected[1], $sizes['file'][1] );
	}

	/**
	 * 800px by 800px not cropped.
	 * Source image is 1600px by 1200px.
	 */
	function test_dynamic_resize_800_800() {
		$resize = array( 800, 800 );
		$expected = array( 800, 600, 0 );
		$sizes = $this->dynamic_resize_helper( 'test-image-large.png', $resize );

		$this->assertEquals( $expected[0], $sizes['src'][1] );
		$this->assertEquals( $expected[1], $sizes['src'][2] );

		$this->assertEquals( $expected[0], $sizes['file'][0] );
		$this->assertEquals( $expected[1], $sizes['file'][1] );
	}

	/**
	 * 800px by 800px missing parameters.
	 * Source image is 1600px by 1200px.
	 */
	function test_dynamic_resize_800() {
		$resize = array( 800 );
		$expected = array( 800, 600 );
		$sizes = $this->dynamic_resize_helper( 'test-image-large.png', $resize );

		$this->assertEquals( $expected[0], $sizes['src'][1] );
		$this->assertEquals( $expected[1], $sizes['src'][2] );

		$this->assertEquals( $expected[0], $sizes['file'][0] );
		$this->assertEquals( $expected[1], $sizes['file'][1] );
	}

	/**
	 * 800px by 800px crop and uncropped (collision detection).
	 * Source image is 1600px by 1200px.
	 */
	function test_dynamic_resize_800_800_1_0() {
		$resize = array( 800, 800, 1 );
		$expected = array( 800, 800 );
		$sizes = $this->dynamic_resize_helper( 'test-image-large.png', $resize );

		$this->assertEquals( $expected[0], $sizes['src'][1] );
		$this->assertEquals( $expected[1], $sizes['src'][2] );

		$this->assertEquals( $expected[0], $sizes['file'][0] );
		$this->assertEquals( $expected[1], $sizes['file'][1] );

		$resize = array( 800, 800, 0 );
		$expected = array( 800, 600 );
		$sizes = $this->dynamic_resize_helper( 'test-image-large.png', $resize );

		$this->assertEquals( $expected[0], $sizes['src'][1] );
		$this->assertEquals( $expected[1], $sizes['src'][2] );

		$this->assertEquals( $expected[0], $sizes['file'][0] );
		$this->assertEquals( $expected[1], $sizes['file'][1] );
	}

	/**
	 * Helper function for quickly resizing images identical to API approach.
	 *
	 * @param  string $file   Filename for test image.
	 * @param  array  $resize Standard width, height, crop array.
	 * @return array          Contains file key with actual sizes and src with meta response.
	 */
	protected function dynamic_resize_helper( $file, $resize ) {
		$image = $this->attachment->create_upload_object( dirname( dirname( __FILE__ ) ) . '/tests/images/' . $file );
		$output = wp_get_attachment_image_src( $image, $resize );

		list( $src, $w, $h, $crop ) = $output;

		$upload_dir = wp_upload_dir();

		$actual = getimagesize( str_replace( get_bloginfo( 'url' ), rtrim( ABSPATH, '/' ), $src ) );

		return array(
			'file'	=> $actual,
			'src'	=> $output,
		);
	}
}
