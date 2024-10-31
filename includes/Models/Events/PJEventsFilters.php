<?php
/**
 * PickleJar Live for Artists & Venues - Events Filters Class.
 *
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar\Models\Events;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Picklejar\Api\Callbacks\DashboardCallbacks;
use Picklejar\Api\Callbacks\PJEventsPageCallbacks;

/**
 * Class PJEventsFilters
 *
 * @since 1.0.0
 * @package Picklejar\Models\Events
 */
class PJEventsFilters {
	/**
	 * Variable Picklejar Events Page Callbacks
	 *
	 * @since 1.0.0
	 * @var PJEventsPageCallbacks
	 * @access private
	 */
	private $callbacks;

	/**
	 * Variable Picklejar Dashboard Callbacks
	 *
	 * @since 1.0.0
	 * @var DashboardCallbacks
	 * @access private
	 */
	private $dashboard_callback;

	/**
	 * Variable Post Data.
	 *
	 * @since 1.0.0
	 * @var string
	 * @access private
	 */
	private $post_data;

	/**
	 * Variable Name
	 *
	 * @since 1.0.0
	 * @var string | null
	 * @access private
	 */
	private $name;

	/**
	 * Variable Categories
	 *
	 * @since 1.0.0
	 * @var string | null
	 * @access private
	 */
	private $categories;

	/**
	 * Variable Event Genres
	 *
	 * @since 1.0.0
	 * @var string | null
	 * @access private
	 */
	private $event_genres;

	/**
	 * Variable Artist Id
	 *
	 * @since 1.0.0
	 * @var string | null
	 * @access private
	 */
	private $artist_id;

	/**
	 * Variable Band Id
	 *
	 * @since 1.0.0
	 * @var string | null
	 * @access private
	 */
	private $band_id;

	/**
	 * Variable Venue Id
	 *
	 * @since 1.0.0
	 * @var string | null
	 * @access private
	 */
	private $venue_id;

	/**
	 * Variable Country
	 *
	 * @since 1.0.0
	 * @var string | null
	 * @access private
	 */
	private $country;

	/**
	 * Variable State
	 *
	 * @since 1.0.0
	 * @var string | null
	 * @access private
	 */
	private $state;

	/**
	 * Variable City
	 *
	 * @since 1.0.0
	 * @var string | null
	 * @access private
	 */
	private $city;

	/**
	 * Variable Zip
	 *
	 * @since 1.0.0
	 * @var string | null
	 * @access private
	 */
	private $zip;

	/**
	 * Variable Start Date
	 *
	 * @since 1.0.0
	 * @var string | null
	 * @access private
	 */
	private $start_date;

	/**
	 * Variable End Date
	 *
	 * @since 1.0.0
	 * @var string | null
	 * @access private
	 */
	private $end_date;

	/**
	 * Variable Picklejar Event Data
	 *
	 * @since 1.0.0
	 * @var array
	 * @access private
	 */
	private $pj_event_data;

	/**
	 * Variable Picklejar Categories Filter Options
	 *
	 * @since 1.0.0
	 * @var array
	 * @access private
	 */
	private $pj_categories_filter_options;

	/**
	 * Variable Picklejar Country Filter Options
	 *
	 * @since 1.0.0
	 * @var array
	 * @access private
	 */
	private $pj_country_filter_options;

	/**
	 * Variable Picklejar State Filter Options
	 *
	 * @since 1.0.0
	 * @var array
	 * @access private
	 */
	private $pj_state_filter_options;

	/**
	 * Variable Picklejar City Filter Options
	 *
	 * @since 1.0.0
	 * @var array
	 * @access private
	 */
	private $pj_city_filter_options;

	/**
	 * Variable Picklejar Zip Filter Options
	 *
	 * @since 1.0.0
	 * @var array
	 * @access private
	 */
	private $pj_zip_filter_options;

	/**
	 * Variable Picklejar Venue Filter Options
	 *
	 * @since 1.0.0
	 * @var array
	 * @access private
	 */
	private array $pj_venue_filter_options;

