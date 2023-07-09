<?php
/*
 * Plugin Name:       Tasks Demo
 * Plugin URI:        https://google.com/
 * Description:       The Purpose of this plugin to show some demo tasks.
 * Version:           1.0.0
 * Author:            Maria
 * Developed By:      Maria
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('show_demo_task')) {

    class show_demo_task {

        public function __construct() {
            add_action('init', array($this, 'on_ip_redirect_users'));

            add_action('init', array($this, 'register_project_post_type'));

            add_action('init', array($this, 'register_project_type_tax'));


            add_filter('template_include', array($this, 'project_archive_template'));

            add_action('wp_ajax_nopriv_get_projects', array($this, 'get_projects'));
            add_action('wp_ajax_get_projects', array($this, 'get_projects'));

            add_action('wp_enqueue_scripts', array($this, 'csp_admin_assets'));
        }



        /**
         * Redirect users on the basis of IP Address
         */
        public function on_ip_redirect_users() {


            $user_ip = $_SERVER['REMOTE_ADDR'];

            // Check if the IP starts with 77.29.
            if (true === strpos($user_ip, '77.29')) {
                $redirect_url = 'https://google.com/';

                // Redirect the user
                wp_redirect($redirect_url);
                exit();
            }
        }

        /**
         * Register the custom post type project
         */
        public function register_project_post_type() {
            $labels = array(
                'name'               => 'Projects',
                'singular_name'      => 'Project',
                'menu_name'          => 'Projects',
                'name_admin_bar'     => 'Project',
                'add_new'            => 'Add New Project',
                'add_new_item'       => 'Add New Project',
                'new_item'           => 'New Project',
                'edit_item'          => 'Edit Project',
                'view_item'          => 'View Project',
                'all_items'          => 'All Projects',
                'search_items'       => 'Search Projects',
                'parent_item_colon'  => 'Parent Projects:',
                'not_found'          => 'No projects found.',
                'not_found_in_trash' => 'No projects found in Trash.',
            );

            $args = array(
                'labels'              => $labels,
                'public'              => false,
                'publicly_queryable'  => false,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'query_var'           => true,
                'rewrite'             => array('slug' => 'projects'),
                'capability_type'     => 'post',
                'has_archive'         => true,
                'hierarchical'        => false,
                'menu_position'       => 30,
                'supports'            => array('title', 'editor'),
            );
            register_post_type('projects', $args);
        }

        /**
         * Register the custom taxanomy for project
         */
        public function register_project_type_tax() {
            $labels = array(
                'name'                       => 'Project Types',
                'singular_name'              => 'Project Type',
                'menu_name'                  => 'Project Types',
                'all_items'                  => 'All Project Types',
                'edit_item'                  => 'Edit Project Type',
                'view_item'                  => 'View Project Type',
                'update_item'                => 'Update Project Type',
                'add_new_item'               => 'Add New Project Type',
                'new_item_name'              => 'New Project Type',
                'parent_item'                => 'Parent Project Type',
                'parent_item_colon'          => 'Parent Project Type:',
                'search_items'               => 'Search Project Types',
                'popular_items'              => 'Popular Project Types',
                'separate_items_with_commas' => 'Separate project types with commas',
                'add_or_remove_items'        => 'Add or remove project types',
                'choose_from_most_used'      => 'Choose from the most used project types',
                'not_found'                  => 'No project types found.',
            );

            $args = array(
                'labels'            => $labels,
                'hierarchical'      => true,
                'public'            => true,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => array('slug' => 'project-type'),
            );

            register_taxonomy('project_type', 'projects', $args);
        }

        /**
         * Load the custom archive template for the Projects post type
         */
        public function project_archive_template($template) {

            if (!is_admin()) {
                $custom_template = plugin_dir_path(__FILE__) . '/templates/archive-projects.php';
                if (file_exists($custom_template)) {
                    return $custom_template;
                }
            }
            return $template;
        }

        /**
         * Ajax callback to return 6 projects to logged in users and 3 to for non logged in users
         * @return void
         */
        public function get_projects() {

            if (isset($_POST['nonce']) && '' != $_POST['nonce']) {

                $nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
            } else {
                $nonce = 0;
            }
            if (!wp_verify_nonce($nonce, 'demo-ajax-nonce')) {

                die('Failed ajax security check!');
            }

            $response = array();

            $number_of_projects = is_user_logged_in() ? 6 : 3;

            // Set the query arguments
            $args = array(
                'post_type' => 'projects',
                'posts_per_page' => $number_of_projects,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'project_type',
                        'field' => 'slug',
                        'terms' => 'architecture',
                    ),
                ),
            );
            $query = new WP_Query($args);

            if ($query->have_posts()) {
                $projects = array();
                while ($query->have_posts()) {
                    $query->the_post();
                    $project = array(
                        'id' => get_the_ID(),
                        'title' => get_the_title(),
                        'link' => get_permalink(),
                    );
                    $projects[] = $project;
                }

                $response['success'] = true;
                $response['data'] = $projects;
            } else {
                $response['success'] = false;
            }

            // Set the JSON response headers
            header('Content-Type: application/json');

            // Return the JSON response
            echo json_encode($response);
            wp_die();
        }
        /**
         * Enqueue Scripts
         */
        public function csp_admin_assets() {


            wp_enqueue_script('addify_csp_admin_js',  plugin_dir_url(__FILE__) . '/assets/js/front_ajax.js', array('jquery'), true, '1.0.0');
            $data = array(
                'admin_url' => admin_url('admin-ajax.php'),
                'nonce'     => wp_create_nonce('demo-ajax-nonce')

            );
            wp_localize_script('addify_csp_admin_js', 'php_vars', $data);
        }

        /**
         * Coffee function
         */
        function hs_give_me_coffee() {
            $response = wp_remote_get('https://coffee.alexflipnote.dev/random.json');


            if (!is_wp_error($response) && $response['response']['code'] === 200) {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);


                if (isset($data['file']) && !empty($data['file'])) {
                    return $data['file'];
                }
            }

            return 'https://t4.ftcdn.net/jpg/01/16/61/93/360_F_116619399_YA611bKNOW35ffK0OiyuaOcjAgXgKBui.jpg';
        }

        /**
         * Fetch Quotes using API
         */
        public  function fetch_random_kanye_quote() {
            $response = wp_remote_get('https://api.kanye.rest/');

            if (!is_wp_error($response) && $response['response']['code'] === 200) {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);

                if (isset($data['quote'])) {
                    return esc_html($data['quote']);
                }
            }

            return 'Failed to fetch a quote.';
        }
        public function kanye_quotes() {
            $quotes = '';
            for ($i = 0; $i < 5; $i++) {
                $quote = $this->fetch_random_kanye_quote();
                $quotes .= "<p>$quote</p>";
            }

            return $quotes;
        }
    }
    new show_demo_task();
}
