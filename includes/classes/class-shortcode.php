<?php
/**
 * @author wpWax
 */

namespace Directorist;

if ( ! defined( 'ABSPATH' ) ) exit;

class ATBDP_Shortcode {
    public static $instance = null;

    public static $shortcodes = [];

    public function __construct() {

        if ( is_null( self::$instance ) ) {

            self::$instance = $this;

            self::$shortcodes = apply_filters(
                'atbdp_shortcodes', [
                // Archive
                    'directorist_all_listing' => [ $this, 'listing_archive' ],
                    'directorist_category'    => [ $this, 'category_archive' ],
                    'directorist_tag'         => [ $this, 'tag_archive' ],
                    'directorist_location'    => [ $this, 'location_archive' ],

                // Taxonomy
                    'directorist_all_categories' => [ $this, 'all_categories' ],
                    'directorist_all_locations'  => [ $this, 'all_locations' ],

                // Search
                    'directorist_search_listing' => [ $this, 'search_listing' ],
                    'directorist_search_result'  => [ $this, 'search_result' ],

                // Author
                    'directorist_author_profile'      => [ $this, 'author_profile' ],
                    'directorist_user_dashboard'      => [ $this, 'user_dashboard' ],
                    'directorist_all_authors'         => [ $this, 'all_authors' ],
                    'directorist_signin_signup'       => [ $this, 'directorist_signin_signup' ],
                    'directorist_custom_registration' => [ $this, 'register_registration_shortcode' ],
                    'directorist_user_login'          => [ $this, 'register_login_shortcode' ],

                // Forms
                    'directorist_add_listing'         => [ $this, 'add_listing' ],

                // Checkout
                    'directorist_checkout'            => [ new \ATBDP_Checkout, 'display_checkout_content' ],
                    'directorist_payment_receipt'     => [ new \ATBDP_Checkout, 'payment_receipt' ],
                    'directorist_transaction_failure' => [ new \ATBDP_Checkout, 'transaction_failure' ],

                // Single
                    'directorist_single_listings_header' => [ $this, 'single_listings_header' ],
                    'directorist_single_listing_section' => [ $this, 'single_listing_section' ],
                    'directorist_single_listing_field' => [ $this, 'single_listing_field' ],

                // Single -- legacy shortcode
                    'directorist_listing_top_area'            => '__return_empty_string',
                    'directorist_listing_tags'                => '__return_empty_string',
                    'directorist_listing_custom_fields'       => '__return_empty_string',
                    'directorist_listing_video'               => '__return_empty_string',
                    'directorist_listing_map'                 => '__return_empty_string',
                    'directorist_listing_contact_information' => '__return_empty_string',
                    'directorist_listing_author_info'         => '__return_empty_string',
                    'directorist_listing_contact_owner'       => '__return_empty_string',
                    'directorist_listing_review'              => '__return_empty_string',
                    'directorist_related_listings'            => '__return_empty_string',

                ]
            );

            // Register Shorcodes
            foreach ( self::$shortcodes as $shortcode => $callback ) {
                add_shortcode( $shortcode, $callback );
            }
        }

        return self::$instance;
    }

    public function single_listings_header( $atts ) {

        // Render dummy shortcode content when user isn't in single listing page
        if ( ! is_singular( ATBDP_POST_TYPE ) ) {
            return Helper::single_listing_dummy_shortcode( 'directorist_single_listings_header', $atts );
        }

        $listing_id = ( isset( $atts['post_id'] ) && is_numeric( $atts['post_id'] ) ) ? ( int ) esc_attr( $atts['post_id'] ) : 0;

        if ( ! $listing_id ) {
            global $post;
            $_temp_post = $post; // Cache global post.
            $listing_id = get_queried_object_id();
            $post       = get_post( get_queried_object_id() ); // Assign custom single page as post.
        }

        $listing = Directorist_Single_Listing::instance( $listing_id );

        ob_start();
        echo '<div class="directorist-single-wrapper">';
        $listing->header_template();
        echo '</div>';

        if ( isset( $_temp_post ) ) {
            $post = $_temp_post;
            unset( $_temp_post );
        }

        return ob_get_clean();
    }

