<?php

/**
 * Class Popbounce_Admin_Options
 *
 * Class for rendering the admin options
 */
class Popbounce_Admin_Options {

	/**
	 * Constructor for the admin class
	 */
	function __construct()
	{
		add_action( 'admin_menu', [ $this, 'create_menu' ] );
		add_action( 'admin_init', [ $this, 'admin_init_options' ] );
	}

	/**
	 * Initialize the settings for the plugin
	 */
	function admin_init_options()
	{
		$plugin = plugin_basename( POPBOUNCE_FILE );
		add_filter( "plugin_action_links_$plugin", [ $this, 'settings_link' ] );
		$this->register_settings();
	}

	/**
	 * Add settings link on plugins page
	 *
	 * @param $links
	 *
	 * @return
	 */
	function settings_link( $links )
	{
		$settings_link = '<a href="options-general.php?page=' . POPBOUNCE_OPTION_KEY . '.php">Settings</a>';
		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Add options page to the menu for the plugin
	 */
	function create_menu()
	{
		add_options_page( 'Exit Intent Settings', 'Exit Intent', 'manage_options', POPBOUNCE_OPTION_KEY, [
			$this,
			'settings_page'
		] );
	}

	/**
	 * Register settings for the plugin
	 */
	function register_settings()
	{
		$arr = [
			// Tab 'Content'
			'_test_mode',
			'_status_default',
			'_title',
			'_content',
			'_footer',
			// Tab 'Options'
			'_full_page',
			'_aggressive_mode',
			'_autofire',
			'_hesitation',
			// Tab 'Styling'
			'_custom_css',
		];

		foreach ( $arr as $i ) {
			register_setting( POPBOUNCE_OPTION_KEY . '-settings-group', POPBOUNCE_OPTION_KEY . $i );
		}

		do_action( POPBOUNCE_OPTION_KEY . '_register_settings_after' );
	}

	/**
	 * Build and render the plugin settings page
	 */
	function settings_page()
	{
		$example = "<p>Paragraph</p>\n\n<form>\n<input type=\"email\" placeholder=\"you@email.com\">\n<input type=\"submit\" value=\"learn more\">\n<p class=\"form-notice\">*this is a fake form</p>\n</form>";
		?>
		<div class="wrap">
			<h1>Exit Intent Ad Settings</h1>

			<?php do_action( POPBOUNCE_OPTION_KEY . '_settings_page_tabs_link_after' ); ?>

			<form method="post" action="options.php">
				<?php settings_fields( POPBOUNCE_OPTION_KEY . '-settings-group' ); ?>
				<?php do_settings_sections( POPBOUNCE_OPTION_KEY . '-settings-group' ); ?>

				<div id="content">
					<h3>Content</h3>

					<table class="form-table">
						<tbody>
						<?php if ( current_user_can( 'install_plugins' ) ) { ?>
							<tr valign="top">
								<th scope="row">Test mode</th>
								<td>
									<input name="<?= POPBOUNCE_OPTION_KEY; ?>_test_mode" type="checkbox" value="1" <?php checked( '1', get_option( POPBOUNCE_OPTION_KEY . '_test_mode' ) ); ?> />
									<label>Check this option to enable "Aggressive Mode" <b>for admins</b>, regardless of the actual
										setting
										in the tab "Options".</label>
								</td>
							</tr>
						<?php } ?>
						<tr valign="top">
							<th scope="row">Active Pages</th>
							<td>
								<select class="select" name="<?= POPBOUNCE_OPTION_KEY; ?>_status_default">
									<option value="on"<?php if ( get_option( POPBOUNCE_OPTION_KEY . '_status_default' ) === 'on' ) {
										echo ' selected="selected"';
									} ?>>Always fire
									</option>
									<option value="on_posts"<?php if ( get_option( POPBOUNCE_OPTION_KEY . '_status_default' ) === 'on_posts' ) {
										echo ' selected="selected"';
									} ?>>Fire on posts
									</option>
									<option value="on_pages"<?php if ( get_option( POPBOUNCE_OPTION_KEY . '_status_default' ) === 'on_pages' ) {
										echo ' selected="selected"';
									} ?>>Fire on pages
									</option>
									<option value="on_posts_pages"<?php if ( get_option( POPBOUNCE_OPTION_KEY . '_status_default' ) === 'on_posts_pages' ) {
										echo ' selected="selected"';
									} ?>>Fire on posts and pages
									</option>
									<option value="off"<?php if ( get_option( POPBOUNCE_OPTION_KEY . '_status_default' ) === 'off' ) {
										echo ' selected="selected"';
									} ?>>Don't fire
									</option>
								</select>

								<p>Define if exit intent ads should be enabled on posts and/or pages by default. You can also override
									the
									default
									setting on every post and page individually.</p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Ad Title</th>
							<td>
								<input type="text" name="<?= POPBOUNCE_OPTION_KEY; ?>_title" placeholder="Get My Free Product!" value="<?= get_option( POPBOUNCE_OPTION_KEY . '_title' ); ?>" style="width:55%"><br><label>The
									title for your advertisement</label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Ad Content <span class="description thin"><br>Add code that should be displayed within the ad window.</span>
							</th>
							<td>
								<textarea rows="14" cols="70" name="<?= POPBOUNCE_OPTION_KEY; ?>_content"><?= get_option( POPBOUNCE_OPTION_KEY . '_content', $example ); ?></textarea>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Ad Footer</th>
							<td>
								<input type="text" name="<?= POPBOUNCE_OPTION_KEY; ?>_footer" placeholder="no, thanks" value="<?= get_option( POPBOUNCE_OPTION_KEY . '_footer' ); ?>" style="width:55%"><br><label>The
									text users will need to click on to dismiss your advertisement.</label>
							</td>
						</tr>
						</tbody>
					</table>

				</div>

				<div id="options">
					<h3>Options</h3>

					<table class="form-table">
						<tbody>
						<tr valign="top">
							<th scope="row">Full Screen Ad</th>
							<td>
								<input name="<?= POPBOUNCE_OPTION_KEY; ?>_full_page" type="checkbox" value="1" <?php checked( '1', get_option( POPBOUNCE_OPTION_KEY . '_full_page' ) ); ?> />
								<label>By default, the ad will show over only part of your website. By enabling this option, the ad will
									cover the whole page.</label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Aggressive mode</th>
							<td>
								<input name="<?= POPBOUNCE_OPTION_KEY; ?>_aggressive_mode" type="checkbox" value="1" <?php checked( '1', get_option( POPBOUNCE_OPTION_KEY . '_aggressive_mode' ) ); ?> />
								<label>By default, the ad will only fire once for each visitor. When the ad is displayed, a cookie is
									created
									to ensure a non obtrusive experience.<br><br>There are cases, however, when you may want to be more
									aggressive. An example use-case might be on your paid landing pages. If you enable aggressive, the
									ad can be fired any time the page is reloaded.</label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Self-acting fire (timer)</th>
							<td>
								<input type="number" name="<?= POPBOUNCE_OPTION_KEY; ?>_autofire" placeholder="milliseconds" value="<?= get_option( POPBOUNCE_OPTION_KEY . '_autofire' ); ?>"/><br><label>Automatically
									trigger the popup after a certain time period. Insert 0 to fire immediately when the page is loaded.
									Leave blank to not use this option.</label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Hesitation</th>
							<td>
								<input type="number" name="<?= POPBOUNCE_OPTION_KEY; ?>_hesitation" placeholder="milliseconds" value="<?= get_option( POPBOUNCE_OPTION_KEY . '_hesitation' ); ?>"/><br><label>By
									default, the exit intent ad will show immediately when the user's mouse leaves the window. You could
									instead configure it to wait a specified time in milliseconds before showing the ad. If the cursor
									re-enters
									the window before the time has passed, the ad will not appear. This can be used to provide a "grace
									period" for visitors instead of immediately presenting the ad.</label>
							</td>
						</tr>
						</tbody>
					</table>

				</div>

				<div id="styling">
					<h3>Styling</h3>

					<table class="form-table">
						<tbody>
						<tr valign="top">
							<th scope="row">Custom CSS <span class="description thin"><br>Add additional CSS. This should override any other stylesheets.</span>
							</th>
							<td>
								<textarea rows="14" cols="70" name="<?= POPBOUNCE_OPTION_KEY; ?>_custom_css" placeholder="selector { property: value; }"><?= get_option( POPBOUNCE_OPTION_KEY . '_custom_css' ); ?></textarea><br>
		            <span>
		              Example:<br>
		              .popbounce-modal .modal-title { background-color: #4ab471; }
		            </span>
							</td>
						</tr>
						</tbody>
					</table>

				</div>

				<?php do_action( POPBOUNCE_OPTION_KEY . '_settings_page_tabs_after' ); ?>

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

}

new Popbounce_Admin_Options();