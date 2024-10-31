<?php
/**
 * PickleJar Live for Artists & Venues  - Artist Filters.
 *
 * @since 1.0.0
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar\Models\Artist;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Picklejar\Api\Callbacks\DashboardCallbacks;
use Picklejar\Api\Callbacks\PJArtistPageCallbacks;
use WP_Post;

/**
 * Class PJArtistFilters
 *
 * @since 1.0.0
 * @package Picklejar\Models\Artist
 */
class PJArtistFilters {

	/**
	 * Variable Callbacks.
	 *
	 * @since 1.0.0
	 * @var PJArtistPageCallbacks
	 */
	private $callbacks;

	/**
	 * Variable Dashboard Callbacks.
	 *
	 * @since 1.0.0
	 * @var DashboardCallbacks
	 */
	private $dashboard_callback;

	/**
	 * Variable Post Data
	 *
	 * @since 1.0.0
	 * @var WP_Post|mixed|null
	 */
	private $post_data;

	/**
	 * Variable Name
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $name;

	/**
	 * Variable Categories
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $categories;

	/**
	 * Variable Genres
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $genres;

	/**
	 * Variable Artist ID
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $artist_id;

	/**
	 * Variable Band ID
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $band_id;

	/**
	 * Variable Venue ID
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $venue_id;

	/**
	 * Variable Country
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $country;

	/**
	 * Variable State
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $state;

	/**
	 * Variable City
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $city;

	/**
	 * Variable Zip
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $zip;

	/**
	 * Varaible Picklejar Artist Data
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $pj_artist_data;

	/**
	 * Variable Picklejar Categories Filter Options
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $pj_categories_filter_options;

	/**
	 * Picklejar Country Filter Options
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $pj_country_filter_options;

	/**
	 * Variable Picklejar State Filter Options
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $pj_state_filter_options;

	/**
	 * Variable City Filter Options
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $pj_city_filter_options;

	/**
	 * Variable Picklejar Zip Filter Options
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $pj_zip_filter_options;

	/**
	 * Variable Picklejar Venue Filter Options
	 *
	 * @var array
	 */
	private $pj_venue_filter_options;

	/**
	 * Function Constructor
	 *
	 * @param WP_POST|mixed|null $post_data Post Data.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $post_data = null ) {
		$this->post_data = $post_data;

		$this->callbacks = new PJArtistPageCallbacks();

		$this->dashboard_callback = new DashboardCallbacks();

		$settings_data = $this->dashboard_callback->get_data()['settings'];

		$entity_type  = $settings_data['pj_entity_type'] ?? '';
		$entity_value = $settings_data['pj_entity_id'] ?? '';
		$artist_value = '';
		$venues_value = '';

		if ( 'Venues' === $entity_type ) {
			$venues_value = $entity_value;
		}

		if ( 'Artists' === $entity_type ) {
			$artist_value = $entity_value;
		}

		$this->pj_categories_filter_options = array();

		$this->pj_venue_filter_options = array();

		$this->pj_country_filter_options = array();

		$this->pj_state_filter_options = array();

		$this->pj_city_filter_options = array();

		$this->pj_zip_filter_options = array();

		$this->pj_artist_data['name'] = array(
			'type'              => 'text',
			'option_name_array' => 'filters',
			'class'             => 'picklejar-form-control picklejar-d-column pj-filters pj-filters-name',
			'attributes'        => 'data-filter="name"',
		);

		/*
		 *
		 * $this->pj_artist_data['categories'] = [
			'label'             => 'Categories',
			'type'              => 'select',
			'option_name_array' => 'filters',
			'class'             => 'widefat picklejar-form-control picklejar-d-column pj-filters pj-filters-category',
			'options'           => $this->pj_categories_filter_options,
			'attributes'        => 'data-filter="categories"'
		];
		*/
		$this->pj_artist_data['genres'] = array(
			'label'             => 'Categories',
			'type'              => 'select',
			'option_name_array' => 'filters',
			'class'             => 'widefat picklejar-form-control picklejar-d-column pj-filters pj-filters-category',
			'options'           => $this->pj_categories_filter_options,
			'attributes'        => 'data-filter="genres"',
		);

