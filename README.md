# FloristPress
Provides standardised updates for floristry websites

Download at: https://wordpress.org/plugins/bakkbone-florist-companion/

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

* Creates triggers for [AutomatorWP](https://wordpress.org/plugins/automatorwp) when delivery dates are marked as blocked (more items for this integration are planned)
* Creates an action for [AutomatorWP](https://wordpress.org/plugins/automatorwp) to mark a delivery date as blocked (more items for this integration are planned)
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

# How can I report security bugs?

You can report security bugs through the Patchstack Vulnerability Disclosure Program. The Patchstack team help validate, triage and handle any security vulnerabilities. [Report a security vulnerability.](https://patchstack.com/database/vdp/bakkbone-florist-companion)
