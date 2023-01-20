=== BAKKBONE Florist Companion ===
Contributors: bakkbone
Donate link: 
Tags: package,woocommerce,filters,florist,ecommerce
Requires at least: 6.0
Tested up to: 6.1.1
Requires PHP: 7.4
Stable tag: 2.0.1
License: GNU General Public License (GPL) 3.0
License URI: https://www.gnu.org/licenses/gpl.html

Provides standardised features for floristry websites.

== Description ==
Provides a suite of features designed specifically for floristry websites:

* ADD - PDF Invoices/Worksheets - one consistent printable format - download worksheets from the dashboard, plus they're attached to order notifications - customers receive their invoice attached to their order confirmation, plus they can retrieve it from their account
* ADD - Delivery Dates - collect at checkout, manage blocked/unavailable days in your dashboard, view orders in a calendar
* ADD - Delivery Suburbs - restrict shipping methods by suburb instead of by postcode
* ADD - "Card Message" field
* ADD - setting on Florist Options page to limit the number of characters in a Card Message
* MODIFY - renames "Shipping" to "Delivery"
* MODIFY - forces display of shipping fields and gets rid of the "Ship to a different address?" question at checkout
* ADD - "Delivery Notes" field for notes about delivery address
* ADD - "Recipient Phone" field as a required field
* ADD - "Delivery Suburb" post type, for creating SEO-friendly pages for your delivery areas
* ADD - "Page Title" and "Site Title" shortcodes ([bkf_page_title] and [bkf_site_title])
* MODIFY - rename checkout fields to match Australian standards
* ADD - creates input masks for the Gravity Forms "Telephone" field for Australian phone number formats
* MODIFY - Re-words the descriptions of fields on WooCommerce Address Book features
* ADD - option to show Short Descriptions on products in archives
* ADD - option to change heading on Cart Cross-Sells section
* MODIFY - delivery address is not requested at checkout if shipping method selected is pickup
* ADD - Integration with Petals Network - receive Petals orders directly to your website (including accepting/rejecting orders)
* ADD - Automatically assign guest orders placed by registered customer to the matching user (so it appears in their order history when logged in)
* ADD - Order statuses "Scheduled", "Prepared", "Out for Delivery", "Relayed", "Ready for Collection" (plus "New" "Accepted" and "Rejected" if Petals Network integration is enabled)
* ADD - Emails to customer for "Scheduled", "Prepared", "Out for Delivery", "Ready for Collection" orders
* MODIFY - rename "Completed" order status to "Delivered" and modify email to customer accordingly
* MODIFY - rename "Processing" order status to "Received"
* MODIFY - rename "Failed" order status to "Payment Unsuccessful" for clarity

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
<ul><li><a href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a></li></ul>

Recommended:
<ul><li><a href="https://wordpress.org/plugins/woo-address-book/">WooCommerce Address Book</a></li></ul>

Not required but we recommend for optimal workflow:
<ul><li><a href="https://rocketgenius.pxf.io/bakkbone">Gravity Forms</a> (affiliate link)</li>
<li><a href="https://booster.io/buy-booster?campaign=bkf&btr=bakkbone">Booster Elite for WooCommerce</a> (affiliate link)</li></ul>
**How do I use the Petals Network integration?**
<ol><li>Enable the integration via the Florist Options screen</li>
<li>Enter your Petals member number and Exchange password on the Petals Network screen</li>
<li>Select a category for the product that will be used for Petals orders and click 'Save Changes', before clicking the link below the product selection box to automatically create a product.</li>
<li>Provide the link listed on the Petals Network page to the Petals <a href="mailto:eflorist@petalsnetwork.com?subject=XML Opt-In&body=Shop Name: %0D%0AMy Full Name: %0D%0APhone: %0D%0AEmail: %0D%0AMember Number: %0D%0AXML Link: %0D%0A%0D%0APlease opt my store into XML orders alongside the Exchange, using the details above.">eFlorist Team</a>, requesting to <em>opt in to XML orders alongside the Exchange to the link provided</em>.</li></ol>
**How do I get support?**
If the plugin isn't functioning as it should or you'd like to suggest a feature, please use the support forum here on the WordPress Plugin Repository. If you require assistance setting up the plugin and/or your website, please contact us <a href="https://www.bakkbone.com.au/" target="_blank">via our website</a>.

== Screenshots ==

1. Florist Options page

== Changelog ==
= 2.0.1 =
* TWEAK: Improve display of attributes on PDF worksheet
* TWEAK: Improved colouring of "Prepared" order status display on admin orders list
* DEV: Auto-created product for Petals orders "Private" so as not to display in category counts on frontend
* ADD: "Ready for Collection" order status and email
= 2.0.0 =
* ADD: PDF Invoices and Worksheets
* CHANGE: Petals Network integration is now direct - no BAKKBONE API subscription is required.
* TWEAK: Visual changes on settings pages and reorganised settings
* DEV: Fire Gravity Forms features only if GF is activated
* DEV: Fire WooCommerce Address Book features only if WAB is activated
* CHANGE: Card Message moved to own custom field, freeing up order comments field
* ADD: Option to disable Order Comments field
* ADD: Delivery Dates feature - collect delivery dates and even timeslots
* ADD: Delivery Suburbs feature - restrict shipping methods by suburb instead of by postcode
* REMOVE: Deprecated Order Delivery Date Pro support as a result of above
* REMOVE: Deprecated WCFM support
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
= 2.0.0 =
This version is not backwards-compatible in a lot of respects as it is a full overhaul of the plugin. Do not upgrade without BAKKBONE assistance unless you have advanced understanding of WP databases.
= 1.2.0 =
Contains functionality previously manually added to BAKKBONE-managed sites, do not upgrade without BAKKBONE assistance.
= 1.1.0 =
Contains native Petals Network integration - if already using BAKKBONE-Petals integration, do not upgrade without BAKKBONE assistance.
= 1.0.4 =
Contains XSS protections, upgrade immediately if using 1.0.3 or lower.