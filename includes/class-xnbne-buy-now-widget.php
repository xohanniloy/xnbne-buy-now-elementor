<?php
/**
 * XNBNE Buy Now Elementor Widget Class
 * File: includes/class-xnbne-buy-now-widget.php
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * XNBNE Buy Now Widget Class
 */
class XNBNE_Buy_Now_Widget extends \Elementor\Widget_Base {
    
    /**
     * Widget name
     */
    public function get_name() {
        return 'xnbne_buy_now_widget';
    }
    
    /**
     * Widget title
     */
    public function get_title() {
        return __('XNBNE Buy Now Button', 'xnbne-buy-now');
    }
    
    /**
     * Widget icon
     */
    public function get_icon() {
        return 'eicon-button';
    }
    
    /**
     * Widget categories
     */
    public function get_categories() {
        return ['woocommerce-elements'];
    }
    
    /**
     * Widget keywords
     */
    public function get_keywords() {
        return ['woocommerce', 'buy now', 'product', 'cart', 'checkout', 'xnbne'];
    }
    
    /**
     * Register widget controls
     */
    protected function _register_controls() {
        $this->register_content_controls();
        $this->register_style_controls();
    }
    
    /**
     * Register content controls
     */
    private function register_content_controls() {
        // Content Section
        $this->start_controls_section(
            'xnbne_content_section',
            [
                'label' => __('Content Settings', 'xnbne-buy-now'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'xnbne_button_text',
            [
                'label' => __('Button Text', 'xnbne-buy-now'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Buy Now', 'xnbne-buy-now'),
                'placeholder' => __('Enter button text', 'xnbne-buy-now'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );
        
        $this->add_control(
            'xnbne_checkout_section_id',
            [
                'label' => __('Checkout Section ID', 'xnbne-buy-now'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'xn-customcheckout',
                'description' => __('Enter the ID of your checkout section (without #)', 'xnbne-buy-now'),
                'placeholder' => 'xn-customcheckout',
            ]
        );
        
        $this->add_control(
            'xnbne_show_quantity',
            [
                'label' => __('Show Quantity Selector', 'xnbne-buy-now'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'xnbne-buy-now'),
                'label_off' => __('No', 'xnbne-buy-now'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        
        $this->add_control(
            'xnbne_clear_cart',
            [
                'label' => __('Clear Cart Before Adding', 'xnbne-buy-now'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'xnbne-buy-now'),
                'label_off' => __('No', 'xnbne-buy-now'),
                'return_value' => 'yes',
                'default' => 'yes',
                'description' => __('Clear existing cart items before adding this product', 'xnbne-buy-now'),
            ]
        );
        
        $this->add_control(
            'xnbne_show_success_message',
            [
                'label' => __('Show Success Message', 'xnbne-buy-now'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'xnbne-buy-now'),
                'label_off' => __('No', 'xnbne-buy-now'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->end_controls_section();
    }
    
    /**
     * Register style controls
     */
    private function register_style_controls() {
        // Button Style Section
        $this->start_controls_section(
            'xnbne_button_style_section',
            [
                'label' => __('Button Style', 'xnbne-buy-now'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_responsive_control(
            'xnbne_button_align',
            [
                'label' => __('Alignment', 'xnbne-buy-now'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'xnbne-buy-now'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'xnbne-buy-now'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'xnbne-buy-now'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'prefix_class' => 'elementor%s-align-',
                'default' => 'left',
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'xnbne_button_typography',
                'selector' => '{{WRAPPER}} .xnbne-buy-now-button',
            ]
        );
        
        $this->add_responsive_control(
            'xnbne_button_width',
            [
                'label' => __('Button Width', 'xnbne-buy-now'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'auto',
                'options' => [
                    'auto' => __('Auto', 'xnbne-buy-now'),
                    'full' => __('Full Width', 'xnbne-buy-now'),
                    'custom' => __('Custom', 'xnbne-buy-now'),
                ],
            ]
        );
        
        $this->add_responsive_control(
            'xnbne_button_custom_width',
            [
                'label' => __('Custom Width', 'xnbne-buy-now'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'condition' => [
                    'xnbne_button_width' => 'custom',
                ],
                'selectors' => [
                    '{{WRAPPER}} .xnbne-buy-now-button' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->start_controls_tabs('xnbne_button_tabs');
        
        // Normal Tab
        $this->start_controls_tab(
            'xnbne_button_normal',
            [
                'label' => __('Normal', 'xnbne-buy-now'),
            ]
        );
        
        $this->add_control(
            'xnbne_button_color',
            [
                'label' => __('Text Color', 'xnbne-buy-now'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .xnbne-buy-now-button' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'xnbne_button_background',
                'label' => __('Background', 'xnbne-buy-now'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .xnbne-buy-now-button',
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'xnbne_button_border',
                'selector' => '{{WRAPPER}} .xnbne-buy-now-button',
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'xnbne_button_box_shadow',
                'selector' => '{{WRAPPER}} .xnbne-buy-now-button',
            ]
        );
        
        $this->end_controls_tab();
        
        // Hover Tab
        $this->start_controls_tab(
            'xnbne_button_hover',
            [
                'label' => __('Hover', 'xnbne-buy-now'),
            ]
        );
        
        $this->add_control(
            'xnbne_button_hover_color',
            [
                'label' => __('Text Color', 'xnbne-buy-now'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .xnbne-buy-now-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'xnbne_button_hover_background',
                'label' => __('Background', 'xnbne-buy-now'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .xnbne-buy-now-button:hover',
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'xnbne_button_hover_border',
                'selector' => '{{WRAPPER}} .xnbne-buy-now-button:hover',
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'xnbne_button_hover_box_shadow',
                'selector' => '{{WRAPPER}} .xnbne-buy-now-button:hover',
            ]
        );
        
        $this->add_control(
            'xnbne_button_hover_transition',
            [
                'label' => __('Transition Duration', 'xnbne-buy-now'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0.3,
                ],
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .xnbne-buy-now-button' => 'transition: all {{SIZE}}s ease;',
                ],
            ]
        );
        
        $this->end_controls_tab();
        $this->end_controls_tabs();
        
        $this->add_responsive_control(
            'xnbne_button_padding',
            [
                'label' => __('Padding', 'xnbne-buy-now'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => 12,
                    'right' => 24,
                    'bottom' => 12,
                    'left' => 24,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .xnbne-buy-now-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );
        
        $this->add_responsive_control(
            'xnbne_button_border_radius',
            [
                'label' => __('Border Radius', 'xnbne-buy-now'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .xnbne-buy-now-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Quantity Style Section
        $this->start_controls_section(
            'xnbne_quantity_style_section',
            [
                'label' => __('Quantity Style', 'xnbne-buy-now'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'xnbne_show_quantity' => 'yes',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'xnbne_quantity_typography',
                'selector' => '{{WRAPPER}} .xnbne-quantity-input',
            ]
        );
        
        $this->add_control(
            'xnbne_quantity_color',
            [
                'label' => __('Text Color', 'xnbne-buy-now'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .xnbne-quantity-input' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'xnbne_quantity_background',
            [
                'label' => __('Background Color', 'xnbne-buy-now'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .xnbne-quantity-input' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'xnbne_quantity_border',
                'selector' => '{{WRAPPER}} .xnbne-quantity-input',
            ]
        );
        
        $this->add_control(
            'xnbne_quantity_border_radius',
            [
                'label' => __('Border Radius', 'xnbne-buy-now'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .xnbne-quantity-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'xnbne_quantity_padding',
            [
                'label' => __('Padding', 'xnbne-buy-now'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .xnbne-quantity-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
    }
    
    /**
     * Render widget output
     */
    protected function render() {
        global $product;
        
        // Check if we're on a product page and product exists
        if (!is_product() || !$product) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div class="xnbne-editor-notice">';
                echo '<p>' . __('This widget only works on WooCommerce product pages.', 'xnbne-buy-now') . '</p>';
                echo '</div>';
            }
            return;
        }
        
        $settings = $this->get_settings_for_display();
        $product_id = $product->get_id();
        $button_classes = ['xnbne-buy-now-button'];
        
        // Add width class
        if ($settings['xnbne_button_width'] === 'full') {
            $button_classes[] = 'xnbne-full-width';
        }
        ?>
        <div class="xnbne-buy-now-widget-wrapper">
            <?php if ($settings['xnbne_show_quantity'] === 'yes') : ?>
                <div class="xnbne-quantity-wrapper">
                    <label for="xnbne-qty-<?php echo esc_attr($product_id); ?>" class="xnbne-quantity-label">
                        <?php _e('Quantity:', 'xnbne-buy-now'); ?>
                    </label>
                    <input type="number" 
                           id="xnbne-qty-<?php echo esc_attr($product_id); ?>" 
                           class="xnbne-quantity-input" 
                           min="1" 
                           value="1" 
                           step="1">
                </div>
            <?php endif; ?>
            
            <button type="button" 
                    class="<?php echo esc_attr(implode(' ', $button_classes)); ?>" 
                    data-product-id="<?php echo esc_attr($product_id); ?>"
                    data-checkout-section="<?php echo esc_attr($settings['xnbne_checkout_section_id']); ?>"
                    data-clear-cart="<?php echo esc_attr($settings['xnbne_clear_cart']); ?>"
                    data-show-message="<?php echo esc_attr($settings['xnbne_show_success_message']); ?>">
                <span class="xnbne-button-text"><?php echo esc_html($settings['xnbne_button_text']); ?></span>
                <span class="xnbne-button-loader" style="display: none;">
                    <span class="xnbne-spinner"></span>
                    <?php _e('Processing...', 'xnbne-buy-now'); ?>
                </span>
            </button>
        </div>
        <?php
    }
}
?>