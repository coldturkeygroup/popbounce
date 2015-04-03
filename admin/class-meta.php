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
		$this->init_post_columns();
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


	/**
	 * Add custom column for posts and pages tables
	 */
	function init_post_columns()
	{
		$screens = [ 'posts', 'pages' ];

		foreach ( $screens as $screen ) {
			add_filter( 'manage_' . $screen . '_columns', [ $this, 'add_post_columns' ] );
			add_action( 'manage_' . $screen . '_custom_column', [ $this, 'render_post_columns' ], 10, 2 );
		}

		add_action( 'admin_footer', [ $this, 'post_columns_css' ] );
	}

	/**
	 * Add the defined custom column
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */
	function add_post_columns( $columns )
	{
		$columns['popbounce'] = 'popBounce';

		return $columns;
	}

	/**
	 * Render our custom column
	 *
	 * @param $column_name
	 * @param $id
	 */
	function render_post_columns( $column_name, $id )
	{
		$select_name      = $this->select_name;
		$popbounce_status = 'default';
		$popbounce_title  = 'Default';

		switch ( $column_name ) {
			case 'popbounce':
				$widget_id = get_post_meta( $id, $select_name, true );

				if ( $widget_id ) {
					$get_post_custom = get_post_meta( $id, $select_name, true );
					switch ( $get_post_custom ) {
						case 'on' :
							$popbounce_status = 'on';
							$popbounce_title  = 'On';
							break;
						case 'off' :
							$popbounce_status = 'off';
							$popbounce_title  = 'Off';
							break;
					}
				}

				echo '<div title="' . $popbounce_title . '" class="popbounce-status ' . $popbounce_status . '"></div>';
		}
	}

	/**
	 * Define the CSS for our custom column
	 */
	function post_columns_css()
	{
		$screen = get_current_screen();
		if ( $screen->base == 'edit' ) {
			wp_enqueue_style( 'popbounce-edit-page', plugins_url( '../css/min/edit-page.css', __FILE__ ) );
		}
	}

}

new Popbounce_Meta();