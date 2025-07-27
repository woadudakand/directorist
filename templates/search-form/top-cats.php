<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 7.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;
$category_align = ! empty( $searchform->atts['category_align'] ) ? $searchform->atts['category_align'] : 'center';

?>

<div class="directorist-listing-category-top">

    <h3><?php echo esc_html( $title ); ?></h3>

    <ul class="justify-content_<?php echo esc_attr( $category_align ); ?>">
        <?php foreach ( $top_categories as $cat ) : ?>

            <li>
                <a href="<?php echo esc_url( ATBDP_Permalink::atbdp_get_category_page( $cat ) ); ?>">
                    <?php directorist_icon( get_cat_icon( $cat->term_id ) ); ?>
                    <?php echo esc_html( $cat->name ); ?>
                </a>
            </li>

        <?php endforeach; ?>
    </ul>

</div>