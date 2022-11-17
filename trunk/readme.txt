=== BAKKBONE Florist Companion ===
Contributors: leggroup
Donate link: 
Tags: package,woocommerce,filters,florist,ecommerce
Requires at least: 5.0
Tested up to: 6.1.1
Requires PHP: 7.3
Stable tag: 1.0.8
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

== Installation ==
= Automatic installation =
<ol><li>Search for "BAKKBONE" in the Plugin Repository from the Plugins > Add New screen</li></ol>
= Manual installation =
<ol><li>Unzip the plugin archive on your computer</li>
<li>Upload `bakkbone-florist-companion` directory to yours `/wp-content/plugins/` directory</li>
<li>Activate the plugin through the 'Plugins' menu in WordPress</li></ol>

== Frequently Asked Questions ==
**What plugins do I need?**
Required:
<ul><li><a href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a></li>
<li><a href="https://wordpress.org/plugins/woo-address-book/">WooCommerce Address Book</a></li>
<li><a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?ref=329&campaign=bkf">Order Delivery Date Pro</a></li></ul>

Recommended:
<ul><li><a href="https://www.gravityforms.com">Gravity Forms</a></li>
<li><a href="https://booster.io/buy-booster?campaign=bkf&btr=bakkbone">Booster Elite for WooCommerce</a></li></ul>
**Why don't all my delivery dates show in WCFM?**
The delivery date column calls on the "Field Label" for delivery dates in Order Delivery Date Pro. If you change the label, only orders placed where the label exactly matches the current label will display correctly.
We recommend contacting BAKKBONE Support before making such a change (if you're a BAKKBONE customer).

== Screenshots ==


== Changelog ==
= 1.0.8 =
* Force valid recipient phone number on checkout
= 1.0.7 =
* Add delivery notes to order emails
= 1.0.6 =
* Tidies up admin menu
* Delivery Suburb post type now supports custom text per suburb directly entered on the Suburb entry
* Re-worded "delivery notes" field description in checkout
* Tweak card message display
= 1.0.5 =
* Improve compatibility directly with WCFM
* Add "Delivery Date" column to WCFM Orders List
= 1.0.4 =
First release hosted on WordPress Plugin Repository
* Fix stable tag listing
* Escape syntax patching
= 1.0.3 =
* Bugfixes
* Adds native option to show Short Description of products in archives
* Option to change cross-sell header on cart page
= 1.0.2 =
* fix Woo Address Book integration
* backwards compatibility for GF phone mask for existing customers
= 1.0.1 =
* Initial release.

== Upgrade Notice ==
= 1.0.4 =
Contains XSS protections, upgrade immediately if using 1.0.3 or lower.