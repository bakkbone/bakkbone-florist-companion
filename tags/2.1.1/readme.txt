=== BAKKBONE Florist Companion ===
Contributors: bakkbone
Donate link: https://ko-fi.com/bakkbone_scott
Tags: package,woocommerce,filters,florist,ecommerce
Requires at least: 6.0
Tested up to: 6.1.1
Requires PHP: 7.4
Stable tag: 2.1.1
License: GNU General Public License (GPL) 3.0
License URI: https://www.gnu.org/licenses/gpl.html

Provides standardised features for floristry websites.

== Description ==
Provides a suite of features designed specifically for floristry websites, and especially customised to the Australian market:

= Petals Network Integration =

* Receive your Petals Network orders through your WooCommerce dashboard
* Accept/reject orders directly from WooCommerce without needing to log into the Petals Exchange
* Send and receive messages to/from Petals about an order

Why? You can view/print your orders all in one place, and in one consistent format!

= PDFs =

* PDF Invoices attached to customer-facing emails and in their online account
* PDF Worksheets, printable in an easy-to-use format for your workroom, attached to order notification emails
* Access invoices and worksheets from your orders list or the order's individual page
* Customise store details display on invoices and optionally add a message at the bottom

= Checkout Fields =

* Force display of delivery address fields and gets rid of the "Ship to a different address?" question at checkout
* Add "Delivery Notes" field for notes about delivery address
* Add "Recipient Phone" field as a required field
* Delivery address not requested at checkout if order method is pickup
* Add "Card Message" as a required field, and limit the maximum length of a message
* Rename fields to match Australian/Commonwealth address standards (eg. "Suburb" instead of "City", "Postcode" instead of "Zip")

= Delivery Dates =

* Collect delivery/collection date at checkout
* Set which weekdays you deliver and your same-day delivery cutoff
* Manage closure days (eg. public holidays) and fully booked dates in your dashboard
* Optionally set timeslots (eg. 9am-12pm) for customers to choose from per delivery method and per day, and optionally set a fee for a timeslot
* Restrict delivery methods per weekday
* Block specific delivery dates per product category (eg. certain ranges not available at Valentine's Day, etc.)

= Order Status =

* Add "Scheduled" status and optional notification email to customer
* Add "Prepared" status and optional notification email to customer
* Add "Out for Delivery" status and optional notification email to customer
* Add "Ready for Collection" status and optional notification email to customer
* Add "Relayed" status for orders forwarded through a relay network or sent to another florist
* Rename "Processing" to "Received"
* Rename "Completed" to "Delivered"
* Rename "Failed" to "Payment Unsuccessful"

= Plugin Compatibility =

* Creates input masks for the [Gravity Forms](https://rocketgenius.pxf.io/bakkbone) "Telephone" field for Australian phone number formats _(affiliate link)_
* Re-words the descriptions of fields on [WooCommerce Address Book](https://wordpress.org/plugins/woo-address-book/) features

= Tweaks =

* Rename "shipping" to "delivery"
* Automatically assign guest orders placed by registered customer to the matching user (so it appears in their order history when logged in)
* Option to display product "short description" in archive listings
* Option to change heading on Cart Cross-sells section
* Option to change the text displayed when no valid delivery method is available based on recipient address
* Option to disable the "Order Comments" freetext field at checkout

== Installation ==
= Automatic installation =
1. Search for "BAKKBONE" in the Plugin Repository from the Plugins > Add New screen
= Manual installation =
1. Unzip the plugin archive on your computer
2. Upload 'bakkbone-florist-companion' directory to your '/wp-content/plugins/' directory
3. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==
**What plugins do I need?**
Required:

* [WooCommerce](https://wordpress.org/plugins/woocommerce/)

Recommended:

* [WooCommerce Address Book](https://wordpress.org/plugins/woo-address-book/)

Not required but we recommend for optimal workflow:

* [Gravity Forms](https://rocketgenius.pxf.io/bakkbone) (affiliate link)
* [Booster Elite for WooCommerce](https://booster.io/buy-booster?campaign=bkf&btr=bakkbone) (affiliate link)

**How do I use the Petals Network integration?**

1. Enable the integration via the Florist Options screen
2. Enter your Petals member number and Exchange password on the Petals Network screen
3. Select a category for the product that will be used for Petals orders and click 'Save Changes', before clicking the link below the product selection box to automatically create a product.
4. Provide the link listed on the Petals Network page to the Petals <a href="mailto:eflorist@petalsnetwork.com?subject=XML Opt-In&body=Shop Name: %0D%0AMy Full Name: %0D%0APhone: %0D%0AEmail: %0D%0AMember Number: %0D%0AXML Link: %0D%0A%0D%0APlease opt my store into XML orders alongside the Exchange, using the details above.">eFlorist Team</a>, requesting to _opt in to XML orders alongside the Exchange to the link provided_.

** What about XYZ feature? **
If there's a feature missing, please let us know in the support forum here on the WordPress Plugin Repository - we'd love to hear your feedback and know what you want to see added next!

**How do I get support?**
If the plugin isn't functioning as it should or you'd like to suggest a feature, please use the support forum here on the WordPress Plugin Repository. If you require assistance setting up the plugin and/or your website, please contact us [via our website](https://www.bakkbone.com.au/).

== Screenshots ==

1. Florist Options page
2. PDF Settings page
3. PDF Settings page
4. Delivery Date Settings page
5. Delivery Date Settings page
6. Delivery Date Blocks page
7. Product Category Blocks page
8. Delivery Timeslots Settings page
9. Petals Network Integration Settings Page
10. Delivery Date Datepicker at checkout
11. Checkout fields


== Changelog ==
= 2.1.1 =
* DEV: Fix input fields on category blocks page
* DEV: Fix input fields on date blocks page
= 2.1.0 =
* ADD: Restrict delivery methods per day
* ADD: Option for fee per time slot
* ADD: Block delivery dates per product category
* TWEAK: Fix over-capitalisation of "delivery" in backend
* TWEAK: Fix delivery time slot display in order emails
= 2.0.2 =
* TWEAK: Improve display of attribute values on PDF worksheet
= 2.0.1 =
* TWEAK: Improve display of attribute keys on PDF worksheet
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