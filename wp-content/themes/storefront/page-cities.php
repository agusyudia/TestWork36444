<?php

/**
 * Template Name: Cities Table
 */

get_header();

do_action('before_cities_table');

// Query the database
global $wpdb;
$cities = $wpdb->get_results("
    SELECT p.ID, p.post_title, t.name AS country, pm1.meta_value AS latitude, pm2.meta_value AS longitude
    FROM $wpdb->posts p
    LEFT JOIN $wpdb->term_relationships tr ON (p.ID = tr.object_id)
    LEFT JOIN $wpdb->term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
    LEFT JOIN $wpdb->terms t ON (tt.term_id = t.term_id)
    LEFT JOIN $wpdb->postmeta pm1 ON (p.ID = pm1.post_id AND pm1.meta_key = '_latitude')
    LEFT JOIN $wpdb->postmeta pm2 ON (p.ID = pm2.post_id AND pm2.meta_key = '_longitude')
    WHERE p.post_type = 'cities' AND p.post_status = 'publish'
");

?>

<form id="search-cities">
    <input type="text" id="city-search" placeholder="Search cities..." />
</form>

<table>
    <thead>
        <tr>
            <th>City</th>
            <th>Country</th>
            <th>Temperature</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($cities as $city) : ?>
            <tr>
                <td><?php echo esc_html($city->post_title); ?></td>
                <td><?php echo esc_html($city->country); ?></td>
                <td><?php echo esc_html(rand(15, 30)); ?> °C</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
do_action('after_cities_table');

get_footer();
