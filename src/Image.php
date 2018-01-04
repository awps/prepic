<?php

/** TODO:
 * Check if the image already exists and return the URL without processing it again.
 * Download external images
 * HTML helpers
 * Create file names depending on crop position.
 */

namespace PrePic;

class Image {

	/**
	 * @var bool|int|string This should keep image URL.
	 */
	protected $image_url = false;

	/**
	 * @var bool|int|string This should keep original image URL.
	 */
	protected $original_image_url = false;

	/**
	 * @var null|int Create a new image with this width.
	 */
	protected $width = null;

	/**
	 * * @var null|int Create a new image with this width.
	 */
	protected $height = null;

	/**
	 * @var array Crop the image
	 */
	protected $crop = array();

	/**
	 * @var bool Scale the image to the new dimensions, even if it is smaller.
	 */
	protected $scale = false;

	/**
	 * @var $wp_content_path
	 */
	protected $wp_content_path;

	/**
	 * @var $wp_content_url
	 */
	protected $wp_content_url;


	/**
	 * Image constructor.
	 *
	 * @param int|string $img_url_or_id The local attachment URL or ID.
	 */
	public function __construct( $img_url_or_id ) {
		$this->wp_content_path = wp_normalize_path( WP_CONTENT_DIR );
		$this->wp_content_url  = $this->normalizeHttp( WP_CONTENT_URL );

		if ( is_numeric( $img_url_or_id ) ) {
			$this->getImageUrlById( $img_url_or_id );
		}
		elseif ( ! empty( $img_url_or_id ) ) {
			$this->image_url = $this->normalizeHttp( $img_url_or_id );
		}

		$this->original_image_url = $this->image_url;
	}

	/**
	 * Get image URL by ID
	 *
	 * @param int $attachment_id
	 */
	protected function getImageUrlById( $attachment_id ) {
		$attachment_url = wp_get_attachment_url( $attachment_id );

		if ( ! empty( $attachment_url ) ) {
			$this->image_url = $this->normalizeHttp( $attachment_url );
		}
	}

	/*
	-------------------------------------------------------------------------------
	Setters
	-------------------------------------------------------------------------------
	*/

	/**
	 * Define the new image width.
	 *
	 * @param int $size
	 *
	 * @return \PrePic\Image
	 */
	public function width( $size ) {
		$size = absint( $size );

		if ( ! empty( $size ) ) {
			$this->width = $size;
		}

		return $this;
	}

	/**
	 * Define the new image height.
	 *
	 * @param int $size
	 *
	 * @return \PrePic\Image
	 */
	public function height( $size ) {
		$size = absint( $size );

		if ( ! empty( $size ) ) {
			$this->height = $size;
		}

		return $this;
	}

	/**
	 * Set both cropping positions to center.
	 */
	public function crop() {
		$this->setCrop( 'x', 'center' );
		$this->setCrop( 'y', 'center' );

		return $this;
	}

	/**
	 * Set the cropping position to top.
	 */
	public function cropTop() {
		$this->setCrop( 'y', 'top' );

		return $this;
	}

	/**
	 * Set the cropping position to bottom.
	 */
	public function cropBottom() {
		$this->setCrop( 'y', 'bottom' );

		return $this;
	}

	/**
	 * Set the cropping position to middle.
	 */
	public function cropMiddle() {
		$this->setCrop( 'y', 'center' );

		return $this;
	}

	/**
	 * Set the cropping position to left.
	 */
	public function cropLeft() {
		$this->setCrop( 'x', 'left' );

		return $this;
	}

	/**
	 * Set the cropping position to right.
	 */
	public function cropRight() {
		$this->setCrop( 'x', 'right' );

		return $this;
	}

	/**
	 * Set the cropping position to center.
	 */
	public function cropCenter() {
		$this->setCrop( 'x', 'center' );

		return $this;
	}

	/**
	 * Should we cancel the cropping?
	 */
	public function cancelCrop() {
		$this->crop = array();

		return $this;
	}

