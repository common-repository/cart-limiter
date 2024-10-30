<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR;

/**
 * Limiter Interface.
 *
 */
interface LimiterInterface {

    public function validate_limits( $cart_item_key = null, $context = '' );
}
