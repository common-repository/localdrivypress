<?php
/**
 * Plugin Name:     Localdrivy Click and collect
 * Description:       Requiert le plugin Elementor. Connecteur wordpress du système click and collect localdrivy. Localdrivy permet aux petits commercants une mise en place et une gestion de leur boutique depuis une application mobile et web. Localdrivy intègre de nombreux modules comme le click and collect, l'envoi de sms, le paiement en ligne Stripe. LocalDrivy s'addresse aux commerçants locaux : restaurants, boucheries, boulangeries, pizzerias, petits producteurs. Essai de 3 mois gratuits.
 * Version:           1.4.5
 * Requires at least: 5.2
 * Requires PHP:      5.6
 * Author:            Evolnet
 * Author URI:        https://www.evolnet.fr
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       Connecteur wordpress du système click and collect localdrivy. Localdrivy permet aux petits commercants une mise en place et une gestion de leur boutique depuis une application mobile et web. Localdrivy intègre de nombreux modules comme le click and collect, l'envoi de sms, le paiement en ligne Stripe.
 */
include_once(__DIR__ . '/LocalDrivyApiClient.php');
require_once(__DIR__ . '/LocalDrivyElExtension.php');
require_once(__DIR__ . '/localdrivy-settings.php');
require_once(__DIR__ . '/LocaldrivyRestApiController.php');

add_action('rest_api_init', function () {
    $controller = new LocaldrivyRestApiController();
    $controller->register_routes();
});

function lcd_enqueue_scripts()
{
    wp_enqueue_script('js_script', plugins_url('localdrivy.js', __FILE__), array(), '1.0', true);
    
    $lcapi = new LocalDrivyApiClient();
    $js = $lcapi->getJs();
    
    wp_add_inline_script('js_script', $js, 'before');
}
add_action('wp_enqueue_scripts', 'lcd_enqueue_scripts');
add_action('wp_head', 'lcd_custom_styles', 10);

function lcd_custom_styles()
{
    $lcapi = new LocalDrivyApiClient();
    $css = $lcapi->getCss();
    echo "<style>".$css."</style>";
}

function lcd_add_elementor_widget_categories($elements_manager)
{
    $elements_manager->add_category(
        'localdrivy',
        [
            'title' => __('LocalDrivy', 'localdrivy'),
            'icon' => 'fa fa-plug',
        ]
    );
}
add_action('elementor/elements/categories_registered', 'lcd_add_elementor_widget_categories');

/*
Plugin Name: Elementor
Description: Elementor Plugin should be installed and active to use this plugin.
Version: 1.0.0
*/
add_action('admin_init', 'lcd_child_plugin_has_parent_plugin');
function lcd_child_plugin_has_parent_plugin()
{
    if (is_admin() && current_user_can('activate_plugins') &&  !is_plugin_active('elementor/elementor.php')) {
        add_action('admin_notices', 'lcd_child_plugin_notice');

        deactivate_plugins(plugin_basename(__FILE__));

        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
    }
}

function lcd_child_plugin_notice()
{
    ?><div class="error"><p>Le plugin Localdrivy necessite l'activation du plugin Elementor.</p></div><?php
}

function lcd_settings_link($links)
{
    $settings_link = '<a href="options-general.php?page=localdrivy+options">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
  $plugin = plugin_basename(__FILE__);
  add_filter("plugin_action_links_$plugin", 'lcd_settings_link');
