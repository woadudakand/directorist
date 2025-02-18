<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 8.0.13
 */
use \Directorist\Helper;

$columns = floor( 12 / $taxonomy->columns );

if ( '5' == $taxonomy->columns ) {
    $columns = $columns . '-5';
}

$taxonomy->atts['type']           = 'category';
$taxonomy->atts['directory_type'] = isset( $_GET['directory_type'] ) && ! empty( $_GET['directory_type'] ) ? $_GET['directory_type'] : '';
?>

<div id="directorist" class="atbd_wrapper directorist-w-100">
    <div class="<?php Helper::directorist_container_fluid(); ?>">
        <div class="directorist-categories" data-attrs="<?php echo esc_attr(wp_json_encode( $taxonomy->atts )); ?>">
            <?php
            /**
             * @since 5.6.6
             */
            do_action( 'atbdp_before_all_categories_loop', $taxonomy );
            ?>
            <?php if ( $categories ) : ?>
                <div class="<?php echo apply_filters( 'directorist_taxonomy_category_wrapper', Helper::directorist_row() . ' taxonomy-category-wrapper' ); ?>">
                    <?php foreach ($categories as $category) {
                        $cat_class      = $category['img'] ? ' directorist-categories__single--image' : '';
                        $img_src        = esc_url( $category['img'] );
                        $category_count = $category['term']->count ?? 0;

                        if ( $category_count === 0 ) {
                            $listing_count_text = esc_html( 'listing', 'directorist' );
                        } else {
                            $listing_count_text = sprintf(
                                _nx(
                                    'listing',
                                    'listings',
                                    $category_count,
                                    'number of listings',
                                    'directorist'
                                ),
                                number_format_i18n( $category_count )
                            );
                        }

                        $listing_count_text = sprintf( '%s <span class="directorist-category-term">%s</span>', $category['grid_count_html'], $listing_count_text );
                        ?>
                        <div class="<?php Helper::directorist_column( $columns ); ?>">
                            <div class="directorist-categories__single<?php echo esc_attr( $cat_class ); ?> directorist-categories__single--style-one" style="background-image: url('<?php echo $category['img'] ? esc_attr($img_src) : 'none'; ?>')">
                                <div class="directorist-categories__single__content">
                                    <?php if ($category['has_icon']) { ?>
                                        <div class="directorist-categories__single__icon">
                                            <?php directorist_icon( $category['icon_class'] ); ?>
                                        </div>
                                    <?php } ?>
                                    <a href="<?php echo esc_url($category['permalink']); ?>" class="directorist-categories__single__name">
                                        <?php echo esc_html($category['name']); ?>
                                    </a>
                                    <?php if ( $taxonomy->show_count ) { ?>
                                        <div class="directorist-categories__single__total">
                                            <?php echo wp_kses_post( $listing_count_text ); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php $taxonomy->pagination(); ?>
                </div>
            <?php else : ?>
                <p><?php esc_html_e( 'No Results found!', 'directorist' ); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
/**
 * @since 5.6.6
 */
do_action( 'atbdp_after_all_categories_loop' );
?>