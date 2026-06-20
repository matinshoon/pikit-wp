<?php
/**
 * Admin settings page.
 *
 * @package Pikit_Booking_Widget
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plugin settings and admin UI.
 */
class Pikit_Booking_Settings {

	/**
	 * Settings page slug.
	 */
	const PAGE_SLUG = 'pikit-booking-widget';

	/**
	 * Admin screen hook suffix for the top-level menu page.
	 */
	const MENU_HOOK = 'toplevel_page_pikit-booking-widget';

	/**
	 * @var Pikit_Booking_Settings|null
	 */
	private static $instance = null;

	/**
	 * @return Pikit_Booking_Settings
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_menu_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
		add_filter( 'plugin_action_links_' . PIKIT_BOOKING_WIDGET_BASENAME, array( $this, 'plugin_action_links' ) );
	}

	/**
	 * Add quick link on the Plugins list screen.
	 *
	 * @param array $links Plugin action links.
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( admin_url( 'admin.php?page=' . self::PAGE_SLUG ) ),
			esc_html__( 'Settings', 'pikit-booking-widget' )
		);

		return array_merge( array( 'pikit-settings' => $settings_link ), $links );
	}

	/**
	 * Enqueue sidebar menu branding (icon sizing) across wp-admin.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 */
	public function enqueue_admin_menu_assets( $hook_suffix ) {
		$css_file = PIKIT_BOOKING_WIDGET_DIR . 'assets/css/admin-menu.css';
		$css_ver  = file_exists( $css_file ) ? (string) filemtime( $css_file ) : PIKIT_BOOKING_WIDGET_VERSION;

		wp_enqueue_style(
			'pikit-booking-admin-menu',
			PIKIT_BOOKING_WIDGET_URL . 'assets/css/admin-menu.css',
			array(),
			$css_ver
		);
	}

	/**
	 * Add body class on the plugin settings screen.
	 *
	 * @param string $classes Admin body classes.
	 * @return string
	 */
	public function admin_body_class( $classes ) {
		if ( $this->is_settings_screen() ) {
			$classes .= ' pikit-admin-page ';
		}
		return $classes;
	}

