<?php
/**
 * @package mimimi_plugin
 */


class GOODMORNING_ADMIN extends GOODMORNING_CORE {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct(){
		$this->filter_dispatcher();
		$this->action_dispatcher();
	}

	/**
	 * filter_dispatcher function.
	 *
	 * @access public
	 * @return void
	 */
	public function filter_dispatcher(){
		parent::filter_dispatcher();
	}

	/**
	 * action_dispatcher function.
	 *
	 * @access public
	 * @return void
	 */
	public function action_dispatcher(){
		parent::action_dispatcher();

		/* Fire our meta box setup function on the post editor screen. */
		add_action( 'load-post.php', 						array( $this, 'post_meta_boxes_setup' ) );
		add_action( 'load-post-new.php', 					array( $this, 'post_meta_boxes_setup' ) );

		/* Enqueue needed scripts */
		add_action(	'admin_enqueue_scripts', 				array( $this, 'enqueue_scripts' ) );

	}

	/**
	 * enqueue_scripts function.
	 *
	 * @access public
	 * @return void
	 */
	public function enqueue_scripts(){
		global $pagenow, $post_type, $hook_suffix;
		$screen = get_current_screen();

		wp_register_script( 'mimimi_admin', GM__PLUGIN_URL . 'js/admin.min.js', array("jquery", "jquery-ui-sortable"), NULL, true);

		if( (($pagenow === "post.php" || $pagenow === "post-new.php") && ($post_type === "br24_news"))){

			// Only load the scripts when we ACTUALLY need them.
			wp_enqueue_script( 'mimimi_admin' );
			wp_localize_script( 'mimimi_admin', 'WP_API_Settings', array( 'root' => esc_url_raw( rest_url() ), 'nonce' => wp_create_nonce( 'wp_rest' ) ) );

		}

		$scripts_are_needed_in = array(
			'edit-tags.php',
		);

		if( in_array($hook_suffix, $scripts_are_needed_in) ){ // Make sure our scripts are only loaded, when we actually need them
   			wp_enqueue_script( 'mimimi_admin' );
   			wp_enqueue_media();
        }

		wp_register_style( 'mimimi_admin_style', GM__PLUGIN_URL . '/admin.css', NULL, 1, 'all');
		wp_enqueue_style( 'mimimi_admin_style' );

		wp_localize_script('mimimi_admin', 'fapsrv', array(
			'choose_image'				=> __("Choose Image", GM__LANG),
			'select_image'				=> __("Select Image", GM__LANG),
		));
	}


	// META BOXES STUFF

	/**
	 * post_meta_boxes_setup function.
	 *
	 * @access public
	 * @return void
	 */
	public function post_meta_boxes_setup() {
		/* Add meta boxes on the 'add_meta_boxes' hook. */
		add_action( 'add_meta_boxes', 		array( $this, 'add_post_meta_boxes' ) );
		add_action( 'save_post', 			array( $this, 'box_save'), 10, 2 );
	}

	/**
	 * add_post_meta_boxes function.
	 *
	 * @access public
	 * @return void
	 */
	public function add_post_meta_boxes() {
		global $post;

		$meta_box_templates = new GOODMORNING_Metaboxes();

		add_meta_box(
			'subheadline',										// Unique ID
			__( 'Subheadline', GM__LANG ), 					// Title
			array( $meta_box_templates, 'subheadline' ),		// Callback function
			'br24_news',										// Admin page (or post type)
			'normal',											// Context
			'high'												// Priority
		);


		add_meta_box(
			'video_meta',										// Unique ID
			__( 'Video', GM__LANG ), 					// Title
			array( $meta_box_templates, 'video_meta' ),		// Callback function
			'br24_news',										// Admin page (or post type)
			'side',											// Context
			'default'											// Priority
		);


		add_meta_box(
			'news_meta',										// Unique ID
			__( 'News', GM__LANG ), 					// Title
			array( $meta_box_templates, 'news_meta' ),		// Callback function
			'br24_news',										// Admin page (or post type)
			'side',											// Context
			'default'											// Priority
		);


		add_meta_box(
			'vote_box',										// Unique ID
			__( 'Vote', GM__LANG ), 					// Title
			array( $meta_box_templates, 'vote_box' ),		// Callback function
			'br24_news',										// Admin page (or post type)
			'side',											// Context
			'default'											// Priority
		);

	}

	/**
	 * List the post meta needed to be saved and forward it to the needed function.
	 *
	 * @access public
	 * @param mixed $post_id
	 * @param mixed $post
	 * @return void
	 */
	public function box_save( $post_id, $post ) {
		/*
		 *  $this->save_post_meta($post_id, $post, 'lh_data_nonce', 'post_value_name', '_meta_value_name');
		 */

		 $this->save_post_meta($post_id, $post, 'lh_data_nonce', 'subheadline', '_subheadline');

	}

	/**
	 * Actually save the post meta.
	 *
	 * @access private
	 * @param mixed $post_id
	 * @param mixed $post
	 * @param mixed $nonce_name
	 * @param mixed $post_value
	 * @param mixed $meta_key
	 * @return void
	 */
	private function save_post_meta( $post_id, $post, $nonce_name, $post_value, $meta_key ) {

		/* Verify the nonce before proceeding. */
		if ( !isset( $_POST[$nonce_name] ) || !wp_verify_nonce( $_POST[$nonce_name], basename( GM__PLUGIN_DIR ) ) )
			return $post_id;

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;

		/* Get the posted data and sanitize it for use as an HTML class. */
		if(isset($_POST[$post_value])){
			$new_meta_value = ($_POST[$post_value]);
		} else {
			$new_meta_value = false;
		}

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		/* If a new meta value was added and there was no previous value, add it. */
		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
		elseif ( $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta_key, $new_meta_value );

		/* If there is no new meta value but an old value exists, delete it. */
		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta_key, $meta_value );
	}
}