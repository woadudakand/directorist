<?php
/**
 * @author wpWax
 */

namespace Directorist;

use ATBDP_Permalink;
use Directorist\database\DB;

if ( ! defined( 'ABSPATH' ) ) exit;

class Directorist_Listing_Dashboard {

	protected static $instance = null;
	public static $display_title = false;

	public $id;

	public $current_listings_query;
	public $user_type;
	public $become_author_button;
	public $become_author_button_text;

	private function __construct() {
		$this->id 						 = get_current_user_id();
		$user_type 		  		 		 = get_user_meta( get_current_user_id(), '_user_type', true );
		$this->user_type  		 		 = ! empty( $user_type ) ? $user_type : '';
		$this->become_author_button 	 = get_directorist_option( 'become_author_button', 1);
		$this->become_author_button_text = get_directorist_option( 'become_author_button_text', __( 'Become An Author', 'directorist' ) );
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function get_id() {
		return $this->id;
	}

	public function ajax_listing_tab() {
		check_ajax_referer( directorist_get_nonce_key() );

		$tab        = isset( $_POST['tab'] ) ? sanitize_key( $_POST['tab'] ) : 'all';
		$paged      = isset( $_POST['paged'] ) ? absint( $_POST['paged'] ) : 1;
		$search     = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
		$action     = isset( $_POST['task'] ) ? sanitize_key( $_POST['task'] ) : '';
		$listing_id = isset( $_POST['taskdata'] ) ? absint( $_POST['taskdata'] ) : 0;

		if ( $action && $listing_id && in_array( $action, array( 'delete' ), true ) ) {
			$this->handle_listing_action( $action, $listing_id );
		}

		$args = array(
			'dashboard' => $this,
			'query'     => $this->listings_query( $tab, $paged, $search ),
		);

		$result = [
			'content'    => Helper::get_template_contents( 'dashboard/listing-row', $args ),
			'pagination' => $this->listing_pagination( 'page/%#%', $paged ),
		];

		wp_send_json_success( $result );

		wp_die();
	}

	public function handle_listing_action( $action, $listing_id ) {
		if ( $action === 'delete' && current_user_can( get_post_type_object( ATBDP_POST_TYPE )->cap->delete_post, $listing_id ) ) {
			wp_delete_post( $listing_id );

			do_action( 'directorist_listing_deleted', $listing_id );
		}
	}

	public function listings_query( $status = 'all', $paged = 1, $search = '' ) {
		$pagination_enabled = (bool) get_directorist_option( 'user_listings_pagination', 1 );
		$per_page           = (int) get_directorist_option( 'user_listings_per_page', 9 );

		$args = array(
			'author'         => get_current_user_id(),
			'post_type'      => ATBDP_POST_TYPE,
			'posts_per_page' => $per_page,
			'order'          => 'DESC',
			'orderby'        => 'date',
		);

		if ( $pagination_enabled) {
			$args['paged'] = $paged;
		} else{
			$args['no_found_rows'] = false;
		}

		if ( $status === 'pending' || $status === 'expired' || $status === 'publish' ) {
			$args['post_status'] = $status;
		} else {
			$args['post_status'] = array( 'publish', 'pending', 'expired', 'private' );
		}

		if ( $search ) {
			$args['s'] = esc_sql( $search );
		}

		$this->current_listings_query = new \WP_Query( apply_filters( 'directorist_dashboard_query_arguments', $args, $status ) );

		return $this->current_listings_query;
	}

	public function get_listing_price_html() {
		$id = get_the_ID();
		$price = get_post_meta( $id, '_price', true );
		$price_range = get_post_meta( $id, '_price_range', true );
		$atbd_listing_pricing = get_post_meta( $id, '_atbd_listing_pricing', true );
		if (!empty($price_range) && ('range' === $atbd_listing_pricing)) {
			return atbdp_display_price_range( $price_range );
		}
		else {
			return atbdp_display_price( $price );
		}
	}

	public function get_listing_expired_html() {
		// TODO: Status has been migrated, remove related code.
		// $id = get_the_ID();
		// $date_format = get_option('date_format');
		// $exp_date  = get_post_meta($id, '_expiry_date', true);
		// $never_exp = get_post_meta($id, '_never_expire', true);
		// $status    = get_post_meta($id, '_listing_status', true);
		// $exp_text  = !empty($never_exp) ? __('Never Expires', 'directorist') : date_i18n($date_format, strtotime($exp_date));
		// $exp_html  = ( $status == 'expired' ) ? '<span style="color: red">' . __('Expired', 'directorist') . '</span>' : $exp_text;
		// return $exp_html;
		$listing_id   = get_the_ID();
		$never_expire = (bool) get_post_meta( $listing_id, '_never_expire', true );
		if ( $never_expire ) {
			return '<span>' . esc_html__( 'Never Expires', 'directorist' ) . '</span>';
		}

		$expiry_date    = strtotime( get_post_meta( $listing_id, '_expiry_date', true ) );
		$formatted_date = date_i18n( get_option( 'date_format' ), $expiry_date );
		$status         = get_post_status( $listing_id );

		// Determine the color based on the status
		$color = ( $status === 'expired' ) ? 'style="color: red"' : '';

		if ( $expiry_date ) {
			return '<span ' . $color . '>' . esc_html( $formatted_date ) . '</span>';
		}

		return '';
	}

	public function listing_pagination( $base = '', $paged = '' ) {
		$query = $this->current_listings_query;
		$paged = $paged ? $paged : atbdp_get_paged_num();
		$big   = 999999999;

		$links = paginate_links(array(
			'base'      => $base ? $base : str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'    => '?paged=%#%',
			'current'   => max(1, $paged),
			'total'     => $query->max_num_pages,
			'prev_text' => directorist_icon( 'fas fa-chevron-left', false ),
			'next_text' => directorist_icon( 'fas fa-chevron-right', false ),
		));

		return $links;
	}

	public function get_listing_status_html() {
		$id = get_the_ID();
		$status = get_post_status( $id );
		$statuses = directorist_get_listing_statuses();
		$status_label = $statuses[$status] ?? __( 'Unknown', 'directorist' );
		$html = sprintf('<span class="directorist_badge dashboard-badge directorist_status_%s">%s</span>', strtolower( $status ), $status_label );
		return $html;
	}

	public function get_listing_type() {
		$type = directorist_get_listing_directory( get_the_ID() );
		$term = get_term( $type );
		return !empty( $term->name ) ? $term->name : '';
	}

	public function get_listing_thumbnail() {
		$id                = get_the_ID();
		$type              = directorist_get_listing_directory( $id );

		$default_image_src = Helper::default_preview_image_src( $type );
		$image_quality     = get_directorist_option('preview_image_quality', 'directorist_preview');
		$listing_prv_img   = directorist_get_listing_preview_image( $id );
		$listing_img       = directorist_get_listing_gallery_images( $id );

		if ( is_array( $listing_img ) && ! empty( $listing_img[0] ) ) {
			$thumbnail_img = atbdp_get_image_source( $listing_img[0], $image_quality );
			$thumbnail_id = $listing_img[0];
		}

		if ( ! empty( $listing_prv_img ) ) {
			$thumbnail_img = atbdp_get_image_source( $listing_prv_img, $image_quality );
			$thumbnail_id = $listing_prv_img;
		}

		if ( ! empty( $img_src ) ) {
			$thumbnail_img = $img_src;
			$thumbnail_id = 0;
		}

		if ( empty( $thumbnail_img ) ) {
			$thumbnail_img = $default_image_src;
			$thumbnail_id = 0;
		}

		$image_src    = is_array($thumbnail_img) ? $thumbnail_img['url'] : $thumbnail_img;
		$image_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
		$image_alt = ( ! empty( $image_alt ) ) ? esc_attr( $image_alt ) : esc_html( get_the_title( $thumbnail_id ) );
		$image_alt = ( ! empty( $image_alt ) ) ? $image_alt : esc_html( get_the_title() );

		return "<img src='$image_src' alt='$image_alt' />";
	}

	public function fav_listing_items() {
		$fav_listing_items = array();

		$fav_listings = DB::favorite_listings_query();

		if ( $fav_listings->have_posts() ){
			foreach ( $fav_listings->posts as $post ) {
				$listing_type  = directorist_get_listing_directory( $post->ID );
				$title         = ! empty( $post->post_title ) ? $post->post_title : __( 'Untitled', 'directorist' );
				$cats          = get_the_terms( $post->ID, ATBDP_CATEGORY );
				$category      = get_post_meta( $post->ID, '_admin_category_select', true );
				$category_name = ! empty( $cats ) ? $cats[0]->name : 'Uncategorized';
				$mark_fav_html = atbdp_listings_mark_as_favourite( $post->ID );


				if (!empty($cats)) {
					$cat_icon = get_cat_icon($cats[0]->term_id);
				}
				$cat_icon = !empty($cat_icon) ? $cat_icon : 'las la-tags';
				$icon = directorist_icon( $cat_icon, false );

				$category_link = ! empty( $cats ) ? esc_url( ATBDP_Permalink::atbdp_get_category_page( $cats[0] ) ) : '#';
				$post_link     = esc_url( get_post_permalink( $post->ID ) );

				$listing_img     	= directorist_get_listing_gallery_images( $post->ID );
				$listing_prv_img 	= directorist_get_listing_preview_image( $post->ID );
				$default_image_src 	= Helper::default_preview_image_src( $listing_type );
				$crop_width      	= get_directorist_option( 'crop_width', 360 );
				$crop_height     	= get_directorist_option( 'crop_height', 300 );

				if ( ! empty( $listing_prv_img ) ) {
					$prv_image = atbdp_get_image_source( $listing_prv_img, 'large' );
				}
				if ( ! empty( $listing_img[0] ) ) {
					$gallery_img = atbdp_get_image_source( $listing_img[0], 'medium' );
				}

				if ( ! empty( $listing_prv_img ) ) {
					$img_src = $prv_image;

				}
				if ( ! empty( $listing_img[0] ) && empty( $listing_prv_img ) ) {
					$img_src = $gallery_img;

				}
				if ( empty( $listing_img[0] ) && empty( $listing_prv_img ) ) {
					$img_src = $default_image_src;
				}

				$fav_listing_items[] = array(
					'obj'           => $post,
					'permalink'     => $post_link,
					'img_src'       => $img_src,
					'title'         => $title,
					'category_link' => $category_link,
					'category_name' => $category_name,
					'icon'          => $icon,
					'mark_fav_html' => $mark_fav_html,
				);
			}
		}

		return $fav_listing_items;
	}

	public function user_info( $type ) {
		$id = $this->id;
		$userdata = get_userdata( $id );
		$result = '';

		switch ( $type ) {
			case 'display_name':
			$result = $userdata->display_name;
			break;

			case 'username':
			$result = $userdata->user_login;
			break;

			case 'first_name':
			$result = $userdata->first_name;
			break;

			case 'last_name':
			$result = $userdata->last_name;
			break;

			case 'email':
			$result = $userdata->user_email;
			break;

			case 'phone':
			$result = get_user_meta( $id, 'atbdp_phone', true );
			break;

			case 'website':
			$result = $userdata->user_url;
			break;

			case 'address':
			$result = get_user_meta( $id, 'address', true );
			break;

			case 'facebook':
			$result = get_user_meta( $id, 'atbdp_facebook', true );
			break;

			case 'twitter':
			$result = get_user_meta( $id, 'atbdp_twitter', true );
			break;

			case 'linkedin':
			$result = get_user_meta( $id, 'atbdp_linkedin', true );
			break;

			case 'youtube':
			$result = get_user_meta( $id, 'atbdp_youtube', true );
			break;

			case 'bio':
			$result = get_user_meta( $id, 'description', true );
			break;

			case 'hide_contact_form':
			$result = get_user_meta( $id, 'directorist_hide_contact_form', true );
			break;

			case 'display_author_email':
			$result = get_user_meta( $id, 'directorist_display_author_email', true );
			break;

			case 'contact_owner_recipient':
			$result = get_user_meta( $id, 'directorist_contact_owner_recipient', true );
			break;
		}

		return $result;
	}

	public function dashboard_tabs() {
		// Tabs
		$dashboard_tabs = array();

		$my_listing_tab     = get_directorist_option( 'my_listing_tab', 1 );
		$my_profile_tab     = get_directorist_option( 'my_profile_tab', 1 );
		$fav_listings_tab   = get_directorist_option( 'fav_listings_tab', 1 );

		if ( $my_listing_tab && ( 'general' != $this->user_type && 'become_author' != $this->user_type ) ) {
			$my_listing_tab_text = get_directorist_option( 'my_listing_tab_text', __( 'My Listing', 'directorist' ) );

			$listings   = $this->listings_query();
			$list_found = $listings->found_posts;

			$dashboard_tabs['dashboard_my_listings'] = array(
				'title'     => sprintf( '%1$s (%2$s)', $my_listing_tab_text, $list_found ),
				'content'   => Helper::get_template_contents( 'dashboard/tab-my-listings', [ 'dashboard' => $this ] ),
				'icon'	    => 'las la-list',
			);
		}

		if ( $my_profile_tab ) {
			$dashboard_tabs['dashboard_profile'] = array(
				'title'     => get_directorist_option('my_profile_tab_text', __('My Profile', 'directorist')),
				'icon'	    => 'las la-user',
				'content'   => Helper::get_template_contents( 'dashboard/tab-profile', [ 'dashboard' => $this ] ),
			);
		}

		if ( $fav_listings_tab ) {
			$dashboard_tabs['dashboard_fav_listings'] = array(
				'title'     => get_directorist_option('fav_listings_tab_text', __('Favorite Listings', 'directorist')),
				'content'   => Helper::get_template_contents( 'dashboard/tab-fav-listings', [ 'dashboard' => $this ] ),
				'icon'		=> 'las la-heart',
			);
		}

		$dashboard_tabs['dashboard_preferences'] = array(
			'title'     => __( 'Preferences', 'directorist' ),
			'content'   => Helper::get_template_contents( 'dashboard/tab-preferences', [ 'dashboard' => $this ] ),
			'icon'		=> 'las la-sliders-h',
		);

		return apply_filters( 'directorist_dashboard_tabs', $dashboard_tabs );
	}

	public function restrict_access_template() {
		$args = array(
			'dashboard'         => $this,
			'login_link'        => ATBDP_Permalink::get_login_page_link(),
			'registration_link' => ATBDP_Permalink::get_registration_page_link(),
		);
		return Helper::get_template_contents( 'dashboard/restrict-access', $args );
	}

	public function profile_pic_template() {
		Helper::get_template( 'dashboard/profile-pic', [ 'dashboard' => $this ] );
	}

	public function notice_template() {
		if ( isset($_GET['renew'] ) ) {
			$renew_token_expired = $_GET['renew'] == 'token_expired' ? true : false;
			$renew_succeed = $_GET['renew'] == 'success' ? true : false;
		}
		else {
			$renew_token_expired = $renew_succeed = false;
		}

		$args = array(
			'dashboard' => $this,
			'renew_token_expired' => $renew_token_expired,
			'renew_succeed' => $renew_succeed,
		);

		Helper::get_template( 'dashboard/notice', $args );
	}

	public function confirmation_text() {
		if ( ! isset( $_GET['notice'] ) ) {
			return;
		}

		$listing_id = isset( $_GET['listing_id'] ) ? absint( $_GET['listing_id'] ) : 0;
		if ( $listing_id && ! directorist_is_listing_post_type( $listing_id ) ) {
			return;
		}

		if ( get_post_status( $listing_id ) === 'publish' ) {
			$message = get_directorist_option(
				'publish_confirmation_msg',
				__( 'Congratulations! Your listing has been approved/published. Now it is publicly available.', 'directorist' )
			);
		} else {
			$message = get_directorist_option(
				'pending_confirmation_msg',
				__( 'Thank you for your submission. Your listing is being reviewed and it may take up to 24 hours to complete the review.', 'directorist' )
			);
		}

		return $message;
	}

	public function navigation_template() {
		Helper::get_template( 'dashboard/navigation', [ 'dashboard' => $this ] );
	}

	public function main_contents_template() {
		Helper::get_template( 'dashboard/main-contents', [ 'dashboard' => $this ] );
	}

	public function nav_buttons_template() {
		Helper::get_template( 'dashboard/nav-buttons', [ 'dashboard' => $this ] );
	}

	public function user_can_submit() {
		$display_submit_btn = get_directorist_option( 'submit_listing_button', 1 );

		if ( $display_submit_btn && 'general' != $this->user_type && 'become_author' != $this->user_type ) {
			return true;
		}
		else {
			return false;
		}
	}

	public function listing_row_template() {
		$args = array(
			'dashboard' => $this,
			'query'     => $this->current_listings_query,
		);
		Helper::get_template( 'dashboard/listing-row', $args );
	}

	public function display_title() {
		return self::$display_title;
	}

	public function render_shortcode( $atts ) {
		$atts = shortcode_atts( ['show_title' => ''], $atts );
		self::$display_title = ( $atts['show_title'] == 'yes' ) ? true : false;

		if (!is_user_logged_in()) {
			return $this->restrict_access_template();
		}

		return Helper::get_template_contents( 'dashboard-contents', [ 'dashboard' => $this ] );
	}

	public function can_renew() {
		// TODO: Status has been migrated, remove related code.
		// $post_id = get_the_ID();
		// $status  = get_post_meta( $post_id, '_listing_status', true );

		// if ( 'renewal' == $status || 'expired' == $status ) {
		// 	$can_renew = get_directorist_option( 'can_renew_listing' );
		// 	if ( $can_renew ) {
		// 		return true;
		// 	}
		// }

		if ( ! directorist_can_user_renew_listings() ) {
			return false;
		}

		$status = get_post_status( get_the_ID() );

		if ( $status !== 'expired' || ( $status === 'publish' && get_post_meta( get_the_ID(), '_listing_status', true ) !== 'renewal' ) ) {
			return false;
		}

		return true;
	}

	public function can_promote() {
		// TODO: Status has been migrated, remove related code.
		// $post_id = get_the_ID();
		// $status  = get_post_meta( $post_id, '_listing_status', true );
		// $featured_active = get_directorist_option( 'enable_featured_listing' );
		// $featured = get_post_meta( $post_id, '_featured', true );

		// if ( 'renewal' == $status || 'expired' == $status ) {
		// 	return false;
		// }

		if ( ! directorist_is_featured_listing_enabled() ) {
			return false;
		}

		$status = get_post_status( get_the_ID() );

		if ( $status === 'expired' || ( $status === 'publish' && get_post_meta( get_the_ID(), '_listing_status', true ) === 'renewal' ) ) {
			return false;
		}

		$is_featured = (bool) get_post_meta( get_the_ID(), '_featured', true );
		if ( $is_featured ) {
			return false;
		}

		return true;
	}

	public function get_renewal_link( $listing_id ) {
		if ( directorist_is_monetization_enabled() && directorist_is_featured_listing_enabled() ) {
			return ATBDP_Permalink::get_fee_renewal_checkout_page_link( $listing_id );
		}

		return ATBDP_Permalink::get_renewal_page_link( $listing_id );
	}

	public function get_action_dropdown_item() {
		$dropdown_items = apply_filters( 'directorist_dashboard_listing_action_items', [], $this );

		$post_id = get_the_ID();

		if ( $this->can_renew() ) {
			$renewal_url = add_query_arg( 'renew_from', 'dashboard', $this->get_renewal_link( $post_id ) );
			$dropdown_items['renew'] = array(
				'class'     => '',
				'data_attr' => '',
				'link'      => wp_nonce_url( $renewal_url, 'directorist_listing_renewal', 'token' ),
				'icon'      => directorist_icon( 'las la-hand-holding-usd', false ),
				'label'     => __( 'Renew', 'directorist' )
			);
		}

		if ( $this->can_promote() ) {
			$dropdown_items['promote'] = array(
				'class'			    => '',
				'data_attr'			=>	'',
				'link'				=>	ATBDP_Permalink::get_checkout_page_link( $post_id ),
				'icon'				=>  directorist_icon( 'las la-ad', false ),
				'label'				=>  __( 'Promote', 'directorist' )
			);
		}

		$dropdown_items['delete'] = array(
			'class'			    => '',
			'data_attr'			=>	'data-task="delete"',
			'link'				=>	'#',
			'icon'				=>  directorist_icon( 'las la-trash', false ),
			'label'				=>  __( 'Delete Listing', 'directorist' )
		);

		return apply_filters( 'directorist_dashboard_listing_action_items_end', $dropdown_items, $this );
	}
}