<?php
/**
 * The template for the waitlist in stock notification email (HTML)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/waitlist-mailout.php.
 *
 * HOWEVER, on occasion WooCommerce Waitlist will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @version 2.4.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$email = sanitize_email( $email );
do_action( 'woocommerce_email_header', $email_heading, $email_class );?>

<p><?php echo esc_html_x( 'Hi There,', 'Email salutation', 'woocommerce-waitlist' ); ?></p>

<p>
	<?php
	printf( __( '%1$s is now back in stock at %2$s. ', 'woocommerce-waitlist' ), esc_html( $product_title ), esc_html( get_bloginfo( 'name' ) ) );
	_e( 'You have been sent this email because your email address was registered on a waitlist for this product.', 'woocommerce-waitlist' );
	?>
</p>
<?php $product_link_name = strtok( $product_link, '?' ); ?>
<p>
	<?php printf( __( 'If you would like to purchase %1$s please visit the following link: %2$s', 'woocommerce-waitlist' ), esc_html( $product_title ), '<a href="' . esc_url( $product_link ) . '">' . esc_html( $product_link_name ) . '</a>' ); ?>
</p>

<?php if ( WooCommerce_Waitlist_Plugin::persistent_waitlists_are_disabled( $product_id ) && ! $triggered_manually ) {
	echo '<p>' . __( 'You have been removed from the waitlist for this product', 'woocommerce-waitlist' ) . '</p>';
}
if ( get_option( 'woocommerce_waitlist_archive_on' ) && ! email_exists( $email ) ) {
	$remove_link = add_query_arg( array(
    'wcwl_remove_user' => esc_attr( $email ),
    'product_id'       => absint( $product_id ),
		'key'              => $key,
	), get_permalink( $product_id ) );
	printf( __( 'To disassociate your email address with this product please click %1$shere%2$s.', 'woocommerce-waitlist' ), '<a href="' . esc_url( $remove_link ) . '">', '</a>' );
}
do_action( 'woocommerce_email_footer', $email_class ); ?>