	/**
	 * Set the cropping position on a single axis.
	 *
	 * If the secondary axis is empty, automatically set to `center`.
	 *
	 * @param string $axis The axis. 'x' or 'y'.
	 * @param        $val
	 */
	protected function setCrop( $axis, $val ) {
		$this->crop[ $axis ] = $val;
		$this->setSecondaryAxis( $axis );
	}

	/**
	 * Set the secondary axis to center if it's value is empty.
	 *
	 * @param string $current_axis 'x' or 'y'
	 */
	protected function setSecondaryAxis( $current_axis ) {
		$axis = 'y';

		if ( 'y' === $current_axis ) {
			$axis = 'x';
		}

		if ( empty( $this->crop[ $axis ] ) ) {
			$this->crop[ $axis ] = 'center';
		}
	}

	/*
	-------------------------------------------------------------------------------
	Getters
	-------------------------------------------------------------------------------
	*/

	/**
	 * Get the new image URL.
	 *
	 * This also may return the original image URL or false if the original image URL is not valid.
	 *
	 * @return bool|string
	 */
	public function getUrl() {
		return $this->image_url;
	}

	/**
	 * Get the new image width.
	 *
	 * @return int|null
	 */
	public function getWidth() {
		return $this->width;
	}

	/**
	 * Get the new image height.
	 *
	 * @return int|null
	 */
	public function getHeight() {
		return $this->height;
	}

	/**
	 * Get the cropping positions.
	 *
	 * @return array
	 */
	public function getCropPositions() {
		return $this->crop;
	}

	/**
	 * Get the crop.
	 *
	 * @return array|bool
	 */
	public function getCrop() {
		if ( empty( $this->crop ) ) {
			return false;
		}

		// Return in the correct order and with 0 and 1 indexes required for
		// 'image_resize_dimensions' from wp-includes/media.php.
		return array(
			$this->crop['x'],
			$this->crop['y'],
		);
	}

	/**
	 * Get cropping position on x-axis
	 *
	 * @return bool|mixed
	 */
	public function getCropPositionX() {
		if ( empty( $this->crop['x'] ) ) {
			return false;
		}

		return $this->crop['x'];
	}

	/**
	 * Get cropping position on y-axis
	 *
	 * @return bool|mixed
	 */
	public function getCropPositionY() {
		if ( empty( $this->crop['y'] ) ) {
			return false;
		}

		return $this->crop['y'];
	}

	/**
	 * Determine if cropping is allowed.
	 *
	 * @return bool
	 */
	protected function canCrop() {
		return ! empty( $this->crop );
	}

	/*
	-------------------------------------------------------------------------------
	Display image
	-------------------------------------------------------------------------------
	*/
	public function getImg() {
		return sprintf(
			'<img src="%s">',
			esc_url( $this->image_url )
		);
	}

	/*
	-------------------------------------------------------------------------------
	File System
	-------------------------------------------------------------------------------
	*/
	protected function getFilename() {

	}

	protected function getRelativeImagePath() {
		return str_replace( $this->getUploadUrl(), '', $this->original_image_url );
	}

	protected function getImagePath() {
		return $this->getUploadPath() . $this->getRelativeImagePath();
	}

	/**
	 * Get the upload path for this site
	 *
	 * May return something like this on multi-site:
	 * D:/www/domains/example.com/wp-content/uploads/sites/7
	 * or
	 * like this on single site:
	 * D:/www/domains/example.com/wp-content/uploads
	 *
	 * Note: Using `wp_normalize_path` the backslash is replaced with a trailing slash.
	 * This is useful for Windows OS.
	 *
	 * @return string
	 */
	public function getUploadPath() {
		$upload_dir = wp_upload_dir();

		return wp_normalize_path( $upload_dir['basedir'] );
	}

	/**
	 * Get the upload URL for this site
	 *
	 * May return something like this on multi-site:
	 * http://example.com/wp-content/uploads/sites/7
	 * or
	 * like this on single site:
	 * http://example.com/wp-content/uploads
	 *
	 * @return string
	 */
	public function getUploadUrl() {
		$upload_dir = wp_upload_dir();

		return $this->normalizeHttp( $upload_dir['baseurl'] );
	}

