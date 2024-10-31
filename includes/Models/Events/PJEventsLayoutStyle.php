<?php
/**
 * PickleJar Live for Artists & Venues  - Events Layout Style.
 *
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar\Models\Events;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Picklejar\Api\Callbacks\PJEventsPageCallbacks;
use Picklejar\Base\BaseController;

/**
 * Class PJEventsLayoutStyle
 *
 * @since 1.0.0
 * @access public
 * @package Picklejar\Models\Events
 * @extends BaseController
 */
class PJEventsLayoutStyle extends BaseController {
	/**
	 * Variable Picklejar Event Settings Data
	 *
	 * @since 1.0.0
	 * @var array
	 * @access public
	 */
	public $pj_event_settings_data = array();

	/**
	 * Variable Picklejar Event Settings Data
	 *
	 * @since 1.0.0
	 * @access public
	 * @var PJEventsPageCallbacks
	 */
	private $callbacks;

	/**
	 * Variable AvatarProfile Border Color
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $avatar_profile_border_color;

	/**
	 * Variable AvatarProfile Text Color
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $avatar_profile_text_color;

	/**
	 * Variable Container Background Image
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $container_background_image;

	/**
	 * Variable Date Background Color
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $date_background_color;

	/**
	 * Variable Date Text Color
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $date_text_color;

	/**
	 * Variable Items To Show
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $items_to_show;

	/**
	 * Variable Layout
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $layout;

	/**
	 * Variable Location Text Color
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $location_text_color;

	/**
	 * Variable Page Full Width
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $page_full_width;


	/**
	 * Variable Title Alignment
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $title_alignment;

	/**
	 * Variant Title Label
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $title_label;

	/**
	 * Variant Title Color
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $title_color;

	/**
	 * Variant Slider Control Color
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $slider_control_color;

	/**
	 * Variant Post Data
	 *
	 * @since 1.0.0
	 * @access private
	 * @var mixed|null
	 */
	private $post_data;