	/**
	 * Register settings.
	 */
	public function register_settings() {
		register_setting(
			'pikit_booking_settings_group',
			PIKIT_BOOKING_OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => array(),
			)
		);
	}

	/**
	 * Sanitize settings on save.
	 *
	 * @param array|string $input Raw input.
	 * @return array
	 */
	public function sanitize_settings( $input ) {
		if ( ! is_array( $input ) ) {
			return Pikit_Booking_Plugin::get_settings();
		}

		$sanitized = array(
			'enabled'           => ! empty( $input['enabled'] ),
			'installation_code' => Pikit_Booking_Plugin::sanitize_installation_code( $input['installation_code'] ?? '' ),
			'load_type'         => in_array( $input['load_type'] ?? '', array( 'SEO_FRIENDLY', 'FAST_LOAD' ), true )
				? $input['load_type']
				: 'SEO_FRIENDLY',
			'scope'             => in_array( $input['scope'] ?? '', array( 'all', 'front_page' ), true )
				? $input['scope']
				: 'all',
		);

		add_settings_error(
			'pikit_booking_settings_group',
			'pikit_settings_saved',
			__( 'Settings saved.', 'pikit-booking-widget' ),
			'success'
		);

		return $sanitized;
	}

	/**
	 * Register top-level admin menu page.
	 */
	public function register_menu() {
		add_menu_page(
			__( 'Pikit Booking', 'pikit-booking-widget' ),
			__( 'Pikit', 'pikit-booking-widget' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_settings_page' ),
			PIKIT_BOOKING_WIDGET_URL . 'assets/images/admin-menu-icon.svg',
			58
		);
	}

	/**
	 * Whether the current request is the plugin settings screen.
	 *
	 * @return bool
	 */
	private function is_settings_screen() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return isset( $_GET['page'] ) && self::PAGE_SLUG === $_GET['page'];
	}

	/**
	 * Enqueue admin styles and fonts on settings page.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		if ( self::MENU_HOOK !== $hook_suffix && ! $this->is_settings_screen() ) {
			return;
		}

		$css_file = PIKIT_BOOKING_WIDGET_DIR . 'assets/css/admin.css';
		$css_ver  = file_exists( $css_file ) ? (string) filemtime( $css_file ) : PIKIT_BOOKING_WIDGET_VERSION;

		wp_enqueue_style(
			'pikit-booking-fonts',
			'https://fonts.googleapis.com/css2?family=Lato:wght@400;500;600;700&display=swap',
			array(),
			PIKIT_BOOKING_WIDGET_VERSION
		);

		wp_enqueue_style(
			'pikit-booking-admin',
			PIKIT_BOOKING_WIDGET_URL . 'assets/css/admin.css',
			array( 'pikit-booking-fonts' ),
			$css_ver
		);
	}

	/**
	 * Render settings page.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings      = Pikit_Booking_Plugin::get_settings();
		$booking_url   = PIKIT_BOOKING_BASE_URL . '/' . $settings['installation_code'];
		$embed_snippet = Pikit_Booking_Plugin::build_embed_snippet(
			$settings['installation_code'],
			$settings['load_type']
		);
		$logo_url      = PIKIT_BOOKING_WIDGET_URL . 'assets/images/logos/logo-type.svg';
		$mark_url      = PIKIT_BOOKING_WIDGET_URL . 'assets/images/logos/logo.svg';

		?>
		<div class="pikit-booking-settings">
			<?php settings_errors( 'pikit_booking_settings_group' ); ?>

			<div class="pikit-admin-hero">
				<div class="pikit-admin-hero__inner">
					<img
						class="pikit-admin-hero__logo"
						src="<?php echo esc_url( $logo_url ); ?>"
						width="160"
						height="44"
						alt="<?php esc_attr_e( 'Pikit', 'pikit-booking-widget' ); ?>"
					/>
					<div class="pikit-admin-hero__content">
						<h2 class="pikit-admin-hero__title"><?php esc_html_e( 'WordPress integration', 'pikit-booking-widget' ); ?></h2>
						<p class="pikit-admin-hero__description">
							<?php esc_html_e( 'Embed online booking on your site so clients can schedule without leaving WordPress.', 'pikit-booking-widget' ); ?>
						</p>
					</div>
					<a
						class="pikit-btn pikit-btn--dark"
						href="<?php echo esc_url( PIKIT_DASHBOARD_SETUP_URL ); ?>"
						target="_blank"
						rel="noopener noreferrer"
					>
						<?php esc_html_e( 'Open Pikit dashboard', 'pikit-booking-widget' ); ?>
						<span class="pikit-btn__icon" aria-hidden="true">↗</span>
					</a>
				</div>
			</div>

			<div class="pikit-admin-layout">
				<aside class="pikit-callout pikit-callout--amber">
					<div class="pikit-callout__title">
						<span class="pikit-icon pikit-icon--info" aria-hidden="true"></span>
						<?php esc_html_e( 'Before you go live', 'pikit-booking-widget' ); ?>
					</div>
					<ol class="pikit-callout__list">
						<li><?php esc_html_e( 'Enable online booking in your Pikit dashboard.', 'pikit-booking-widget' ); ?></li>
						<li><?php esc_html_e( 'Set your website URL in Pikit Business settings (must match this WordPress site, HTTPS).', 'pikit-booking-widget' ); ?></li>
						<li><?php esc_html_e( 'Paste your installation code below.', 'pikit-booking-widget' ); ?></li>
						<li><?php esc_html_e( 'Add a Pikit Button in the block editor, Elementor, or WPBakery.', 'pikit-booking-widget' ); ?></li>
						<li><?php esc_html_e( 'Use Verify Connection in Pikit to confirm the widget is live.', 'pikit-booking-widget' ); ?></li>
					</ol>
					<a
						class="pikit-btn pikit-btn--outline pikit-btn--sm"
						href="<?php echo esc_url( PIKIT_DASHBOARD_SETUP_URL ); ?>"
						target="_blank"
						rel="noopener noreferrer"
					>
						<?php esc_html_e( 'Setup & Integration', 'pikit-booking-widget' ); ?>
					</a>
				</aside>

				<div class="pikit-admin-main">
					<form method="post" action="options.php" class="pikit-admin-form">
						<?php settings_fields( 'pikit_booking_settings_group' ); ?>

						<section class="pikit-card">
							<div class="pikit-card__header">
								<div class="pikit-card__icon pikit-card__icon--primary" aria-hidden="true">
									<span class="pikit-icon pikit-icon--globe"></span>
								</div>
								<div>
									<h2 class="pikit-card__title"><?php esc_html_e( 'Widget embed', 'pikit-booking-widget' ); ?></h2>
									<p class="pikit-card__subtitle">
										<?php esc_html_e( 'Loads the official Pikit booking widget on your website.', 'pikit-booking-widget' ); ?>
									</p>
								</div>
								<label class="pikit-switch" title="<?php esc_attr_e( 'Enable embed', 'pikit-booking-widget' ); ?>">
									<input
										type="checkbox"
										name="<?php echo esc_attr( PIKIT_BOOKING_OPTION_KEY ); ?>[enabled]"
										value="1"
										<?php checked( $settings['enabled'] ); ?>
									/>
									<span class="pikit-switch__track" aria-hidden="true"></span>
									<span class="screen-reader-text"><?php esc_html_e( 'Enable embed', 'pikit-booking-widget' ); ?></span>
								</label>
							</div>

							<div class="pikit-card__body pikit-field-grid">
								<div class="pikit-field">
									<label class="pikit-label" for="pikit-installation-code">
										<?php esc_html_e( 'Installation code', 'pikit-booking-widget' ); ?>
									</label>
									<input
										type="text"
										class="pikit-input"
										id="pikit-installation-code"
										name="<?php echo esc_attr( PIKIT_BOOKING_OPTION_KEY ); ?>[installation_code]"
										value="<?php echo esc_attr( $settings['installation_code'] ); ?>"
										placeholder="<?php esc_attr_e( 'e.g. mysalon', 'pikit-booking-widget' ); ?>"
										pattern="[a-zA-Z0-9]+"
									/>
									<p class="pikit-help">
										<?php esc_html_e( 'From Pikit → Business settings → Online booking → Setup & Integration.', 'pikit-booking-widget' ); ?>
									</p>
								</div>

								<div class="pikit-field">
									<label class="pikit-label" for="pikit-load-type">
										<?php esc_html_e( 'Load type', 'pikit-booking-widget' ); ?>
									</label>
									<select
										class="pikit-select"
										id="pikit-load-type"
										name="<?php echo esc_attr( PIKIT_BOOKING_OPTION_KEY ); ?>[load_type]"
									>
										<option value="SEO_FRIENDLY" <?php selected( $settings['load_type'], 'SEO_FRIENDLY' ); ?>>
											<?php esc_html_e( 'SEO friendly (recommended)', 'pikit-booking-widget' ); ?>
										</option>
										<option value="FAST_LOAD" <?php selected( $settings['load_type'], 'FAST_LOAD' ); ?>>
											<?php esc_html_e( 'Fast load', 'pikit-booking-widget' ); ?>
										</option>
									</select>
									<p class="pikit-help">
										<?php esc_html_e( 'SEO friendly waits for visitor interaction. Fast load prepares the widget sooner.', 'pikit-booking-widget' ); ?>
									</p>
								</div>

								<div class="pikit-field">
									<label class="pikit-label" for="pikit-scope">
										<?php esc_html_e( 'Embed scope', 'pikit-booking-widget' ); ?>
									</label>
									<select
										class="pikit-select"
										id="pikit-scope"
										name="<?php echo esc_attr( PIKIT_BOOKING_OPTION_KEY ); ?>[scope]"
									>
										<option value="all" <?php selected( $settings['scope'], 'all' ); ?>>
											<?php esc_html_e( 'All pages', 'pikit-booking-widget' ); ?>
										</option>
										<option value="front_page" <?php selected( $settings['scope'], 'front_page' ); ?>>
											<?php esc_html_e( 'Front page only', 'pikit-booking-widget' ); ?>
										</option>
									</select>
								</div>
							</div>

							<div class="pikit-card__footer">
								<button type="submit" name="submit" id="submit" class="pikit-btn pikit-btn--primary">
									<?php esc_html_e( 'Save settings', 'pikit-booking-widget' ); ?>
								</button>
							</div>
						</section>
					</form>

					<section class="pikit-card">
						<div class="pikit-card__header">
							<div class="pikit-card__icon pikit-card__icon--muted" aria-hidden="true">
								<span class="pikit-icon pikit-icon--link"></span>
							</div>
							<div>
								<h2 class="pikit-card__title"><?php esc_html_e( 'Direct booking link', 'pikit-booking-widget' ); ?></h2>
								<p class="pikit-card__subtitle">
									<?php esc_html_e( 'Share this link or use it as a fallback without embedding.', 'pikit-booking-widget' ); ?>
								</p>
							</div>
						</div>
						<div class="pikit-card__body">
							<?php if ( $settings['installation_code'] ) : ?>
								<p class="pikit-field-label"><?php esc_html_e( 'Booking link', 'pikit-booking-widget' ); ?></p>
								<a class="pikit-link" href="<?php echo esc_url( $booking_url ); ?>" target="_blank" rel="noopener noreferrer">
									<?php echo esc_html( $booking_url ); ?>
								</a>
								<p class="pikit-card__actions">
									<a class="pikit-btn pikit-btn--dark pikit-btn--sm" href="<?php echo esc_url( $booking_url ); ?>" target="_blank" rel="noopener noreferrer">
										<?php esc_html_e( 'Visit booking page', 'pikit-booking-widget' ); ?>
										<span class="pikit-btn__icon" aria-hidden="true">↗</span>
									</a>
								</p>
							<?php else : ?>
								<p class="pikit-help"><?php esc_html_e( 'Enter an installation code to see your booking link.', 'pikit-booking-widget' ); ?></p>
							<?php endif; ?>
						</div>
					</section>

					<section class="pikit-card">
						<div class="pikit-card__header">
							<div class="pikit-card__icon pikit-card__icon--muted" aria-hidden="true">
								<span class="pikit-icon pikit-icon--code"></span>
							</div>
							<div>
								<h2 class="pikit-card__title"><?php esc_html_e( 'Embed snippet', 'pikit-booking-widget' ); ?></h2>
								<p class="pikit-card__subtitle">
									<?php esc_html_e( 'This plugin injects the following in your site head when enabled.', 'pikit-booking-widget' ); ?>
								</p>
							</div>
						</div>
						<div class="pikit-card__body">
							<pre class="pikit-code-block"><?php echo esc_html( $embed_snippet ); ?></pre>
						</div>
					</section>

					<section class="pikit-card">
						<div class="pikit-card__header">
							<div class="pikit-card__icon pikit-card__icon--muted" aria-hidden="true">
								<span class="pikit-icon pikit-icon--button"></span>
							</div>
							<div>
								<h2 class="pikit-card__title"><?php esc_html_e( 'Add a Pikit Button', 'pikit-booking-widget' ); ?></h2>
								<p class="pikit-card__subtitle">
									<?php esc_html_e( 'Place a button that opens the booking widget when clicked.', 'pikit-booking-widget' ); ?>
								</p>
							</div>
						</div>
						<div class="pikit-card__body">
							<ul class="pikit-list">
								<li><?php esc_html_e( 'Block editor: search “Pikit Button” (Pikit category).', 'pikit-booking-widget' ); ?></li>
								<li><?php esc_html_e( 'Elementor: “Pikit Button” widget (Pikit category).', 'pikit-booking-widget' ); ?></li>
								<li><?php esc_html_e( 'WPBakery: “Pikit Button” element (Pikit category).', 'pikit-booking-widget' ); ?></li>
								<li><code class="pikit-code-inline">[pikit_book_button]</code></li>
							</ul>
						</div>
					</section>
				</div>
			</div>

			<footer class="pikit-admin-footer">
				<img
					class="pikit-admin-footer__logo"
					src="<?php echo esc_url( $mark_url ); ?>"
					width="24"
					height="19"
					alt=""
				/>
				<span>
					<?php
					printf(
						/* translators: %s: Pikit website URL */
						wp_kses_post( __( 'Booking powered by <a href="%s" target="_blank" rel="noopener noreferrer">Pikit</a>', 'pikit-booking-widget' ) ),
						esc_url( 'https://pikit.io' )
					);
					?>
				</span>
			</footer>
		</div>
		<?php
	}
}
