=== BAKKBONE Florist Companion ===
Contributors: bakkbone
Donate link: https://ko-fi.com/bakkbone_scott
Tags: package,woocommerce,filters,florist,ecommerce
Requires at least: 6.0
Tested up to: 6.3
Requires PHP: 7.4
Stable tag: 3.0.7
License: GNU General Public License (GPL) 3.0
License URI: https://www.gnu.org/licenses/gpl.html

Provides standardized features for floristry websites.

== Description ==
Provides a suite of features designed specifically for floristry websites, with the ability to customize to suit your country/region:

### Petals Network Integration

* Receive your Petals Network orders through your WooCommerce dashboard
* Send Petals orders from your website dashboard
* Accept/reject orders directly from WooCommerce without needing to log into the Petals Exchange
* Send and receive messages to/from Petals about an order

Why? You can view/print your orders all in one place, and in one consistent format!

### PDFs

* PDF Invoices attached to customer-facing emails and in their online account
* PDF Worksheets, printable in an easy-to-use format for your workroom, attached to order notification emails
* Access invoices and worksheets from your orders list or the order's individual page
* Customize store details display on invoices and optionally add a message at the bottom

### Checkout Fields

* Force display of delivery address fields and gets rid of the "Ship to a different address?" question at checkout
* Add "Delivery Notes" field for notes about delivery address
* Add "Recipient Phone" field as a required field
* Delivery address not requested at checkout if order method is pickup
* Add "Card Message" as a required field, and limit the maximum length of a message
* Option to require confirmation of email at checkout for logged-out users
* Option to disable the "Order Comments" freetext field at checkout

### Delivery Dates