	/**
	 * Function Constructor
	 *
	 * @param array $post_data Post Data.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $post_data = null ) {
		$this->post_data = $post_data;

		$this->callbacks = new PJEventsPageCallbacks();

		$this->dashboard_callback = new DashboardCallbacks();

		$settings_data = $this->dashboard_callback->get_data()['settings'];
		$entity_type   = $settings_data['pj_entity_type'] ?? '';
		$entity_value  = $settings_data['pj_entity_id'] ?? '';
		$artist_value  = '';
		$venues_value  = '';

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

		$this->pj_event_data['name'] = array(
			'type'              => 'text',
			'option_name_array' => 'filters',
			'class'             => 'picklejar-form-control picklejar-d-column pj-filters pj-filters-name',
			'attributes'        => 'data-filter="name"',
		);

		/*
		* $this->pj_event_data['categories'] = [
			'label'             => 'Categories',
			'type'              => 'select',
			'option_name_array' => 'filters',
			'class'             => 'widefat picklejar-form-control picklejar-d-column pj-filters pj-filters-category',
			'options'           => $this->pj_categories_filter_options,
			'attributes'        => 'data-filter="categories"'
		];
		*/

		$this->pj_event_data['event_genres'] = array(
			'label'             => 'Categories',
			'type'              => 'select',
			'option_name_array' => 'filters',
			'class'             => 'widefat picklejar-form-control picklejar-d-column pj-filters pj-filters-category',
			'options'           => $this->pj_categories_filter_options,
			'attributes'        => 'data-filter="event_genres"',
		);

		/*
		$this->pj_event_data['artist_band_id'] = [
			'label' => 'Artist',
			'type' => 'select',
			'option_name_array' => 'filters',
			'class' => 'widefat picklejar-form-control picklejar-d-column pj-filters picklejar-filters-artists-band',
			'options' => [],
			'attributes' => 'data-filter="artist_band_id" data-default-value="' . $artist_value . '"'
		];

		$this->pj_event_data['artist_id'] = [
			'label' => 'Artist',
			'type' => 'select',
			'option_name_array' => 'filters',
			'class' => 'widefat picklejar-form-control picklejar-d-column pj-filters picklejar-filters-artists pj-trigger-apply-filter picklejar-hidden',
			'options' => [],
			'attributes' => 'data-filter="artist_id" data-default-value="' . $artist_value . '"',
			'form_group_class' => 'picklejar-hidden'
		];

		$this->pj_event_data['band_id'] = [
			'label' => 'Band',
			'type' => 'select',
			'option_name_array' => 'filters',
			'class' => 'widefat picklejar-form-control picklejar-d-column pj-filters picklejar-filters-bands pj-trigger-apply-filter picklejar-hidden',
			'options' => [],
			'attributes' => 'data-filter="band_id" data-default-value="' . $artist_value . '"',
			'form_group_class' => 'picklejar-hidden'
		];

		if ($entity_type === 'Venues') {
			$this->pj_event_data['vendor_id'] = [
				'label' => 'Venue',
				'type' => 'select',
				'option_name_array' => 'filters',
				'class' => 'widefat picklejar-form-control picklejar-d-column pj-filters picklejar-filters-venue pj-trigger-apply-filter',
				'options' => $this->pj_venue_filter_options,
				'attributes' => 'data-filter="venue_id" data-default-value="' . $venues_value . '"'
			];
		}

		$this->pj_event_data['country'] = [
			'label' => 'Country',
			'type' => 'select',
			'option_name_array' => 'filters',
			'class' => 'widefat picklejar-form-control picklejar-d-column pj-filters pj-filters-country',
			'options' => $this->pj_country_filter_options,
			'attributes' => 'data-filter="country"'
		];

		$this->pj_event_data['state'] = [
			'label' => 'State',
			'type' => 'select',
			'option_name_array' => 'filters',
			'class' => 'widefat picklejar-form-control picklejar-d-column pj-filters pj-filters-state',
			'options' => $this->pj_state_filter_options,
			'attributes' => 'data-filter="state"'
		];

		$this->pj_event_data['city'] = [
			'label' => 'City',
			'type' => 'select',
			'option_name_array' => 'filters',
			'class' => 'widefat picklejar-form-control picklejar-d-column pj-filters pj-filters-city',
			'options' => $this->pj_city_filter_options,
			'attributes' => 'data-filter="city"'
		];

		$this->pj_event_data['zip'] = [
			'label' => 'Zip',
			'type' => 'select',
			'option_name_array' => 'filters',
			'class' => 'widefat picklejar-form-control picklejar-d-column pj-filters pj-filters-zip',
			'options' => $this->pj_zip_filter_options,
			'attributes' => 'data-filter="zip"'
		];
		*/
		$this->pj_event_data['start_date'] = array(
			'label'             => 'Start Date',
			'type'              => 'date',
			'option_name_array' => 'filters',
			'class'             => 'picklejar-form-control picklejar-d-column pj-filters picklejar-date-filters pj-filters-start-date',
			'attributes'        => 'data-filter="start_date"',
		);