    public function single_listing_section( $atts = [] ) {

        // Render dummy shortcode content when user isn't in single listing page
        if ( ! is_singular( ATBDP_POST_TYPE ) ) {
            return Helper::single_listing_dummy_shortcode( 'directorist_single_listing_section', $atts );
        }

        $listing_id = ( isset( $atts['post_id'] ) && is_numeric( $atts['post_id'] ) ) ? ( int ) esc_attr( $atts['post_id'] ) : 0;

        if ( ! $listing_id ) {
            global $post;
            $_temp_post = $post; // Cache global post.
            $listing_id = get_queried_object_id();
            $post       = get_post( get_queried_object_id() ); // Assign custom single page as post.
        }

        $listing = Directorist_Single_Listing::instance( $listing_id );

        ob_start();

        foreach ( $listing->content_data as $section ) {
            $section_id = isset( $section['section_id'] ) ? strval( $section['section_id'] ) : '';

            $section_key  = ( isset( $atts['key'] ) ) ? $atts['key'] : '';
            $section_key  = trim( preg_replace( '/\s{2,}/', ' ', $section_key ) );
            $section_keys = preg_split( '/\s*[,]\s/', $section_key );

            if ( ! empty( $section_keys ) && ! in_array( $section_id, $section_keys ) ) {
                continue;
            }

            $listing->section_template( $section );
        }

        if ( isset( $_temp_post ) ) {
            $post = $_temp_post;
            unset( $_temp_post );
        }

        return ob_get_clean();
    }

    public function single_listing_field( $atts = [] ) {

        if ( ! isset( $atts[ 'field_key' ] ) || empty( $atts[ 'field_key' ] ) ) return;

        // Render dummy shortcode content when user isn't in single listing page
        if ( ! is_singular( ATBDP_POST_TYPE ) ) {
            return Helper::single_listing_dummy_shortcode( 'directorist_single_listing_field', $atts );
        }

        $listing_id = ( isset( $atts['post_id'] ) && is_numeric( $atts['post_id'] ) ) ? ( int ) esc_attr( $atts['post_id'] ) : 0;

        if ( ! $listing_id ) {
            global $post;
            $_temp_post = $post; // Cache global post.
            $listing_id = get_queried_object_id();
            $post       = get_post( get_queried_object_id() ); // Assign custom single page as post.
        }

        $listing = Directorist_Single_Listing::instance( $listing_id );

        ob_start();

        foreach ( $listing->content_data as $section ) {
            foreach ( $section[ 'fields' ] as $field ) {
                if ( isset( $field[ 'field_key' ] ) && $field[ 'field_key' ] === $atts[ 'field_key' ] ) {

                    /** Card & Wrapper - Open */
                    if ( isset( $atts[ 'card' ] ) && $atts[ 'card' ] === 'true' ) echo '<div class="directorist-card"><div class="directorist-card__body">';
                    if ( isset( $atts[ 'wrap' ] ) && $atts[ 'wrap' ] === 'true' ) echo '<div class="directorist-details-info-wrap">';

                    $listing->field_template( $field );

                    /** Card & Wrapper - Close */
                    if ( isset( $atts[ 'wrap' ] ) && $atts[ 'wrap' ] === 'true' ) echo '</div>';
                    if ( isset( $atts[ 'card' ] ) && $atts[ 'card' ] === 'true' ) echo '</div></div>';

                    continue 2;
                }
            }
        }

        if ( isset( $_temp_post ) ) {
            $post = $_temp_post;
            unset( $_temp_post );
        }

        return ob_get_clean();
    }

    public function listing_archive( $atts ) {
        $atts = ! empty( $atts ) ? $atts : [];
        $listings = new Directorist_Listings( $atts );

        if ( empty( $atts[ 'shortcode' ] ) ) {
            $atts[ 'shortcode' ] = 'directorist_all_listing';
        }

        return $listings->render_shortcode( $atts );
    }

    public function category_archive( $atts ) {
        $atts             = ! empty( $atts ) ? $atts : [];
        $category_slug    = ! empty( $_GET['category'] ) ? directorist_clean( wp_unslash( $_GET['category'] ) ) : urldecode( get_query_var( 'atbdp_category' ) );
        $atts['category'] = sanitize_text_field( $category_slug );

        $atts[ 'shortcode' ] = 'directorist_category';

        return $this->listing_archive( $atts );
    }

