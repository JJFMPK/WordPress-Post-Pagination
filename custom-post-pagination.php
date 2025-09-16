<?php
/*
Plugin Name: Modern Page Navigation
Plugin URI: https://example.com/modern-page-navigation
Description: A smart pagination system for WordPress posts with multiple pages using <!--nextpage--> tag
Version: 1.0.1
Author: Mansoor Mehdi
Text Domain: modern-page-navigation
*/

// سیکیورٹی چیک
if (!defined('ABSPATH')) {
    exit;
}

class ModernPageNavigation {
    
    public function __construct() {
        add_filter('wp_link_pages', array($this, 'modern_pagination'), 10, 2);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }
    
    // سٹائل شیٹ لوڈ کریں
    public function enqueue_styles() {
        wp_enqueue_style('modern-page-nav', plugin_dir_url(__FILE__) . 'style.css', array(), '1.0.1');
    }
    
    // جدید پیج نیویگیشن
    public function modern_pagination($output, $args) {
        global $page, $numpages;
        
        if ($numpages <= 1) {
            return $output;
        }
        
        $defaults = array(
            'before' => '<div class="page-links">',
            'after' => '</div>',
            'link_before' => '',
            'link_after' => '',
            'next_or_number' => 'number',
            'separator' => ' ',
            'nextpagelink' => __('Next Page'),
            'previouspagelink' => __('Previous Page'),
            'pagelink' => '%',
            'echo' => 1
        );
        
        $args = wp_parse_args($args, $defaults);
        
        if ($numpages <= 7) {
            return $this->simple_pagination($page, $numpages, $args);
        }
        
        return $this->advanced_pagination($page, $numpages, $args);
    }
    
    // سادہ نیویگیشن (7 صفحات تک)
    private function simple_pagination($current, $total, $args) {
        $links = array();
        
        for ($i = 1; $i <= $total; $i++) {
            if ($i == $current) {
                $links[] = '<span class="page-numbers current">' . $i . '</span>';
            } else {
                $links[] = $this->get_page_link($i, $args['link_before'] . $i . $args['link_after'], $args);
            }
        }
        
        return $args['before'] . implode($args['separator'], $links) . $args['after'];
    }
    
    // جدید نیویگیشن (7 صفحات سے زیادہ)
    private function advanced_pagination($current, $total, $args) {
        $links = array();
        
        // پہلا صفحہ
        if ($current > 1) {
            $links[] = $this->get_page_link(1, $args['link_before'] . '1' . $args['link_after'], $args);
        }
        
        // پچھلا صفحہ بٹن
        if ($current > 1) {
            $links[] = $this->get_page_link($current - 1, $args['link_before'] . '&laquo;' . $args['link_after'], $args);
        }
        
        // درمیانی صفحات سے پہلے dots
        if ($current > 3) {
            $links[] = '<span class="page-numbers dots">...</span>';
        }
        
        // موجودہ صفحہ اور اس کے اردگرد کے صفحات
        $start = max(1, $current - 2);
        $end = min($total, $current + 2);
        
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $current) {
                $links[] = '<span class="page-numbers current">' . $i . '</span>';
            } else {
                $links[] = $this->get_page_link($i, $args['link_before'] . $i . $args['link_after'], $args);
            }
        }
        
        // درمیانی صفحات کے بعد dots
        if ($current < $total - 2) {
            $links[] = '<span class="page-numbers dots">...</span>';
        }
        
        // اگلا صفحہ بٹن
        if ($current < $total) {
            $links[] = $this->get_page_link($current + 1, $args['link_before'] . '&raquo;' . $args['link_after'], $args);
        }
        
        // آخری صفحہ
        if ($current < $total) {
            $links[] = $this->get_page_link($total, $args['link_before'] . $total . $args['link_after'], $args);
        }
        
        return $args['before'] . implode($args['separator'], $links) . $args['after'];
    }
    
    // پوسٹ کے اندر صفحات کے لیے صحیح لنک بنائیں
    private function get_page_link($page_number, $text, $args) {
        global $post;

        $base_link = get_permalink($post->ID);

        if ($page_number == 1) {
            $link = $base_link;
        } else {
            $link = trailingslashit($base_link) . user_trailingslashit('page/' . $page_number, 'single_paged');
        }

        return '<a href="' . esc_url($link) . '" class="page-numbers">' . $text . '</a>';
    }
}

// پلگ ان کو شروع کریں
new ModernPageNavigation();
