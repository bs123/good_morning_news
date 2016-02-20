<?php
/**
 * @package mimimi_plugin
 */


class GOODMORNING_CORE {
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
	}

	/**
	 * action_dispatcher function.
	 *
	 * @access public
	 * @return void
	 */
	public function action_dispatcher(){
		add_action( 'init',					array($this, 'register_post_types'), 1 );
		add_action( 'init',					array($this, 'register_taxonomies'), 1 );
		add_action( 'init', 				array($this, 'load_textdomain') );

	}

	/**
	 * register_post_types function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_post_types(){

		// The NEWS custom post type
		$labels = array(
			'name'               => _x( 'News', 'post type general name', GM__LANG ),
			'singular_name'      => _x( 'News', 'post type singular name', GM__LANG ),
			'menu_name'          => _x( 'News', 'admin menu', GM__LANG ),
			'name_admin_bar'     => _x( 'News', 'add new on admin bar', GM__LANG ),
			'add_new'            => _x( 'Add New', 'news', GM__LANG ),
			'add_new_item'       => __( 'Add New News', GM__LANG ),
			'new_item'           => __( 'New News', GM__LANG ),
			'edit_item'          => __( 'Edit News', GM__LANG ),
			'view_item'          => __( 'View News', GM__LANG ),
			'all_items'          => __( 'All News', GM__LANG ),
			'search_items'       => __( 'Search News', GM__LANG ),
			'parent_item_colon'  => __( 'Parent News:', GM__LANG ),
			'not_found'          => __( 'No News found.', GM__LANG ),
			'not_found_in_trash' => __( 'No News found in trash.', GM__LANG )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'news' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'taxonomies'		=> array( 'post_tag' ),
			'supports'           => array( 'title', 'thumbnail', 'editor', 'revisions' )
		);

		register_post_type( 'br24_news', $args );

	}

	/**
	 * register_taxonomies function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_taxonomies(){
		global $wpdb;

	}


	/**
	 * load_textdomain function.
	 *
	 * @access public
	 * @return void
	 */
	public function load_textdomain(){
		load_plugin_textdomain( GM__LANG, false, dirname( plugin_basename( GM__PLUGIN_FILE ) ) . '/lang' );
	}

	/**
	 * onInstall function.
	 *
	 * @access public
	 * @return void
	 */
	public function onInstall(){
		if ( ! wp_next_scheduled( 'gm_crawl_api' ) ) {
		  wp_schedule_event( time(), 'minute', 'gm_crawl_api' );
		  wp_schedule_event( time(), 'minute', 'gm_crawl_api_legacy' );
		}
	}

	public function onUninstall(){
		wp_clear_scheduled_hook('gm_crawl_api');
		delete_option('br24_offset');
	}


}