		/*
		$this->pj_artist_data['artist_band_id'] = [
			'label' => 'Artist',
			'type' => 'select',
			'option_name_array' => 'filters',
			'class' => 'widefat picklejar-form-control picklejar-d-column pj-filters picklejar-filters-artists-band',
			'options' => [],
			'attributes' => 'data-filter="artist_band_id" data-default-value="' . $artist_value . '"'
		];

		$this->pj_artist_data['artist_id'] = [
			'label' => 'Artist',
			'type' => 'select',
			'option_name_array' => 'filters',
			'class' => 'widefat picklejar-form-control picklejar-d-column pj-filters picklejar-filters-artists pj-trigger-apply-filter picklejar-hidden',
			'options' => [],
			'attributes' => 'data-filter="artist_id" data-default-value="' . $artist_value . '"',
			'form_group_class' => 'picklejar-hidden'
		];

		$this->pj_artist_data['band_id'] = [
			'label' => 'Band',
			'type' => 'select',
			'option_name_array' => 'filters',
			'class' => 'widefat picklejar-form-control picklejar-d-column pj-filters picklejar-filters-bands pj-trigger-apply-filter picklejar-hidden',
			'options' => [],
			'attributes' => 'data-filter="band_id" data-default-value="' . $artist_value . '"',
			'form_group_class' => 'picklejar-hidden'
		];

		if ($entity_type === 'Venues') {
			$this->pj_artist_data['vendor_id'] = [
				'label' => 'Venue',
				'type' => 'select',
				'option_name_array' => 'filters',
				'class' => 'widefat picklejar-form-control picklejar-d-column pj-filters picklejar-filters-venue pj-trigger-apply-filter',
				'options' => $this->pj_venue_filter_options,
				'attributes' => 'data-filter="venue_id" data-default-value="' . $venues_value . '"'
			];
		}

		$this->pj_artist_data['country'] = [
			'label' => 'Country',
			'type' => 'select',
			'option_name_array' => 'filters',
			'class' => 'widefat picklejar-form-control picklejar-d-column pj-filters pj-filters-country',
			'options' => $this->pj_country_filter_options,
			'attributes' => 'data-filter="country"'
		];

		$this->pj_artist_data['state'] = [
			'label' => 'State',
			'type' => 'select',
			'option_name_array' => 'filters',
			'class' => 'widefat picklejar-form-control picklejar-d-column pj-filters pj-filters-state',
			'options' => $this->pj_state_filter_options,
			'attributes' => 'data-filter="state"'
		];

		$this->pj_artist_data['city'] = [
			'label' => 'City',
			'type' => 'select',
			'option_name_array' => 'filters',
			'class' => 'widefat picklejar-form-control picklejar-d-column pj-filters pj-filters-city',
			'options' => $this->pj_city_filter_options,
			'attributes' => 'data-filter="city"'
		];

		$this->pj_artist_data['zip'] = [
			'label' => 'Zip',
			'type' => 'select',
			'option_name_array' => 'filters',
			'class' => 'widefat picklejar-form-control picklejar-d-column pj-filters pj-filters-zip',
			'options' => $this->pj_zip_filter_options,
			'attributes' => 'data-filter="zip"'
		];

		$this->pj_artist_data['start_date'] = [
			'label' => 'Start Date',
			'type' => 'date',
			'option_name_array' => 'filters',
			'class' => 'picklejar-form-control picklejar-d-column pj-filters picklejar-date-filters pj-filters-start-date',
			'attributes' => 'data-filter="start_date"'
		];

		$this->pj_artist_data['end_date'] = [
			'label' => 'End Date',
			'type' => 'date',
			'option_name_array' => 'filters',
			'class' => 'picklejar-form-control picklejar-d-column pj-filters picklejar-date-filters pj-filters-end-date',
			'attributes' => 'data-filter="end_date"'
		];
		*/
		$this->pj_artist_data['apply_filter'] = array(
			'label'             => 'Preview Applied Filters',
			'type'              => 'button',
			'option_name_array' => 'filters',
			'class'             => 'pj-filters-apply button-default button-large pj-loading-button',
			'attributes'        => 'data-entity-type="artists"',
		);

		$this->init( $post_data );
	}

	/**
	 * Get Picklejar Artist Data.
	 *
	 * @return array $pj_artist_data
	 * @since 1.0.0
	 * @access public
	 */
	public function get_pj_artist_data() {
		return $this->pj_artist_data;
	}

	/**
	 * Function Init.
	 *
	 * @param array $post_data Post data.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function init( $post_data ) {
		$layout_filter_settings = $this->callbacks->get_filter_settings_group();
		$filters                = $post_data[ $layout_filter_settings ] ?? array();
		$this->name             = ! empty( $filters['name'] ) ? $filters['name'] : null;
		$this->categories       = ! empty( $filters['categories'] ) ? $filters['categories'] : null;
		$this->genres           = ! empty( $filters['genres'] ) ? $filters['genres'] : null;
		$this->artist_id        = ! empty( $filters['artist_id'] ) ? $filters['artist_id'] : null;
		$this->band_id          = ! empty( $filters['band_id'] ) ? $filters['band_id'] : null;
		$this->country          = ! empty( $filters['country'] ) ? $filters['country'] : null;
		$this->state            = ! empty( $filters['state'] ) ? $filters['state'] : null;
		$this->city             = ! empty( $filters['city'] ) ? $filters['city'] : null;
		$this->zip              = ! empty( $filters['zip'] ) ? $filters['zip'] : null;
		$this->venue_id         = ! empty( $filters['venue_id'] ) ? $filters['venue_id'] : null;
	}

	/**
	 * Get Picklejar Artist Filters Data.
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 */
	public function get_pj_artist_filters_data() {
		return array(
			'name'       => $this->name,
			'categories' => $this->categories,
			'genres'     => $this->genres,
			'artist_id'  => $this->artist_id,
			'band_id'    => $this->band_id,
			'venue_id'   => $this->venue_id,
			'country'    => $this->country,
			'state'      => $this->state,
			'city'       => $this->city,
			'zip'        => $this->zip,
		);
	}
}
