<?php
/**
 * @package mimimi_plugin
 */


class GOODMORNING_REST {
	private $namespace = 'goodmorning-news/1.0';

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
		add_action( 'rest_api_init', array($this, 'register_rest_routes') );
	}

	/**
	 * register_rest_routes function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_rest_routes(){
		register_rest_route( $this->namespace, '/list-news/', array(
			'methods'  => 'GET',
			'callback' => array($this, 'get_news'),
		) );
	}

	public function get_news(){
		if(!is_user_logged_in()){
			// The user is not logged in
			return $this->generic_news();
		} else {
			return $this->generic_news();
		}
	}

	private function generic_news(){

		$args = array(
			"posts_per_page"	=> 10,
			"post_type"			=> "br24_news"
		);

		$query = new WP_Query($args);

		$return_array = array();

		if($query->have_posts()){
			while($query->have_posts()){
				$query->the_post();

				$post_data = array(
					"title"			=> get_the_title(),
					"headline"		=> get_post_meta(get_the_ID(), "_subheadline", true),
					"datetime"		=> get_the_date("U", get_the_ID()),
					"content"		=> apply_filters("the_content", get_the_content()),
					"thumbnail"		=> NULL,
					"video"			=> array(
						"video_src"	=> NULL,
						"video_dur"	=> NULL,
					),
				);

				$thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'post-thumbnail' );
				if(is_array($thumb)){
					$post_data['thumbnail']	= $thumb[0];
				}

				$video_data = get_post_meta(get_the_id(), "_video_data");
				if(isset($video_data['src']) && $video_data['src'] != ""){
					$post_data['video']['video_src'];
					// This is arbirary code for the photo. They will never know! MUHAHAHAHAH!
				}

				$return_array[] = $post_data;
			}

			return $return_array;

		} else {
			return false;
		}

	}

}
