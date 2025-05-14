<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 8.0.2
 */

if (!defined('ABSPATH')) exit;

$min_distance     		= 0;
$default_min_distance   = 0;
$max_distance     		= $data['max_radius_distance'] ?? 0;
$default_max_distance   = $data['max_radius_distance'] ?? 100;
$default_distance 		= $data['default_radius_distance'] ?? 50;
$value            		= ! empty( $_REQUEST['miles'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['miles'] ) ) : $min_distance . '-' . $max_distance;

if ( ! empty( $_REQUEST['miles'] ) ) {
	$distance =	directorist_get_distance_range( $_REQUEST['miles'] );
	$min_distance = $distance['min'];
	$max_distance = $distance['max'];
}
?>

<div class="directorist-search-field directorist-search-field-radius_search <?php echo esc_attr( $empty_label ); ?>">
	<?php if (!empty($data['label'])) : ?>
		<label class="directorist-search-field__label"><?php echo esc_html($data['label']); ?></label>
	<?php endif; ?>
	<div class="directorist-custom-range-slider">
		<div class="directorist-custom-range-slider__range__wrap">
			<span class="directorist-custom-range-slider__range__show"></span>
			<span><?php echo esc_attr($searchform->range_slider_unit($data)); ?></span>
		</div>
		<div class="directorist-custom-range-slider__slide" default-value="<?php echo esc_attr($default_distance); ?>" max-value="<?php echo esc_attr($default_max_distance); ?>" min-value="<?php echo esc_attr($default_min_distance); ?>"></div>
		<div class="directorist-custom-range-slider__wrap">
			<input type="hidden" placeholder="Min" value="<?php echo esc_attr($min_distance); ?>" class="directorist-custom-range-slider__radius directorist-custom-range-slider__value__min">
			<input type="hidden" placeholder="Max" value="<?php echo esc_attr($max_distance); ?>" class="directorist-custom-range-slider__radius directorist-custom-range-slider__value__max">
			<input type="hidden" name="miles" class="directorist-custom-range-slider__range" value="<?php echo esc_attr( $value ); ?>">
		</div>
	</div>

</div>