    public function tag_archive( $atts ) {
        $atts        = ! empty( $atts ) ? $atts : [];
        $tag_slug    = ! empty( $_GET['tag'] ) ? directorist_clean( wp_unslash( $_GET['tag'] ) ) : get_query_var( 'atbdp_tag' );
        $atts['tag'] = sanitize_title_for_query( $tag_slug );

        $atts[ 'shortcode' ] = 'directorist_tag';

        return $this->listing_archive( $atts );
    }

    public function location_archive( $atts ) {
        $atts             = ! empty( $atts ) ? $atts : [];
        $location_slug    = ! empty( $_GET['location'] ) ? directorist_clean( wp_unslash( $_GET['location'] ) ) : urldecode( get_query_var( 'atbdp_location' ) );
        $atts['location'] = sanitize_text_field( $location_slug );

        $atts[ 'shortcode' ] = 'directorist_location';

        return $this->listing_archive( $atts );
    }

    public function all_categories( $atts ) {
        $atts = ! empty( $atts ) ? $atts : [];
        $taxonomy = new Directorist_Listing_Taxonomy( $atts, 'category' );

        $atts[ 'shortcode' ] = 'directorist_all_categories';

        return $taxonomy->render_shortcode( $atts );
    }

    public function all_locations( $atts ) {
        $atts = ! empty( $atts ) ? $atts : [];
        $taxonomy = new Directorist_Listing_Taxonomy( $atts, 'location' );

        $atts[ 'shortcode' ] = 'directorist_all_locations';

        return $taxonomy->render_shortcode( $atts );
    }

    public function search_listing( $atts ) {
        $atts = ! empty( $atts ) ? $atts : [];
        $listing_type = '';
        if ( ! empty( $atts['listing_type'] ) ) {
            $listing_type = $atts['listing_type'];
        }
        $searchform = new Directorist_Listing_Search_Form( 'search_form', $listing_type, $atts );

        $atts[ 'shortcode' ] = 'directorist_search_listing';

        return $searchform->render_search_shortcode( $atts );
    }

    public function search_result( $atts ) {
        $atts = ! empty( $atts ) ? $atts : [];
        $listings = new Directorist_Listings( $atts, 'search_result' );

        $atts[ 'shortcode' ] = 'directorist_search_result';

        return $listings->render_shortcode( $atts );
    }

    public function author_profile( $atts ) {
        $atts = ! empty( $atts ) ? $atts : [];
        $author = Directorist_Listing_Author::instance();

        $atts[ 'shortcode' ] = 'directorist_author_profile';

        return $author->render_shortcode_author_profile( $atts );
    }

    public function all_authors() {
        $all_authors = new Directorist_All_Authors();
        return $all_authors->render_shortcode_all_authors();
    }

    public function user_dashboard( $atts ) {
        $atts      = ! empty( $atts ) ? $atts : [];
        $dashboard = Directorist_Listing_Dashboard::instance();

        $atts[ 'shortcode' ] = 'directorist_user_dashboard';

        return $dashboard->render_shortcode( $atts );
    }

    public function directorist_signin_signup( $atts ) {
        $atts = ! empty( $atts ) ? $atts : [];
        $account = Directorist_Account::instance();

        $atts[ 'shortcode' ] = 'directorist_signin_signup';

        return $account->render( $atts );
    }

    public function add_listing( $atts ) {
        $atts  = ! empty( $atts ) ? $atts : [];
        $id    = get_query_var( 'atbdp_listing_id', 0 );
        $id    = empty( $id ) && ! empty( $_REQUEST['edit'] ) ? directorist_clean( wp_unslash( $_REQUEST['edit'] ) ) : $id;

        $forms = Directorist_Listing_Form::instance( $id );

        $atts[ 'shortcode' ] = 'directorist_add_listing';

        return $forms->render_shortcode( $atts );
    }

    /**
     * Render custom registration shortcode.
     * Added for backward compatibility with version 8.0.0
     *
     * @return string
     */
    public function register_registration_shortcode() {
        $atts = [
            'shortcode'   => 'directorist_custom_registration',
            'active_form' => 'signup',
        ];

        return Directorist_Account::instance()->render( $atts );
    }

    /**
     * Render custom login shortcode.
     * Added for backward compatibility with version 8.0.0
     *
     * @return string
     */
    public function register_login_shortcode() {
        $atts = [
            'shortcode'   => 'directorist_user_login',
            'active_form' => 'signin',
        ];

        return Directorist_Account::instance()->render( $atts );
    }
}