	/**
	 * Get wp-content path and URL
	 *
	 * @return array
	 */
	public function getFileSystemRoutes() {
		return array(
			'original_image_url' => $this->original_image_url,
			'image_path'         => $this->getImagePath(),
			'relative_path'      => $this->getRelativeImagePath(),
			'path'               => $this->wp_content_path,
			'url'                => $this->wp_content_url,
		);
	}

	protected function normalizeHttp( $url ) {
		if ( is_ssl() ) {
			return $this->fixHttp( $url, 'https://' );
		}

		return $this->fixHttp( $url, 'http://' );
	}

	protected function fixHttp( $url, $replace_to = 'http://' ) {
		$check = 'https://';

		if ( 'https://' === $replace_to ) {
			$check = 'http://';
		}

		$relative = "//";

		if ( strncmp( $url, $check, mb_strlen( $check ) ) === 0 ) {
			$url = str_ireplace( $check, $replace_to, $url );
		}
		elseif ( strncmp( $url, $relative, mb_strlen( $relative ) ) === 0 ) {
			$url = str_ireplace( $relative, $replace_to, $url );
		}

		return $url;
	}


	/*
	-------------------------------------------------------------------------------
	Process image
	-------------------------------------------------------------------------------
	*/

	public function saveOLD() {
		try {


			$img_path = $this->getImagePath();

			// Check if img path exists, and is an image indeed.
			if ( ! file_exists( $img_path ) or ! getimagesize( $img_path ) ) {
				throw new Exception( 'Image file does not exist (or is not an image): ' . $img_path );
			}

			// Get image info.
			$info = pathinfo( $img_path );
			$ext  = $info['extension'];
			list( $orig_w, $orig_h ) = getimagesize( $img_path );

			// Get image size after cropping.
			$dims  = image_resize_dimensions( $orig_w, $orig_h, $this->getWidth(), $this->getHeight(), $this->getCrop() );
			$dst_w = $dims[4];
			$dst_h = $dims[5];

			// Return the original image only if it exactly fits the needed measures.
			if ( ! $dims || ( ( ( null === $this->getHeight() && $orig_w == $this->getWidth() ) xor ( null === $this->getWidth() && $orig_h == $this->getHeight() ) ) xor ( $this->getHeight() == $orig_h && $this->getWidth() == $orig_w ) ) ) {
				$img_url = $this->image_url;
				$dst_w   = $orig_w;
				$dst_h   = $orig_h;
			}
			else {
				// Use this to check if cropped image already exists, so we can return that instead.
				$suffix       = "{$dst_w}x{$dst_h}";
				$dst_rel_path = str_replace( '.' . $ext, '', $rel_path );
				$destfilename = "{$this->getUploadPath()}{$dst_rel_path}-{$suffix}.{$ext}";

//				if ( ! $dims || ( true == $this->getCrop() && false == $upscale && ( $dst_w < $this->getWidth() || $dst_h < $this->getHeight() ) ) ) {
//					// Can't resize, so return false saying that the action to do could not be processed as planned.
//					throw new Exception( 'Unable to resize image because image_resize_dimensions() failed' );
//				}
//				// Else check if cache exists.
//				elseif ( file_exists( $destfilename ) && getimagesize( $destfilename ) ) {
//					$img_url = "{$this->$this->getUploadUrl()}{$dst_rel_path}-{$suffix}.{$ext}";
//				}
//				// Else, we resize the image and return the new resized image url.
//				else {

				$editor = wp_get_image_editor( $img_path );

				if ( is_wp_error( $editor ) || is_wp_error( $editor->resize( $this->getWidth(), $this->getHeight(), $this->getCrop() ) ) ) {
					throw new Exception( 'Unable to get WP_Image_Editor: ' .
					                     $editor->get_error_message() . ' (is GD or ImageMagick installed?)' );
				}

				$resized_file = $editor->save( $destfilename );

				if ( ! is_wp_error( $resized_file ) ) {
					$resized_rel_path = str_replace( $this->getUploadPath(), '', $resized_file['path'] );
					$img_url          = $this->getUploadUrl() . $resized_rel_path;
				}
				else {
					throw new Exception( 'Unable to save resized image file: ' . $editor->get_error_message() );
				}

//				}
			}

			$this->image_url = $img_url;

		} catch ( Exception $ex ) {
			error_log( 'Aq_Resize.process() error: ' . $ex->getMessage() );
		}

		return $this;
	}