	/**
	 * Function Constructor
	 *
	 * @param mixed $post_data Post Data.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $post_data = null ) {
		parent::__construct();
		$this->post_data = $post_data;
		$this->callbacks = new PJEventsPageCallbacks();
		$this->init( $post_data );
		$this->render_fields();
	}

	/**
	 * Function Init
	 *
	 * @param mixed $post_data Post Data.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function init( $post_data ) {
		$global_data           = $this->callbacks->pj_get_events_list_global_configuration_data();
		$layout_style_settings = $this->callbacks->get_style_settings_group();
		$layout_style          = array();

		if ( isset( $global_data[ $layout_style_settings ] ) ) {
			$layout_style = $global_data[ $layout_style_settings ];
		}

		$primary_color              = $this->global_configuration['color']['primary'];
		$dark_color                 = $this->global_configuration['color']['secondary'];
		$items_to_show              = 12;
		$container_background_image = null;
		if ( ! empty( $layout_style['container_background_image'] ) ) {
			$container_background_image = wp_get_attachment_image_src( $layout_style['container_background_image'], 'full' )[0];
		}

		$this->avatar_profile_border_color = ! empty( $layout_style['avatar_profile_border_color'] ) ? $layout_style['avatar_profile_border_color'] : $primary_color;
		$this->avatar_profile_text_color   = ! empty( $layout_style['avatar_profile_text_color'] ) ? $layout_style['avatar_profile_text_color'] : $primary_color;
		$this->container_background_image  = $container_background_image;
		$this->date_background_color       = ! empty( $layout_style['date_background_color'] ) ? $layout_style['date_background_color'] : $primary_color;
		$this->date_text_color             = ! empty( $layout_style['date_text_color'] ) ? $layout_style['date_text_color'] : $dark_color;
		$this->layout                      = ! empty( $layout_style['layout'] ) ? $layout_style['layout'] : 'grid';
		$this->location_text_color         = ! empty( $layout_style['location_text_color'] ) ? $layout_style['location_text_color'] : $primary_color;
		$this->items_to_show               = ! empty( $layout_style['layout'] ) && $layout_style['items_to_show'] > 0 ? $layout_style['items_to_show'] : $items_to_show;
		$this->page_full_width             = ( ! empty( $data['page_full_width'] ) && ! $this->is_elementor_active ) ? $layout_style['page_full_width'] : null;
		$this->slider_control_color        = ! empty( $layout_style['slider_control_color'] ) ? $layout_style['slider_control_color'] : $primary_color;
		$this->title_alignment             = ! empty( $layout_style['title_alignment'] ) ? $layout_style['title_alignment'] : null;
		$this->title_label                 = ! empty( $global_data['title_label'] ) ? $global_data['title_label'] : null;
		$this->title_color                 = ! empty( $layout_style['title_color'] ) ? $layout_style['title_color'] : $primary_color;

		if ( ! empty( $post_data ) ) {
			$layout_style = $post_data[ $layout_style_settings ];
			$this->update_data( $post_data, $layout_style );
		}
	}

	/**
	 * Function Update Data
	 *
	 * @param array $global_data Global Data.
	 * @param array $layout_style Layout Style.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access private
	 */
	private function update_data(
		$global_data,
		$layout_style
	) {
		if ( ! empty( $layout_style['avatar_profile_border_color'] ) ) {
			$this->avatar_profile_border_color = $layout_style['avatar_profile_border_color'];
		}

		if ( ! empty( $layout_style['avatar_profile_text_color'] ) ) {
			$this->avatar_profile_text_color = $layout_style['avatar_profile_text_color'];
		}

		if ( ! empty( $global_data['layout'] ) ) {
			$this->layout = $global_data['layout'];
		}

		if ( ! empty( $layout_style['container_background_image'] ) ) {
			$this->container_background_image = wp_get_attachment_image_src( $layout_style['container_background_image'], 'full' )[0];
		}

		if ( ! empty( $layout_style['date_background_color'] ) ) {
			$this->date_background_color = $layout_style['date_background_color'];
		}

		if ( ! empty( $layout_style['date_text_color'] ) ) {
			$this->date_text_color = $layout_style['date_text_color'];
		}

		if ( ! empty( $layout_style['layout'] ) ) {
			$this->layout = $layout_style['layout'];
		}

		if ( ! empty( $layout_style['location_text_color'] ) ) {
			$this->location_text_color = $layout_style['location_text_color'];
		}

		if ( ! empty( $layout_style['items_to_show'] ) ) {
			$this->items_to_show = $layout_style['items_to_show'];
		}

		if ( ! ( empty( $data['page_full_width'] ) && $this->is_elementor_active ) ) {
			$this->page_full_width = $layout_style['page_full_width'];
		}

		if ( ! empty( $layout_style['slider_control_color'] ) ) {
			$this->slider_control_color = $layout_style['slider_control_color'];
		}

		if ( ! empty( $layout_style['title_alignment'] ) ) {
			$this->title_alignment = $layout_style['title_alignment'];
		}

		if ( ! empty( $global_data['title_label'] ) ) {
			$this->title_label = $global_data['title_label'];
		}

		if ( ! empty( $layout_style['title_color'] ) ) {
			$this->title_color = $layout_style['title_color'];
		}
	}

