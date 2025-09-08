<?php
/**
 * XNBNE Cart Handler Class
 * File: includes/class-xnbne-cart-handler.php
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handle cart operations
 */
class XNBNE_Cart_Handler {

    /**
     * Constructor
     */

    public function __construct() {
        // Register AJAX actions (for logged in + guest users)
        add_action( 'wp_ajax_xnbne_buy_now_add_to_cart', [$this, 'handle_buy_now_request'] );
        add_action( 'wp_ajax_nopriv_xnbne_buy_now_add_to_cart', [$this, 'handle_buy_now_request'] );
    }

    /**
     * Handle buy now AJAX request
     */

    public function handle_buy_now_request() {
        check_ajax_referer( 'xnbne_buy_now_nonce', 'nonce' );

        $product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
        $quantity = isset( $_POST['quantity'] ) ? intval( $_POST['quantity'] ) : 1;

        if ( $product_id <= 0 ) {
            wp_send_json_error( [
                'message' => __( 'Invalid product.', 'xnbne-buy-now' ),
            ] );
        }

        // Clear cart if requested
        if ( isset( $_POST['clear_cart'] ) && $_POST['clear_cart'] === 'yes' ) {
            WC()->cart->empty_cart();
        }

        // Try adding to cart
        $added = WC()->cart->add_to_cart( $product_id, $quantity );

        if ( !$added ) {
            wp_send_json_error( [
                'message' => __( 'Could not add product to cart.', 'xnbne-buy-now' ),
            ] );
        }

        // $product_url = get_permalink( $product_id );
        $product_url = get_permalink( $product_id );
        $checkout_hash = !empty( $_POST['checkout_section'] ) ? sanitize_text_field( $_POST['checkout_section'] ) : 'xn-customcheckout';

        $redirect_url = $product_url . '#' . $checkout_hash;

        wp_send_json_success( [
            'message'      => __( 'Product added successfully!', 'xnbne-buy-now' ),
            'redirect_url' => $redirect_url,
            'product_id'   => $product_id,
            'quantity'     => $quantity,
        ] );
    }

    // public function handle_buy_now_request() {
    //     check_ajax_referer( 'xnbne_buy_now_nonce', 'nonce' );

    //     $product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
    //     $quantity = isset( $_POST['quantity'] ) ? intval( $_POST['quantity'] ) : 1;

    //     if ( $product_id <= 0 ) {
    //         wp_send_json_error( [
    //             'message' => __( 'Invalid product.', 'xnbne-buy-now' ),
    //         ] );
    //     }

    //     // Clear cart if requested
    //     if ( isset( $_POST['clear_cart'] ) && $_POST['clear_cart'] === 'yes' ) {
    //         WC()->cart->empty_cart();
    //     }

    //     // Add to cart
    //     $added = WC()->cart->add_to_cart( $product_id, $quantity );

    //     if ( !$added ) {
    //         wp_send_json_error( [
    //             'message' => __( 'Could not add product to cart.', 'xnbne-buy-now' ),
    //         ] );
    //     }

    //     $product = wc_get_product( $product_id );

    //     wp_send_json_success( [
    //         'message'      => sprintf( __( '"%s" added to checkout!', 'xnbne-buy-now' ), $product->get_name() ),
    //         'product_id'   => $product_id,
    //         'product_name' => $product->get_name(),
    //         'quantity'     => $quantity,
    //         'price_html'   => $product->get_price_html(),
    //     ] );
    // }

    /**
     * Sanitize product ID
     */
    private function sanitize_product_id( $product_id ) {
        $product_id = intval( $product_id );
        if ( $product_id <= 0 ) {
            throw new Exception( __( 'Invalid product ID', 'xnbne-buy-now' ) );
        }
        return $product_id;
    }

    /**
     * Sanitize quantity
     */
    private function sanitize_quantity( $quantity ) {
        $quantity = intval( $quantity ?: 1 );
        if ( $quantity < 1 ) {
            throw new Exception( __( 'Quantity must be at least 1', 'xnbne-buy-now' ) );
        }
        if ( $quantity > 999 ) {
            throw new Exception( __( 'Quantity cannot exceed 999', 'xnbne-buy-now' ) );
        }
        return $quantity;
    }

    /**
     * Build success response
     */
    private function build_success_response( $product, $quantity, $checkout_section ) {
        $checkout_url = wc_get_checkout_url();

        // Add section anchor if provided
        if ( !empty( $checkout_section ) ) {
            $checkout_url .= '#' . $checkout_section;
        }

        return array(
            'message'      => sprintf(
                __( '%s has been added to your cart.', 'xnbne-buy-now' ),
                $product->get_name()
            ),
            'redirect_url' => $checkout_url,
            'product_name' => $product->get_name(),
            'quantity'     => $quantity,
            'cart_count'   => WC()->cart->get_cart_contents_count(),
            'cart_total'   => WC()->cart->get_cart_total(),
        );
    }

    /**
     * Before add to cart hook
     */
    public function before_add_to_cart( $product_id, $quantity ) {
        // Custom logic before adding to cart
        // Example: Log the action, send analytics, etc.
        error_log( sprintf( 'XNBNE: Adding product %d (qty: %d) to cart', $product_id, $quantity ) );
    }

    /**
     * After add to cart hook
     */
    public function after_add_to_cart( $product_id, $quantity, $cart_item_key ) {
        // Custom logic after adding to cart
        // Example: Send email notifications, update inventory, etc.
        error_log( sprintf( 'XNBNE: Successfully added product %d to cart with key %s', $product_id, $cart_item_key ) );

        // Update cart fragments for AJAX cart widgets
        WC_AJAX::get_refreshed_fragments();
    }

    /**
     * Get product validation errors
     */
    public function get_product_validation_errors( $product, $quantity ) {
        $errors = array();

        if ( !$product->exists() ) {
            $errors[] = __( 'Product does not exist', 'xnbne-buy-now' );
        }

        if ( !$product->is_purchasable() ) {
            $errors[] = __( 'Product is not available for purchase', 'xnbne-buy-now' );
        }

        if ( !$product->has_enough_stock( $quantity ) ) {
            $errors[] = sprintf(
                __( 'Only %d items available in stock', 'xnbne-buy-now' ),
                $product->get_stock_quantity()
            );
        }

        if ( $product->is_type( 'variable' ) && !isset( $_POST['variation_id'] ) ) {
            $errors[] = __( 'Please select product variations', 'xnbne-buy-now' );
        }

        return $errors;
    }

    /**
     * Handle variable product variations
     */
    public function handle_variable_product( $product_id, $variation_id = null, $variation_data = array() ) {
        if ( empty( $variation_id ) ) {
            throw new Exception( __( 'Please select product options', 'xnbne-buy-now' ) );
        }

        $variation = wc_get_product( $variation_id );
        if ( !$variation || !$variation->exists() ) {
            throw new Exception( __( 'Selected variation does not exist', 'xnbne-buy-now' ) );
        }

        return $variation;
    }
}
?>