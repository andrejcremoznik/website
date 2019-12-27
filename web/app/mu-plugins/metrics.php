<?php
/**
 * Plugin Name: Metrics
 * Description: Provide health metrics for Prometheus
 * Version:     1.0.0
 * Author:      Andrej Cremoznik
 * Author URI:  https://keybase.io/andrejcremoznik
 */

if (!defined('WPINC')) die();

if ($_SERVER['REQUEST_URI'] === '/metrics') :
  header('Content-Type: text/plain');
?>
# HELP wordpress_alive WordPress application works.
# TYPE wordpress_alive gauge
wordpress_alive 1

# HELP wordpress_https HTTPS enabled.
# TYPE wordpress_https gauge
wordpress_https <?= $_SERVER['HTTPS'] === 'on' ? 1 : 0 ?>
<?php
die();
endif;
