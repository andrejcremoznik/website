<?php

// NOTE: If you need to rely on a plugin (like ACF) uncomment this snippet
// if (!function_exists('get_field') && !is_admin()) {
//   wp_die(sprintf(
//     '<b>crean</b> template requires <a href="https://www.advancedcustomfields.com/">ACF PRO</a> plugin. After installing the plugin enable it in the <a href="%s">Dashboard</a>.',
//     admin_url('plugins.php')
//   ));
// }

new \Timber\Timber();
Timber::$dirname = ['views'];
Timber::$cache = false; // WP_CACHE // Read up on this in config/environments/production.php

class crean extends Timber\Site {

  private $asset_version = 'vDEV'; // String replaced with a timestamp during deploy

  public $cache_itr; // Cache iteration

  public function __construct() {
    // Get cache iteration
    $this->cache_itr = $this->get_cache_itr();

    // Run action hooks
    add_action('after_setup_theme',  [$this, 'setup']);
    // add_action('widgets_init',       [$this, 'widgets_init']);
    add_action('wp_enqueue_scripts', [$this, 'scripts_styles']);
    add_action('save_post',          [$this, 'flush_theme_cache']);
    add_action('deleted_post',       [$this, 'flush_theme_cache']);

    // Run filters
    add_filter('body_class',         [$this, 'body_class']);
    add_filter('timber/context',     [$this, 'timber_context']);
    add_filter('timber/twig',        [$this, 'timber_twig']);
    // NOTE: Hide ACF admin on production
    // if (WP_ENV === 'production') {
    //   add_filter('acf/settings/show_admin', '__return_false');
    // }

    parent::__construct();
  }

  private function get_cache_itr() {
    $cache_itr = wp_cache_get('theme_cache_itr');
    if ($cache_itr === false) {
      $cache_itr = 1;
      wp_cache_set('theme_cache_itr', $cache_itr);
    }
    return $cache_itr;
  }
  public function flush_theme_cache() {
    wp_cache_incr('theme_cache_itr');
  }

  /**
   * Add custom values global Timber context
   */
  public function timber_context($context) {
    $context['site'] = [
      'name' => $this->name,
      'theme_uri' => $this->theme->uri,
      'wp_uri' => WP_SITEURL,
      'env' => WP_ENV,
      'charset' => $this->charset,
      'language' => $this->language
    ];
    // Menus
    $context['primary_navigation'] = new Timber\Menu('primary_navigation');
    // Language strings
    $context['i18n'] = [
      'no_content' => __('Sorry, no content.', 'crean'),
      'missing_title' => __('Missing page!', 'crean'),
      'missing_description' => __('We couldn’t find any content at this address.', 'crean'),
      'author' => __('Author', 'crean'),
      'password' => __('Password', 'crean'),
      'submit' => __('Submit', 'crean'),
      'comments' => __('Comments', 'crean'),
      'published' => __('Published', 'crean'),
      'tagged' => __('Tagged', 'crean')
    ];
    return $context;
  }

  /**
   * Add custom functions to Twig
   */
  public function timber_twig($twig) {
    $twig->enableStrictVariables();
    return $twig;
  }

  /**
   * Set up theme's defaults, register various features
   */
  public function setup() {

    /**
     * Load theme text domain
     * https://developer.wordpress.org/themes/functionality/internationalization/
     */
    load_theme_textdomain('crean', get_template_directory() . '/languages');

    /**
     * Custom editor style
     *
     * To enable custom styles for the visual editor add editor_theme_default.css
     * to the template assets subdirectory and uncomment the line below
     */
    //add_editor_style('assets/editor_theme_default.css');

    /**
     * Register navigation menus
     */
    register_nav_menus([
      'primary_navigation' => __('Primary Navigation', 'crean')
    ]);

    /**
     * Enable support for certain theme features
     */
    add_theme_support('title-tag');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('post-thumbnails');
    //add_theme_support('post-formats', ['aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio']);
    //add_theme_support('custom-header');
    //add_theme_support('custom-background');

    /**
     * Custom thumbnail sizes
     *
     * Define custom image sizes with 'add_image_size'.
     *
     * To list any of the defined sizes in WP media manager dropdown
     * uncomment the 'image_size_names_choose' filter and add them to the
     * 'image_sizes' function defined below
     */
    //add_image_size('size_name', 300, 200, true);
    //add_filter('image_size_names_choose', [$this, 'image_sizes']);

  }
  /*
  function image_sizes($sizes) {
    $sizes['size_name'] = __('New size label', 'crean');
    return $sizes;
  }
  */

  /**
   * Add a unique class to <body> based on request URI
   */
  public function body_class($classes) {
    $url_parts = array_filter(explode('/', $_SERVER['REQUEST_URI']));
    array_pop($url_parts);
    if (empty($url_parts)) $url_parts[] = 'frontpage';
    array_splice($url_parts, 0, 0, ['path']);
    $classes[] = implode('-', $url_parts);
    return $classes;
  }

  /**
   * Set up theme's sidebars
   */
  public function widgets_init() {
    register_sidebar([
      'name'          => __('Primary Sidebar', 'crean'),
      'id'            => 'primary_sidebar',
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget'  => '</div>',
      'before_title'  => '<h3 class="widget__title">',
      'after_title'   => '</h3>'
    ]);
  }

  /**
   * Register styles and scripts for frontend
   *
   * Styles (wp_register_style, wp_enqueue_style)
   * Scripts (wp_register_script, wp_enqueue_script, wp_localize_script)
   */
  public function scripts_styles() {
    // Deregister scripts
    wp_deregister_script('wp-embed');

    // Register styles
    wp_register_style('default', get_template_directory_uri() . '/assets/theme_default.css', [], $this->asset_version, 'all');

    // Register scripts
    wp_register_script('app', get_template_directory_uri() . '/assets/app.js', ['jquery-core'], $this->asset_version, true);

    // Enqueue styles
    wp_enqueue_style('default');

    // Enqueue scripts
    wp_enqueue_script('app');
  }
}

new crean();
