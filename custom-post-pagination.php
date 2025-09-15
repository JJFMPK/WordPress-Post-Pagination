<?php
/**
 * Plugin Name: Custom Post Pagination
 * Description: Multi-page posts کے لیے condensed pagination (1 2 3 ... 50) Bootstrap-style pills اور "Go to Page" input کے ساتھ۔
 * Version: 1.4
 * Author: آMansoor Mehdi
 */

// Pagination generate کرنے والا فنکشن
function cpp_paginate_post_links() {
    global $page, $numpages, $multipage;

    if (!$multipage) return;

    $args = array(
        'base'      => add_query_arg('page','%#%'),
        'format'    => '',
        'total'     => $numpages,
        'current'   => $page,
        'mid_size'  => 2,   // درمیان کے صفحات
        'end_size'  => 1,   // شروع اور آخر کے صفحات
        'prev_text' => __('« Prev'),
        'next_text' => __('Next »'),
        'type'      => 'array',
        'show_all'  => false // ✅ condensed pagination only
    );

    $links = paginate_links($args);

    if (is_array($links)) {
        echo '<nav class="cpp-pagination-wrapper" aria-label="Page navigation">';
        echo '<ul class="cpp-pagination">';
        foreach ($links as $link) {
            if (strpos($link, 'current') !== false) {
                echo '<li class="active"><span class="page-link">' . strip_tags($link) . '</span></li>';
            } elseif (strpos($link, 'dots') !== false) {
                echo '<li class="disabled"><span class="page-link">…</span></li>';
            } else {
                echo '<li>' . str_replace('page-numbers', 'page-link', $link) . '</li>';
            }
        }
        echo '</ul>';

        // Go to page form
        echo '<div class="cpp-goto">';
        echo '<form method="get" action="">';
        echo '<label for="cpp-goto-input">Go to page:</label> ';
        echo '<input type="number" min="1" max="' . $numpages . '" name="page" id="cpp-goto-input" value="' . $page . '">';
        echo '<button type="submit">Go</button>';
        echo '</form>';
        echo '</div>';

        echo '</nav>';
    }
}

// Default pagination ہٹائیں اور نیا والا لگائیں
function cpp_replace_post_pagination() {
    if (is_single()) {
        remove_filter('the_content','wp_link_pages');
        add_filter('the_content','cpp_filter_content_with_pagination');
    }
}
add_action('wp_footer','cpp_replace_post_pagination');

function cpp_filter_content_with_pagination($content) {
    if (is_singular() && in_the_loop() && is_main_query()) {
        ob_start();
        cpp_paginate_post_links();
        $pagination = ob_get_clean();
        return $content . $pagination;
    }
    return $content;
}

// CSS include کریں
function cpp_enqueue_styles() {
    wp_enqueue_style('cpp-styles', plugin_dir_url(__FILE__) . 'style.css');
}
add_action('wp_enqueue_scripts', 'cpp_enqueue_styles');