		$this->pj_event_data['end_date'] = array(
			'label'             => 'End Date',
			'type'              => 'date',
			'option_name_array' => 'filters',
			'class'             => 'picklejar-form-control picklejar-d-column pj-filters picklejar-date-filters pj-filters-end-date',
			'attributes'        => 'data-filter="end_date"',
		);

		$this->pj_event_data['apply_filter'] = array(
			'label'             => 'Preview Applied Filters',
			'type'              => 'button',
			'option_name_array' => 'filters',
			'class'             => 'pj-filters-apply button-default button-large pj-loading-button',
			'attributes'        => 'data-entity-type="events"',
		);

		$this->init( $post_data );
	}

	/**
	 * Function Get Picklejar Event Data
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 */
	public function get_pj_event_data() {
		return $this->pj_event_data;
	}

	/**
	 * Function Init
	 *
	 * @param array $post_data Post Data.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function init( $post_data ) {
		$layout_filter_settings = $this->callbacks->get_filter_settings_group();
		$filters                = array();
		if ( $post_data && isset( $post_data[ $layout_filter_settings ] ) ) {
			$filters = $post_data[ $layout_filter_settings ];
		}
		$this->name         = ! empty( $filters['name'] ) ? $filters['name'] : null;
		$this->categories   = ! empty( $filters['categories'] ) ? $filters['categories'] : null;
		$this->event_genres = ! empty( $filters['event_genres'] ) ? $filters['event_genres'] : null;
		$this->artist_id    = ! empty( $filters['artist_id'] ) ? $filters['artist_id'] : null;
		$this->band_id      = ! empty( $filters['band_id'] ) ? $filters['band_id'] : null;
		$this->country      = ! empty( $filters['country'] ) ? $filters['country'] : null;
		$this->state        = ! empty( $filters['state'] ) ? $filters['state'] : null;
		$this->city         = ! empty( $filters['city'] ) ? $filters['city'] : null;
		$this->zip          = ! empty( $filters['zip'] ) ? $filters['zip'] : null;
		$this->venue_id     = ! empty( $filters['venue_id'] ) ? $filters['venue_id'] : null;
		$this->start_date   = ! empty( $filters['start_date'] ) ? $filters['start_date'] : null;
		$this->end_date     = ! empty( $filters['end_date'] ) ? $filters['end_date'] : null;
	}

	/**
	 * Function Get Picklejar Event Filters Data
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 */
	public function get_pj_event_filters_data() {
		return array(
			'name'         => $this->name,
			'categories'   => $this->categories,
			'event_genres' => $this->event_genres,
			'artist_id'    => $this->artist_id,
			'band_id'      => $this->band_id,
			'venue_id'     => $this->venue_id,
			'country'      => $this->country,
			'state'        => $this->state,
			'city'         => $this->city,
			'zip'          => $this->zip,
			'start_date'   => $this->start_date,
			'end_date'     => $this->end_date,
		);
	}
}