	public function save() {
		try {

			// Get the absolute image path
			$img_path = $this->getImagePath();

			// Check the image file exists
			if ( ! file_exists( $img_path ) ) {
				throw new Exception( 'The specified file does not exist or is not an image: ' . $img_path );
			}

			// Get image info.
			$info = pathinfo( $img_path );
			$ext  = $info['extension'];
			list( $orig_w, $orig_h ) = getimagesize( $img_path );

			// Get image size after cropping.
			$dims  = image_resize_dimensions( $orig_w, $orig_h, $this->getWidth(), $this->getHeight(), $this->getCrop() );
			$dst_w = $dims[4];
			$dst_h = $dims[5];

			// Return the original image only if it exactly fits the needed measures.
			if ( ! $dims || ( ( ( null === $this->getHeight() && $orig_w == $this->getWidth() ) xor ( null === $this->getWidth() && $orig_h == $this->getHeight() ) ) xor ( $this->getHeight() == $orig_h && $this->getWidth() == $orig_w ) ) ) {
				$img_url = $this->image_url;
				$dst_w   = $orig_w;
				$dst_h   = $orig_h;
			}
			else {
				$suffix = '';

				if ( $this->getCropPositionX() !== $this->getCropPositionY() ) {
					if ( $this->getCropPositionX() ) {
						$suffix .= substr( $this->getCropPositionX(), 0, 1 );
					}

					if ( $this->getCropPositionY() ) {
						$suffix .= substr( $this->getCropPositionY(), 0, 1 ) . '-';
					}
				}

				// Use this to check if cropped image already exists, so we can return that instead.
				$suffix       .= "{$dst_w}x{$dst_h}";
				$dst_rel_path = str_replace( '.' . $ext, '', $this->getRelativeImagePath() );
				$destfilename = "{$this->getUploadPath()}{$dst_rel_path}-{$suffix}.{$ext}";

//				if ( ! $dims || ( true == $this->getCrop() && false == $upscale && ( $dst_w < $this->getWidth() || $dst_h < $this->getHeight() ) ) ) {
//					// Can't resize, so return false saying that the action to do could not be processed as planned.
//					throw new Exception( 'Unable to resize image because image_resize_dimensions() failed' );
//				}
//				// Else check if cache exists.
//				elseif ( file_exists( $destfilename ) && getimagesize( $destfilename ) ) {
//					$img_url = "{$this->$this->getUploadUrl()}{$dst_rel_path}-{$suffix}.{$ext}";
//				}
//				// Else, we resize the image and return the new resized image url.
//				else {

				$editor = wp_get_image_editor( $img_path );

				if ( is_wp_error( $editor ) || is_wp_error( $editor->resize( $this->getWidth(), $this->getHeight(), $this->getCrop() ) ) ) {
					throw new Exception( 'Unable to get WP_Image_Editor: ' .
					                     $editor->get_error_message() . ' (is GD or ImageMagick installed?)' );
				}

				$resized_file = $editor->save( $destfilename );

				if ( ! is_wp_error( $resized_file ) ) {
					$resized_rel_path = str_replace( $this->getUploadPath(), '', $resized_file['path'] );
					$img_url          = $this->getUploadUrl() . $resized_rel_path;
				}
				else {
					throw new Exception( 'Unable to save resized image file: ' . $editor->get_error_message() );
				}

//				}
			}

			$this->image_url = $img_url;

		} catch ( Exception $ex ) {
			echo get_class( $this ) . ' error: ' . $ex->getMessage();
			error_log( get_class( $this ) . ' error: ' . $ex->getMessage() );

			$this->image_url = false;
		}

		return $this;
	}

}
