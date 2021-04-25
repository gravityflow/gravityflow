<?php
/**
 * Main T15S library.
 *
 * @since 2.7.3
 */

use DateTime;

class Gravity_Flow_Language_Packs {

	public function __construct( $type, $slug, $api_url ) {
		$this->type    = $type;
		$this->slug    = $slug;
		$this->api_url = $api_url;
	}

	/**
	 * Adds a new project to load translations for.
	 *
	 * @since 2.7.3
	 *
	 * @param string $this->type    Project type. Either plugin or theme.
	 * @param string $this->slug    Project directory slug.
	 * @param string $this->api_url Full GlotPress API URL for the project.
	 */
	public function add_project() {
		if ( ! has_action( 'init',  array( $this, 'register_clean_translations_cache' ) ) ) {
			add_action( 'init', array( $this, 'register_clean_translations_cache' ), 9999 );
		}

		add_filter( 'translations_api', array( $this, 'translations_api' ), 10, 3 );
		add_filter( 'site_transient_update_' . $this->type . 's', array( $this, 'site_transient_update_plugins' ) );
	}

	/**
	 * Short-circuits translations API requests for private projects.
	 *
	 * @since 2.7.3
	 *
	 * @param bool|array $result         The result object. Default false.
	 * @param string     $requested_type The type of translations being requested.
	 * @param object     $args           Translation API arguments.
	 * @return bool|array
	 */
	public function translations_api( $result, $requested_type, $args ) {
		if ( $this->type . 's' === $requested_type && $this->slug === $args['slug'] ) {
			return $this->get_translations( $this->type, $args['slug'], $this->api_url );
		}

		return $result;
	}

	/**
	 * Filters the translations transients to include the private plugin or theme.
	 *
	 * @see wp_get_translation_updates()
	 *
	 * @since 2.7.3
	 *
	 * @param bool|array $value The transient value.
	 */
	public function site_transient_update_plugins( $value ) {
		if ( ! $value ) {
			$value = new \stdClass();
		}

		if ( ! isset( $value->translations ) ) {
			$value->translations = [];
		}

		$all_translations = $this->get_translations( $this->type, $this->slug, $this->api_url );
		$plugin_name = $this->slug;
		$translations = $all_translations->$plugin_name[$this->slug];

		if ( ! isset( $translations['translations'] ) ) {
			return $value;
		}

		$installed_translations = wp_get_installed_translations( $this->type . 's' );

		foreach ( (array) $translations['translations'] as $translation ) {
			if ( in_array( $translation['language'], get_available_languages() ) ) {
				if ( isset( $installed_translations[ $this->slug ][ $translation['language'] ] ) && $translation['updated'] ) {
					$local  = new DateTime( $installed_translations[ $this->slug ][ $translation['language'] ]['PO-Revision-Date'] );
					$remote = new DateTime( $translation['updated'] );

					if ( $local >= $remote ) {
						continue;
					}
				}

				$translation['type'] = $this->type;
				$translation['slug'] = $this->slug;

				$value->translations[] = $translation;
			}
		}

		return $value;
	}


	/**
	 * Registers actions for clearing translation caches.
	 *
	 * @since 2.7.3
	 */
	public function register_clean_translations_cache() {
		$clear_plugin_translations = function() {
			$this->clean_translations_cache( 'plugin' );
		};
		$clear_theme_translations  = function() {
			$this->clean_translations_cache( 'theme' );
		};

		add_action( 'set_site_transient_update_plugins', $clear_plugin_translations );
		add_action( 'delete_site_transient_update_plugins', $clear_plugin_translations );

		add_action( 'set_site_transient_update_themes', $clear_theme_translations );
		add_action( 'delete_site_transient_update_themes', $clear_theme_translations );
	}

	/**
	 * Clears existing translation cache for a given type.
	 *
	 * @since 2.7.3
	 *
	 * @param string $this->type Project type. Either plugin or theme.
	 */
	public function clean_translations_cache( $type ) {
		$transient_key = 't15s-registry-' . $this->slug . '-' . $type;
		$translations  = get_site_transient( $transient_key );

		if ( ! is_object( $translations ) ) {
			return;
		}

		/*
		 * Don't delete the cache if the transient gets changed multiple times
		 * during a single request. Set cache lifetime to maximum 15 seconds.
		 */
		$cache_lifespan   = 15;
		$time_not_changed = isset( $translations->_last_checked ) && ( time() - $translations->_last_checked ) > $cache_lifespan;

		if ( ! $time_not_changed ) {
			return;
		}

		delete_site_transient( $transient_key );
	}

	/**
	 * Gets the translations for a given project.
	 *
	 * @since 2.7.3
	 *
	 * @param string $this->type Project type. Either plugin or theme.
	 * @param string $this->slug Project directory slug.
	 * @param string $url  Full GlotPress API URL for the project.
	 *
	 * @return array Translation data.
	 */
	public function get_translations( $type, $slug, $url ) {
		$transient_key = 't15s-registry-' . $slug . '-' . $type;
		$translations  = get_site_transient( $transient_key );

		if ( false !== $translations ) {
			return $translations;
		}

		if ( ! is_object( $translations ) ) {
			$translations = new \stdClass();
		}

		if ( isset( $translations->{$slug} ) && is_array( $translations->{$slug} ) ) {
			return $translations->{$slug};
		}

		$result = json_decode( wp_remote_retrieve_body( wp_remote_get( $url, [ 'timeout' => 2 ] ) ), true );
		if ( ! is_array( $result ) ) {
			$result = [];
		}

		$translations->{$slug}       = $result;
		$translations->_last_checked = time();

		set_site_transient( $transient_key, $translations );
		return $result;
	}

}
