<?php
defined( 'ABSPATH' ) || die( 'Direct access is not allowed.' );

use \Directorist\Helper;
use \Directorist\Directorist_All_Authors;

if ( ! class_exists( 'ATBDP_Ajax_Handler' ) ) :

	/**
	 * Class ATBDP_Ajax_Handler.
	 * It handles all ajax requests from our plugin
	 */
	/**
	 * Class ATBDP_Ajax_Handler
	 */
	class ATBDP_Ajax_Handler {


		/**
		 * It registers our ajax functions to our ajax hooks
		 */
		public function __construct() {
			add_action( 'wp_ajax_atbdp_social_info_handler', array( $this, 'atbdp_social_info_handler' ) );
			add_action( 'wp_ajax_nopriv_atbdp_social_info_handler', array( $this, 'atbdp_social_info_handler' ) );

			add_action( 'wp_ajax_remove_listing', array( $this, 'remove_listing' ) ); // delete a listing
			add_action( 'wp_ajax_update_user_profile', array( $this, 'update_user_profile' ) );
			add_action( 'wp_ajax_update_user_preferences', array( $this, 'update_user_preferences' ) );

			/*CHECKOUT RELATED STUFF*/
			add_action( 'wp_ajax_atbdp_format_total_amount', array( 'ATBDP_Checkout', 'ajax_atbdp_format_total_amount' ) );
			add_action( 'wp_ajax_nopriv_atbdp_format_total_amount', array( 'ATBDP_Checkout', 'ajax_atbdp_format_total_amount' ) );

			/*REPORT ABUSE*/
			add_action( 'wp_ajax_atbdp_public_report_abuse', array( $this, 'ajax_callback_report_abuse' ) );
			add_action( 'wp_ajax_nopriv_atbdp_public_report_abuse', array( $this, 'ajax_callback_report_abuse' ) );

			/*CONTACT FORM*/
			add_action( 'wp_ajax_atbdp_public_send_contact_email', array( $this, 'ajax_callback_send_contact_email' ) );
			add_action( 'wp_ajax_nopriv_atbdp_public_send_contact_email', array( $this, 'ajax_callback_send_contact_email' ) );

			/*
			 * stuff for handling add to favourites
			 */
			add_action( 'wp_ajax_atbdp_public_add_remove_favorites', array( $this, 'atbdp_public_add_remove_favorites' ) );
			add_action( 'wp_ajax_nopriv_atbdp_public_add_remove_favorites', array( $this, 'atbdp_public_add_remove_favorites' ) );

			// location & category child term
			add_action( 'wp_ajax_bdas_public_dropdown_terms', array( $this, 'bdas_dropdown_terms' ) );
			add_action( 'wp_ajax_nopriv_bdas_public_dropdown_terms', array( $this, 'bdas_dropdown_terms' ) );
			// custom field search
			add_action( 'wp_ajax_atbdp_custom_fields_search', array( $this, 'custom_field_search' ), 10, 1 );
			add_action( 'wp_ajax_nopriv_atbdp_custom_fields_search', array( $this, 'custom_field_search' ), 10, 1 );
			add_action( 'wp_ajax_atbdp-favourites-all-listing', array( $this, 'atbdp_public_add_remove_favorites_all' ) );
			add_action( 'wp_ajax_nopriv_atbdp-favourites-all-listing', array( $this, 'atbdp_public_add_remove_favorites_all' ) );
			add_action( 'wp_ajax_atbdp_post_attachment_upload', array( $this, 'atbdp_post_attachment_upload' ) );
			add_action( 'wp_ajax_nopriv_atbdp_post_attachment_upload', array( $this, 'atbdp_post_attachment_upload' ) );
			// login
			add_action( 'wp_ajax_ajaxlogin', array( $this, 'atbdp_ajax_login' ) );
			add_action( 'wp_ajax_nopriv_ajaxlogin', array( $this, 'atbdp_ajax_login' ) );

			/**
			 * @todo need to remove code as it has no uses
			 */
			add_action( 'wp_ajax_atbdp_ajax_quick_login', array( $this, 'atbdp_quick_ajax_login' ) );
			add_action( 'wp_ajax_nopriv_atbdp_ajax_quick_login', array( $this, 'atbdp_quick_ajax_login' ) );

			// regenerate pages
			add_action( 'wp_ajax_atbdp_upgrade_old_pages', array( $this, 'upgrade_old_pages' ) );
			// default listing type
			add_action( 'wp_ajax_atbdp_listing_default_type', array( $this, 'atbdp_listing_default_type' ) );
			// listing type slug edit
			add_action( 'wp_ajax_directorist_type_slug_change', array( $this, 'directorist_type_slug_change' ) );

			// Guset Reception
			add_action( 'wp_ajax_atbdp_guest_reception', array( $this, 'guest_reception' ) );
			add_action( 'wp_ajax_nopriv_atbdp_guest_reception', array( $this, 'guest_reception' ) );

			// custom field
			// add_action( 'wp_ajax_atbdp_custom_fields_listings', array( $this, 'ajax_callback_custom_fields' ), 10, 2 );
			// add_action( 'wp_ajax_nopriv_atbdp_custom_fields_listings', array( $this, 'ajax_callback_custom_fields' ), 10, 2 );

			add_action( 'wp_ajax_directorist_load_category_custom_fields', array( $this, 'ajax_callback_custom_fields' ), 10, 2 );
			add_action( 'wp_ajax_nopriv_directorist_load_category_custom_fields', array( $this, 'ajax_callback_custom_fields' ), 10, 2 );

			// add_action('wp_ajax_atbdp_custom_fields_listings_front_selected',        array($this, 'ajax_callback_custom_fields'), 10, 2);
			// add_action('wp_ajax_nopriv_atbdp_custom_fields_listings_front_selected', array($this, 'ajax_callback_custom_fields'), 10, 2);
			// add_action('wp_ajax_atbdp_custom_fields_listings',                       array($this, 'ajax_callback_custom_fields'), 10, 2 );
			// add_action('wp_ajax_atbdp_custom_fields_listings_selected',              array($this, 'ajax_callback_custom_fields'), 10, 2 );

			add_action( 'wp_ajax_atbdp_listing_types_form', array( $this, 'atbdp_listing_types_form' ) );
			add_action( 'wp_ajax_nopriv_atbdp_listing_types_form', array( $this, 'atbdp_listing_types_form' ) );

			add_action( 'wp_ajax_directorist_category_custom_field_search', array( $this, 'category_custom_field_search' ) );
			add_action( 'wp_ajax_nopriv_directorist_category_custom_field_search', array( $this, 'category_custom_field_search' ) );

			// dashboard become author
			add_action( 'wp_ajax_atbdp_become_author', array( $this, 'atbdp_become_author' ) );
			add_action( 'wp_ajax_atbdp_user_type_approved', array( $this, 'atbdp_user_type_approved' ) );
			add_action( 'wp_ajax_atbdp_user_type_deny', array( $this, 'atbdp_user_type_deny' ) );

			add_action( 'wp_ajax_directorist_prepare_listings_export_file', array( $this, 'handle_prepare_listings_export_file_request' ) );

			add_action( 'wp_ajax_directorist_ajax_quick_login', array( $this, 'directorist_quick_ajax_login' ) );
			add_action( 'wp_ajax_nopriv_directorist_ajax_quick_login', array( $this, 'directorist_quick_ajax_login' ) );

			// author sorting
			add_action( 'wp_ajax_directorist_author_alpha_sorting', array( $this, 'directorist_author_alpha_sorting' ) );
			add_action( 'wp_ajax_nopriv_directorist_author_alpha_sorting', array( $this, 'directorist_author_alpha_sorting' ) );

			// author paginate
			add_action( 'wp_ajax_directorist_author_pagination', array( $this, 'author_pagination' ) );
			add_action( 'wp_ajax_nopriv_directorist_author_pagination', array( $this, 'author_pagination' ) );

			// instant search
			add_action( 'wp_ajax_directorist_instant_search', array( $this, 'instant_search' ) );
			add_action( 'wp_ajax_nopriv_directorist_instant_search', array( $this, 'instant_search' ) );

			// user verification
			add_action('wp_ajax_directorist_send_confirmation_email', [$this, 'send_confirm_email'] );
			add_action('wp_ajax_nopriv_directorist_send_confirmation_email', [$this, 'send_confirm_email'] );

			// zipcode search
			add_action( 'wp_ajax_directorist_zipcode_search', array( $this, 'zipcode_search' ) );
			add_action( 'wp_ajax_nopriv_directorist_zipcode_search', array( $this, 'zipcode_search' ) );

			add_action( 'wp_ajax_directorist_generate_nonce', [ $this, 'handle_generate_nonce' ] );

			add_action( 'wp_ajax_directorist_taxonomy_pagination', [ $this, 'directorist_taxonomy_pagination' ] );
			add_action( 'wp_ajax_nopriv_directorist_taxonomy_pagination', [ $this, 'directorist_taxonomy_pagination' ] );

			add_action( 'wp_ajax_directorist_update_view_count', [ static::class, 'update_view_count' ] );
			add_action( 'wp_ajax_nopriv_directorist_update_view_count', [ static::class, 'update_view_count' ] );
		}

		public function directorist_taxonomy_pagination() {
			// Verify nonce for security
			if ( ! directorist_verify_nonce( 'nonce' ) ) {
				wp_send_json(
					array(
						'search_form' => __( 'Something went wrong, please try again.', 'directorist' ),
					)
				);
			}

			$page = isset($_REQUEST['page']) ? absint($_REQUEST['page']) : '';
			$atts = !empty( $_REQUEST['attrs'] ) && is_array($_REQUEST['attrs']) ? $_REQUEST['attrs'] : [];
			$type = is_array($atts) && isset($atts['type']) ? $atts['type'] : '';

			$taxonomy = new Directorist\Directorist_Listing_Taxonomy($atts, $type );
			$taxonomy->set_terms($page);

			wp_send_json_success(array('content' => $taxonomy->render_shortcode( $atts )));
		}

		public function send_confirm_email() {
			if ( ! check_ajax_referer( 'directorist_nonce', 'directorist_nonce', false ) ) {
				wp_send_json_error([
					'code' => 'invalid_nonce',
					'message'  => __( 'Invalid Nonce', 'directorist' )
				]);
				exit;
			}

			if ( ! directorist_is_email_verification_enabled() ) {
				wp_send_json_error([
					'code' => 'invalid_request',
					'message'  => __( 'Invalid Request', 'directorist' )
				]);
				exit;
			}

			$email = isset( $_REQUEST['user'] ) ? sanitize_email( wp_unslash( $_REQUEST['user'] ) ) : '';
			if ( ! is_email( $email ) ) {
				wp_send_json_error([
					'code' => 'invalid_email',
					'message'  => __( 'Invalid email address', 'directorist' )
				]);
				exit;
			}

			$user  = get_user_by( 'email', $email );
			if ( $user instanceof \WP_User && get_user_meta( $user->ID, 'directorist_user_email_unverified', true ) ) {
				ATBDP()->email->send_user_confirmation_email( $user );
			}

			$args = ATBDP_Permalink::get_signin_signup_page_link( array(
				'send_verification_email' => true
			) );

			wp_safe_redirect( $args );
			exit;
		}

		public function zipcode_search() {
			if ( ! directorist_verify_nonce( 'nonce' ) ) {
				wp_send_json(
					array(
						'search_form' => __( 'Something went wrong, please try again.', 'directorist' ),
					)
				);
			}
			$google_api = get_directorist_option( 'map_api_key' );
			$zipcode = ! empty( $_POST['zipcode'] ) ? sanitize_text_field( $_POST['zipcode'] ) : '';
			$url      = 'https://maps.googleapis.com/maps/api/place/textsearch/json?query=postcode+' . $zipcode . '&key=' . $google_api;
			$data     = wp_remote_get( $url );
			$response = wp_remote_retrieve_body( $data );
			$json     = $response ? json_decode( $response, true ) : array();
			$lat_long = ! empty( $json['results'][0]['geometry']['location'] ) ? directorist_clean( $json['results'][0]['geometry']['location'] ) : array();
			if( ! empty( $lat_long ) ) {
				wp_send_json( $lat_long );
			} else {
				wp_send_json_error(
					array(
						'error_message' => sprintf(
							__( '<div class="error_message">%s <p>%s</p></div>', 'directorist' ),
							directorist_icon('fas fa-info-circle', false), __( 'Please enter a valid zip code.', 'directorist' ) )
					)
				);
			}
		}

		public function instant_search() {
			if ( empty( $_POST['_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['_nonce'] ), 'bdas_ajax_nonce' ) ) { // @codingStandardsIgnoreLine.WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				wp_send_json(
					array(
						'search_result'  => esc_html__( 'Something went wrong, please try again.', 'directorist' ),
						'directory_type' => '',
						'view_as'        => '',
						'count'          => '',
					)
				);
			}

			$args = array();

			if ( ! empty( $_POST['data_atts'] ) ) {
				$args = directorist_clean( (array) wp_unslash( $_POST['data_atts'] ) );
			}

			if( ! empty( $args['_current_page'] ) && 'search_result' == $args['_current_page'] ) {
				$type = 'search_result';
			} else {
				$type = 'instant_search';
			}

			if ( ! empty( $args['ids'] ) && ! isset( $_REQUEST['ids'] ) ) {
				$_REQUEST['ids'] = $args['ids'];
				$_POST['ids']    = $args['ids'];
			}

			$listings = new Directorist\Directorist_Listings( $args, $type );

			ob_start();
			if( 'list' === $listings->view ) {
				$listings->render_list_view( $listings->post_ids() );
			} else {
				$listings->render_grid_view( $listings->post_ids() );
			}
			$render_listings = ob_get_clean();

			ob_start();
			$listings->archive_view_template();
			$archive_view 			= ob_get_clean();

			$display_listings_count = get_directorist_option( 'display_listings_count', true );
			$category_id 			= ! empty( $_POST['in_cat'] ) ? absint( $_POST['in_cat'] ) : 0;
			$category 				= get_term_by( 'id', $category_id, ATBDP_CATEGORY );
			$location_id			= ! empty( $_POST['in_loc'] ) ? absint( $_POST['in_loc'] ) : 0;
			$location 				= get_term_by( 'id', $location_id, ATBDP_LOCATION );

			wp_send_json(
				array(
					'search_result'  => $archive_view,
					'directory_type' => $listings->render_shortcode(),
					'view_as'        => $archive_view,
					'count'          => $listings->query_results->total,
					'header_title'   => $display_listings_count ? $listings->listings_header_title() : '',
					'category_name'	 => $category ? $category->name : '',
					'location_name'	 => $location ? $location->name : '',

					'render_listings' => $render_listings,
					'view' => $listings->view
				)
			);
		}

		// directorist_quick_ajax_login
		public function directorist_quick_ajax_login() {
			$nonce = '';

			if ( isset( $_POST['token'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_POST['token'] ) );
			} elseif ( isset( $_POST['directorist-quick-login-security'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_POST['directorist-quick-login-security'] ) );
			}

			if ( ! wp_verify_nonce( $nonce, 'directorist-quick-login-nonce' ) ) {
				wp_send_json(
					array(
						'loggedin' => false,
						'message'  => __( 'Invalid Nonce', 'directorist' ),
					)
				);
			}

			if ( is_user_logged_in() ) {
				wp_send_json(
					array(
						'loggedin' => true,
						'message'  => __( 'Your are already loggedin', 'directorist' ),
					)
				);
			}

			$username   = ! empty( $_POST['username'] ) ? sanitize_user( wp_unslash( $_POST['username'] ) ) : '';
			$password   = ! empty( $_POST['password'] ) ? $_POST['password'] : ''; // @codingStandardsIgnoreLine.WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$rememberme = ! empty( $_POST['rememberme'] ) ? boolval( $_POST['rememberme'] ) : false;

			$user = wp_signon( array(
				'user_login'    => $username,
				'user_password' => $password,
				'remember'      => $rememberme,
			) );

			if ( is_wp_error( $user ) ) {
				wp_send_json(
					array(
						'loggedin' => false,
						'message'  => __( 'Wrong username or password.', 'directorist' ),
					)
				);
			}

			wp_send_json(
				array(
					'loggedin' => true,
					'message'  => __( 'Login successful, redirecting...', 'directorist' ),
				)
			);
		}

		// directorist_author_alpha_sorting
		public function directorist_author_alpha_sorting() {
			if ( ! empty( $_POST['_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['_nonce'] ), 'directorist_author_sorting' ) ) {
				$authors = new Directorist_All_Authors();
				Helper::get_template( 'all-authors', array( 'authors' => $authors ) );
				wp_die();
			}
		}

		// directorist_author_pagination
		public function author_pagination() {
			$authors = new Directorist_All_Authors();
			$content = Helper::get_template_contents( 'all-authors', array( 'authors' => $authors ) );
			wp_send_json( $content );
		}

		// handle_prepare_listings_export_file_request
		public function handle_prepare_listings_export_file_request() {

			if ( ! directorist_verify_nonce() ) {
				$data['success'] = false;
				$data['message'] = __( 'Something is wrong! Please refresh and retry.', 'directorist' );

				return wp_send_json( $data );
			}

			$file = Directorist\Listings_Exporter::get_prepared_listings_export_file();

			wp_send_json( $file );
		}

		public function atbdp_user_type_deny() {
			if ( ! empty( $_POST['_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['_nonce'] ), 'atbdp_user_type_deny' ) ) { // @codingStandardsIgnoreLine.WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$user_id = ! empty( $_POST['userId'] ) ? absint( $_POST['userId'] ) : 0;

				update_user_meta( $user_id, '_user_type', 'general' );

				wp_send_json(
					array(
						'user_type' => __( 'User', 'directorist' ),
					)
				);
			}
		}

		public function atbdp_user_type_approved() {
			if ( ! empty( $_POST['_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['_nonce'] ), 'atbdp_user_type_approve' ) ) { // @codingStandardsIgnoreLine.WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$user_id = ! empty( $_POST['userId'] ) ? absint( $_POST['userId'] ) : 0;
				update_user_meta( $user_id, '_user_type', 'author' );
				wp_send_json(
					array(
						'user_type' => __( 'Author', 'directorist' ),
					)
				);
			}
		}

		public function atbdp_become_author() {
			if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'atbdp_become_author' ) ) { // @codingStandardsIgnoreLine.WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$user_id = ! empty( $_POST['userId'] ) ? absint( $_POST['userId'] ) : '';
				do_action( 'atbdp_become_author', $user_id );
				update_user_meta( $user_id, '_user_type', 'become_author' );
				wp_send_json( __( 'Sent successfully', 'directorist' ) );
			}
		}

		// atbdp_quick_ajax_login
		public function atbdp_quick_ajax_login() {
			if ( empty( $_POST['directorist-quick-login-security'] ) || ! wp_verify_nonce( wp_unslash( $_POST['directorist-quick-login-security'] ), 'directorist-quick-login-nonce' ) ) { // @codingStandardsIgnoreLine.WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				wp_send_json(
					array(
						'loggedin' => false,
						'message'  => __( 'Invalid request.', 'directorist' ),
					)
				);
			}

			if ( is_user_logged_in() ) {
				wp_send_json(
					array(
						'loggedin' => true,
						'message'  => __( 'Your are already loggedin', 'directorist' ),
					)
				);
			}

			$credentials = array(
				'user_login'    => ! empty( $_POST['username'] ) ? sanitize_user( wp_unslash( $_POST['username'] ) ) : '',
				'user_password' => ! empty( $_POST['password'] ) ? $_POST['password'] : '',  // @codingStandardsIgnoreLine.WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				'remember'      => ! empty( $_POST['rememberme'] ) ? boolval( $_POST['rememberme'] ) : false,
			);

			$logged_in_user = wp_signon( $credentials, false );

			if ( is_wp_error( $logged_in_user ) ) {
				wp_send_json(
					array(
						'loggedin' => false,
						'message'  => __( 'Wrong username or password.', 'directorist' ),
					)
				);

			}

			wp_set_current_user( $logged_in_user->ID );

			wp_send_json(
				array(
					'loggedin' => true,
					'message'  => __( 'Login successful, redirecting...', 'directorist' ),
				)
			);
		}

		// atbdp_listing_types_form
		public function atbdp_listing_types_form() {
			if ( ! directorist_verify_nonce( 'nonce' ) ) {
				wp_send_json(
					array(
						'search_form' => __( 'Something went wrong, please try again.', 'directorist' ),
					)
				);
			}

			$listing_type    = ! empty( $_POST['listing_type'] ) ? sanitize_key( $_POST['listing_type'] ) : '';
			$atts            = ! empty( $_POST['atts'] ) ? json_decode( base64_decode( wp_unslash( $_POST['atts'] ) ), true ) : array(); // @codingStandardsIgnoreLine.WordPress.Security.ValidatedSanitizedInput.InputNotSanitize
			$term            = get_term_by( 'slug', $listing_type, ATBDP_TYPE );
			$listing_type_id = ( $term ) ? $term->term_id : 0;
			$searchform      = new \Directorist\Directorist_Listing_Search_Form( 'search_form', $listing_type_id, $atts );
			$search_form     = Helper::get_template_contents( 'search-form-contents', array( 'searchform' => $searchform ) );
			wp_send_json(
				array(
					'search_form' => $search_form,
				)
			);
		}

		// category_custom_field_search
		public function category_custom_field_search() {
			if ( ! directorist_verify_nonce( 'nonce' ) ) {
				wp_send_json(
					array(
						'search_form' => __( 'Invalid request, please reload the page and try again.', 'directorist' ),
					)
				);
			}

			$directory_slug = '';
			if ( ! empty( $_POST['directory'] ) ) {
				$directory_slug = sanitize_key( $_POST['directory'] );
			} elseif ( ! empty( $_POST['listing_type'] ) ) {
				$directory_slug = sanitize_key( $_POST['listing_type'] );
			}

			$atts           = ! empty( $_POST['atts'] ) ? json_decode( wp_unslash( $_POST['atts'] ), true ) : array();  // @codingStandardsIgnoreLine.WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$directory_term = get_term_by( 'slug', $directory_slug, ATBDP_TYPE );
			$directory_id   = $directory_term ? (int) $directory_term->term_id : 0;
			if ( ! $directory_id ) {
				wp_send_json(
					array(
						'search_form' => __( 'Invalid directory.', 'directorist' ),
					)
				);
			}

			$current_page   = isset( $atts['_current_page'] ) ? $atts['_current_page'] : '';
			$listing_layout = get_directorist_option( 'all_listing_layout', 'no_sidebar' );

			$selectors = array(
				'no_sidebar'    => '.directorist-advanced-filter__advanced',
				'left_sidebar'  => '.directorist-advanced-filter__advanced',
				'right_sidebar' => '.directorist-advanced-filter__advanced',
				'search_home'   => '.directorist-search-modal--advanced',
			);

			if ( $current_page === 'listing' || $current_page === 'search_result' ) {
				$selector  = isset( $selectors[ $listing_layout ] ) ? $selectors[ $listing_layout ] : 'no_sidebar';
				$form_type = 'search_result';
			} else {
				$selector = $selectors['search_home'];
				$form_type = 'search_form';
			}

			if ( ! empty( $atts ) ) {
				$atts = array_filter( $atts, static function( $key ) {
					return substr( $key, 0, 7 ) == 'filter_';
				}, ARRAY_FILTER_USE_KEY );
			}

			$search_form = new \Directorist\Directorist_Listing_Search_Form( $form_type, $directory_id, $atts );

			// search form
			ob_start();
				if ( $form_type === 'search_form' ) :
					$search_form->advanced_search_form_fields_template();
				else: ?>
					<input type="hidden" name="directory_type" value="<?php echo esc_attr( $directory_slug ); ?>">
					<?php foreach ( $search_form->form_data[1]['fields'] as $field ) : ?>
						<div class="directorist-advanced-filter__advanced__element directorist-search-field-<?php echo esc_attr( $field['widget_name'] ) ?>">
							<?php $search_form->field_template( $field ); ?>
						</div>
					<?php endforeach;
				endif;
			$markup = ob_get_clean();

			wp_send_json( array(
				'search_form' => $markup,
				'container'   => $selector,
			) );
		}

		public function atbdp_listing_default_type() {
			if ( ! directorist_verify_nonce( 'nonce', 'atbdp_nonce_action_js' ) ) {
				wp_send_json( 'Invalid request.' );
			}

			$default_directory_id = ! empty( $_POST['type_id'] ) ? absint( $_POST['type_id'] ) : 0;
			if ( ! term_exists( $default_directory_id, ATBDP_DIRECTORY_TYPE ) ) {
				wp_send_json( 'Invalid directory.' );
			}

			$current_language = apply_filters( 'wpml_current_language', null );

			do_action( 'directorist_before_set_default_directory_type', $default_directory_id, $current_language );

			$directory_types = directorist_get_directories( array(
				'fields'  => 'ids',
				'exclude' => $default_directory_id,
			) );

			if ( ! empty( $directory_types ) || ! is_wp_error( $directory_types ) ) {
				foreach ( $directory_types as $directory_type ) {
					update_term_meta( $directory_type, '_default', false );

					do_action( 'directorist_after_unset_default_directory_type', $directory_type, $directory_types );
				}
			}

			update_term_meta( $default_directory_id, '_default', true );

			do_action( 'directorist_after_set_default_directory_type', $default_directory_id );

			wp_send_json( 'Updated Successfully!' );
		}

		public function directorist_type_slug_change() {

			if ( ! directorist_verify_nonce() ) {
				wp_send_json(
					array(
						'error'=> __( 'Session expired, please reload the window and try again.', 'directorist' ),
					)
				);
			}

			$type_id     = isset( $_POST['type_id'] ) ? sanitize_key( $_POST['type_id'] ) : '';
			$update_slug = isset( $_POST['update_slug'] ) ? sanitize_key( $_POST['update_slug'] ) : '';

			$directory_slugs = array();
			$listing_types   = directorist_get_directories();

			if ( $listing_types ) {
				foreach ( $listing_types as $listing_type ) {
					$directory_slugs[] = $listing_type->slug;
					if ( $type_id == $listing_type->term_id ) {
						$old_slug = $listing_type->slug;
					}
				}
			}

			if ( in_array( $update_slug, $directory_slugs ) ) {
				wp_send_json(
					array(
						'error'    => __( 'This slug already in use.', 'directorist' ),
						'old_slug' => ! empty( $old_slug ) ? $old_slug : '',
					)
				);
			} else {
				$update_type_slug = wp_update_term( $type_id, ATBDP_TYPE, array( 'slug' => $update_slug ) );
				if ( $update_type_slug ) {
					wp_send_json(
						array(
							'success' => __( 'Slug changes successfully.', 'directorist' ),
						)
					);
				}
			}
		}

		public function ajax_callback_custom_fields() {
			if ( ! directorist_verify_nonce() ) {
				wp_send_json_error( __( 'Invalid request!', 'directorist'), 400 );
			}

			$directory_id = ! empty( $_POST['directory_id'] ) ? sanitize_text_field( wp_unslash( $_POST['directory_id'] ) ) : 0;
			$category_ids = ! empty( $_POST['category_ids'] ) ? wp_parse_id_list( (array) wp_unslash( $_POST['category_ids'] ) ) : array();
			$listing_id   = ! empty( $_POST['listing_id'] ) ? absint( wp_unslash( $_POST['listing_id'] ) ) : 0;

			if ( ! $category_ids ) {
				wp_send_json_error( __( 'No category selected.', 'directorist' ) );
			}

			if ( $listing_id && ! directorist_is_listing_post_type( $listing_id ) ) {
				wp_send_json_error( __( 'Invalid listing.', 'directorist' ), 400 );
			}

			// If directory_id is slug of the directory then check by slug.
			if ( ! directorist_is_directory( $directory_id ) ) {
				$directory_term = get_term_by( 'slug', $directory_id, ATBDP_DIRECTORY_TYPE );

				if ( ! $directory_term ) {
					wp_send_json_error( __( 'Invalid directory.', 'directorist' ), 400 );
				}

				$directory_id = $directory_term->term_id;
			}

			$directory_id = (int) $directory_id;
			$form_fields  = directorist_get_listing_form_fields( $directory_id );
			$result       = array();

			foreach ( $form_fields as $field_key => $field_properties ) {
				$field = directorist_get_field( $field_properties );

				if ( ! $field->is_category_only() || ! $field->get_assigned_category() ) {
					continue;
				}

				if ( in_array( $field->get_assigned_category(), $category_ids, true ) ) {
					ob_start();

					\Directorist\Directorist_Listing_Form::instance()->add_listing_category_custom_field_template( $field_properties, $listing_id );

					$result[ $field_key ]= ob_get_clean();
				}
			}

			wp_send_json_success( $result );
		}

		// guest_reception
		public function guest_reception() {

			// Data Validation
			// ---------------------------
			$error_log = array();

			if ( ! directorist_verify_nonce() ) {
				$error_log['email'] = array(
					'key'     => 'invalid_email',
					'message' => 'Invalid Email',
				);

				$data = array(
					'status'      => false,
					'status_code' => 'nonce_varification_failed',
					'message'     => __('The session has expired, please reload and try again.', 'directorist' ),
					'data'        => array(
						'error_log' => $error_log,
					),
				);

				wp_send_json( $data, 200 );
			}

			// Get the data
			$email = ( ! empty( $_REQUEST['email'] ) ) ? sanitize_email( wp_unslash( $_REQUEST['email'] ) ) : '';

			// Validate email
			if ( empty( $email ) ) {
				$error_log['email'] = array(
					'key'     => 'invalid_email',
					'message' => 'Invalid Email',
				);
			}

			// Send error log if has any error
			if ( ! empty( $error_log ) ) {
				$data = array(
					'status'      => false,
					'status_code' => 'invalid_data',
					'message'     => 'Invalid data found',
					'data'        => array(
						'error_log' => $error_log,
					),
				);

				wp_send_json( $data, 200 );
			}

			// User Validation
			// ---------------------------
			// Check if user exist
			$email = esc_html( $email );
			$email = sanitize_email( $email );
			$user  = get_user_by( 'email', $email );

			if ( $user ) {
				$data = array(
					'status'      => true,
					'status_code' => 'user_exist',
					'message'     => 'User already existed',
					'data'        => array(
						'user_id' => $user->ID,
					),
				);

				wp_send_json( $data, 200 );
			}

			// User Registration
			// ---------------------------
			// Register the user
			$user_name = preg_replace( '/@.+$/', '', $email );
			$rand      = rand( 10000, 90000 );
			$username  = "{$user_name}_{$rand}";
			$new_user  = register_new_user( $username, $email );

			if ( ! $new_user ) {
				$data = array(
					'status'      => false,
					'status_code' => 'unknown_error',
					'message'     => 'Sorry, something went wrong, please try again',
					'data'        => null,
				);

				wp_send_json( $data, 200 );
			}

			$data = array(
				'status'      => true,
				'status_code' => 'registration_successfull',
				'message'     => 'The user is registrated successfully',
				'data'        => array(
					'user_id' => $new_user,
				),
			);

			wp_send_json( $data, 200 );
		}

		/**
		 * It upgrades old pages and make them compatible with new shortcodes
		 */
		public function upgrade_old_pages() {
			update_option( 'atbdp_pages_version', 0 );
			wp_send_json_success( __( 'Congratulations! All old pages have been updated successfully', 'directorist' ) );
		}

		public function atbdp_ajax_login() {

			if ( ! directorist_verify_nonce( 'security', 'ajax-login-nonce' ) ) {
				echo json_encode(
					array(
						'loggedin'    => false,
						'message'     => __( 'Something went wrong, please reload the page', 'directorist' ),
						'nonce_faild' => true,
					)
				);

				die();
			}

			if ( is_user_logged_in() ) {
				echo json_encode(
					array(
						'loggedin' => true,
						'message'  => __( 'Login successful, redirecting...', 'directorist' ),
					)
				);

				die();
			}

			// Nonce is checked, get the POST data and sign user on
			$keep_signed_in = ( isset( $_POST['rememberme'] ) && ( $_POST['rememberme'] === 1 || $_POST['rememberme'] === '1' ) ) ? true : false;

			$info                  = array();
			$info['user_login']    = ( ! empty( $_POST['username'] ) ) ? sanitize_user( wp_unslash( $_POST['username'] ) ) : '';
			$info['user_password'] = ( ! empty( $_POST['password'] ) ) ? $_POST['password'] : ''; // phpcs:ignore
			$info['remember']      = $keep_signed_in;

			$user_signon = wp_signon( $info, $keep_signed_in );
			if ( is_wp_error( $user_signon ) ) {
				echo json_encode(
					array(
						'loggedin' => false,
						'message'  => $user_signon->get_error_message()
					)
				);
			} else {
				wp_set_current_user( $user_signon->ID );

				echo json_encode(
					array(
						'loggedin' => true,
						'message'  => __( 'Login successful, redirecting...', 'directorist' ),
					)
				);
			}

			die();
		}

		/**
		 * Handle ajax file upload via plupload.
		 */
		public function atbdp_post_attachment_upload() {
			// security
			check_ajax_referer( 'atbdp_attachment_upload', '_ajax_nonce' );

			try {
				$field_id  = isset( $_POST['imgid'] ) ? sanitize_text_field( wp_unslash( $_POST['imgid'] ) ) : '';
				$post_id   = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : '';
				$directory = isset( $_POST['directory'] ) ? absint( $_POST['directory'] ) : 0;

				if ( empty( $field_id ) || empty( $directory ) ) {
					throw new \Exception( __( 'Invalid upload request!', 'directorist' ), 400 );
				}

				if ( ! term_exists( $directory, ATBDP_TYPE ) ) {
					throw new \Exception( __( 'Invalid directory type!', 'directorist' ), 400 );
				}

				$fixed_file = ( ! empty( $_FILES[ $field_id . 'async-upload' ] ) ) ? directorist_clean( wp_unslash( $_FILES[ $field_id . 'async-upload' ] ) ) : '';

				$form_fields  = get_term_meta( $directory, 'submission_form_fields', true );
				$field_config = array_values( wp_list_filter( $form_fields['fields'], array( 'field_key' => $field_id ) ) );
				$field_config = current( $field_config );

				$file_type = ! empty( $field_config['file_type'] ) ? $field_config['file_type'] : 'image';
				$file_size = ! empty( $field_config['file_size'] ) ? $field_config['file_size'] : '2mb';

				if ( in_array( $file_type, array( '', 'all_types', 'all' ), true ) ) {
					$file_types = directorist_get_supported_file_types();
				} else {
					$groups = directorist_get_supported_file_types_groups();

					if ( isset( $groups[ $file_type ] ) ) {
						$file_types = $groups[ $file_type ];
					} else {
						$file_types = (array) $file_type;
					}
				}

				$_supported_mimes = array();
				foreach ( get_allowed_mime_types() as $ext => $mime ) {
					$_exts = explode( '|', $ext );
					$match = array_intersect( $file_types, $_exts );
					if ( count( $match ) ) {
						$_supported_mimes[ $ext ] = $mime;
					}
				}

				// Set temporary upload directory.
				add_filter( 'upload_dir', array( __CLASS__, 'set_temporary_upload_dir' ) );

				// handle file upload
				$status = wp_handle_upload(
					$fixed_file,
					array(
						'test_form' => true,
						'action'    => 'atbdp_post_attachment_upload',
						'mimes'     => $_supported_mimes,
					)
				);

				// Restore to default upload directory.
				remove_filter( 'upload_dir', array( __CLASS__, 'set_temporary_upload_dir' ) );

				if ( ! empty( $status['error'] ) ) {
					throw new \Exception( $status['error'], 400 );
				}

				if ( empty( $status['url'] ) ) {
					throw new \Exception( __( 'Could not upload your file, please try again.' ), 400 );
				}

				// Update the meta when post id is available.
				if ( ! empty( $post_id ) ) {
					update_post_meta( $post_id, '_' . $field_id, $status['url'] );

					wp_send_json_success( $status['url'], 201 );
				}

				wp_send_json_success( $status['url'] );

				// if file exists it should have been moved if uploaded correctly so now we can remove it
				/*
				if(!empty($status['file']) && $post_id){
					wp_delete_file( $status['file'] );
				}*/
				// atbdp_Media::post_attachment_upload();
				// ATBDP()->atbdp_Media->post_attachment_upload();
			} catch ( \Exception $e ) {
				wp_send_json_error( $e->getMessage() );
			}
		}

		public static function set_temporary_upload_dir( $upload ) {
			$upload['subdir'] = '/atbdp_temp';
			$upload['path']   = $upload['basedir'] . $upload['subdir'];
			$upload['url']    = $upload['baseurl'] . $upload['subdir'];

			return $upload;
		}

		/**
		 * Add or Remove favourites.
		 *
		 * @since    4.0
		 * @access   public
		 */
		public function atbdp_public_add_remove_favorites_all() {

			if ( ! directorist_verify_nonce() ) {
				wp_send_json( false, 200 );
			}

			$user_id    = get_current_user_id();
			$listing_id = ( ! empty( $_POST['post_id'] ) ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;

			if ( ! $user_id ) {
				$data = 'login_required';
				echo esc_attr( $data );
				wp_die();
			}

			$favorites = directorist_get_user_favorites( $user_id );
			if ( in_array( $listing_id, $favorites ) ) {
				directorist_delete_user_favorites( $user_id, $listing_id );
			} else {
				directorist_add_user_favorites( $user_id, $listing_id );
			}

			$favorites = directorist_get_user_favorites( $user_id );
			if ( in_array( $listing_id, $favorites ) ) {
				$data = $listing_id;
			} else {
				$data = false;
			}

			echo wp_json_encode( $data );
			wp_die();
		}

		/**
		 * Add or Remove favourites.
		 *
		 * @since    4.0
		 * @access   public
		 */
		public function atbdp_public_add_remove_favorites() {

			if ( ! directorist_verify_nonce() ) {
				wp_send_json( false, 200 );
			}

			$listing_id = ( ! empty( $_POST['post_id'] ) ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
			$user_id    = get_current_user_id();
			$favorites  = directorist_get_user_favorites( $user_id );

			if ( in_array( $listing_id, $favorites ) ) {
				directorist_delete_user_favorites( $user_id, $listing_id );
			} else {
				directorist_add_user_favorites( $user_id, $listing_id );
			}

			echo wp_kses_post( the_atbdp_favourites_link( $listing_id ) );

			wp_die();
		}

		/**
		 * It update user from from the front end dashboard using ajax
		 */
		public function update_user_profile() {

			$user_id = get_current_user_id();

			// Make sure current user have appropriate permission
			if ( ! current_user_can( 'edit_user', $user_id ) ) {
				wp_send_json_error( array( 'message' => __( 'You are not allowed to perform this operation', 'directorist' ) ) );
			}

			if ( ! directorist_verify_nonce() ) {
				wp_send_json_error( array( 'message' => __( 'Ops! something went wrong. Try again.', 'directorist' ) ) );
			}

			// process the data and the return a success
			if ( ! empty( $_POST['user'] ) ) {

				if ( ! empty( $_POST['profile_picture_meta'] ) && count( $_POST['profile_picture_meta'] ) ) {
					$meta_data = ( ! empty( $_POST['profile_picture_meta'][0] ) ) ? directorist_clean( wp_unslash( $_POST['profile_picture_meta'][0] ) ) : [];

					if ( 'true' !== $meta_data['oldFile'] ) {
						foreach ( $_FILES as $file => $array ) {
							$id = $this->insert_attachment( $file, 0 );
							update_user_meta( $user_id, 'pro_pic', $id );
						}
					}
				} else {
					update_user_meta( $user_id, 'pro_pic', '' );
				}

				$success = directorist_update_profile( wp_unslash( $_POST['user'] ) ); // directorist_update_profile() will handle sanitisation, so we can just the pass the data through it

				if ( $success ) {
					wp_send_json_success( array( 'message' => __( 'Profile updated successfully', 'directorist' ) ) );
				}

				wp_send_json_error( array( 'message' => __( 'Ops! something went wrong. Try again.', 'directorist' ) ) );

			}

			wp_send_json_error( array( 'message' => __( 'Ops! something went wrong. Try again.', 'directorist' ) ) );

		}

		public function update_user_preferences() {

			$user_id = get_current_user_id();

			// Make sure current user have appropriate permission
			if ( ! current_user_can( 'edit_user', $user_id ) ) {
				wp_send_json_error( array( 'message' => __( 'You are not allowed to perform this operation', 'directorist' ) ) );
			}

			if ( ! directorist_verify_nonce() ) {
				wp_send_json_error( array( 'message' => __( 'Ops! something went wrong. Try again.', 'directorist' ) ) );
			}

			$hide_contact_form 		= isset( $_POST['directorist_hide_contact_form'] ) ? sanitize_text_field( $_POST['directorist_hide_contact_form'] ) : 'no';
			$display_author_email 	= isset( $_POST['directorist_display_author_email'] ) ? sanitize_text_field( $_POST['directorist_display_author_email'] ) : '';
			$contact_owner_recipient 	= isset( $_POST['directorist_contact_owner_recipient'] ) ? sanitize_text_field( $_POST['directorist_contact_owner_recipient'] ) : '';

			// Save the sanitized value to user meta
			if ( ! empty( $hide_contact_form ) ) {
				update_user_meta( $user_id, 'directorist_hide_contact_form', $hide_contact_form );
			}

			if ( ! empty( $display_author_email ) ) {
				update_user_meta( $user_id, 'directorist_display_author_email', $display_author_email );
			}

			if( ! empty( $contact_owner_recipient ) ) {
				update_user_meta( $user_id, 'directorist_contact_owner_recipient', $contact_owner_recipient );
			}

			// Return a success message
			wp_send_json_success( array( 'message' => __( 'Preferences updated successfully.', 'directorist' ) ) );

		}

		private function insert_attachment( $file_handler, $post_id, $setthumb = 'false' ) {
			// check to make sure its a successful upload
			if ( ! empty( $_FILES[ $file_handler ]['error'] ) && $_FILES[ $file_handler ]['error'] !== UPLOAD_ERR_OK ) {
				__return_false();
			}

			require_once ABSPATH . 'wp-admin' . '/includes/image.php';
			require_once ABSPATH . 'wp-admin' . '/includes/file.php';
			require_once ABSPATH . 'wp-admin' . '/includes/media.php';

			$attach_id = media_handle_upload( $file_handler, $post_id );

			if ( $setthumb ) {
				update_post_meta( $post_id, '_thumbnail_id', $attach_id );
			}
			return $attach_id;
		}

		public function remove_listing() {

			if ( ! directorist_verify_nonce() ) {
				wp_send_json( 'error', 200 );
			}

			// delete the listing from here. first check the nonce and then delete and then send success.
			// save the data if nonce is good and data is valid
			if ( valid_js_nonce() && ! empty( $_POST['listing_id'] ) ) {
				$pid = (int) $_POST['listing_id'];
				// Check if the current user is the owner of the post
				$listing = get_post( $pid );
				// delete the post if the current user is the owner of the listing
				if ( get_current_user_id() == $listing->post_author || current_user_can( 'delete_at_biz_dirs' ) ) {
					$success = ATBDP()->listing->db->delete_listing_by_id( $pid );
					if ( $success ) {
						echo 'success';
					} else {
						echo 'error';
					}
				}
			} else {
				echo 'error';
				// show error message
			}
			wp_die();
		}

		/**
		 * send email to listing's owner for review
		 *
		 * @deprecated
		 * @todo remove
		 */
		public function atbdp_send_email_review_to_user() {

			if ( ! directorist_verify_nonce() ) {
				return;
			}

			if ( ! in_array( 'listing_review', get_directorist_option( 'notify_user', array() ) ) ) {
				return false;
			}
			// sanitize form values
			$post_id = ( ! empty( $_POST['post_id'] ) ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
			$message = ( ! empty( $_POST['content'] ) ) ? sanitize_textarea_field( wp_unslash( $_POST['content'] ) ) : '';

			// vars
			$user          = wp_get_current_user();
			$site_name     = get_bloginfo( 'name' );
			$site_url      = get_bloginfo( 'url' );
			$listing_title = get_the_title( $post_id );
			$listing_url   = get_permalink( $post_id );

			$placeholders = array(
				'{site_name}'     => $site_name,
				'{site_link}'     => sprintf( '<a href="%s">%s</a>', $site_url, $site_name ),
				'{site_url}'      => sprintf( '<a href="%s">%s</a>', $site_url, $site_url ),
				'{listing_title}' => $listing_title,
				'{listing_link}'  => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_title ),
				'{listing_url}'   => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_url ),
				'{sender_name}'   => $user->display_name,
				'{sender_email}'  => $user->user_email,
				'{message}'       => $message,
			);
			$send_email   = get_directorist_option( 'admin_email_lists' );

			$to = $user->user_email;

			$subject = __( '[{site_name}] New review at "{listing_title}"', 'directorist' );
			$subject = strtr( $subject, $placeholders );

			$message = __( 'Dear User,<br /><br />A new review at {listing_url}.<br /><br />', 'directorist' );
			$message = strtr( $message, $placeholders );

			$headers  = "From: {$user->display_name} <{$user->user_email}>\r\n";
			$headers .= "Reply-To: {$user->user_email}\r\n";

			$to      = $user->user_email;
			$is_sent = ATBDP()->email->send_mail( $to, $subject, $message, $headers );

			// Action Hook
			$action_args = array(
				'is_sent'    => $is_sent,
				'to_email'   => $to,
				'subject'    => $subject,
				'message'    => $message,
				'headers'    => $headers,
				'listing_id' => $post_id,
				'reviewer'   => $user,
			);

			do_action( 'directorist_email_on_send_email_review_to_user', $action_args );

			return $is_sent;
		}

		/**
		 * send email to admin for review
		 *
		 * @deprecated
		 * @todo remove
		 */
		public function atbdp_send_email_review_to_admin() {

			if ( ! directorist_verify_nonce() ) {
				return false;
			}

			if ( get_directorist_option( 'disable_email_notification' ) ) {
				return false; // vail if email notification is off
			}

			if ( ! in_array( 'listing_review', get_directorist_option( 'notify_admin', array() ) ) ) {
				return false; // vail if order created notification to admin off
			}
			// sanitize form values
			$post_id = ( ! empty( $_POST['post_id'] ) ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
			$message = ! empty( $_POST['content'] ) ? sanitize_textarea_field( wp_unslash( $_POST['content'] ) ) : '';

			// vars
			$user          = wp_get_current_user();
			$site_name     = get_bloginfo( 'name' );
			$site_url      = get_bloginfo( 'url' );
			$listing_title = get_the_title( $post_id );
			$listing_url   = get_permalink( $post_id );

			$placeholders = array(
				'{site_name}'     => $site_name,
				'{site_link}'     => sprintf( '<a href="%s">%s</a>', $site_url, $site_name ),
				'{site_url}'      => sprintf( '<a href="%s">%s</a>', $site_url, $site_url ),
				'{listing_title}' => $listing_title,
				'{listing_link}'  => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_title ),
				'{listing_url}'   => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_url ),
				'{sender_name}'   => $user->display_name,
				'{sender_email}'  => $user->user_email,
				'{message}'       => $message,
			);
			$send_email   = get_directorist_option( 'admin_email_lists' );

			$to = ! empty( $send_email ) ? $send_email : get_bloginfo( 'admin_email' );

			$subject = __( '[{site_name}] New review at "{listing_title}"', 'directorist' );
			$subject = strtr( $subject, $placeholders );

			$message = __( 'Dear Administrator,<br /><br />A new review at {listing_url}.<br /><br />Name: {sender_name}<br />Email: {sender_email}', 'directorist' );
			$message = strtr( $message, $placeholders );

			$headers  = "From: {$user->display_name} <{$user->user_email}>\r\n";
			$headers .= "Reply-To: {$user->user_email}\r\n";

			$is_sent = ATBDP()->email->send_mail( $to, $subject, $message, $headers );

			// Action Hook
			$action_args = array(
				'is_sent'    => $is_sent,
				'to_email'   => $to,
				'subject'    => $subject,
				'message'    => $message,
				'headers'    => $headers,
				'listing_id' => $post_id,
				'reviewer'   => $user,
			);

			do_action( 'directorist_email_on_send_email_review_to_admin', $action_args );

			return $is_sent;
		}


		/**
		 * It checks if the user has filled up proper data for adding a review
		 *
		 * @deprecated
		 * @todo remove
		 *
		 * @return bool It returns true if the review data is perfect and false otherwise
		 */
		public function validate_listing_review() {

			if ( ! directorist_verify_nonce() ) {
				return false;
			}

			$enable_reviewer_content   = get_directorist_option( 'enable_reviewer_content', 1 );
			$required_reviewer_content = get_directorist_option( 'required_reviewer_content', 1 );
			if ( ! empty( $_POST['rating'] ) && ( empty( $enable_reviewer_content ) || ( ! empty( $_POST['content'] ) || empty( $required_reviewer_content ) ) ) && ! empty( $_POST['post_id'] ) ) {
				return true;
			}
			return false;
		}

		/**
		 *  Add new Social Item in the member page in response to Ajax request
		 */
		public function atbdp_social_info_handler() {

			if ( ! directorist_verify_nonce() ) {
				wp_send_json( '' );
			}

			$id = ( ! empty( $_POST['id'] ) ) ? absint( $_POST['id'] ) : 0;

			$social_info = array(
				'id'  => '',
				'url' => '',
			);

			$path = 'listing-form/social-item';

			Directorist\Helper::get_template( $path, compact( 'id', 'social_info' ) );

			die();
		}

		/**
		 * Send listing report email to admin.
		 *
		 * @param  int    $user_id             User who reported.
		 * @param  int    $listing_id          Reported listing.
		 * @param  string $report_message   Report message.
		 *
		 * @return bool
		 */
		public function send_listing_report_email_to_admin( $user_id, $listing_id, $report_message ) {
			$message       = esc_textarea( $report_message );
			$user          = get_user_by( 'id', $user_id );
			$site_name     = get_bloginfo( 'name' );
			$site_url      = get_bloginfo( 'url' );
			$listing_title = get_the_title( $listing_id );
			$listing_url   = get_permalink( $listing_id );

			$placeholders = array(
				'{site_name}'     => $site_name,
				'{site_link}'     => sprintf( '<a href="%s">%s</a>', esc_url( $site_url ), $site_name ),
				'{site_url}'      => sprintf( '<a href="%s">%s</a>', esc_url( $site_url ), $site_url ),

				'{listing_title}' => $listing_title,
				'{listing_link}'  => sprintf( '<a href="%s">%s</a>', esc_url( $listing_url ), $listing_title ),
				'{listing_url}'   => sprintf( '<a href="%s">%s</a>', esc_url( $listing_url ), $listing_url ),

				'{sender_name}'   => $user->display_name,
				'{sender_email}'  => $user->user_email,
				'{message}'       => $message,
			);

			$admin_email = get_directorist_option( 'admin_email_lists' );
			if ( ! $admin_email || ! is_email( $admin_email ) ) {
				$admin_email = get_bloginfo( 'admin_email' );
			}

			$subject = __( '{site_name} Report Abuse via "{listing_title}"', 'directorist' );
			$subject = strtr( $subject, $placeholders );

			$message = __( 'Dear Administrator,<br /><br />This is an email abuse report for a listing at {listing_url}.<br /><br />Name: {sender_name}<br />Email: {sender_email}<br />Message: {message}', 'directorist' );
			$message = strtr( $message, $placeholders );
			$message = atbdp_email_html( $subject, $message );

			$headers  = "From: {$user->display_name} <{$user->user_email}>\r\n";
			$headers .= "Reply-To: {$user->user_email}\r\n";

			return ATBDP()->email->send_mail( $admin_email, $subject, $message, $headers );
		}

		public function ajax_callback_report_abuse() {
			$data = array(
				'error' => 0,
			);

			if ( ! directorist_verify_nonce() ) {
				$data['error']   = 1;
				$data['message'] = __( 'Something is wrong! Please refresh and retry.', 'directorist' );

				wp_send_json( $data );
			}

			$listing_id = ! empty( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
			$message    = ! empty( $_POST['message'] ) ? trim( sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) ) : '';

			if ( empty( $listing_id ) || get_post_type( $listing_id ) !== ATBDP_POST_TYPE ) {
				$data['error']   = 1;
				$data['message'] = __( 'Trying to report invalid listing.', 'directorist' );

				wp_send_json( $data );
			}

			if ( empty( $message ) ) {
				$data['error']   = 1;
				$data['message'] = __( 'Report message cannot be empty.', 'directorist' );

				wp_send_json( $data );
			}

			$mail_sent = $this->send_listing_report_email_to_admin( get_current_user_id(), $listing_id, $message );
			if ( ! $mail_sent ) {
				$data['error']   = 1;
				$data['message'] = __( 'Sorry! Please try again.', 'directorist' );

				wp_send_json( $data );
			}

			$data['message'] = __( 'Your message sent successfully.', 'directorist' );

			do_action( 'directorist_listing_reported', $listing_id );

			wp_send_json( $data );
		}

		/**
		 * Send contact message to the listing owner.
		 *
		 * @return   string    $result    Message based on the result.
		 * @since    4.0.0
		 */
		function atbdp_email_listing_owner_listing_contact() {
			if ( ! directorist_verify_nonce() ) {
				return false;
			}

			// sanitize form values
			$post_id       = ! empty( $_POST['atbdp-post-id'] ) ? sanitize_text_field( absint( $_POST['atbdp-post-id'] ) ) : '';
			$name          = ( ! empty( $_POST['atbdp-contact-name'] ) ) ? sanitize_text_field( wp_unslash( $_POST['atbdp-contact-name'] ) ) : '';
			$email         = ! empty( $_POST['atbdp-contact-email'] ) ? sanitize_email( wp_unslash( $_POST['atbdp-contact-email'] ) ) : '';
			$listing_email = get_post_meta( $post_id, '_email', true );
			$message       = ( ! empty( $_POST['atbdp-contact-message']  ) ) ? stripslashes( sanitize_textarea_field( wp_unslash( $_POST['atbdp-contact-message'] ) ) ) : '';
			// vars
			$post_author_id        = get_post_field( 'post_author', $post_id );
			$user                  = get_userdata( $post_author_id );
			$site_name             = get_bloginfo( 'name' );
			$site_url              = get_bloginfo( 'url' );
			$site_email            = get_bloginfo( 'admin_email' );
			$listing_title         = get_the_title( $post_id );
			$listing_url           = get_permalink( $post_id );
			$date_format           = get_option( 'date_format' );
			$time_format           = get_option( 'time_format' );
			$current_time          = current_time( 'timestamp' );
			$contact_email_subject = get_directorist_option( 'email_sub_listing_contact_email' );
			$contact_email_body    = get_directorist_option( 'email_tmpl_listing_contact_email' );
			$contact_recipient 	   = get_user_meta( $post_author_id, 'directorist_contact_owner_recipient', true );
			$user_email            = ! empty( $contact_recipient ) ? $contact_recipient : 'author';

			$placeholders = array(
				'==NAME=='          => $user->display_name,
				'==USERNAME=='      => $user->user_login,
				'==SITE_NAME=='     => $site_name,
				'==SITE_LINK=='     => sprintf( '<a href="%s">%s</a>', $site_url, $site_name ),
				'==SITE_URL=='      => sprintf( '<a href="%s">%s</a>', $site_url, $site_url ),
				'==LISTING_TITLE==' => $listing_title,
				'==LISTING_LINK=='  => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_title ),
				'==LISTING_URL=='   => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_url ),
				'==SENDER_NAME=='   => $name,
				'==SENDER_EMAIL=='  => $email,
				'==MESSAGE=='       => $message,
				'==TODAY=='         => date_i18n( $date_format, $current_time ),
				'==NOW=='           => date_i18n( $date_format . ' ' . $time_format, $current_time ),
			);
			if ( 'listing_email' == $user_email ) {
				$to = $listing_email;
			} else {
				$to = $user->user_email;
			}
			$subject  = strtr( $contact_email_subject, $placeholders );
			$message  = strtr( $contact_email_body, $placeholders );
			$message  = nl2br( $message );
			$headers  = ATBDP()->email->get_email_headers( [ 'name' => $name, 'email' => $email ] );
			$message  = atbdp_email_html( $subject, $message );
			// return true or false, based on the result
			$is_sent = ATBDP()->email->send_mail( $to, $subject, $message, $headers ) ? true : false;

			// Action Hook
			$action_args = array(
				'is_sent'        => $is_sent,

				'to_email'       => $to,
				'subject'        => $subject,
				'message'        => $message,
				'headers'        => $headers,

				'sender_name'    => $name,
				'from_email'     => $email,

				'listing_author' => $user,
				'listing_id'     => $post_id,
				'listing_title'  => $listing_title,
				'listing_url'    => $listing_url,

				'send_to'        => $user_email,
				'listing_email'  => $listing_email,
				'current_time'   => $current_time,

				'site_name'      => $site_name,
			);

			do_action( 'directorist_email_on_send_contact_messaage_to_listing_owner', $action_args );

			return $is_sent;
		}

		/**
		 * Send contact message to the admin.
		 *
		 * @since    4.0
		 */
		function atbdp_email_admin_listing_contact() {

			if ( ! directorist_verify_nonce() ) {
				return false;
			}

			// sanitize form values
			$post_id = ! empty( $_POST['atbdp-post-id'] ) ? sanitize_text_field( absint( $_POST['atbdp-post-id'] ) ) : '';
			$name    = ( ! empty( $_POST['atbdp-contact-name'] ) ) ? sanitize_text_field( wp_unslash( $_POST['atbdp-contact-name'] ) ) : '';
			$email   = ( ! empty( $_POST['atbdp-contact-email'] ) ) ? sanitize_email( wp_unslash( $_POST['atbdp-contact-email'] ) ) : '';
			$message = ( ! empty( $_POST['atbdp-contact-message'] ) ) ? sanitize_textarea_field( wp_unslash( $_POST['atbdp-contact-message'] ) ) : '';
			// vars
			$site_name     = get_bloginfo( 'name' );
			$site_url      = get_bloginfo( 'url' );
			$listing_title = get_the_title( $post_id );
			$listing_url   = get_permalink( $post_id );
			$date_format   = get_option( 'date_format' );
			$time_format   = get_option( 'time_format' );
			$current_time  = current_time( 'timestamp' );
			$placeholders  = array(
				'{site_name}'     => $site_name,
				'{site_link}'     => sprintf( '<a href="%s">%s</a>', $site_url, $site_name ),
				'{site_url}'      => sprintf( '<a href="%s">%s</a>', $site_url, $site_url ),
				'{listing_title}' => $listing_title,
				'{listing_link}'  => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_title ),
				'{listing_url}'   => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_url ),
				'{sender_name}'   => $name,
				'{sender_email}'  => $email,
				'{message}'       => $message,
				'{today}'         => date_i18n( $date_format, $current_time ),
				'{now}'           => date_i18n( $date_format . ' ' . $time_format, $current_time ),
			);
			$send_emails   = ATBDP()->email->get_admin_email_list();
			$to            = ! empty( $send_emails ) ? $send_emails : get_bloginfo( 'admin_email' );
			$subject       = __( '{site_name} Contact via {listing_title}', 'directorist' );
			$subject       = strtr( $subject, $placeholders );
			$message       = __( "Dear Administrator,<br /><br />A listing on your website {site_name} received a message.<br /><br />Listing URL: {listing_url}<br /><br />Name: {sender_name}<br />Email: {sender_email}<br />Message: {message}<br />Time: {now}<br /><br />This is just a copy of the original email and was already sent to the listing owner. You don't have to reply this unless necessary.", 'directorist' );
			$message       = strtr( $message, $placeholders );
			$headers       = "From: {$name} <{$email}>\r\n";
			$headers      .= "Reply-To: {$email}\r\n";
			$message       = atbdp_email_html( $subject, $message );

			$is_sent = ATBDP()->email->send_mail( $to, $subject, $message, $headers ) ? true : false;

			// Action Hook
			$action_args = array(
				'is_sent'       => $is_sent,

				'to_email'      => $to,
				'subject'       => $subject,
				'message'       => $message,
				'headers'       => $headers,

				'sender_name'   => $name,
				'from_email'    => $email,

				'listing_id'    => $post_id,
				'listing_title' => $listing_title,
				'listing_url'   => $listing_url,

				'current_time'  => $current_time,

				'site_name'     => $site_name,
			);

			do_action( 'directorist_email_on_send_contact_messaage_to_admin', $action_args );

			return $is_sent;
		}

		/**
		 * Send contact email.
		 *
		 * @since    4.0
		 * @access   public
		 */
		public function ajax_callback_send_contact_email() {
			if ( ! directorist_verify_nonce() ) {
				wp_send_json( [
					'error' => 1,
					'message' => __( 'Something is wrong! Please refresh and retry.', 'directorist' )
				], 200 );
			}

			/**
			 * If fires sending processing the submitted contact information
			 *
			 * @since 4.4.0
			 */
			do_action( 'atbdp_before_processing_contact_to_owner' );

			$disable_all_email = get_directorist_option( 'disable_email_notification' );

			$error_response = [
				'error' => 1,
				'message' => __( 'Sorry! Please try again.', 'directorist' )
			];

			// is admin disabled all the notification
			if ( $disable_all_email ) {
				echo wp_json_encode($error_response);
				die();
			}

			$sendOwner = in_array( 'listing_contact_form', get_directorist_option( 'notify_user' ), true );
			$sendAdmin = in_array( 'listing_contact_form', get_directorist_option( 'notify_admin' ), true );

			// is admin disabled both notification
			if ( ! $sendOwner && ! $sendAdmin ) {
				echo wp_json_encode($error_response);
				die();
			}

			// let's check is admin decides to send email to it's owner
			if ( $sendOwner ) {
				$send_to_owner = $this->atbdp_email_listing_owner_listing_contact();
				if ( ! $send_to_owner ) {
					echo wp_json_encode($error_response);
					die();
				}
			}
			// let's check is admin decides to send email to him/her
			if ( $sendAdmin ) {
				$send_to_admin = $this->atbdp_email_admin_listing_contact();
				if ( ! $send_to_admin ) {
					echo wp_json_encode($error_response);
					die();
				}
			}

			/**
			 * @package Directorist
			 * @since 6.3.3
			 * It fires when a contact is made by visitor with listing owner
			 */
			do_action( 'atbdp_listing_contact_owner_submitted' );

			echo wp_json_encode( [
				'error' => 1,
				'message' => __( 'Your message sent successfully.', 'directorist' )
			] );
			die();
		}

		public function bdas_dropdown_terms() {
			check_ajax_referer( 'bdas_ajax_nonce', 'security' );

			if ( isset( $_POST['taxonomy'] ) && isset( $_POST['parent'] ) ) {

				$args = array(
					'taxonomy'  => sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) ),
					'base_term' => 0,
					'parent'    => (int) $_POST['parent'],
				);

				if ( 'at_biz_dir-location' == $args['taxonomy'] ) {

					$args['orderby'] = 'date';
					$args['order']   = 'ASC';
				}

				if ( 'at_biz_dir-category' == $args['taxonomy'] ) {

					$args['orderby'] = 'date';
					$args['order']   = 'ASC';
				}

				if ( isset( $_POST['class'] ) && '' != trim( wp_unslash( $_POST['class'] ) ) ) { // phpcs:ignore
					$args['class'] = sanitize_text_field( wp_unslash( $_POST['class'] ) );
				}

				if ( $args['parent'] != $args['base_term'] ) {
					bdas_dropdown_terms( $args );
				}
			}

			wp_die();
		}

		public function custom_field_search( $term_id = 0 ) {
			$ajax = false;
			if ( isset( $_POST['term_id'] ) ) {

				check_ajax_referer( 'bdas_ajax_nonce', 'security' );

				$ajax    = true;
				$term_id = (int) $_POST['term_id'];
			}
			// Get custom fields
			$custom_field_ids = atbdp_get_custom_field_ids( $term_id );

			$args = array(
				'post_type'      => ATBDP_CUSTOM_FIELD_POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'post__in'       => $custom_field_ids,
				'meta_query'     => array(
					array(
						'key'     => 'searchable',
						'value'   => 1,
						'type'    => 'NUMERIC',
						'compare' => '=',
					),
				),
				'orderby'        => 'meta_value_num',
				'order'          => 'ASC',
				'fields'         => 'ids',
			);

			$custom_fields = ATBDP_Cache_Helper::get_the_transient(
				array(
					'group'      => 'atbdp_custom_field_query',
					'name'       => 'atbdp_custom_fields',
					'query_args' => $args,
					'cache'      => apply_filters( 'atbdp_cache_custom_fields', true ),
					'value'      => function( $data ) {
						return get_posts( $data['query_args'] );
					},
				)
			);

			// Process output
			require ATBDP_VIEWS_DIR . 'custom-fields.php';
			wp_reset_postdata(); // Restore global post data stomped by the_post()

			if ( $ajax ) {
				wp_die();
			}
		}

		public function handle_generate_nonce() {
			// Ensure the user is logged in
			if ( ! is_user_logged_in() ) {
				wp_send_json_error( [ 'message' => __( 'User not logged in.', 'directorist' ) ] );
			}

			wp_send_json_success( [
				'directorist_nonce' => wp_create_nonce( directorist_get_nonce_key() )
			] );
		}

		public static function update_view_count() {
			if ( ! directorist_verify_nonce( 'nonce' ) ) {
				wp_send_json_error( ['message' => __( 'Invalid nonce.', 'directorist' ) ], 400 );
			}

			$ids = isset( $_POST['ids'] ) ? array_map( 'absint', $_POST['ids'] ) : [];
			if ( count( $ids ) < 1 ) {
				wp_send_json_error( ['message' => __( 'No listing ID found.', 'directorist' ) ], 400 );
			}

			$ids        = array_unique( $ids );
			$view_count = [];

			foreach ( $ids as $id ) {
				$view_count[ $id ] = directorist_get_listing_views_count( $id );
			}

			wp_send_json_success( [ 'view_count' => $view_count ] );
		}
	}

endif;
