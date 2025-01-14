<?php
/**
 * The Theme specific functionality.
 *
 * @since   4.0.0 Manifest as DI.
 * @since   1.0.0
 * @package Inf_Theme\Theme
 */

namespace Inf_Theme\Theme;

use Eightshift_Libs\Core\Service;
use Eightshift_Libs\Assets\Manifest_Data;

/**
 * Class Theme
 */
class Theme implements Service {

  /**
   * Instance variable of manifest data.
   *
   * @var object
   *
   * @since 4.0.0 Init.
   */
  protected $manifest;

  /**
   * Create a new admin instance that injects manifest data for use in assets registration.
   *
   * @param Manifest_Data $manifest Inject manifest which holds data about assets from manifest.json.
   *
   * @since 4.0.0 Init.
   */
  public function __construct( Manifest_Data $manifest ) {
      $this->manifest = $manifest;
  }

  /**
   * Register all the hooks
   *
   * @return void
   *
   * @since 1.0.0
   */
  public function register() : void {
    add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
    add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
  }

  /**
   * Register the Stylesheets for the theme area.
   *
   * @return void
   *
   * @since 1.0.0
   */
  public function enqueue_styles() : void {

    // Main style file.
    \wp_register_style( THEME_NAME . '-style', $this->manifest->get_assets_manifest_item( 'application.css' ), [], THEME_VERSION );
    \wp_enqueue_style( THEME_NAME . '-style' );

  }

  /**
   * Register the JavaScript for the theme area.
   *
   * First jQuery that is loaded by default by WordPress will be deregistered and then
   * 'enqueued' with empty string. This is done to avoid multiple jQuery loading, since
   * one is bundled with webpack and exposed to the global window.
   *
   * @return void
   *
   * @since 1.0.0
   */
  public function enqueue_scripts() : void {

    // Vendor file.
    \wp_register_script( THEME_NAME . '-scripts-vendors', $this->manifest->get_assets_manifest_item( 'vendors.js' ), [], THEME_VERSION, true );
    \wp_enqueue_script( THEME_NAME . '-scripts-vendors' );

    // Main Javascript file.
    \wp_register_script( THEME_NAME . '-scripts', $this->manifest->get_assets_manifest_item( 'application.js' ), [ THEME_NAME . '-scripts-vendors' ], THEME_VERSION, true );
    \wp_enqueue_script( THEME_NAME . '-scripts' );

    // Global variables for ajax and translations.
    \wp_localize_script(
      THEME_NAME . '-scripts',
      'themeLocalization',
      [
        'ajaxurl' => \admin_url( 'admin-ajax.php' ),
      ]
    );
  }

}