	/**
	 * Function Render Fields
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function render_fields() {
		if ( ! $this->is_elementor_active ) {
			$this->pj_event_settings_data['title_label']     = array(
				'label'          => 'Title Label',
				'label_for'      => 'title_label',
				'name'           => 'title_label',
				'option_name'    => $this->callbacks->get_option_name(),
				'class'          => 'picklejar-form-control picklejar-d-column pj-title-label pj-title-label-events',
				'type'           => 'text',
				'wrapper_before' => '<div class="picklejar-form-control">',
				'wrapper_after'  => '</div>',
				'callback'       => 'text_field',
			);
			$this->pj_event_settings_data['title_alignment'] = array(
				'label'             => 'Title Alignment',
				'label_for'         => 'title_alignment',
				'name'              => 'title_alignment',
				'option_name'       => $this->callbacks->get_option_name(),
				'option_name_array' => $this->callbacks->get_style_settings_group(),
				'class'             => 'picklejar-form-control picklejar-d-column pj-title-alignment pj-title-alignment-events',
				'type'              => 'alignment',
				'wrapper_before'    => '<div class="picklejar-form-control">',
				'wrapper_after'     => '</div>',
				'callback'          => 'text_field',
			);

			$this->pj_event_settings_data['title_color'] = array(
				'label'             => 'Title Color',
				'label_for'         => 'title_color',
				'name'              => 'title_color',
				'option_name'       => $this->callbacks->get_option_name(),
				'option_name_array' => $this->callbacks->get_style_settings_group(),
				'class'             => 'picklejar-form-control color pj-title-color',
				'type'              => 'color',
				'wrapper_before'    => '<div class="picklejar-form-control color">',
				'wrapper_after'     => '</div>',
				'default_value'     => $this->title_color,
				'callback'          => 'text_field',
			);
		}

		$this->pj_event_settings_data['date_background_color'] = array(
			'label'             => 'Date Container',
			'label_for'         => 'date_background_color',
			'name'              => 'date_background_color',
			'option_name'       => $this->callbacks->get_option_name(),
			'option_name_array' => $this->callbacks->get_style_settings_group(),
			'class'             => 'picklejar-form-control color pj-date-background-color',
			'type'              => 'color',
			'wrapper_before'    => '<div class="picklejar-form-control color">',
			'wrapper_after'     => '</div>',
			'default_value'     => $this->date_background_color,
			'callback'          => 'text_field',
		);

		$this->pj_event_settings_data['date_text_color'] = array(
			'label'             => 'Date Text',
			'label_for'         => 'date_text_color',
			'name'              => 'date_text_color',
			'option_name'       => $this->callbacks->get_option_name(),
			'option_name_array' => $this->callbacks->get_style_settings_group(),
			'class'             => 'picklejar-form-control color pj-date-text-color',
			'type'              => 'color',
			'wrapper_before'    => '<div class="picklejar-form-control color">',
			'wrapper_after'     => '</div>',
			'default_value'     => $this->date_text_color,
			'callback'          => 'text_field',
		);

		$this->pj_event_settings_data['avatar_profile_border_color'] = array(
			'label'             => 'Profile Border Color',
			'label_for'         => 'avatar_profile_border_color',
			'name'              => 'avatar_profile_border_color',
			'option_name'       => $this->callbacks->get_option_name(),
			'option_name_array' => $this->callbacks->get_style_settings_group(),
			'class'             => 'picklejar-form-control color pj-avatar-profile-border-color',
			'type'              => 'color',
			'wrapper_before'    => '<div class="picklejar-form-control color">',
			'wrapper_after'     => '</div>',
			'default_value'     => $this->avatar_profile_border_color,
			'callback'          => 'text_field',
		);

		$this->pj_event_settings_data['avatar_profile_text_color'] = array(
			'label'             => 'Profile Text',
			'label_for'         => 'avatar_profile_text_color',
			'name'              => 'avatar_profile_text_color',
			'option_name'       => $this->callbacks->get_option_name(),
			'option_name_array' => $this->callbacks->get_style_settings_group(),
			'class'             => 'picklejar-form-control color pj-avatar-profile-text-color',
			'type'              => 'color',
			'wrapper_before'    => '<div class="picklejar-form-control color">',
			'wrapper_after'     => '</div>',
			'default_value'     => $this->avatar_profile_text_color,
			'callback'          => 'text_field',
		);

		$this->pj_event_settings_data['location_text_color'] = array(
			'label'             => 'Location Text',
			'label_for'         => 'location_text_color',
			'name'              => 'location_text_color',
			'option_name'       => $this->callbacks->get_option_name(),
			'option_name_array' => $this->callbacks->get_style_settings_group(),
			'class'             => 'picklejar-form-control color pj-location-text-color',
			'type'              => 'color',
			'wrapper_before'    => '<div class="picklejar-form-control color">',
			'wrapper_after'     => '</div>',
			'default_value'     => $this->location_text_color,
			'callback'          => 'text_field',
		);

		$this->pj_event_settings_data['slider_control_color'] = array(
			'label'             => 'Slider Control Color',
			'label_for'         => 'slider_control_color',
			'name'              => 'slider_control_color',
			'option_name'       => $this->callbacks->get_option_name(),
			'option_name_array' => $this->callbacks->get_style_settings_group(),
			'class'             => 'picklejar-form-control color pj-slider-control-color',
			'type'              => 'color',
			'wrapper_before'    => '<div class="picklejar-form-control color picklejar-slider-form-control">',
			'wrapper_after'     => '</div>',
			'default_value'     => $this->slider_control_color,
			'callback'          => 'text_field',
		);

		if ( ! $this->is_elementor_active ) {
			$this->pj_event_settings_data['page_full_width'] = array(
				'label'             => 'Page Full Width',
				'label_for'         => 'page_full_width',
				'name'              => 'page_full_width',
				'option_name'       => $this->callbacks->get_option_name(),
				'option_name_array' => $this->callbacks->get_style_settings_group(),
				'class'             => 'picklejar-form-control pj-page-full-width picklejar-ui-toggle',
				'type'              => 'toggle',
				'wrapper_before'    => '<div class="picklejar-form-control color">',
				'wrapper_after'     => '</div>',
				'default_value'     => $this->page_full_width,
				'callback'          => 'text_field',
			);
		}
		$this->pj_event_settings_data['items_to_show'] = array(
			'label'             => 'Items to show',
			'label_for'         => 'items_to_show',
			'name'              => 'items_to_show',
			'option_name'       => $this->callbacks->get_option_name(),
			'option_name_array' => $this->callbacks->get_style_settings_group(),
			'class'             => 'picklejar-form-control picklejar-d-column picklejar-d-flex pj-items-to-show',
			'type'              => 'number',
			'default_value'     => $this->items_to_show,
			'callback'          => 'text_field',
		);

		if ( ! $this->is_elementor_active ) {
			$this->pj_event_settings_data['container_background_image'] = array(
				'label'             => 'Background Image',
				'label_for'         => 'container_background_image',
				'name'              => 'container_background_image',
				'option_name'       => $this->callbacks->get_option_name(),
				'option_name_array' => $this->callbacks->get_style_settings_group(),
				'class'             => 'picklejar-d-flex picklejar-d-column pj-background-image',
				'type'              => 'image',
				'placeholder'       => 'eg. picklejar-btn-primary',
				'multiple'          => false,
				'value'             => isset( $this->update_post_value['picklejar-btn-primary'] ) ? $this->update_post_value['image'] : '',
				'callback'          => 'img_field',
			);
		}

		$this->pj_event_settings_data['layout'] = array(
			'label'             => 'Select Layout Design',
			'label_for'         => 'layout',
			'name'              => 'layout',
			'option_name'       => $this->callbacks->get_option_name(),
			'option_name_array' => $this->callbacks->get_style_settings_group(),
			'class'             => 'picklejar-d-flex picklejar-d-column',
			'type'              => 'pj_events_layout_input',
			'icon_list'         => $this->callbacks->template_layouts,
			'callback'          => 'pj_events_layout_input',
			'title'             => 'Events Layout',
		);
	}

	/**
	 * Function Get Picklejar Event Inputs Settings
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_pj_event_inputs_settings() {
		return $this->pj_event_settings_data;
	}

	/**
	 * Function Get Picklejar Event Settings Data
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 */
	public function get_pj_event_settings_data() {
		return array(
			'avatar_profile_border_color' => $this->avatar_profile_border_color,
			'avatar_profile_text_color'   => $this->avatar_profile_text_color,
			'container_background_image'  => $this->pj_elementor_enabled_clear_parameter( $this->container_background_image ),
			'date_background_color'       => $this->date_background_color,
			'date_text_color'             => $this->date_text_color,
			'location_text_color'         => $this->location_text_color,
			'items_to_show'               => $this->items_to_show,
			'layout'                      => $this->layout,
			'page_full_width'             => $this->page_full_width,
			'slider_control_color'        => $this->slider_control_color,
			'title_alignment'             => $this->title_alignment,
			'title_label'                 => $this->pj_elementor_enabled_clear_parameter( $this->title_label ),
			'title_color'                 => $this->title_color,
		);
	}
}
