<?php
/**
 * @package mimimi_plugin
 */


class GOODMORNING_REST {
	private $namespace = 'goodmorning-news/1.0';
	private $matches = array();

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

		register_rest_route( $this->namespace, '/upvote/(?P<id>[\d]+)', array(
			'methods'  => 'GET',
			'callback' => array($this, 'upvote'),
			'permission_callback' => function () {
				return true;
			}
		) );

		register_rest_route( $this->namespace, '/downvote/(?P<id>[\d]+)', array(
			'methods'  => 'GET',
			'callback' => array($this, 'downvote'),
			'permission_callback' => function () {
				return true;
			}
		) );

		register_rest_route( $this->namespace, '/read/(?P<id>[\d]+)', array(
			'methods'  => 'GET',
			'callback' => array($this, 'read'),
			'permission_callback' => function () {
				return true;
			}
		) );

	}

	public function get_news(){
		if(!is_user_logged_in()){
			// The user is not logged in
			return $this->generic_news();
		} else {
			return $this->specialized_news();
		}
	}

	private function generic_news(){

		$args = array(
			"posts_per_page"	=> 20,
			"post_type"			=> "br24_news"
		);

		$query = new WP_Query($args);

		$return_array = array();

		if($query->have_posts()){
			while($query->have_posts()){
				$query->the_post();

				$return_array[] = $this->format_post();
			}

			return $return_array;

		} else {
			return false;
		}

	}

	public function format_post(){
		$post_data = array(
			"id"			=> get_the_ID(),
			"title"			=> get_the_title(),
			"headline"		=> get_post_meta(get_the_ID(), "_subheadline", true),
			"date"			=> get_the_date("d.m.Y H:i \U\h\\r", get_the_ID()),
			"datetime"		=> get_the_date("U", get_the_ID()),
			"content"		=> apply_filters("the_content", get_the_content()),
			"consume_dur"	=> intval(get_post_meta(get_the_ID(), "_consume_duration", true)),
			"thumbnail"		=> NULL,
			"match"			=> $this->matches[get_the_ID()],
			"video"			=> array(
				"video_src"	=> NULL,
				"video_dur"	=> NULL,
			),
		);

		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'post-thumbnail' );
		if(is_array($thumb)){
			$post_data['thumbnail']	= $thumb[0];
		}

		$video_data = (array) get_post_meta(get_the_id(), "_video_data", true);
		if(isset($video_data['url']) && $video_data['url'] != ""){
			$post_data['video']['video_src'] = $video_data['url'];
			$post_data['video']['video_dur'] = $video_data['duration'];
			// This is arbirary code for the photo. They will never know! MUHAHAHAHAH!
		}

		return $post_data;
	}

	private function specialized_news(){
		global $wpdb;
		$user_positive_topics = (array) get_user_meta(get_current_user_id(), "_br24_positive", true);
		$user_negative_topics = (array) get_user_meta(get_current_user_id(), "_br24_negative", true);

		$pos_topics = implode(", ", $user_positive_topics);
		$neg_topics = implode(", ", $user_negative_topics);
		$date_offset = date("Y-m-d H:i:s", time() - 60 * 60 * 6);

		$term_dist = "
				SELECT p.ID, SUM(tr.term_taxonomy_id = tp.term_taxonomy_id) as term_similarity
				FROM {$wpdb->prefix}posts as p
					RIGHT JOIN {$wpdb->prefix}term_relationships as tr on tr.object_id = p.ID
					RIGHT JOIN {$wpdb->prefix}term_relationships as tp on tp.term_taxonomy_id IN ($pos_topics)
				WHERE p.post_type = 'br24_news' AND p.post_date > '$date_offset'
				GROUP BY p.ID
				ORDER BY term_similarity DESC
				LIMIT 0, 100
		";

		$results = 	$wpdb->get_results($term_dist);

		if(!is_array($results)){
			return false;
		}

		$post_ids = array();
		foreach($results as $r){
			$post_ids[] = $r->ID;
			$this->matches[$r->ID] = $r->term_similarity;
		}

		$args = array(
			"posts_per_page"	=> 20,
			"post_type"			=> "br24_news",
			"post__in"			=> array_diff($post_ids, (array) get_user_meta(get_current_user_id(), "_br24_read", true))
		);

		$query = new WP_Query($args);

		$return_array = array();

		if($query->have_posts()){
			while($query->have_posts()){
				$query->the_post();

				$return_array[] = $this->format_post();
			}

			return $return_array;

		} else {
			return false;
		}

		return $results;
	}

	/**
	 * upvote function.
	 *
	 * @access public
	 * @param mixed $request
	 * @return void
	 */
	public function upvote($request){
		if(is_user_logged_in()){
			$params = $request->get_params();
			$post_id = $params['id'];

			$tags = get_the_tags($post_id);

			$tag_ids = array();
			foreach($tags as $t){
				$tag_ids[] = $t->term_id;
			}

			// Add these IDs to the positive list
			$user_negative_topics = (array) get_user_meta(get_current_user_id(), "_br24_negative", true);
			$new_negatvie_topics = array_splice(array_unique(array_filter(array_diff($user_negative_topics, $tag_ids))), 0, 100);
			update_user_meta(get_current_user_id(), "_br24_negative", $new_negatvie_topics);

			// Remove these IDs from the negative list
			$user_positive_topics = (array) get_user_meta(get_current_user_id(), "_br24_positive", true);
			$new_positive_topics = array_splice(array_unique(array_filter(array_merge($user_positive_topics, $tag_ids))), 0, 100);
			update_user_meta(get_current_user_id(), "_br24_positive", $new_positive_topics);

			return array($new_positive_topics, $new_negatvie_topics);
		} else {
			return false;
		}
	}

	/**
	 * upvote function.
	 *
	 * @access public
	 * @param mixed $request
	 * @return void
	 */
	public function downvote($request){
		if(is_user_logged_in()){
			$params = $request->get_params();
			$post_id = $params['id'];

			$tags = get_the_tags($post_id);

			$tag_ids = array();
			foreach($tags as $t){
				$tag_ids[] = $t->term_id;
			}

			// Add these IDs to the Negative list
			$user_negative_topics = (array) get_user_meta(get_current_user_id(), "_br24_negative", true);
			$new_negatvie_topics = array_splice(array_unique(array_filter(array_merge($user_negative_topics, $tag_ids))), 0, 100);
			update_user_meta(get_current_user_id(), "_br24_negative", $new_negatvie_topics);

			// Remove these IDs from the positive list
			$user_positive_topics = (array) get_user_meta(get_current_user_id(), "_br24_positive", true);
			$new_positive_topics = array_splice(array_unique(array_filter(array_diff($user_positive_topics, $tag_ids))), 0, 100);
			update_user_meta(get_current_user_id(), "_br24_positive", $new_positive_topics);

			return array($new_positive_topics, $new_negatvie_topics);
		} else {
			return false;
		}
	}


	public function read($request){
		if(is_user_logged_in()){
			$params = $request->get_params();
			$post_id = $params['id'];

			$read_list = (array) get_user_meta(get_current_user_id(), "_br24_read", true);
			$read_list[] = $params['id'];
			$read_list = array_splice(array_unique(array_filter($read_list)), 0, 100);

			update_user_meta(get_current_user_id(), "_br24_read", $read_list);

			return $read_list;
		} else {
			return false;
		}
	}


}
