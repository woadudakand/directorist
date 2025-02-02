<?php
$tag_label                   = get_directorist_option('tag_label',__('Tag','directorist'));
$address_label               = get_directorist_option('address_label',__('Address','directorist'));
$fax_label                   = get_directorist_option('fax_label',__('Fax','directorist'));
$email_label                 = get_directorist_option('email_label',__('Email','directorist'));
$website_label               = get_directorist_option('website_label',__('Website','directorist'));
$zip_label                   = get_directorist_option('zip_label',__('Zip','directorist'));
$price_range_placeholder = get_directorist_option('price_range_placeholder', __('Price Range', 'directorist'));
$currency = get_directorist_option('g_currency', 'USD');
$c_symbol = atbdp_currency_symbol($currency);
?>
<div class="atbdp search-area default-ad-search">
    <form action="<?php echo ATBDP_Permalink::get_search_result_page_link(); ?>">
        <?php if(!empty($search_by_text_field)) { ?>
            <div class="form-group">
                <input type="text" name="q" placeholder="<?php _e('What are you looking for?','directorist');?>" value="<?php echo !empty($_GET['q']) ? $_GET['q'] : ''; ?>" class="form-control">
            </div><!-- ends: .form-group -->
        <?php } ?>
        <?php if(!empty($search_by_location)) {
                if('map_api' == $location_source) {
                    $select_listing_map = get_directorist_option('select_listing_map','google');
                    wp_enqueue_script('atbdp-geolocation-widget');
                    wp_localize_script('atbdp-geolocation-widget', 'adbdp_geolocation', array('select_listing_map'=> $select_listing_map));
                    $geo_loc = ('google' == $select_listing_map) ? '<span class="atbd_get_loc_wid la la-crosshairs"></span>' : '<span class="atbd_get_loc_wid la la-crosshairs"></span>';
                    ?>
                    <div class="form-group">
                        <div class="position-relative">
                            <input type="text" name="address" autocomplete="off" id="address_widget" value="<?php echo !empty($_GET['address']) ? $_GET['address'] : ''; ?>" placeholder="<?php _e('Location','directorist');?>"
                                   class="form-control widget-location-name"><?php echo $geo_loc; ?>
                        </div>
                        <div id="address_widget_result">
                        </div>
                        <input type="hidden" id="cityLat" name="cityLat" value="" />
                        <input type="hidden" id="cityLng" name="cityLng" value="" />
                    </div><!-- ends: .form-group -->
                <?php } else { ?>
            <div class="form-group">
                <?php
                bdas_dropdown_terms( array(
                    'show_option_none'   => __( 'Select a Location', 'directorist' ),
                    'option_none_value'  => -1,
                    'taxonomy'           => 'at_biz_dir-location',
                    'name' 			     => 'in_loc',
                    'class'              => 'form-control bdas-location-search select-basic',
                    'orderby'            => 'date',
                    'order'              => 'ASC',
                    'selected'           => isset( $_GET['in_loc'] ) ? $_GET['in_loc'] : -1,
                ) );
                ?>
            </div>
        <?php }
        } ?>
        <?php if(!empty($search_by_category)) {?>
            <div class="form-group">
                <?php
                bdas_dropdown_terms( array(
                    'show_option_none'   => __( 'Select a category', 'directorist' ),
                    'option_none_value'  => -1,
                    'taxonomy'           => 'at_biz_dir-category',
                    'name' 			     => 'in_cat',
                    'class'              => 'form-control bdas-category-search select-basic',
                    'orderby'            => 'date',
                    'order'              => 'ASC',
                    'selected'           => isset( $_GET['in_cat'] ) ? $_GET['in_cat'] : -1,
                ) );
                ?>
            </div>
        <?php } ?>
        <?php if('map_api' == $location_source && !empty($search_by_radius)) { ?>
        <div class="form-group">
            <div class="atbdpr-range rs-primary">
                <div class="atbdp-labels">
                    <label><?php _e('Distance:','directorist'); ?></label>
                    <span class="atbdpr_amount"></span>
                </div>
                <div class="atbd_slider-range-wrapper">
                    <div class="atbd_slider-range"></div>
                    <input type="hidden" id="atbd_rs_value" name="miles" value="<?php echo !empty($_GET['miles']) ? $_GET['miles'] : 0 ; ?>">
                </div>
            </div>
        </div>
        <?php } ?>
        <?php if(!empty($search_by_custom_fields)) { ?>
            <div id="atbdp-custom-fields-search" class="atbdp-custom-fields-search">
                <?php do_action( 'wp_ajax_atbdp_custom_fields_search', isset( $_GET['in_cat'] ) ? $_GET['in_cat'] : 0 ); ?>
            </div>
        <?php } ?>
        <?php if(!empty($search_by_price)) {?>

            <div class="form-group ">
                <label><?php _e( 'Price Range', 'directorist' ); ?></label>
                <div class="price_ranges">
                    <div>
                        <input type="text" name="price[0]" class="form-control" placeholder="<?php _e( 'min', 'directorist' ); ?>" value="<?php if( isset( $_GET['price'] ) ) echo esc_attr( $_GET['price'][0] ); ?>">
                    </div>
                    <div>
                        <input type="text" name="price[1]" class="form-control" placeholder="<?php _e( 'max', 'directorist' ); ?>" value="<?php if( isset( $_GET['price'] ) ) echo esc_attr( $_GET['price'][1] ); ?>">
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if(!empty($search_by_price_range)) {?>
            <div class="form-group">
                <div class="select-basic">
                    <select name="price_range" class="form-control">
                        <option value="none"><?php echo esc_attr($price_range_placeholder); ?></option>
                        <option value="skimming" <?php if(!empty($_GET['price_range']) && 'skimming' == $_GET['price_range']) { echo 'selected';}?>><?php echo __('Ultra High ', 'directorist').'('.$c_symbol,$c_symbol,$c_symbol,$c_symbol.')'; ?></option>
                        <option value="moderate" <?php if(!empty($_GET['price_range']) && 'moderate' == $_GET['price_range']) { echo 'selected';}?>><?php echo __('Expensive ', 'directorist').'('.$c_symbol,$c_symbol,$c_symbol.')'; ?></option>
                        <option value="economy" <?php if(!empty($_GET['price_range']) && 'economy' == $_GET['price_range']) { echo 'selected';}?>><?php echo __('Moderate ', 'directorist').'('.$c_symbol,$c_symbol.')'; ?></option>
                        <option value="bellow_economy" <?php if(!empty($_GET['price_range']) && 'bellow_economy' == $_GET['price_range']) { echo 'selected';}?> ><?php echo __('Cheap ', 'directorist').'('.$c_symbol.')'; ?></option>
                    </select>
                </div>
            </div><!-- ends: .form-group -->
        <?php } ?>
        <?php if(!empty($search_by_open_now) && in_array( 'directorist-business-hours/bd-business-hour.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {?>
            <div class="check-btn">
                <div class="btn-checkbox active-color-secondary">
                    <label>
                        <input type="checkbox" name="open_now" value="open_now" <?php if(!empty($_GET['open_now']) && 'open_now' == $_GET['open_now']) { echo 'checked';}?>><span class="text-success"><i
                                    class="fa fa-clock-o"></i> Open Now</span>
                    </label>
                </div>
            </div>
        <?php } ?>
        <?php if(!empty($search_by_website)) { ?>
            <div class="form-group">
                <input type="text" name="website" placeholder="<?php echo !empty($website_label) ? $website_label : __('Website','directorist');?>" value="<?php echo !empty($_GET['website']) ? $_GET['website'] : ''; ?>" class="form-control">
            </div><!-- ends: .form-group -->
        <?php } ?>
        <?php if(!empty($search_by_email)) { ?>
            <div class="form-group">
                <input type="text" name="email" placeholder="<?php echo !empty($email_label) ? $email_label : __('Email','directorist');?>" value="<?php echo !empty($_GET['email']) ? $_GET['email'] : ''; ?>" class="form-control">
            </div><!-- ends: .form-group -->
        <?php } ?>
        <?php if(!empty($search_by_phone)) { ?>
            <div class="form-group">
                <input type="text" name="phone" placeholder="<?php _e('Phone Number','directorist');?>" value="<?php echo !empty($_GET['phone']) ? $_GET['phone'] : ''; ?>" class="form-control">
            </div><!-- ends: .form-group -->
        <?php } ?>
        <?php if(!empty($search_by_zip_code)) { ?>
            <div class="form-group">
                <div class="position-relative">
                    <input type="text" name="zip_code" placeholder="<?php echo !empty($zip_label) ? $zip_label : __('zip','directorist');?>"
                           value="<?php echo !empty($_GET['zip_code']) ? $_GET['zip_code'] : ''; ?>" class="form-control">
                </div>
            </div><!-- ends: .form-group -->
        <?php } ?>
        <?php if(!empty($search_by_tag)) {
            $terms = get_terms(ATBDP_TAGS);
            ?>

            <div class="form-group filter-checklist">
                <label><?php  _e('Filter by Tags','directorist');?></label>
                <div class="checklist-items">
                    <?php
                    if(!empty($terms)) {
                        foreach($terms as $term) {
                            ?>
                            <div class="custom-control custom-checkbox checkbox-outline checkbox-outline-primary">
                                <input type="checkbox" class="custom-control-input" id="<?php echo $term->term_id;?>" name="in_tag[]" value="<?php echo $term->term_id;?>" <?php if(!empty($_GET['in_tag']) && in_array($term->term_id,$_GET['in_tag'])) { echo "checked";}?>>
                                <span class="check--select"></span>
                                <label class="custom-control-label" for="<?php echo $term->term_id;?>"><?php echo $term->name;?></label>
                            </div>
                        <?php } } ?>
                </div>
            </div><!-- ends: .filter-checklist -->
        <?php } ?>
        <?php if(!empty($search_by_review)) { ?>
            <div class="form-group filter-checklist">
                <label><?php _e('Filter by Ratings','directorist'); ?></label>
                <div class="sort-rating">
                    <div class="custom-control custom-checkbox checkbox-outline checkbox-outline-primary">
                        <input type="radio" value="5" name="search_by_rating" class="custom-control-input" id="customCheck7" <?php if(!empty($_GET['search_by_rating']) && '5' == $_GET['search_by_rating']) { echo 'checked';}?>>
                        <span class="radio--select"></span>
                        <label class="custom-control-label" for="customCheck7">
                            <span class="active"><i class="fa fa-star"></i></span>
                            <span class="active"><i class="fa fa-star"></i></span>
                            <span class="active"><i class="fa fa-star"></i></span>
                            <span class="active"><i class="fa fa-star"></i></span>
                            <span class="active"><i class="fa fa-star"></i></span>
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox checkbox-outline checkbox-outline-primary">
                        <input type="radio" value="4" name="search_by_rating" class="custom-control-input" id="customCheck8" <?php if(!empty($_GET['search_by_rating']) && '4' == $_GET['search_by_rating']) { echo 'checked';}?>>
                        <span class="radio--select"></span>
                        <label class="custom-control-label" for="customCheck8">
                            <span class="active"><i class="fa fa-star"></i></span>
                            <span class="active"><i class="fa fa-star"></i></span>
                            <span class="active"><i class="fa fa-star"></i></span>
                            <span class="active"><i class="fa fa-star"></i></span>
                            <span><i class="fa fa-star"></i></span>
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox checkbox-outline checkbox-outline-primary">
                        <input type="radio" value="3" name="search_by_rating" class="custom-control-input" id="customCheck9" <?php if(!empty($_GET['search_by_rating']) && '3' == $_GET['search_by_rating']) { echo 'checked';}?>>
                        <span class="radio--select"></span>
                        <label class="custom-control-label" for="customCheck9">
                            <span class="active"><i class="fa fa-star"></i></span>
                            <span class="active"><i class="fa fa-star"></i></span>
                            <span class="active"><i class="fa fa-star"></i></span>
                            <span><i class="fa fa-star"></i></span>
                            <span><i class="fa fa-star"></i></span>
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox checkbox-outline checkbox-outline-primary">
                        <input type="radio" value="2" name="search_by_rating" class="custom-control-input" id="customCheck10" <?php if(!empty($_GET['search_by_rating']) && '2' == $_GET['search_by_rating']) { echo 'checked';}?>>
                        <span class="radio--select"></span>
                        <label class="custom-control-label" for="customCheck10">
                            <span class="active"><i class="fa fa-star"></i></span>
                            <span class="active"><i class="fa fa-star"></i></span>
                            <span><i class="fa fa-star"></i></span>
                            <span><i class="fa fa-star"></i></span>
                            <span><i class="fa fa-star"></i></span>
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox checkbox-outline checkbox-outline-primary">
                        <input type="radio" value="1" name="search_by_rating" class="custom-control-input" id="customCheck11" <?php if(!empty($_GET['search_by_rating']) && '1' == $_GET['search_by_rating']) { echo 'checked';}?>>
                        <span class="radio--select"></span>
                        <label class="custom-control-label" for="customCheck11">
                            <span class="active"><i class="fa fa-star"></i></span>
                            <span><i class="fa fa-star"></i></span>
                            <span><i class="fa fa-star"></i></span>
                            <span><i class="fa fa-star"></i></span>
                            <span><i class="fa fa-star"></i></span>
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox checkbox-outline checkbox-outline-primary">
                        <input type="radio" name="search_by_rating" value="0" class="custom-control-input" id="customCheck12" <?php if(!empty($_GET['search_by_rating']) && '0' == $_GET['search_by_rating']) { echo 'none';}?>>
                        <span class="radio--select"></span>
                        <label class="custom-control-label" for="customCheck12">
                            <span><i class="fa fa-star"></i></span>
                            <span><i class="fa fa-star"></i></span>
                            <span><i class="fa fa-star"></i></span>
                            <span><i class="fa fa-star"></i></span>
                            <span><i class="fa fa-star"></i></span>
                        </label>
                    </div>
                </div>
            </div><!-- ends: .filter-checklist -->
        <?php } ?>
        <div class="form-group submit_btn">
            <button type="reset" class="btn btn-default"><?php _e( 'Reset ', 'directorist' ); ?></button>
            <button type="submit" class="btn btn-primary btn-icon icon-right"><?php _e( 'Search Listings', 'directorist' ); ?></button>
        </div>
    </form><!-- ends: form -->
</div><!-- ends: .default-ad-search -->