<?php

function localdrivy_render_plugin_settings_page()
{
    ?>
    <h2> <a href="https://www.localdrivy.fr" target="_blank"><img src='<?php echo esc_url(plugins_url('assets/images/logo-02-2021.JPG', __FILE__))?>' title="Localdrivy"></a>  </h2>
    <form action="options.php" method="post">
        <?php
        settings_fields('localdrivy_plugin_options');
    do_settings_sections('localdrivy_plugin'); ?>
        <hr style="margin-top:30px;">
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e('Save'); ?>" />
    </form>
<?php
}
function localdrivy_add_settings_page()
{
    add_options_page('Localdrivy', 'Localdrivy', 'manage_options', 'localdrivy options', 'localdrivy_render_plugin_settings_page');
}
add_action('admin_menu', 'localdrivy_add_settings_page');

function localdrivy_register_settings()
{
    register_setting('localdrivy_plugin_options', 'localdrivy_plugin_options');
    add_settings_section('api_settings', 'Paramètres Localdrivy', 'localdrivy_plugin_section_text', 'localdrivy_plugin');

    add_settings_field('localdrivy_plugin_setting_api_key', 'Clé Api', 'localdrivy_plugin_setting_api_key', 'localdrivy_plugin', 'api_settings');
}
function localdrivy_plugin_section_text()
{
    echo '<p>Entrez les paramètres depuis votre back office localdrivy sous Paramètres > api (<a href="https://pro.localdrivy.fr/api-integration">https://pro.localdrivy.fr/api-integration)</a></p><p>Vous pouvez tester la clé api de démo : 123456</p>';
}
function localdrivy_plugin_setting_api_key()
{
    $options = get_option('localdrivy_plugin_options');
    echo '<input id="localdrivy_plugin_setting_api_key" size="200" name="localdrivy_plugin_options[api_key]" type="text" value="' . esc_attr($options['api_key']) . '" />';
}

add_action('admin_init', 'localdrivy_register_settings');
