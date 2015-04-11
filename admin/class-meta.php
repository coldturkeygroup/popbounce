<?php

/**
 * Class Popbounce_Meta
 *
 * Class for rendering popBounce meta boxes
 */
class Popbounce_Meta {

	private $select_name = 'popbounce_status';

	/**
	 * Constructor for the meta class
	 */
	public function __construct()
	{
		$this->init_meta_boxes();
	}

	/**
	 * Initialize meta boxes on posts & pages editor
	 */
	public function init_meta_boxes()
	{
		add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
		add_action( 'save_post', [ $this, 'save' ] );
	}

	/**
	 * Define where the meta box should appear & render
	 */
	public function add_meta_box()
	{
		$screens = [ 'post', 'page' ];

		foreach ( $screens as $screen ) {
			add_meta_box(
				'meta-box-popbounce',
				'Exit Intent Ad',
				[ $this, 'meta_box' ],
				$screen,
				'side',
				'low'
			);
		}
	}

	/**
	 * Build the meta box content
	 *
	 * @param $post
	 */
	public function meta_box( $post )
	{
		$select_name        = $this->select_name;
		$meta_element_class = get_post_meta( $post->ID, $select_name, true );
		wp_nonce_field( 'popbounce_nonce', 'popbounce_nonce' );
		?>

		<p style="margin-top:0"><label for="<?= $select_name; ?>">Show exit intent ad on
				this <?= get_current_screen()->post_type; ?>?</label></p>
		<p>
			<select class="select" name="<?= $select_name; ?>" id="<?= $select_name; ?>">
				<option value="default" <?php selected( $meta_element_class, 'default' ); ?>>Default</option>
				<option value="on" <?php selected( $meta_element_class, 'on' ); ?>>On</option>
				<option value="off" <?php selected( $meta_element_class, 'off' ); ?>>Off</option>
			</select>
		</p>
	<?php }

	/**
	 * Save the data from the meta box
	 *
	 * @param $post_id
	 */
	public function save( $post_id )
	{
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		// nonce validation
		if ( ! isset( $_POST['popbounce_nonce'] ) || ! wp_verify_nonce( $_POST['popbounce_nonce'], 'popbounce_nonce' ) )
			return;

		$select_name = $this->select_name;
		if ( isset( $_POST[ $select_name ] ) )
			update_post_meta( $post_id, $select_name, esc_attr( $_POST[ $select_name ] ) );
	}
}

new Popbounce_Meta();