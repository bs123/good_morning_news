<?php


class GOODMORNING_Metaboxes {

	/**
	 * subheadline function.
	 *
	 * @access public
	 * @param mixed $object
	 * @param mixed $box
	 * @return void
	 */
	public function subheadline($object, $box){
		wp_nonce_field( basename( MIMIMI__PLUGIN_DIR ), 'lh_data_nonce' );
		$subheadline = get_post_meta($object->ID, '_subheadline', true);

		?>
			<input type="text" class="widefat" name="subheadline" id="news-subheadline" value="<?php echo $subheadline; ?>">
		<?php
	}

	/**
	 * video_meta function.
	 *
	 * @access public
	 * @param mixed $object
	 * @param mixed $box
	 * @return void
	 */
	public function video_meta($object, $box){
		wp_nonce_field( basename( MIMIMI__PLUGIN_DIR ), 'lh_data_nonce' );
		$video_data = (array) get_post_meta($object->ID, '_video_data', true);

		?>
			<p>
				<label for="video_url">Video URL</label>
				<input type="text" class="widefat" name="video_data[url]" id="video_url" value="<?php echo $video_data['url']; ?>">
			</p>
			<p>
				<label for="video_duration">Video Duration (in seconds)</label>
				<input type="text" class="widefat" name="video_data[duration]" id="video_duration" value="<?php echo intval($video_data['duration']); ?>">
			</p>
		<?php
	}

	/**
	 * news_meta function.
	 *
	 * @access public
	 * @param mixed $object
	 * @param mixed $box
	 * @return void
	 */
	public function news_meta($object, $box){
		wp_nonce_field( basename( MIMIMI__PLUGIN_DIR ), 'lh_data_nonce' );
		$id = get_post_meta($object->ID, '_br24_id', true);
		$consume_duration = get_post_meta($object->ID, '_consume_duration', true);

		?>
			<p>
				<b>BR24 ID</b><br />
				<?php echo $id; ?>
			</p>
			<p>
				<b>Consume Duration</b><br />
				<?php echo intval($consume_duration); ?> seconds
			</p>
		<?php
	}
}