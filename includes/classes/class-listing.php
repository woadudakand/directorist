<?php
/**
 * ATBDP Listing class
 *
 * This class is for interacting with Listing, eg, searching, displaying etc.
 *
 * @package     ATBDP
 * @subpackage  inlcudes/classes Listing
 * @copyright   Copyright (c) 2018, AazzTech
 * @since       1.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    die('You should not access this file directly...');
}

if (!class_exists('ATBDP_Listing')):

    class ATBDP_Listing
    {
        /**
         * ATBDP_Add_Listing Object.
         *
         * @var object|ATBDP_Add_Listing
         * @since 1.0
         */
        public $add_listing;

        /**
         * ATBDP_Add_Listing Object.
         *
         * @var object|ATBDP_Add_Listing
         * @since 1.0
         */
        public $db;

        public function __construct()
        {
            $this->include_files();
            $this->add_listing = new ATBDP_Add_Listing;
            $this->db = new ATBDP_Listing_DB;
            // for search functionality
            // add_action('pre_get_posts', array($this, 'modify_search_query'), 1, 10);
            // remove adjacent_posts_rel_link_wp_head for accurate post views
            remove_action('wp_head', array($this, 'adjacent_posts_rel_link_wp_head', 10));
            add_action('plugins_loaded', array($this, 'manage_listings_status'));

            add_filter('post_thumbnail_html', array($this, 'post_thumbnail_html'), 10, 3);
            add_action('wp_head', array($this, 'og_metatags'));

			// add_action('template_redirect', array($this, 'atbdp_listing_status_controller')); // This method has been renamed to update_listing_status_after_review
            add_action('template_redirect', array( $this, 'update_listing_status_after_review' ) );

            // listing filter
            add_action('restrict_manage_posts', array($this, 'atbdp_listings_filter'));
            add_filter('parse_query', array($this, 'listing_type_search_query'));

            add_action('wp_ajax_directorist_track_listing_views', array( $this, 'directorist_track_listing_views' ) );
            add_action('wp_ajax_nopriv_directorist_track_listing_views', array( $this, 'directorist_track_listing_views' ) );

			add_filter( 'the_title', array( $this, 'add_preview_prefix_in_title' ), 10, 2 );
        }

		public function add_preview_prefix_in_title( $title, $listing_id ) {
			if ( is_admin() || ! directorist_is_listing_post_type( $listing_id ) || ! isset( $_GET['preview'] ) ) {
				return $title;
			}

			return sprintf(
				__( 'Preview: %s', 'directorist' ),
				( 'private' === get_post_status( $listing_id ) ? str_replace( 'Private:', '', $title ) : $title )
			);
		}

        public function listing_type_search_query( $query )
        {
            global $pagenow;
            $type = 'post';
            if (isset($_GET['post_type'])) {
                $type = ! empty( $_GET['post_type'] ) ? directorist_clean( wp_unslash( $_GET['post_type'] ) ) : '';
            }
            if ('at_biz_dir' == $type && is_admin() && $pagenow == 'edit.php' && isset($_GET['directory_type']) && ! empty( $_GET['directory_type'] ) ) {
                $value = ! empty( $_GET['directory_type'] ) ? directorist_clean( wp_unslash( $_GET['directory_type'] ) ) : '';
                $tax_query = array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => ATBDP_TYPE,
                        'terms'    => $value,
                    ),
                );
                $query->set( 'tax_query', $tax_query );
            }
        }

        public function atbdp_listings_filter(  ) {
            $type = 'post';
            if (isset($_GET['post_type'])) {
                $type = ! empty( $_GET['post_type'] ) ? directorist_clean( wp_unslash( $_GET['post_type'] ) ) : '';
            }

            //only add filter to post type you want
            if ( ( 'at_biz_dir' == $type ) && directorist_is_multi_directory_enabled() ) { ?>
                <select name="directory_type">
                    <option value=""><?php esc_html_e( 'Filter by directory ', 'directorist' ); ?></option>
                    <?php
                    $current_v = ! empty( $_GET['directory_type'] ) ? directorist_clean( wp_unslash( $_GET['directory_type'] ) ) : '';

                    $listing_types = get_terms([
                        'taxonomy'   => 'atbdp_listing_types',
                        'hide_empty' => false,
                      ]);
                      foreach ($listing_types as $listing_type) { ?>
                        <option value="<?php echo esc_attr( $listing_type->term_id ); ?>" <?php echo $listing_type->term_id == $current_v ? ' selected="selected"' : ''; ?>><?php echo esc_attr( $listing_type->name ); ?></option>
                        <?php } ?>
                </select>
            <?php
            }
        }

        /**
         * @since 6.3.5
         */
		public function update_listing_status_after_review() {
			// Exit early if listing status or review status isn't set, or if preview mode is enabled
			if ( ( empty( $_GET['listing_status'] ) && empty( $_GET['reviewed'] ) ) || isset( $_GET['preview'] ) ) {
				return;
			}

			// Retrieve listing ID from multiple possible query parameters
			$listing_id = $this->get_listing_id_from_request();
			if ( ! $listing_id || ! directorist_is_listing_post_type( $listing_id ) ) {
				return;
			}

			if ( ! $this->validate_nonce( $listing_id ) ) {
				return;
			}

			// Retrieve directory ID and validate or set it if not numeric
			$directory_id = $this->get_or_set_directory_id( $listing_id );
			if ( ! $directory_id ) {
				return;
			}

			// Prepare status for post update
			$args = $this->prepare_post_update_args( $listing_id, $directory_id );

			// Update post status
			wp_update_post( $args );

			// Trigger custom action after updating listing status
			do_action( 'directorist_listing_status_updated', $listing_id, $args );

			wp_safe_redirect( remove_query_arg( [ '_token', 'edited', 'post_id', 'reviewed' ] ) );
		}

		protected function validate_nonce( $listing_id ) {
			if ( ! isset( $_GET['_token'] ) ) {
				return false;
			}

			$nonce = wp_unslash( $_GET['_token'] );

			if ( ! wp_verify_nonce( $nonce, 'directorist_listing_form_redirect_url_' . $listing_id ) ) {
				return false;
			}

			return true;
		}

		protected function get_listing_id_from_request() {
			if ( ! empty( $_GET['listing_id'] ) ) {
				return absint( $_GET['listing_id'] );
			}

			if ( ! empty( $_GET['post_id'] ) ) {
				return absint( $_GET['post_id'] );
			}

			if ( ! empty( $_GET['atbdp_listing_id'] ) ) {
				return absint( $_GET['atbdp_listing_id'] );
			}

			return get_the_ID();
		}

		protected function get_or_set_directory_id( $listing_id ) {
			$directory_id = directorist_get_listing_directory( $listing_id );

			// Check if directory_id is numeric, if not try to retrieve and set it
			if ( ! is_numeric( $directory_id ) ) {
				$directory_term = get_term_by( 'slug', $directory_id, ATBDP_TYPE );

				if ( ! $directory_term ) {
					return null;
				}

				$directory_id = (int) $directory_term->term_id;
				directorist_set_listing_directory( $listing_id, $directory_id );
			}

			return absint( $directory_id );
		}

		protected function prepare_post_update_args( $listing_id, $directory_id ) {
			$create_status = directorist_get_listing_create_status( $directory_id );
			$edit_status   = directorist_get_listing_edit_status( $directory_id, $listing_id );
			$edited        = isset( $_GET['edited'] ) ? sanitize_text_field( $_GET['edited'] ) : 'no';

			$args = [
				'id'            => $listing_id,
				'edited'        => filter_var( $edited, FILTER_VALIDATE_BOOLEAN ),
				'new_l_status'  => $create_status,
				'edit_l_status' => $edit_status,
				'create_status' => $create_status,
				'edit_status'   => $edit_status,
			];

			// Filter for custom argument modifications
			return apply_filters( 'atbdp_reviewed_listing_status_controller_argument', [
				'ID'          => $listing_id,
				'post_status' => atbdp_get_listing_status_after_submission( $args ),
				'edited'      => $args['edited'],
			] );
		}

        // manage_listings_status
        public function manage_listings_status() {
            add_action('atbdp_order_created', [ $this, 'update_listing_status'], 10, 2);
        }

        // update_listing_status
        public function update_listing_status( $order_id, $listing_id ) {
            $pricing_plan_enabled = is_fee_manager_active();

            if ( $pricing_plan_enabled ) {
				return;
			};

            if ( ! directorist_is_featured_listing_enabled() ) {
				return;
			};

            $directory_type = directorist_get_listing_directory( $listing_id );
            $post_status = get_term_meta( $directory_type, 'new_listing_status', true );

            $order_meta = get_post_meta( $order_id );
            $payment_status = $order_meta['_payment_status'][0];

            if ( 'completed' !== $payment_status ) {
                $post_status = 'pending';
            }

            $args = array(
                'ID' => $listing_id,
                'post_status' => $post_status,
            );

            $is_directory_post = ( 'at_biz_dir' === get_post_type( $listing_id ) ) ? true : false;

            if ( $is_directory_post ) {
                wp_update_post( apply_filters('atbdp_reviewed_listing_status_controller_argument', $args) );
            }
        }

        /**
         * Adds the Facebook OG tags and Twitter Cards.
         *
         * @since    1.0.0
         * @access   public
         */
        public function og_metatags()
        {

            global $post;

            if (!isset($post)) return;

            if (is_singular('at_biz_dir')) {


                $title = get_the_title();

                // Get Location page title

                ?>
                <meta property="og:url" content="<?php echo esc_url( atbdp_get_current_url() ); ?>" />
                <meta property="og:type" content="article" />
                <meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
                <meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo('name') ); ?>" />
                <meta name="twitter:card" content="summary" />

                <?php
                if (!empty($post->post_content)) { ?>
                    <meta property="og:description" content="<?php echo esc_attr( wp_trim_words($post->post_content, 150) ); ?>" />
                <?php }

                $images = directorist_get_listing_preview_image( $post->ID );
                if (!empty($images)) {
                    $thumbnail = atbdp_get_image_source($images, 'full');

                    if ( ! empty( $thumbnail ) ) { ?>
                        <meta property="og:image" content="<?php echo esc_attr( $thumbnail ); ?>" />
                        <meta name="twitter:image" content="<?php echo esc_attr( $thumbnail ); ?>" />
                    <?php }

                }

            }

        }

        /**
         * Filter the post content.
         *
         * @param string $html The post thumbnail HTML.
         * @return   string    $html    Filtered thumbnail HTML.
         * @since    5.4.0
         * @access   public
         *
         */
        public function post_thumbnail_html($html, $post_id)
        {
            $double_thumb = get_directorist_option( 'fix_listing_double_thumb', 1 );
            if (!empty($double_thumb)) {
                if (is_singular('at_biz_dir')) {
                    if (!isset($post_id)) return '';
                    if ( ATBDP_POST_TYPE === get_post_type( $post_id ) ) {
                        $html = '';
                    }
                }
            }
            return $html;
        }

        public function modify_search_query(WP_Query $query)
        {
            if (!is_admin() && $query->is_main_query() && $query->is_archive()) {
                global $wp_query;
                $post_type = get_query_var('post_type');
                $s = get_query_var('s');
                $post_type = (!empty($post_type)) ? $post_type : (!empty($query->post_type) ? $query->post_type : 'any');

                if ($query->is_search() && $post_type == ATBDP_POST_TYPE) {
                    /*@TODO; make the number of items to show dynamic using setting panel*/
                    $srch_p_num = get_directorist_option('search_posts_num', 6);
                    $query->set('posts_per_page', absint($srch_p_num));

                }
                return $query;
            } else {
                return $query;
            }

        }

        public function include_files()
        {
            load_some_file(array('class-template'), ATBDP_CLASS_DIR);
            load_some_file(array('class-add-listing'), ATBDP_CLASS_DIR);
            load_some_file(array('class-listing-db'), ATBDP_CLASS_DIR);
        }

        public function set_post_views($postID)
        {
            /*@todo; add option to verify the user using his/her IP address so that reloading the page multiple times by the same user does not increase his post view of the same post on the same day.*/
            directorist_set_listing_views_count( $postID );
        }

        public function directorist_track_listing_views() {
            if ( ! directorist_verify_nonce() ) {
				wp_send_json_error(
					array(
						'error'=> __( 'Session expired, please reload the window and try again.', 'directorist' ),
					),
				);
			}

            $count_loggedin = get_directorist_option( 'count_loggedin_user' );
            if ( is_user_logged_in() && empty( $count_loggedin ) ) {
                return;
            }

            if ( isset( $_POST['listing_id'] ) ) {
                $listing_id         = absint( $_POST['listing_id'] );
                $this->set_post_views( $listing_id );
                // Return 'success' to the AJAX request to indicate that the view has been counted.
                wp_send_json_success();
            }

            die();
        }
    }

endif;