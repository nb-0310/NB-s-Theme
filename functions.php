<?php
function nbtheme_theme_support() {
    add_theme_support('title-tag');
}

add_action('after_setup_theme', 'nbtheme_theme_support');
function nbtheme_register_styles()
{
    $version = wp_get_theme() -> get('Version');
    wp_enqueue_style('nbtheme-style', get_template_directory_uri() . '/style.css', array('nbtheme-bootstrap', 'nbtheme-fontawesome'), $version, 'all');
    wp_enqueue_style('nbtheme-bootstrap', "https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css", array(), '4.4.1', 'all');
    wp_enqueue_style('nbtheme-fontawesome', "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css", array(), '5.13.0', 'all');
}

add_action('wp_enqueue_scripts', 'nbtheme_register_styles');

function nbtheme_register_scripts()
{
    wp_enqueue_script('nbtheme-jquery', "https://code.jquery.com/jquery-3.4.1.slim.min.js", array(), '3.4.1', true);
    wp_enqueue_script('nbtheme-popper', "https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js", array(), '1.16.0', true);
    wp_enqueue_script('nbtheme-bootstrap', "https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js", array(), '4.4.1', true);
    wp_enqueue_script('nbtheme-main', get_template_directory_uri() . 'assets/js/main.js', array(), '1.0', true);
}

add_action('wp_enqueue_scripts', 'nbtheme_register_scripts');

function add_custom_plugin_filter($views) {
    $custom_url = add_query_arg('plugin_status', 'custom', 'plugins.php');

    // Add the new filter to the array
    $views['custom'] = sprintf('<a href="%s">%s</a>', esc_url($custom_url), __('Custom', 'text-domain'));

    return $views;
}
add_filter('views_plugins', 'add_custom_plugin_filter');

function filter_plugins_by_custom_status($query) {
    global $pagenow;

    // Check if we are on the plugins page and the custom filter is selected
    if ($pagenow === 'plugins.php' && isset($_GET['plugin_status']) && $_GET['plugin_status'] === 'custom') {
        // Modify the query to show the plugins you want
        // For example, filter by a specific keyword in the plugin description
        $query->query_vars['s'] = 'keyword'; // Replace 'keyword' with your custom criteria
    }
}
add_action('pre_get_posts', 'filter_plugins_by_custom_status');

function adjust_plugin_counts($views) {
    // Get the count of plugins that match your custom criteria
    $custom_count = count_custom_plugins(); // You need to define this function

    // Update the count for your custom filter
    if (isset($views['custom'])) {
        $views['custom'] = preg_replace('/\(\d+\)/', "($custom_count)", $views['custom']);
    }

    return $views;
}
add_filter('views_plugins', 'adjust_plugin_counts');

function count_custom_plugins() {
    // Query to get all plugins
    $all_plugins = get_plugins();

    // Filter plugins based on your criteria
    $custom_plugins = array_filter($all_plugins, function($plugin) {
        return strpos($plugin['Description'], 'keyword') !== false; // Replace 'keyword' with your custom criteria
    });

    return count($custom_plugins);
}
