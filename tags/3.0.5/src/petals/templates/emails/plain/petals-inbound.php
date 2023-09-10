<?php
/**
 * Petals Inbound email (plain text)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/plain/petals-inbound.php.
 *
 */

defined( 'ABSPATH' ) || exit;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo sprintf( __( 'The outcome of sending order {order_number} via Petals is listed below.', 'bakkbone-florist-companion' ) ) . "\n\n";
printf( $email->message->comment_content );

echo "\n\n----------------------------------------\n\n";

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
}

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
