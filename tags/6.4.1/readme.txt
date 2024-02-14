=== FloristPress – Customize your Woo store for your Florist ===
Contributors: bakkbone
Donate link: https://ko-fi.com/bakkbone_scott
Tags: woocommerce,florist,ecommerce
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 6.4.1
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl.html

Provides standardized features for floristry websites – built by florists, for florists.

== Description ==
Provides a suite of highly customizable features designed specifically for floristry websites, with internationalization front of mind.

### Delivery Suburbs

* Adds the ability to limit specific delivery methods to specific suburbs within their Zones
* Adds a Custom Post Type of `Delivery Suburb` for easy setup of SEO-friendly pages for each suburb you deliver to

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

* Validate phone numbers to match the address location (eg. billing address in New Zealand must provide valid NZ phone number, recipient in Australia must have valid Australian phone number)
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
* Set your lead time or same-day delivery cutoff globally
* Set separate lead times for specific delivery methods (ie. delivery areas)
* Manage closure days (eg. public holidays) and fully booked dates in your dashboard
* Optionally set timeslots (eg. 9am-12pm) for customers to choose from per delivery method and per day, and optionally set a fee for a timeslot
* Restrict delivery methods per weekday
* Block specific delivery dates per product category (eg. certain ranges not available at Valentine's Day, etc.)
* Optionally set an additional fee per weekday
* Optionally set an additional fee for specific dates
* View all orders on a calendar, and export order list as a CSV or PDF

### Order Status

* Option to automatically change all orders to "Processed" if you don't regularly log into the dashboard and rely on emails instead
* Change default display on admin orders list to "active" orders (not yet delivered, not rejected/cancelled/refunded)
* Add "Scheduled" status and optional notification email to customer
* Add "Prepared" status and optional notification email to customer
* Add "Out for Delivery" status and optional notification email to customer
* Add "Ready for Collection" status and optional notification email to customer
* Add "Relayed" status for orders forwarded through a relay network or sent to another florist
* Add "Collected" status for orders picked up by customer
* Add "Processed" status for virtual orders
* Rename "Completed" to "Delivered"
* Rename "Processing" to "Received"
* Rename "Failed" to "Payment Unsuccessful"

### Admin Dashboard

* "Today's Deliveries" Widget (all orders with today as delivery/collection date)
* "Recent Orders" Widget (5 latest orders)
* "Delivery Methods" Widget (all delivery methods configured, with cost, list of suburbs, and edit link)
* Admin bar navigation for all FloristPress-generated pages
* Option to add admin bar navigation of WooCommerce settings

### Plugin Compatibility

* Creates input masks for the [Gravity Forms](https://rocketgenius.pxf.io/bakkbone) "Telephone" field for Australian phone number formats _(affiliate link)_
* Creates an Australian address format for the [Gravity Forms](https://rocketgenius.pxf.io/bakkbone) Address field _(affiliate link)_
* Re-words the descriptions of fields on [WooCommerce Address Book](https://wordpress.org/plugins/woo-address-book/) features
* Provides compatibility in PDF invoices/worksheets for Product Add-Ons from [Booster for WooCommerce](https://booster.io/buy-booster?campaign=bkf&btr=bakkbone) _(affiliate link)_
* Acknowledges duplicated functionality with [Breakdance](https://breakdance.com/ref/357/) Page Builder when clashing features enabled, via persistent admin notice _(affiliate link)_

### Page Builder Integrations

* [Breakdance](https://breakdance.com/ref/357/) _(affiliate link)_ – ajax delivery suburb search element

### Localization

* Rename fields on frontend to match local address standards (eg. "Suburb" instead of "City", "Postcode" instead of "Zip")
* Option to change heading on Cart Cross-sells section
* Option to change the text displayed when no valid delivery method is available based on recipient address

### Order Notifier

* Optional feature to play a sound when new orders arrive
* Choose from 9 possible sounds for your alert

### Shortcodes

* `[bkf_site_title]` – your site's title
* `[bkf_page_title]` – current post/page's title
* `[bkf_suburb_search]` – ajax search of delivery suburbs with rates

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
= Is the plugin well-documented? =

We believe so! Check it out [here](https://docs.floristpress.org/).

= What plugins do I need? =

* [WooCommerce](https://wordpress.org/plugins/woocommerce/)

= What plugins/themes are compatible/integrated?

Check the current listing in our documentation: [Third Party Compatibility](https://docs.floristpress.org/3pc/)

= The plugin/theme I want to use isn't compatible, what do I do?

We recommend a double-headed approach:

First, open a topic on [Canny](https://floristpress.canny.io/), so we can investigate whether it's a quick fix and demonstrate there's interest for the feature.

Then, reach out to the plugin or theme's developer and ask them to integrate with us. Feel free to use (or modify) this template for contacting them:

`I've requested integration with __________ and FloristPress via their Canny at https://floristpress.canny.io/. The FloristPress volunteer team is happy to work with developers wherever they can, plus they welcome both issues and pull requests on GitHub. If your integration requires a hook to be added, just let them know – they can probably add it! Drop them a line at developers@floristpress.org and let them know how they can help – or feel free to interact on Canny. For plugins, they also highly recommend adding a "BKF tested up to" header to your plugin/theme, and you can add a "BKF requires at least" header if needed – these are honoured in v3.2.0 onwards.`

= How do I request or upvote a feature? =

If there's a feature missing, please let us know on [Canny](https://floristpress.canny.io/) - we'd love to hear your feedback and know what you want to see added next!

= How do I get support? =

If the plugin isn't functioning as it should or you'd like to suggest a feature, please use the support forum here on the WordPress Plugin Repository. If you require assistance setting up the plugin and/or your website, please contact us [via our website](https://www.floristwebsites.com.au/).

== Screenshots ==

1. Delivery Date Datepicker at checkout
2. Checkout fields
3. Florist Options page
4. Weekdays and Lead Times page
5. PDF Settings page
6. PDF Settings page
7. Delivery Date Settings page
8. Delivery Method Restrictions page
9. Delivery Date Blocks page
10. Product Category Blocks page
11. Delivery Timeslots Settings page
12. Petals Network Integration Settings Page
13. Order Notifier Toggle

== Changelog ==

We maintain the changelog here from no more than one previous minor release (x.X.x - middle digit changes) until present, in line with the versions we host in the repository. The full changelog is hosted on [Github](https://github.com/bakkbone/bakkbone-florist-companion/blob/main/CHANGELOG.md), as are earlier releases of the plugin.

### [Unreleased]

#### Changed
- Planned: Add debug logging for multiple functions across FloristPress when `bkf_debug()` is true
- Planned: Sort orders on calendar PDF export by date

### [6.4.1] - 2024-02-14

#### Fixed
- Delivery cost calculations causing error in suburb search

### [6.4.0] - 2024-02-12

#### Added
- `bkf_debug` filter (boolean)
- `bkf_debug()` (returns boolean affected by above filter - default is value of `WP_DEBUG`)
- `bkf_debug_log($message, $level = 'debug')` (adds to FloristPress log in WC_Logger)

#### Changed
- Tidied code in Petals outbound order Ajax processing plus improve handling of improper messages received

#### Fixed
- Rectified timeslot field not appearing as intended at checkout
- Rectified order type field not functioning as intended at checkout
- Delivery Dates fees settings not saving correctly
- Added check to force appearance in db of fees settings due to error in some previous release
- Delivery calendar PDF meta query error resolved
- Strip slashes in more meta fields in PDFs
- `Processed` status added to paid statuses filter
- `Invoiced` status added to pending statuses filter
- Add slashes as required in category blocks settings

### [6.3.1] - 2024-01-20

#### Fixed
- Prevent fatal error if Woo not enabled

### [6.3.0] - 2024-01-20

#### Added
- Customization of delivery date field label
- FloristPress checkout fields to WooCommerce Orders REST API
- FloristPress delivery suburbs to WooCommerce Shipping Zone Methods REST API

#### Changed
- Incompatibility declared for WooCommerce Block Checkout

#### Fixed
- Fix plugin compatibility notices

== Upgrade Notice ==
