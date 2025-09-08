/**
 * XNBNE Buy Now Widget JavaScript
 * File: assets/js/xnbne-buy-now.js
 */

(function ($) {
  'use strict';

  /**
   * XNBNE Buy Now Handler Class
   */
  class XNBNEBuyNowHandler {
    constructor() {
      this.init();
    }

    /**
     * Initialize the handler
     */
    init() {
      this.bindEvents();
      this.handlePageLoad();
    }

    /**
     * Bind events
     */
    bindEvents() {
      $(document).on('click', '.xnbne-buy-now-button', this.handleBuyNowClick.bind(this));
      $(document).on('input', '.xnbne-quantity-input', this.handleQuantityChange.bind(this));
      $(window).on('load', this.handleSmoothScroll.bind(this));
    }

    /**
     * Handle buy now button click
     */
    handleBuyNowClick(e) {
      e.preventDefault();

      const $button = $(e.currentTarget);
      const $wrapper = $button.closest('.xnbne-buy-now-widget-wrapper');

      // Prevent double clicks
      if ($button.hasClass('xnbne-processing')) {
        return;
      }

      // Get data
      const data = this.extractButtonData($button, $wrapper);

      // Validate quantity
      if (!this.validateQuantity(data.quantity)) {
        return;
      }

      // Start processing
      this.setLoadingState($button, true);

      // Make AJAX request
      this.makeAjaxRequest(data)
        .done(response => this.handleSuccess(response, $button))
        .fail(xhr => this.handleError(xhr, $button))
        .always(() => this.setLoadingState($button, false));
    }



    /**
     * Extract button data
     */
    extractButtonData($button, $wrapper) {
      const $qtyInput = $wrapper.find('.xnbne-quantity-input');

      return {
        product_id: $button.data('product-id'),
        quantity: $qtyInput.length ? parseInt($qtyInput.val()) : 1,
        checkout_section: $button.data('checkout-section'),
        clear_cart: $button.data('clear-cart'),
        show_message: $button.data('show-message'),
      };
    }

    /**
     * Validate quantity
     */
    validateQuantity(quantity) {
      if (isNaN(quantity) || quantity < 1) {
        this.showMessage(xnbne_buy_now_ajax.quantity_error || 'Please enter a valid quantity.', 'error');
        return false;
      }

      if (quantity > 999) {
        this.showMessage(xnbne_buy_now_ajax.max_quantity_error || 'Quantity cannot exceed 999.', 'error');
        return false;
      }

      return true;
    }

    /**
     * Make AJAX request
     */
    makeAjaxRequest(data) {
      return $.ajax({
        url: xnbne_buy_now_ajax.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'xnbne_buy_now_add_to_cart',
          product_id: data.product_id,
          quantity: data.quantity,
          checkout_section: data.checkout_section,
          clear_cart: data.clear_cart,
          nonce: xnbne_buy_now_ajax.nonce,
        },
      });
    }

    handleSuccess(response, $button) {
      if (!response || !response.data) {
        this.showMessage('Unexpected response from server.', 'error');
        return;
      }

      const data = response.data;
      const showMessage = $button.data('show-message') !== 'no';

      if (showMessage && data.message) {
        this.showMessage(data.message, 'success');
      }

      if (data.redirect_url) {
        this.redirectToCheckout(data.redirect_url);
      }

    }

  

   // AJAX success


    /**
     * Handle error response
     */
    handleError(xhr, $button) {
      let message = xnbne_buy_now_ajax.error_text;

      if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
        message = xhr.responseJSON.data.message;
      }

      this.showMessage(message, 'error');

      // Trigger custom error event
      $(document).trigger('xnbne_add_to_cart_error', [xhr.responseJSON]);
    }



    /**
     * Set loading state
     */
    setLoadingState($button, loading) {
      const $text = $button.find('.xnbne-button-text');
      const $loader = $button.find('.xnbne-button-loader');

      if (loading) {
        $button.addClass('xnbne-processing').prop('disabled', true);
        $text.hide();
        $loader.show();
      } else {
        $button.removeClass('xnbne-processing').prop('disabled', false);
        $text.show();
        $loader.hide();
      }
    }

    /**
     * Show message
     */
    showMessage(message, type = 'info') {
      // Remove existing messages
      $('.xnbne-message').remove();

      const messageClass = `xnbne-message xnbne-message-${type}`;
      const $message = $(`<div class="${messageClass}"><span>${message}</span></div>`);

      // Add message to page
      $('body').append($message);

      // Show with animation
      $message.addClass('xnbne-show');

      // Auto remove after delay
      setTimeout(() => {
        $message.removeClass('xnbne-show').addClass('xnbne-hide');
        setTimeout(() => $message.remove(), 300);
      }, 4000);
    }

    /**
     * Handle quantity change
     */
    handleQuantityChange(e) {
      const $input = $(e.currentTarget);
      let value = parseInt($input.val());

      // Ensure minimum value
      if (isNaN(value) || value < 1) {
        $input.val(1);
        return;
      }

      // Ensure maximum value
      if (value > 999) {
        $input.val(999);
        return;
      }
    }

    /**
     * Redirect to checkout
     */
    // redirectToCheckout(url) {
    //   // Add slight delay for better UX
    //   setTimeout(() => {
    //     window.location.href = url;
    //   }, 500);
    // }

    // redirectToCheckout(url) {
    //   setTimeout(() => {
    //     window.location.assign(url); // force reload + hash
    //   }, 500);
    // }

    redirectToCheckout(url) {
      setTimeout(() => {
        // Force full page reload to the URL with hash
        window.location.href = url;
        window.location.reload(); // ensures reload even if hash is same
      }, 500);
    }


    /**
     * Handle smooth scroll to section
     */
    handleSmoothScroll() {
      const hash = window.location.hash;
      if (hash && hash.length > 1) {
        const $target = $(hash);
        if ($target.length) {
          this.smoothScrollTo($target);
        }
      }
    }

    /**
     * Smooth scroll to element
     */
 
    smoothScrollTo($element, offset = 100) {
      const targetPosition = $element.offset().top - offset;
      $('html, body').animate({ scrollTop: targetPosition }, { duration: 800, easing: 'swing' });
  }


    /**
     * Handle page load
     */
    handlePageLoad() {
      // Initialize quantity inputs
      $('.xnbne-quantity-input').each(function () {
        const $input = $(this);
        if (!$input.val() || parseInt($input.val()) < 1) {
          $input.val(1);
        }
      });
    }
  }

  /**
   * Initialize when document is ready
   */
  // $(document).ready(function () {
  //   new XNBNEBuyNowHandler();
  // });

    $(document).ready(function () {
    new XNBNEBuyNowHandler();
  });

  /**
   * Expose handler for external use
   */
  window.XNBNEBuyNowHandler = XNBNEBuyNowHandler;
})(jQuery);
