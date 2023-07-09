<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
get_header();

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
    'post_type' => 'projects',
    'posts_per_page' => 6,
    'paged' => $paged,
);

$query = new WP_Query($args);

if ($query->have_posts()) :
    while ($query->have_posts()) :
        $query->the_post();
        // Display the project details as needed
?>
        <div class="pagination">
            <h2><?php the_title(); ?></h2>
            <div><?php the_content(); ?></div>
        </div>
<?php
    endwhile;
    // Display pagination
    echo '<div class="pagination">';
    echo paginate_links(array(
        'total' => $query->max_num_pages,
        'current' => $paged,
        'prev_text' => __('Prev'),
        'next_text' => __('Next'),
    ));
    echo '</div>';

else :
    // No projects found
    echo '<p>No projects found.</p>';

endif;
wp_reset_postdata();

$quote_obj = new show_demo_task();

// Fetch the link of coffee
$coffee = $quote_obj->hs_give_me_coffee();
echo '<div class="pagination">';
echo '<a href="' . $coffee . '" target="_blank">Click here to get coffee</a>';
echo '<div/>';
// Display Quotes.

$quotes = $quote_obj->kanye_quotes();
echo '<div class="pagination">';
echo '<h4>Kanye Quotes</h4>';
echo $quotes;
echo '</div>';





get_footer();
