<?php
/**
 * Plugin Name: XNBNE Buy Now Elementor Widget
 * Description: Class-based OOP Elementor widget for WooCommerce Buy Now functionality
 * Version: 1.0.0
 * Author: XNBNE
 * Text Domain: xnbne-buy-now
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('XNBNE_BUY_NOW_VERSION', '1.0.0');
define('XNBNE_BUY_NOW_PLUGIN_URL', plugin_dir_url(__FILE__));
define('XNBNE_BUY_NOW_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Main Plugin Class
 */
class XNBNE_Buy_Now_Elementor {
    
    /**
     * Plugin instance
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('plugins_loaded', array($this, 'init_plugin'));
        // add_action('elementor/widgets/widgets_registered', array($this, 'register_widgets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_xnbne_buy_now_add_to_cart', array($this, 'handle_ajax_add_to_cart'));
        add_action('wp_ajax_nopriv_xnbne_buy_now_add_to_cart', array($this, 'handle_ajax_add_to_cart'));
    }
    
    /**
     * Load dependencies
     */
    private function load_dependencies() {
        // require_once XNBNE_BUY_NOW_PLUGIN_PATH . 'includes/class-xnbne-buy-now-widget.php';
        require_once XNBNE_BUY_NOW_PLUGIN_PATH . 'includes/class-xnbne-cart-handler.php';

            // Only load the widget class after Elementor is loaded
            add_action( 'elementor/widgets/register', function( $widgets_manager ) {
                require_once XNBNE_BUY_NOW_PLUGIN_PATH . 'includes/class-xnbne-buy-now-widget.php';
                $widgets_manager->register( new \XNBNE_Buy_Now_Widget() );
            });
    }
    
    /**
     * Initialize plugin
     */
    public function init_plugin() {
        // Check if Elementor is installed and activated
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', array($this, 'admin_notice_missing_elementor'));
            return;
        }
        
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'admin_notice_missing_woocommerce'));
            return;
        }
        
        // Initialize cart handler
        new XNBNE_Cart_Handler();
    }
    
    /**
     * Register widgets
     */
    public function register_widgets() {
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new XNBNE_Buy_Now_Widget());
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // Enqueue CSS
        wp_enqueue_style(
            'xnbne-buy-now-styles',
            XNBNE_BUY_NOW_PLUGIN_URL . 'assets/css/xnbne-buy-now.css',
            array(),
            XNBNE_BUY_NOW_VERSION
        );
        
        // Enqueue JavaScript
        wp_enqueue_script(
            'xnbne-buy-now-script',
            XNBNE_BUY_NOW_PLUGIN_URL . 'assets/js/xnbne-buy-now.js',
            array('jquery'),
            XNBNE_BUY_NOW_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('xnbne-buy-now-script', 'xnbne_buy_now_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('xnbne_buy_now_nonce'),
            'loading_text' => __('Processing...', 'xnbne-buy-now'),
            'error_text' => __('Something went wrong. Please try again.', 'xnbne-buy-now')
        ));
    }
    
    /**
     * Handle AJAX add to cart
     */
    public function handle_ajax_add_to_cart() {
        $cart_handler = new XNBNE_Cart_Handler();
        $cart_handler->handle_buy_now_request();
    }
    
    /**
     * Admin notice for missing Elementor
     */
    public function admin_notice_missing_elementor() {
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'xnbne-buy-now'),
            '<strong>' . esc_html__('XNBNE Buy Now Elementor Widget', 'xnbne-buy-now') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'xnbne-buy-now') . '</strong>'
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    /**
     * Admin notice for missing WooCommerce
     */
    public function admin_notice_missing_woocommerce() {
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'xnbne-buy-now'),
            '<strong>' . esc_html__('XNBNE Buy Now Elementor Widget', 'xnbne-buy-now') . '</strong>',
            '<strong>' . esc_html__('WooCommerce', 'xnbne-buy-now') . '</strong>'
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
}

// Initialize the plugin
XNBNE_Buy_Now_Elementor::get_instance();

// Create directory structure if needed
if (!file_exists(XNBNE_BUY_NOW_PLUGIN_PATH . 'includes/')) {
    wp_mkdir_p(XNBNE_BUY_NOW_PLUGIN_PATH . 'includes/');
}
if (!file_exists(XNBNE_BUY_NOW_PLUGIN_PATH . 'assets/css/')) {
    wp_mkdir_p(XNBNE_BUY_NOW_PLUGIN_PATH . 'assets/css/');
}
if (!file_exists(XNBNE_BUY_NOW_PLUGIN_PATH . 'assets/js/')) {
    wp_mkdir_p(XNBNE_BUY_NOW_PLUGIN_PATH . 'assets/js/');
}
?>