<?php
/**
 * PickleJar Live for Artists & Venues Settings API Class.
 *
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class SettingsApi
 *
 * @since 1.0.0
 * @package Picklejar\Api
 */
class SettingsApi {
	/**
	 * Variable admin_pages
	 *
	 * @since 1.0.0
	 * @var array
	 * @access public
	 */
	public $admin_pages = array();

	/**
	 * Variable admin_subpages
	 *
	 * @since 1.0.0
	 * @var array
	 * @access public
	 */
	public $admin_subpages = array();

	/**
	 * Variable settings
	 *
	 * @since 1.0.0
	 * @var array
	 * @access public
	 */
	public $settings = array();

	/**
	 * Variable sections
	 *
	 * @since 1.0.0
	 * @var array
	 * @access public
	 */
	public $sections = array();

	/**
	 * Variable fields
	 *
	 * @since 1.0.0
	 * @var array
	 * @access public
	 */
	public $fields = array();


	/**
	 * Register actions
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function register() {
		if ( ! empty( $this->admin_pages ) || ! ( empty( $this->admin_subpages ) ) ) {
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		}

		if ( ! empty( $this->settings ) ) {
			add_action( 'admin_init', array( $this, 'register_custom_fields' ) );
		}
	}

	/**
	 * Function Add Pages
	 *
	 * @param array $pages Array of pages.
	 *
	 * @return $this
	 * @since 1.0.0
	 * @access public
	 */
	public function add_pages( array $pages ) {
		$this->admin_pages = $pages;

		return $this;
	}

	/**
	 * Function to add sub pages
	 *
	 * @param string $title Title of the sub page.
	 *
	 * @return $this
	 * @since 1.0.0
	 * @access public
	 */
	public function with_sub_page( string $title = null ) {
		if ( empty( $this->admin_pages ) ) {
			return $this;
		}

		$admin_page = $this->admin_pages[0];
		$sub_page   = array(
			array(
				'parent_slug' => $admin_page['menu_slug'],
				'page_title'  => $admin_page['page_title'],
				'menu_title'  => $title ?? $admin_page['menu_title'],
				'capability'  => $admin_page['capability'],
				'menu_slug'   => $admin_page['menu_slug'],
				'callback'    => $admin_page['callback'],
			),
		);

		$this->admin_subpages = $sub_page;

		return $this;
	}

	/**
	 * Function to add sub pages
	 *
	 * @param array $pages Array of sub pages.
	 *
	 * @return $this
	 * @since 1.0.0
	 * @access public
	 */
	public function add_sub_pages( array $pages ) {
		$this->admin_subpages = array_merge( $this->admin_subpages, $pages );

		return $this;
	}

	/**
	 * Function to add admin menu
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function add_admin_menu() {
		foreach ( $this->admin_pages as $page ) {
			add_menu_page(
				$page['page_title'],
				$page['menu_title'],
				$page['capability'],
				$page['menu_slug'],
				$page['callback'],
				$page['icon_url'],
				$page['position']
			);
		}
		foreach ( $this->admin_subpages as $page ) {
			add_submenu_page(
				$page['parent_slug'],
				$page['page_title'],
				$page['menu_title'],
				$page['capability'],
				$page['menu_slug'],
				$page['callback']
			);
		}
	}

	/**
	 * Function to set settings
	 *
	 * @param array $settings Array of settings.
	 *
	 * @return $this
	 * @since 1.0.0
	 * @access public
	 */
	public function set_settings( array $settings ) {
		$this->settings = $settings;

		return $this;
	}

	/**
	 * Function set sections
	 *
	 * @param array $sections Array of sections.
	 *
	 * @return $this
	 * @since 1.0.0
	 * @access public
	 */
	public function set_sections( array $sections ) {
		$this->sections = $sections;

		return $this;
	}

	/**
	 * Function set fields
	 *
	 * @param array $fields Array of fields.
	 *
	 * @return $this
	 * @since 1.0.0
	 * @access public
	 */
	public function set_fields( array $fields ) {
		$this->fields = $fields;

		return $this;
	}

	/**
	 * Function to register custom fields
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function register_custom_fields() {
		// register setting.
		foreach ( $this->settings as $setting ) {
			register_setting( $setting['option_group'], $setting['option_name'], ( $setting['callback'] ?? '' ) );
		}
		// add settings section.
		foreach ( $this->sections as $section ) {
			add_settings_section( $section['id'], $section['title'], ( $section['callback'] ?? '' ), $section['page'] );
		}
		// add settings fields.
		foreach ( $this->fields as $field ) {
			add_settings_field( $field['id'], $field['title'], ( $field['callback'] ?? '' ), $field['page'], $field['section'], ( $field['args'] ?? '' ) );
		}
	}
}
