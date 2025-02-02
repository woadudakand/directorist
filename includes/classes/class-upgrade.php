<?php
// it handles directorist upgrade
class ATBDP_Upgrade{

    public function __construct()
    {
        add_action('wp_ajax_atbdp_upgrade_old_pages', array($this, 'upgrade_old_pages'));
        add_action('admin_init', array( $this, 'check_need_to_upgrade_database' ), 100 );
        add_action('admin_notices', array( $this, 'upgrade_notice' ), 100 );
    }

    public function upgrade_notice() {
        $user_id = get_current_user_id();
        if (!current_user_can('administrator')) return false;
        $update_link = admin_url().'edit.php?post_type=at_biz_dir&page=aazztech_settings#_pages';

        //check the version of Directorist
        $directorist_header = get_plugins( '/' . explode( '/', plugin_basename( __FILE__ ) )[0] );
        $current_version = '';
        foreach ($directorist_header as $key => $val){
            $current_version = $val['Version'];
        }

        if ('true' == get_user_meta( $user_id, '_atbdp_shortcode_regenerate_notice',true)){
            $link_regen = '<a href="'.$update_link.'">Generate Pages</a>';
            //update notice for single category and location page.
            if ( true != get_user_meta( $user_id, '_atbdp_location_category_page',true )){
                echo '<div id="message" class="notice notice-info" style="display: flex; background: #f7bdc7;  justify-content: space-between;"><p>';
                printf(__('Directorist plugin requires two new pages with the [directorist_category] and [directorist_location] shortcodes to function single category and location pages properly. You can create these pages yourself or let the plugin do this for you.<br> %s', 'directorist'), $link_regen);
                echo '</p><p><a href="?location-category-page">Hide</a></p></div>';
            }
        }

     /*   $link = '<a href="'.$update_link.'">please replace</a>';
        $is_generated_pages = get_user_meta( $user_id, '_atbdp_shortcode_regenerate_notice',true );
        if (empty($is_generated_pages) && (!function_exists('direo_setup') && !function_exists('dlist_setup') && !function_exists('dservice_setup') && !function_exists('drestaurant_setup'))){
            echo '<div id="message" class="notice notice-info" style="display: flex; background: #ffc733;  justify-content: space-between;"><p>';
            printf(__('If you are an old user of the %s plugin, %s your shortcodes as we have restructured our shortcodes.', 'directorist'), ATBDP_NAME, $link);
            echo '</p><p><a href="?my-plugin-dismissed">Hide</a></p></div>';
        }*/
    }

    public function check_need_to_upgrade_database( ){
        $user_id = get_current_user_id();
        if ( isset( $_GET['my-plugin-dismissed'] ) ){
            update_user_meta( $user_id, '_atbdp_shortcode_regenerate_notice', 'new_true' );
        }if(isset( $_POST['shortcode-updated'] )){
            update_option('atbdp_pages_version', 0);
        }
        if ( isset( $_GET['location-category-page'] ) ){
            update_user_meta( $user_id, '_atbdp_location_category_page', 'true' );
        }
    }
    /**
     * It upgrades old pages and make them compatible with new shortcodes
     */
    public function upgrade_old_pages()
    {
        update_option('atbdp_pages_version', 0);
        wp_send_json_success(__('Congratulations! All old pages have been updated successfully', 'directorist'));
    }
}