* Collect delivery/collection date at checkout
* Set which weekdays you deliver
* Set your same-day delivery cutoff globally
* Set separate same-day cutoffs for specific delivery methods (ie. delivery areas)
* Manage closure days (eg. public holidays) and fully booked dates in your dashboard
* Optionally set timeslots (eg. 9am-12pm) for customers to choose from per delivery method and per day, and optionally set a fee for a timeslot
* Restrict delivery methods per weekday
* Block specific delivery dates per product category (eg. certain ranges not available at Valentine's Day, etc.)
* Optionally set an additional fee per weekday
* Optionally set an additional fee for specific dates
* View all orders on a calendar, and export order list as a CSV or PDF

### Order Status

* Change default display on admin orders list to "active" orders (not yet delivered, not rejected/cancelled/refunded)
* Add "Scheduled" status and optional notification email to customer
* Add "Prepared" status and optional notification email to customer
* Add "Out for Delivery" status and optional notification email to customer
* Add "Ready for Collection" status and optional notification email to customer
* Add "Relayed" status for orders forwarded through a relay network or sent to another florist
* Rename "Processing" to "Received"
* Rename "Completed" to "Delivered"
* Rename "Failed" to "Payment Unsuccessful"

### Plugin Compatibility

* Creates input masks for the [Gravity Forms](https://rocketgenius.pxf.io/bakkbone) "Telephone" field for Australian phone number formats _(affiliate link)_
* Creates an Australian address format for the [Gravity Forms](https://rocketgenius.pxf.io/bakkbone) Address field _(affiliate link)_
* Re-words the descriptions of fields on [WooCommerce Address Book](https://wordpress.org/plugins/woo-address-book/) features
* Provides compatibility in PDF invoices/worksheets for Product Add-Ons from [Booster for WooCommerce](https://booster.io/buy-booster?campaign=bkf&btr=bakkbone) _(affiliate link)_

### Localization

* Rename fields on frontend to match local address standards (eg. "Suburb" instead of "City", "Postcode" instead of "Zip")
* Option to change heading on Cart Cross-sells section
* Option to change the text displayed when no valid delivery method is available based on recipient address

### Order Notifier

* Optional feature to play a sound when new orders arrive
* Choose from 9 possible sounds for your alert

### Tweaks

* Rename "shipping" to "delivery"
* Automatically assign guest orders placed by registered customer to the matching user (so it appears in their order history when logged in)
* Option to display product "short description" in archive listings

== Installation ==
### Automatic installation
1. Search for "BAKKBONE" in the Plugin Repository from the Plugins > Add New screen

### Manual installation
1. Unzip the plugin archive on your computer
2. Upload `bakkbone-florist-companion` directory to your `/wp-content/plugins/` directory
3. Activate the plugin through the `Plugins` menu in WordPress

== Frequently Asked Questions ==
= What plugins do I need? =

* [WooCommerce](https://wordpress.org/plugins/woocommerce/)

= What plugins are compatible/integrated?

* [WooCommerce Address Book](https://wordpress.org/plugins/woo-address-book/)
* [Gravity Forms](https://rocketgenius.pxf.io/bakkbone) (affiliate link)
* [Booster for WooCommerce](https://booster.io/buy-booster?campaign=bkf&btr=bakkbone) (affiliate link)

= How do I use the Petals Network Integration? =

1. Enable the integration via the Florist Options screen
2. Enter your Petals member number and Exchange password on the Petals Network screen
3. Select a category for the product that will be used for Petals orders and click 'Save Changes', before clicking the link below the product selection box to automatically create a product.
4. Provide the link listed on the Petals Network page to the Petals <a href="mailto:eflorist@petalsnetwork.com?subject=XML Opt-In&body=Shop Name: %0D%0AMy Full Name: %0D%0APhone: %0D%0AEmail: %0D%0AMember Number: %0D%0AXML Link: %0D%0A%0D%0APlease opt my store into XML orders alongside the Exchange, using the details above.">eFlorist Team</a>, requesting to _opt in to XML orders alongside the Exchange to the link provided_.

Please note that the integration is not functional until Petals advises they have processed your request.

= Why isn't there a phone/address format for my country for Gravity Forms? =

We're happy to include your country in the next release - please add your request in the support forum!

= What about XYZ feature? =

If there's a feature missing, please let us know in the support forum here on the WordPress Plugin Repository - we'd love to hear your feedback and know what you want to see added next!

= How do I get support? =

If the plugin isn't functioning as it should or you'd like to suggest a feature, please use the support forum here on the WordPress Plugin Repository. If you require assistance setting up the plugin and/or your website, please contact us [via our website](https://www.floristwebsites.com.au/).

== Screenshots ==

1. Delivery Date Datepicker at checkout
2. Checkout fields
3. Florist Options page
4. Weekdays page
5. PDF Settings page
6. PDF Settings page
7. Delivery Date Settings page
8. Delivery Date Settings page
9. Delivery Date Blocks page
10. Product Category Blocks page
11. Delivery Timeslots Settings page
12. Petals Network Integration Settings Page
13. Delivery Suburbs Settings page
14. Order Notifier Toggle

== Changelog ==
### 3.0.7
* ADD: Feature to require email confirmation at checkout for logged-out users
* DEV: Tweak order notifier sound effect testing on options page
### 3.0.6
* TWEAK: Improve display of delivery date field at checkout
* TEST: Tested WP 6.3.1
* TEST: Tested Woo 8.1.1
* TEST: Tested Elementor 3.16.4
* TEST: Tested Elementor Pro 3.16.2
### 3.0.5
* TWEAK: Set default email content type to HTML
* DEV: Fix bugs with email overrides
* DEV: RSS feed in admin widget no longer shows endless entries
* TEST: Tested WP 6.3.1
* TEST: Tested Woo 8.0.3
### 3.0.4
* DEV: Fix Petals-related emails being available when Petals functions disabled
* TEST: Tested WP 6.3
* TEST: Tested Woo 8.0.2
* TEST: Tested Elementor 3.15.2
### 3.0.3.1
* TWEAK: Delete delivery date block by clicking on calendar event, remove separate list on page
* TWEAK: Delete delivery date category block by clicking on calendar event, remove separate list on page
* TWEAK: Delete date-specific fee by clicking on calendar event, remove separate list on page
* TWEAK: Fix responsive properties on DD options pages
* TWEAK: Remove deprecated notice about tax on date-specific fees options page
* DEV: Fix issue with closed dates display on category blocks page causing JS error
* TEST: Tested Elementor 3.15.1
### 3.0.3
* ADD: Page size option for PDFs
* TWEAK: Display delivery method costs on delivery suburbs options page
* DEV: Smoother responsive display on delivery suburbs options page
* DEV: Fix `bkf_order_has_physical()`
* UPDATE: Update Dompdf to v2.0.3
* UPDATE: Update ACF to v6.1.7
* UPDATE: Update Action Scheduler to v3.6.1
* UPDATE: Update FullCalendar to v6.1.8
### 3.0.2.2
* DEV: Fix critical error in implementation of 3.0.2.1
### 3.0.2.1
* DEV: Initialize most classes only if WooCommerce is active
### 3.0.2
* DEV: Fix critical error in Gravity Forms localization integration
### 3.0.1
* ADD: `Florist Tools` page to resend invoices and download documents
* TWEAK: Deprecate and remove `bkf-select` in favor of `Select2`
* DEV: Include full `Select2` instead of base
* DEV: Tidy ajax functions
* DEV: Tidy tabbing in all php code
* DEV: Fix some missed localizations
* DEV: Fix error with timeslots in emails
### 3.0.0
* ADD: Compatibility in PDFs for Booster for WooCommerce's Product Add-Ons
* ADD: Modal on click of event in delivery calendar
* TWEAK: All fees generated by BKF are now *inclusive* of tax
* TWEAK: `bkf_get_currency()` now accepts $echo argument - true to echo, false to return, false by default
* DEV: Fix display of fees in PDF invoices/worksheets
* DEV: Include `Select2` library so no longer required via CDN
### 2.7.3
* TWEAK: Improve display of pickup orders on worksheet PDF and color-code pickup/delivery title
* TWEAK: Improve display of delivery fee on worksheet/invoice PDF
* DEV: Fix display of delivery calendar when name contains double quotes
* DEV: Fix display of timeslots in correct timezone
* DEV: Redundancy for display of timeslots on orders if timeslot has been deleted in admin
* DEV: Escape remaining echoed localization strings
* DEV: Disable non-functioning `Mark as Delivered` option in Petals messaging
### 2.7.2
* DEV: Fix formatting of "Today's Deliveries" widget on admin dashboard
* DEV: Improve display of pickup orders in admin dashboard widgets
* DEV: Move order type field to `woocommerce_checkout_fields` hook
* DEV: Override most core emails to wrap them in WooCommerce template
* DEV: Squash bug causing some localization fields to not save
* DEV: Fix HTML signature causing PHP error when being inserted
* DEV: Add 'Generator' meta tag in frontend
* DEV: Squash bug with blocked dates in delivery date metabox on order edit page
* DEV: Decentralize localization to improve compatibility
* DEV: Add countdown on characters for Card Message field
### 2.7.1
* TWEAK: Squash bug with closed dates causing calendar to fail on checkout
### 2.7.0
* ADD: Phone Order form
* ADD: "Suburb" field localization
* ADD: Australian address format for Gravity Forms
* TWEAK: Include currency symbol in fee amount fields
* TWEAK: "Delivery Suburbs" feature out of testing - removed option to disable
* TWEAK: Add 'Clear' button to delivery date filter field in admin orders list
* TWEAK: Move shared functions out of class and prepend with `bkf_`, plus add new functions
* TWEAK: Move all ajax functions to class `BkfAjax`, add new ajax functions
* TWEAK: Remove "local pickup" shipping methods from Delivery Suburbs options page
* TWEAK: Color-coding on blocked dates in admin area when admin role override is allowing dates to be selected
* TWEAK: Include date-specific fees in overnight purge
* TWEAK: Include currency symbol in date-specific fees calendar
* TWEAK: Hide methods with no timeslots in list on timeslots settings page
* TWEAK: Recurring blocked/closed days in calendar and datepicker now display only from current week forward
* TWEAK: Host font for BKF admin pages locally
* TWEAK: Color-code pickup vs. delivery on delivery calendar
* DEV: Patch bug where timeslots field may not correctly display when only one delivery method is available
* DEV: Remove unnecessary `check()` function in `BkfSuburbs` class
* DEV: Additional localization on Petals options page
* DEV: Fix dashicon display on BKF admin pages
* DEV: Fix order count in admin menu to match 'Active' count
* DEV: Fix time detection in admin bar greeting
* DEV: Delete redundant `incl/petals/decision.php`
* DEV: Improve localization strings on same day cutoff settings page
* DEV: Fix localization on Petals options page
* DEV: Reduce padding on date lists on blocks/fees pages
* DEV: Fix bug with delivery method by weekday restrictions at checkout
* DEV: Switch to minified version of FullCalendar script
* DEV: Fix display of pickups on delivery calendar
* DEV: Fix display of delivery calendar PDF export
* DEV: Fix number of products in list on Petals Options page
* UPDATE: Update Action Scheduler to v3.5.4
### 2.6.4
* DEV: Re-squash timeslot checkout validation bug
* TEST: WP 6.2 tested
### 2.6.3
* ADD: Admin dashboard widget with most recent orders
* ADD: Admin dashboard widget with plugin news/updates
* DEV: Add `payment_complete()` to outbound Petals order processing
* DEV: Fix consistency of order statuses for "Active" filter
* DEV: Fix display of order notes on inbound Petals orders
* DEV: Optimize admin order list for inbound Petals orders
* TWEAK: Move some functions to a callable class (`BakkboneFloristCompanion`) to allow sharing - `get_rss_feed($url)`, `full_count()`, `all_count()`
### 2.6.2
* ADD: Admin dashboard widget with today's deliveries
* ADD: Move "all" order filter to end of list, and make "active" the default filter on orders list.
* TWEAK: Hide completed/cancelled/refunded/failed/rejected orders from orders list by default
* TWEAK: Update order count in admin menu to include "New (Petals)" status
* DEV: De-clutter Petals message emails
* DEV: Add "petals_order_number" placeholder in some emails
* DEV: Hide Petals order notes from feed in admin dashboard widget
### 2.6.1
* ADD: Order Notifier feature
* DEV: Fix font consistency
* DEV: Change required capability for settings pages from `manage_options` to `manage_woocommerce`
* DEV: Localization inside inline js
* TWEAK: Display "Delivered" action in Actions column for wc-collect status
* TWEAK: Rename columns on order list
### 2.6.0
* ADD: Petals inbound orders
* DEV: Fix timeslot validation at checkout
* DEV: Fix undefined nonce indexes
* DEV: Fix post URL for Petals messaging
### 2.5.2
* DEV: Fix card message label in emails
### 2.5.1
* DEV: Patch localization at checkout
### 2.5.0
* ADD: Options page for localization
* TWEAK: Massive localization overhaul
* TWEAK: Move CS Heading and No-Ship to Localization settings
* TWEAK: Universal localization of "Download" for PDFs
* TWEAK: Change default pre-ordering period to 8 weeks
### 2.4.4
* DEV: Squash bug in timeslot field at checkout
### 2.4.3
* DEV: Fix time display on Method-Specific Timeslots page
* DEV: Fix time display on Timeslots page
### 2.4.2
* DEV: Fix delivery date field showing for virtual orders
### 2.4.1
* TWEAK: Fix CSS for "Order Type" field
* DEV: Fix POST URL for Petals decision
* DEV: Amend date() to wp_date() to resolve timezone issue
### 2.4.0
* ADD: "Order Type" field at top of checkout if a pickup method is available
* TWEAK: Move pickup features to own file for clarity
* TWEAK: Remove date_default_timezone_set()
* TWEAK: Improve wording of shipping field validation
* DEV: Upgrade minimum PHP to 7.4
* DEV: Fix variable names in base file
* DEV: Fix validation of shipping fields at checkout
* DEV: Fix translation of shipping to delivery
* DEV: Improve localization throughout
* DEV: Remove unnecessary comment clutter in code
* DEV: Condense CS Heading code
* DEV: Fix php error presenting when delivery weekday validated
* DEV: Identify some JS scripts
### 2.3.5
* DEV: Fix reported issue with delivery address fields at checkout (as relating to changing to/from local pickup methods)
* DEV: Fix typo in Petals new order email
* DEV: Fix divs on timeslot page
### 2.3.4
* DEV: Squash bug where same-day delivery cutoff was reflecting as passed on checkout regardless of time
### 2.3.3
* DEV: Fix hooks in filter.php
* DEV: Further fixes to timeslot metabox save fix from v2.3.2
### 2.3.2
* ADD: Filter orders by delivery date
* DEV: Fix timeslot metabox save function
* DEV: Fix delivery date validation at checkout
### 2.3.1
* DEV: Fix display of timeslots page
* DEV: Fix fees options in menu
### 2.3.0
* ADD: Custom same-day delivery cutoffs per delivery method
* DEV: Remove unnecessary comment clutter
* DEV: Fix function names in catblocks.php
### 2.2.8
* TWEAK: Add help tabs to all BKF pages and documentation link to plugin list
* DEV: Fix reported bug in delivery calendar
### 2.2.7
* DEV: Reapply patch for previously reported Petals bug in v2.2.6
### 2.2.6
* TWEAK: Add category blocks to nightly purge of past delivery date blocks
* DEV: Fix reported bug where Petals orders crash on receipt
* UPDATE: Update Dompdf to v2.0.2
### 2.2.5
* DEV: Fix reported bug where timeslots field stuck on checkout page if no time slots configured in backend, preventing checkout
### 2.2.4
* DEV: Fix reported bug where no delivery methods populate when adding a time slot in backend
### 2.2.3
* DEV: Fix reported bug where timeslots not validated at checkout
### 2.2.2
* DEV: Fix timeslot array in dd execution
### 2.2.1
* DEV: Fix display of delivery calendar
### 2.2.0
* ADD: Option for fee per weekday
* ADD: Option for fee per specific delivery date
* ADD: CSV/PDF exports on Delivery Calendar
* TWEAK: Reorganize submenus
* DEV: Apply priorities to actions for submenu functions
* DEV: Add localization to strings on DD Blocks settings page
* DEV: Add placeholder to fields on DD Blocks settings page
### 2.1.1
* DEV: Fix input fields on category blocks page
* DEV: Fix input fields on date blocks page
### 2.1.0
* ADD: Restrict delivery methods per day
* ADD: Option for fee per time slot
* ADD: Block delivery dates per product category
* TWEAK: Fix over-capitalization of "delivery" in backend
* TWEAK: Fix delivery time slot display in order emails
### 2.0.2
* TWEAK: Improve display of attribute values on PDF worksheet
### 2.0.1
* ADD: "Ready for Collection" order status and email
* TWEAK: Improve display of attribute keys on PDF worksheet
* TWEAK: Improved coloring of "Prepared" order status display on admin orders list
* DEV: Auto-created product for Petals orders "Private" so as not to display in category counts on frontend
### 2.0.0
* ADD: PDF Invoices and Worksheets
* ADD: Option to disable Order Comments field
* ADD: Delivery Dates feature - collect delivery dates and even timeslots
* ADD: Delivery Suburbs feature - restrict shipping methods by suburb instead of by postcode
* TWEAK: Petals Network integration is now direct - no BAKKBONE API subscription is required.
* TWEAK: Visual changes on settings pages and reorganized settings
* TWEAK: Card Message moved to own custom field, freeing up order comments field
* DEV: Fire Gravity Forms features only if GF is activated
* DEV: Fire WooCommerce Address Book features only if WAB is activated
* REMOVE: Deprecated Order Delivery Date Pro support as a result of above
* REMOVE: Deprecated WCFM support
### 1.2.1
* TWEAK: Fix display of "Delivery details" header at checkout
### 1.2.0
* ADD: Order statuses "Scheduled", "Prepared", "Out for Delivery", "Relayed" (plus "New" "Accepted" and "Rejected" if Petals Network integration is enabled)
* ADD: Emails to customer for "Scheduled", "Prepared", "Out for Delivery" orders
* TWEAK: rename "Completed" order status to "Delivered" and modify email to customer accordingly
* DEV: Settings and support links added to plugins list
### 1.1.0
* ADD: Ability to integrate Petals Network to receive/accept/reject orders in same format as your own orders
* ADD: Hide delivery address fields in checkout when pickup is selected
* ADD: Option to customize text displayed when customer enters a suburb for delivery that you do not service
* ADD: Automatically assign guest orders placed by registered customer to the matching user (so it appears in their order history when logged in)
* TWEAK: Improve display of delivery date in WCFM
* TWEAK: Set default values for freetext-based plugin options
### 1.0.8
* TWEAK: Force valid recipient phone number on checkout
### 1.0.7
* ADD: Add delivery notes to order emails
### 1.0.6
* ADD: Delivery Suburb post type now supports custom text per suburb directly entered on the Suburb entry
* TWEAK: Tidies up admin menu
* TWEAK: Re-worded "delivery notes" field description in checkout
* TWEAK: Tweak card message display
### 1.0.5
* ADD: Improve compatibility directly with WCFM
* ADD: Add "Delivery Date" column to WCFM Orders List
### 1.0.4
First release hosted on WordPress Plugin Repository
* TWEAK: Fix stable tag listing
* DEV: Escape syntax patching
### 1.0.3
* ADD: Adds native option to show Short Description of products in archives
* ADD: Option to change cross-sell header on cart page
* DEV: Bugfixes
### 1.0.2
* ADD: backwards compatibility for GF phone mask for existing customers
* TWEAK: fix WooCommerce Address Book integration
### 1.0.1
* Initial release.

== Upgrade Notice ==
### 3.0.2.2
Fixes an urgent issue with v3.0.2.1 - if on v3.0.2.1, please update immediately.
### 3.0.0
* If you use BKF fees, these are now *inclusive* of tax and may need to be adjusted in your settings.
* Adds compatibility for Product Add-Ons via Booster for WooCommerce
### 2.7.0
MAJOR UPDATE - check changelog for details.
### 2.6.0
Petals Inbound order feature (send order into Petals Network) added.
### 2.5.1
Fixes bug at checkout from v2.5.0
### 2.3.3
Fixes bug on order list page from v2.3.2
### 2.2.3
Update is urgent if your site uses time slots - patches time slots not being validated at checkout
### 2.0.0
This version is not backwards-compatible in a lot of respects as it is a full overhaul of the plugin. Do not upgrade without BAKKBONE assistance unless you have advanced understanding of WP databases.
### 1.2.0
Contains functionality previously manually added to BAKKBONE-managed sites, do not upgrade without BAKKBONE assistance.
### 1.1.0
Contains native Petals Network integration - if already using BAKKBONE-Petals integration, do not upgrade without BAKKBONE assistance.
### 1.0.4
Contains XSS protections, upgrade immediately if using 1.0.3 or lower.