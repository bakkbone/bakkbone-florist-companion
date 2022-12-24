=== BAKKBONE Florist Companion ===
Contributors: leggroup
Donate link: 
Tags: package,woocommerce,filters,florist,ecommerce
Requires at least: 5.0
Tested up to: 6.1.1
Requires PHP: 7.3
Stable tag: 1.2.1
License: GNU General Public License (GPL) 3.0
License URI: https://www.gnu.org/licenses/gpl.html

Provides standardised features for floristry websites.

== Description ==
Provides standardised updates for floristry websites:

* MODIFY - repurpose "Order Notes" field as "Card Message" field, and rename it as such throughout WooCommerce
* ADD - setting on Florist Options page to limit the number of characters in a Card Message
* MODIFY - renames "Shipping" to "Delivery"
* MODIFY - forces display of shipping fields and gets rid of the "Ship to a different address?" question at checkout
* ADD - "Delivery Notes" field for notes about delivery address
* ADD - "Recipient Phone" field as a required field
* ADD - "Delivery Suburb" post type, for creating SEO-friendly pages for your delivery areas
* ADD - "Page Title" and "Site Title" shortcodes ([bkf_page_title] and [bkf_site_title])
* MODIFY - rename checkout fields to match Australian standards
* ADD - creates input masks for the Gravity Forms "Telephone" field for Australian phone number formats
* ADD - option to show Short Descriptions on products in archives
* ADD - option to change heading on Cart Cross-Sells section
* ADD - Delivery Date column on WCFM Orders List
* MODIFY - delivery address is not requested at checkout if shipping method selected is pickup
* ADD - Integration with Petals Network
* ADD - Automatically assign guest orders placed by registered customer to the matching user (so it appears in their order history when logged in)
* ADD - Order statuses "Scheduled", "Prepared", "Out for Delivery", "Relayed" (plus "New" "Accepted" and "Rejected" if Petals Network integration is enabled)
* ADD - Emails to customer for "Scheduled", "Prepared", "Out for Delivery" orders
* MODIFY - rename "Completed" order status to "Delivered" and modify email to customer accordingly

== Installation ==
= Automatic installation =
<ol><li>Search for "BAKKBONE" in the Plugin Repository from the Plugins > Add New screen</li></ol>
= Manual installation =
<ol><li>Unzip the plugin archive on your computer</li>
<li>Upload 'bakkbone-florist-companion' directory to your '/wp-content/plugins/' directory</li>
<li>Activate the plugin through the 'Plugins' menu in WordPress</li></ol>

== Frequently Asked Questions ==
**What plugins do I need?**
Required:
<ul><li><a href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a></li>
<li><a href="https://wordpress.org/plugins/woo-address-book/">WooCommerce Address Book</a></li>
<li><a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?ref=329&campaign=bkf">Order Delivery Date Pro</a> (affiliate link)</li></ul>

Recommended:
<ul><li><a href="https://rocketgenius.pxf.io/bakkbone">Gravity Forms</a> (affiliate link)</li>
<li><a href="https://booster.io/buy-booster?campaign=bkf&btr=bakkbone">Booster Elite for WooCommerce</a> (affiliate link)</li></ul>
**How do I use the Petals Network integration?**
This requires configuration on the backend, and a plan that includes the BAKKBONE API. Please <a href="mailto:hello@floristwebsites.au?subject=Petals Network Integration&body=Shop Name: %0D%0AMy Full Name: %0D%0APhone: %0D%0AEmail: %0D%0AWebsite: %0D%0A">contact us</a> to proceed.

== Screenshots ==

1. Florist Options page

== Changelog ==
= 1.2.1 =
* TWEAK: Fix display of "Delivery details" header at checkout
= 1.2.0 =
* ADD: Order statuses "Scheduled", "Prepared", "Out for Delivery", "Relayed" (plus "New" "Accepted" and "Rejected" if Petals Network integration is enabled)
* ADD: Emails to customer for "Scheduled", "Prepared", "Out for Delivery" orders
* TWEAK: rename "Completed" order status to "Delivered" and modify email to customer accordingly
* DEV: Settings and support links added to plugins list
= 1.1.0 =
* ADD: Ability to integrate Petals Network to receive/accept/reject orders in same format as your own orders
* ADD: Hide delivery address fields in checkout when pickup is selected
* ADD: Option to customise text displayed when customer enters a suburb for delivery that you do not service
* TWEAK: Improve display of delivery date in WCFM
* TWEAK: Set default values for freetext-based plugin options
* ADD: Automatically assign guest orders placed by registered customer to the matching user (so it appears in their order history when logged in)
= 1.0.8 =
* TWEAK: Force valid recipient phone number on checkout
= 1.0.7 =
* ADD: Add delivery notes to order emails
= 1.0.6 =
* TWEAK: Tidies up admin menu
* ADD: Delivery Suburb post type now supports custom text per suburb directly entered on the Suburb entry
* TWEAK: Re-worded "delivery notes" field description in checkout
* TWEAK: Tweak card message display
= 1.0.5 =
* ADD: Improve compatibility directly with WCFM
* ADD: Add "Delivery Date" column to WCFM Orders List
= 1.0.4 =
First release hosted on WordPress Plugin Repository
* TWEAK: Fix stable tag listing
* DEV: Escape syntax patching
= 1.0.3 =
* DEV: Bugfixes
* ADD: Adds native option to show Short Description of products in archives
* ADD: Option to change cross-sell header on cart page
= 1.0.2 =
* TWEAK: fix Woo Address Book integration
* ADD: backwards compatibility for GF phone mask for existing customers
= 1.0.1 =
* Initial release.

== Upgrade Notice ==
= 1.2.0 =
Contains functionality previously manually added to BAKKBONE-managed sites, do not upgrade without BAKKBONE assistance.
= 1.1.0 =
Contains native Petals Network integration - if already using BAKKBONE-Petals integration, do not upgrade without BAKKBONE assistance.
= 1.0.4 =
Contains XSS protections, upgrade immediately if using 1.0.3 or lower.
