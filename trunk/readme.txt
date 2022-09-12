=== BAKKBONE Florist Companion ===
Contributors: leggroup
Donate link: 
Tags: package,woocommerce,filters,florist,ecommerce
Requires at least: 5.0
Tested up to: 6.0.1
Requires PHP: 7.3
Stable tag: 1.0.5
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
1. Unzip the plugin archive on your computer
2. Upload `bakkbone-florist-companion` directory to yours `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==
**What plugins do I need?**
Required:
<ul><li>WooCommerce</li>
<li>WooCommerce Address Book</li>
<li>Order Delivery Date Pro</li></ul>

Recommended:
<ul><li>Gravity Forms</li>
<li>Booster Plus for WooCommerce</li></ul>

== Screenshots ==


== Changelog ==
= 1.0.1 =
* Initial release.
= 1.0.2 =
* fix Woo Address Book integration
* backwards compatibility for GF phone mask for existing customers
= 1.0.3 =
* Bugfixes
* Adds native option to show Short Description of products in archives
* Option to change cross-sell header on cart page
= 1.0.4 =
* Fix stable tag listing
* Escape syntax patching
= 1.0.5 =
* Improve compatibility directly with WCFM
* Add "Delivery Date" column to WCFM Orders List

== Upgrade Notice ==
