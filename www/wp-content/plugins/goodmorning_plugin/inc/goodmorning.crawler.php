<?php

class GOODMORNING_CRAWLER {

	private $words_per_minute = 200;


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
		add_filter( 'cron_schedules', array($this, 'gm_crawl_api_custom_recurrence') );
	}

	/**
	 * action_dispatcher function.
	 *
	 * @access public
	 * @return void
	 */
	public function action_dispatcher(){

		add_action('gm_crawl_api', 	array($this, 'do_crawl'));
		add_action('gm_crawl_api_legacy', 	array($this, 'do_legacy_crawl'));
	}

	// Custom Cron Recurrences
	function gm_crawl_api_custom_recurrence( $schedules ) {
		$schedules['fiveminutes'] = array(
			'display' => __( 'Five Minutes', GM__LANG ),
			'interval' => 300,
		);


		$schedules['minute'] = array(
			'display' => __( 'Per Minute', GM__LANG ),
			'interval' => 60,
		);

		return $schedules;
	}

	/**
	 * do_crawl function.
	 *
	 * @access public
	 * @return void
	 */
	public function do_crawl(){

		if(!is_admin()){
			$response = $this->get_api();

			if(is_array($response['data'])){
				$this->insert_posts($response['data']);
			}
		}
	}


	public function do_legacy_crawl(){

		if(!is_admin()){
			$response = $this->get_api(10, get_option('br24_offset'));

			if(is_array($response['data'])){
				$this->insert_posts($response['data']);

				update_option('br24_offset', end(array_values($response['data']))['publicationDate']);

			}

		}
	}

	/**
	 * get_api function.
	 *
	 * @access public
	 * @param int $count (default: 10)
	 * @return void
	 */
	public function get_api($count = 10, $offset = NULL) {
		$url = "https://br24-backend-hackathon.br.de/api/v2/news";

		$query_args = array(
			"count"		=> $count
		);

		if($offset != NULL){
			$query_args['offset'] = $offset;
		}

		$url = add_query_arg($query_args, $url);

		$response = wp_remote_get($url);

		if( is_array($response) && $response['response']['code'] == 200) {
		  return json_decode($response['body'], true); // use the content
		}
	}

	/**
	 * insert_posts function.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	public function insert_posts($data){
		global $post;
		if(!is_array($data)){
			return false;
		}

		// Single out the data IDs from the API
		$data_ids = array();
		foreach($data as $d) {
			$data_ids[] = $d['id'];
		}

		// Check if we have that in the database
		$duplicate_query = new WP_Query(array(
			'post_type'	=> 'br24_news',
			'meta_query' => array(
				array(
					'key'     => '_br24_id',
					'value'   => $data_ids,
					'compare' => 'IN',
				),
			),
		));

		$data_ids_in_database = array();
		if($duplicate_query->have_posts()){ while($duplicate_query->have_posts()){
			$duplicate_query->the_post();

			$data_ids_in_database[] = get_post_meta($post->ID, "_br24_id", true);
		}}

		foreach($data as $d){

			if(in_array($d['id'], $data_ids_in_database)){
				continue;
			}

			// How long does it take to consume this news?
			$consume_duration = 0;

			$post = array(
				"post_type"		=> "br24_news",
				"post_title"	=> $d['title'],
				'post_status'	=> 'publish',
				"post_content"	=> preg_replace( "/\r|\n/", "", "<p>" . $d['teaserText'] . "<p>" . $d['text']),
				"post_date_gmt"	=> date("Y-m-d H:i:s", strtotime($d['publicationDate'])),
			);


			// Update the reading duration
			$consume_duration = $consume_duration + intval((str_word_count($post['post_content']) / $this->words_per_minute * 60));

			$post_id = wp_insert_post($post);

			if($post_id){
				wp_set_post_terms($post_id, $d['tags']);
				update_post_meta($post_id, "_br24_id", $d['id']);
				update_post_meta($post_id, "_subheadline", $d['headline']);

				// Do we have a video?
				if(is_array($d['video']) && count($d['video']) > 0){
					update_post_meta($post_id, "_video_data", $d['video'][0]);
					$consume_duration = intval($d['video'][0]['duration']) + $consume_duration;
				}

				$image_id = $this->sideload_image($d['images'][0]['url'], $post_id, $d['images'][0]['description']);
				set_post_thumbnail($post_id, $image_id);

				update_post_meta($post_id, "_consume_duration", $consume_duration);

			}
		}

	}

	public function sideload_image($url, $post_id, $desc){
		// Need to require these files
		if ( !function_exists('media_handle_upload') ) {
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			require_once(ABSPATH . "wp-admin" . '/includes/file.php');
			require_once(ABSPATH . "wp-admin" . '/includes/media.php');
		}

		$tmp = download_url( $url );
		if( is_wp_error( $tmp ) ){
			// download failed, handle error
		}

		$file_array = array();

		// Set variables for storage
		// fix file filename for query strings
		preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $url, $matches);
		$file_array['name'] = basename($matches[0]);
		$file_array['tmp_name'] = $tmp;

		// If error storing temporarily, unlink
		if ( is_wp_error( $tmp ) ) {
			@unlink($file_array['tmp_name']);
			$file_array['tmp_name'] = '';
		}

		// do the validation and storage stuff
		$id = media_handle_sideload( $file_array, $post_id, $desc );

		// If error storing permanently, unlink
		if ( is_wp_error($id) ) {
			@unlink($file_array['tmp_name']);
			return false;
		}

		return $id;
	}


}