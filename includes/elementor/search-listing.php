<?php
/**
 * @author AazzTech
 */

namespace AazzTech\Directorist\Elementor;

use Elementor\Controls_Manager;
use Directorist\Helper;

if ( ! defined( 'ABSPATH' ) ) exit;

class Directorist_Search_Listing extends Custom_Widget_Base {
    public function __construct( $data = [], $args = null ) {
        $this->az_name = __( 'Search Form', 'directorist' );
        $this->az_base = 'directorist_search_listing';
        parent::__construct( $data, $args );
    }

    private function az_listing_types() {
        $directories = directorist_get_directories();

        if ( is_wp_error( $directories ) || empty( $directories ) ) {
            return [];
        }

        return wp_list_pluck( $directories, 'name', 'slug' );
    }

    public function az_fields() {
        $fields = [
            [
                'mode'    => 'section_start',
                'id'      => 'sec_general',
                'label'   => __( 'General', 'directorist' ),
            ],
            [
                'type'      => Controls_Manager::SWITCHER,
                'id'        => 'show_subtitle',
                'label'     => __( 'Add Element Title & Subtitle?', 'directorist' ),
                'default'   => 'yes',
            ],
            [
                'type'      => Controls_Manager::CHOOSE,
                'id'        => 'title_subtitle_alignment',
                'label'     => __( 'Title/Subtitle Alignment', 'directorist' ),
                'options'   => [
                    'left'   => [
                        'title' => __( 'Left', 'directorist' ),
                        'icon'  => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'directorist' ),
                        'icon'  => 'fa fa-align-center',
                    ],
                    'right'  => [
                        'title' => __( 'Right', 'directorist' ),
                        'icon'  => 'fa fa-align-right',
                    ],
                ],
                'toggle'    => true,
                'selectors' => [
                    '{{WRAPPER}} .directorist-search-top__title' => 'text-align: {{VALUE}}',
                    '{{WRAPPER}} .directorist-search-top__subtitle' => 'text-align: {{VALUE}}',
                ],
                'condition' => [ 'show_subtitle' => [ 'yes' ] ],
            ],
            [
                'type'      => Controls_Manager::TEXTAREA,
                'id'        => 'title',
                'label'     => __( 'Search Form Title', 'directorist' ),
                'default'   => __( 'Search here', 'directorist' ),
                'condition' => [ 'show_subtitle' => [ 'yes' ] ],
            ],
            [
                'type'      => Controls_Manager::TEXTAREA,
                'id'        => 'subtitle',
                'label'     => __( 'Search Form Subtitle', 'directorist' ),
                'default'   => __( 'Find the best match of your interest', 'directorist' ),
                'condition' => [ 'show_subtitle' => [ 'yes' ] ],
            ],
            [
                'type'     => Controls_Manager::SELECT2,
                'id'       => 'type',
                'label'    => __( 'Directory Types', 'directorist' ),
                'multiple' => true,
                'options'  => $this->az_listing_types(),
                'condition' => directorist_is_multi_directory_enabled() ? '' : ['nocondition' => true],
            ],
            [
                'type'     => Controls_Manager::SELECT2,
                'id'       => 'default_type',
                'label'    => __( 'Default Directory Types', 'directorist' ),
                'options'  => $this->az_listing_types(),
                'condition' => directorist_is_multi_directory_enabled() ? '' : ['nocondition' => true],
            ],
            [
                'type'      => Controls_Manager::TEXT,
                'id'        => 'search_btn_text',
                'label'     => __( 'Search Button Label', 'directorist' ),
                'default'   => __( 'Search Listing', 'directorist' ),
            ],
            [
                'type'      => Controls_Manager::SWITCHER,
                'id'        => 'show_more_filter_btn',
                'label'     => __( 'Show More Search Field?', 'directorist' ),
                'default'   => 'yes',
            ],
            [
                'type'      => Controls_Manager::TEXT,
                'id'        => 'more_filter_btn_text',
                'label'     => __( 'More Search Field Button Label', 'directorist' ),
                'default'   => __( 'More Filters', 'directorist' ),
                'condition' => [ 'show_more_filter_btn' => [ 'yes' ] ],
            ],
            [
                'type'      => Controls_Manager::SWITCHER,
                'id'        => 'more_filter_reset_btn',
                'label'     => __( 'Show More Field Reset Button?', 'directorist' ),
                'default'   => 'yes',
                'condition' => [ 'show_more_filter_btn' => [ 'yes' ] ],
            ],
            [
                'type'      => Controls_Manager::TEXT,
                'id'        => 'more_filter_reset_btn_text',
                'label'     => __( 'More Field Reset Button Label', 'directorist' ),
                'default'   => __( 'Reset Filters', 'directorist' ),
                'condition' => [ 'more_filter_reset_btn' => 'yes', 'show_more_filter_btn' => 'yes' ],
            ],
            [
                'type'      => Controls_Manager::SWITCHER,
                'id'        => 'more_filter_search_btn',
                'label'     => __( 'Show More Field Search Button?', 'directorist' ),
                'default'   => 'yes',
                'condition' => [ 'show_more_filter_btn' => [ 'yes' ] ],
            ],
            [
                'type'      => Controls_Manager::TEXT,
                'id'        => 'more_filter_search_btn_text',
                'label'     => __( 'More Field Search Button Label', 'directorist' ),
                'default'   => __( 'Apply Filters', 'directorist' ),
                'condition' => [ 'more_filter_search_btn' => 'yes', 'show_more_filter_btn' => 'yes' ],
            ],
            [
                'type'      => Controls_Manager::SWITCHER,
                'id'        => 'user',
                'label'     => __( 'Show only for logged in user?', 'directorist' ),
                'default'   => 'no',
            ],
            [
                'mode' => 'section_end',
            ],
            [
                'mode'  => 'section_start',
                'id'    => 'sec_style',
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => __( 'Color', 'directorist' ),
                'condition' => [ 'show_subtitle' => [ 'yes' ] ],
            ],
            [
                'type'      => Controls_Manager::COLOR,
                'id'        => 'title_color',
                'label'     => __( 'Title', 'directorist' ),
                'default'   => '#51526e',
                'selectors' => [ '{{WRAPPER}} .directorist-search-top__title' => 'color: {{VALUE}}' ],
                'condition' => [ 'show_subtitle' => [ 'yes' ] ],
            ],
            [
                'type'      => Controls_Manager::COLOR,
                'id'        => 'subtitle_color',
                'label'     => __( 'Subtitle', 'directorist' ),
                'default'   => '#51526e',
                'selectors' => [ '{{WRAPPER}} .directorist-search-top__subtitle' => 'color: {{VALUE}}' ],
                'condition' => [ 'show_subtitle' => [ 'yes' ] ],
            ],
            [
                'mode' => 'section_end',
            ],
            [
                'mode'  => 'section_start',
                'id'    => 'sec_style_type',
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => __( 'Typography', 'directorist' ),
                'condition' => [ 'show_subtitle' => [ 'yes' ] ],
            ],
            [
                'mode'     => 'group',
                'type'     => \Elementor\Group_Control_Typography::get_type(),
                'id'       => 'title_typo',
                'label'    => __( 'Title', 'directorist' ),
                'selector' => '{{WRAPPER}} .directorist-search-top__title',
            ],
            [
                'mode'     => 'group',
                'type'     => \Elementor\Group_Control_Typography::get_type(),
                'id'       => 'subtitle_typo',
                'label'    => __( 'Subtitle', 'directorist' ),
                'selector' => '{{WRAPPER}} .directorist-search-top__subtitle',
            ],
            [
                'mode' => 'section_end',
            ],
        ];
        return $fields;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $atts = [
            'show_title_subtitle'   => $settings['show_subtitle'],
            'search_bar_title'      => $settings['title'],
            'search_bar_sub_title'  => $settings['subtitle'],
            'search_button_text'    => $settings['search_btn_text'],
            'more_filters_button'   => $settings['show_more_filter_btn'],
            'more_filters_text'     => $settings['more_filter_btn_text'],
            'reset_filters_button'  => $settings['more_filter_reset_btn'],
            'apply_filters_button'  => $settings['more_filter_search_btn'],
            'reset_filters_text'    => $settings['more_filter_reset_btn_text'],
            'apply_filters_text'    => $settings['more_filter_search_btn_text'],
            'logged_in_user_only'   => $settings['user'] ? $settings['user'] : 'no',
        ];

        if ( directorist_is_multi_directory_enabled() ) {
            if ( $settings['type'] ) {
                $atts['directory_type'] = implode( ',', $settings['type'] );
            }
            if ( $settings['default_type'] ) {
                $atts['default_directory_type'] = $settings['default_type'];
            }
        }

        $this->az_run_shortcode( 'directorist_search_listing', $atts );
    }
}