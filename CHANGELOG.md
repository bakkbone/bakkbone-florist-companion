# Changelog

### [Unreleased]

#### Changed
- Planned: Add debug logging for multiple functions across FloristPress when `bkf_debug()` is true
- Planned: Sort orders on calendar PDF export by date

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

### [6.2.0] - 2024-01-06

#### Fixed
- Fix erroneous delivery date validation after checkout

### [6.1.1] - 2024-01-04

#### Fixed
- Fix email hooks

### [6.1.0] - 2024-01-04

#### Fixed
- Fix Petals CPT function causing 500 error
- Fix timeslot field not hiding on checkout load when not required
- Force shipping cache refresh so that `ship_type` field works as intended

### [6.0.0] - 2023-12-30

#### Changed
- Back to inline loading for delivery dates at checkout
- Settings pages migrated into WooCommerce Settings
- Florist Tools page moved to Tools Menu
- Simplified TinyMCE controls for some shortcodes, added TinyMCE control for suburb search shortcode

#### Fixed
- Code tidying as a result of the settings migration, merged some classes and functions that were similar
- Fixed `WC_Tax` being called for checkout fees when storewide tax not enabled
- Remove `bkf_compare_semantic_version()` and use PHP `compare_version()` instead

### [5.1.0] - 2023-12-24

#### Added
- Delivery Suburb Search element for Breakdance

#### Fixed
- Minify CSS and JS
- Fix dd purge removing incorrect items

### [5.0.6] - 2023-12-23

#### Fixed
- Dompdf v2.0.4 compatibility – make DejaVu Sans default font

### [5.0.5.1] - 2023-12-19

#### Fixed
- Fix ACF path

### [5.0.5] - 2023-12-19

#### Fixed
- Fix slashes in suburb search shortcode's results

### [5.0.4] - 2023-12-19

#### Fixed
- Tweak order statuses shown in dash widgets

#### Security
- Update Dompdf to v2.0.4

### [5.0.3] - 2023-12-13

#### Fixed
- Patch issue with checkout js

#### Security
- Tested Woo 8.4.0

### [5.0.2] - 2023-12-04

#### Fixed
- Add redundancy in checkout js for no shipping method yet selected

### [5.0.1] - 2023-11-28

#### Fixed
- Prevent adding empty delivery date blocks

### [5.0.0] - 2023-11-27

#### Added
- Woo settings admin bar feature
- Session storage of delivery notes and card message
- Lead time in days alongside cutoff time
- Realtime delivery date validation on checkout submission to mitigate orders submitted after cutoff

#### Changed
- Delivery date availability now checked via Ajax to be closer to realtime display at checkout
- Improve display of email confirmation field at checkout
- Move DD scripts for checkout to js asset instead of inline
- Force visibility of tooltips with reason date is unavailable on checkout calendar
- Improve session storage of delivery date and timeslot

#### Removed
- Delete support for WooCommerce Address Book

#### Fixed
- Fix activation settings list
- Restructure folders
- Update meta calls on delivery calendar to combat warnings

#### Security
- Update FullCalendar to Scheduler under GPL3 license arrangement

### [4.1.0] - 2023-11-23

#### Added
- Auto process feature
- Validation of card message to exclude emoji characters

#### Fixed
- Improve validation of phone numbers to prevent errors
- Localize email verification field label
- Strip slashes from card message in PDFs

#### Security
- Tested Woo 8.3.1

### [4.0.2] - 2023-11-22

#### Changed
- Formatting changes on worksheet

#### Fixed
- Patch phone number validation not running on virtual orders
- Recent orders widget patched to exclude `shop_order_refund`
- Preserve line breaks in card message in emails/worksheet/thank you page

### [4.0.1] - 2023-11-21

#### Fixed
- Patch local pickup not appearing

### [4.0.0] - 2023-11-16

#### Removed
- Retire legacy delivery suburbs feature

#### Security
- Cleanse legacy delivery suburbs feature from database

### [3.5.1.1] - 2023-11-15

#### Fixed
- Patch "Processed" and "Collected" orders not showing in calendar etc.

### [3.5.1] - 2023-11-15

#### Fixed
- Patch order PDF download buttons

### [3.5.0] - 2023-11-13

#### Added
- "Collected" and "Processed" order statuses

#### Security
- Tested Woo 8.2.2

### [3.4.3] - 2023-11-12

#### Fixed
- Patch checkout error for virtual orders
- Patch backslashes in some checkout fields

#### Security
- Tested WP 6.4

### [3.4.2] - 2023-11-02

#### Added
- `[bkf_suburb_search]` shortcode
- Ajax search of suburb costs in Delivery Methods dashboard widget

#### Fixed
- Patch delivery methods for modern delivery suburbs feature on phone orders
- Patch PHP warnings on phone order screen
- Patch shipping tax on phone orders
- Add `invoiced` and `phone-draft` to `woocommerce_valid_order_statuses_for_payment` filter
- Patch 500 error for SiteGround customers on admin dashboard
- Simplify `BKF_Shortcodes` class

### [3.4.1.1] - 2023-10-31

#### Fixed
- Patch PHP warning on activation

### [3.4.1] - 2023-10-31

#### Added
- `bkf_is_woocommerce_active()` – returns bool

#### Fixed
- Patch PHP error if Woo not active

### [3.4.0] - 2023-10-31

#### Added
- Advanced validation for phone fields at checkout
- Admin bar link to delivery date blocks
- Admin dashboard widget with blocked delivery dates

#### Fixed
- Patch incorrect links in admin bar menu
- Convert download links on order edit screen to buttons
- Re-word send invoice order action on order edit screen
- Improve db query for Today's Deliveries widget
- Refine display conditions for order notifier JS

### [3.3.2] - 2023-10-28

#### Fixed
- Patch orders list critical error

### [3.3.1] - 2023-10-27

#### Fixed
- Patch unclosed `<strong>` tags in Today's Deliveries dashboard widget
- Patch "add node" being accidentally commented out

### [3.3.0] - 2023-10-27

#### Changed
- Shortened documentation links

#### Fixed
- WooCommerce HPOS Compatibility throughout

### [3.2.2.2] - 2023-10-27

#### Fixed
- Patch activation error

### [3.2.2.1] - 2023-10-19

#### Fixed
- Patch error with shipping zone functions

### [3.2.2] - 2023-10-18

#### Added
- New dashboard widget showing delivery methods and a summary of suburbs included
- Complete Admin Bar navigation for all FloristPress-generated pages of the dashboard

#### Fixed
- Fix class capitalization in custom delivery method
- Patch delivery suburbs not being sanitized on save
- Delivery suburbs auto-alphabetize when being sanitized

### [3.2.1] - 2023-10-17

#### Removed
- Removed `Action Scheduler` files since they are provided by WooCommerce core

#### Security
- Tested Woo 8.2.1

### [3.2.0] - 2023-10-16

#### Added
- Testing/Minimum plugin headers
- `bkf_dd_title` class added to title of delivery date section of checkout for easier customization
- `bkf_dd_fields` class added to enclosing `<div>` of delivery date section of checkout for easier customization

#### Changed
- New Delivery Suburbs management interface
- Removed superfluous code from `src/suburbs/suburbs.php`
- General tidying – we like clean code
- Renamed all classes for clarity
- Delivery date field moved to above payment

#### Deprecated
- Suburbs options page deprecated, to be removed in v4.0.0

#### Fixed
- Fixed `WC_Eval_Math` error appearing on v1 delivery suburbs options page
- Improve display of method-specific cutoffs on settings page

### [3.1.0] - 2023-10-13

#### Changed
- Rebrand to FloristPress
- Limit delivery calendar to past 3 months to prevent slow loading times

#### Fixed
- Fix issue with new line in delivery notes causing delivery calendar to crash
- Fix audio test button for order notifier not updating when sound changed
- Fix js on florist tools page not updating form correctly and improved UX during GET request

#### Security
- Tested value for WP updated to v6.3 in line with current standards (indicate major release only)
- Tested Woo 8.2.0
- Removed Elementor testing values
- Update FullCalendar to v6.1.9
- Update ACF to v6.2.1
- Update Action Scheduler to v3.6.4

### [3.0.8.1] - 2023-10-06

#### Fixed
- Fix email verification displaying for logged-in users in error

### [3.0.8] - 2023-10-06

#### Added
- `bkf_shipping_tax_rates()` - universal helper function that returns `WC_Tax::get_rates()`

#### Changed
- More detailed messaging to remind user about delivery methods with no suburbs attached, and link through to relevant Zone settings page
- `bkf_get_shipping_rates()` now includes shipping zone's ID as `['zoneid']`

#### Fixed
- Fix display of backslashes on Delivery Suburbs options page
- Improve display of delivery methods on Delivery Suburbs options page, particularly where tax is concerned

### [3.0.7.2] - 2023-09-26

#### Fixed
- Fix options database entry from 3.0.7.1

### [3.0.7.1] - 2023-09-23

#### Fixed
- Fix bug with implementation of 3.0.7's new feature

### [3.0.7] - 2023-09-22

#### Added
- Feature to require email confirmation at checkout for logged-out users

#### Fixed
- Tweak order notifier sound effect testing on options page

### [3.0.6] - 2023-09-21

#### Fixed
- Improve display of delivery date field at checkout

#### Security
- Tested WP 6.3.1
- Tested Woo 8.1.1
- Tested Elementor 3.16.4
- Tested Elementor Pro 3.16.2

### [3.0.5] - 2023-09-10

#### Fixed
- Set default email content type to HTML
- Fix bugs with email overrides
- RSS feed in admin widget no longer shows endless entries

#### Security
- Tested WP 6.3.1
- Tested Woo 8.0.3

### [3.0.4] - 2023-08-16

#### Fixed
- Fix Petals-related emails being available when Petals functions disabled

#### Security
- Tested WP 6.3
- Tested Woo 8.0.2
- Tested Elementor 3.15.2

### [3.0.3.1] - 2023-08-04

#### Changed
- Delete delivery date block by clicking on calendar event, remove separate list on page
- Delete delivery date category block by clicking on calendar event, remove separate list on page
- Delete date-specific fee by clicking on calendar event, remove separate list on page

#### Removed
- Remove deprecated notice about tax on date-specific fees options page

#### Fixed
- Fix responsive properties on DD options pages
- Fix issue with closed dates display on category blocks page causing JS error

#### Security
- Tested Elementor 3.15.1

### [3.0.3] - 2023-07-24

#### Added
- Page size option for PDFs
- Display delivery method costs on delivery suburbs options page

#### Changed
- Smoother responsive display on delivery suburbs options page

#### Fixed
- Fix `bkf_order_has_physical()`

#### Security
- Update Dompdf to v2.0.3
- Update ACF to v6.1.7
- Update Action Scheduler to v3.6.1
- Update FullCalendar to v6.1.8

### [3.0.2.2] - 2023-06-15

#### Fixed
- Fix critical error in implementation of 3.0.2.1

### [3.0.2.1] - 2023-06-14

#### Fixed
- Initialize most classes only if WooCommerce is active

### [3.0.2] - 2023-06-14

#### Fixed
- Fix critical error in Gravity Forms localization integration

### [3.0.1] - 2023-06-08
#### Added
- `Florist Tools` page to resend invoices and download documents

#### Removed
- Deprecate and remove `bkf-select` in favor of `Select2`

#### Fixed
- Include full `Select2` instead of base
- Tidy ajax functions
- Tidy tabbing in all php code
- Fix some missed localizations
- Fix error with timeslots in emails

### [3.0.0] - 2023-06-06

#### Added
- Compatibility in PDFs for Booster for WooCommerce's Product Add-Ons
- Modal on click of event in delivery calendar

#### Changed
- All fees generated by BKF are now -inclusive- of tax
- `bkf_get_currency()` now accepts $echo argument - true to echo, false to return, false by default

#### Fixed
- Fix display of fees in PDF invoices/worksheets
- Include `Select2` library so no longer required via CDN

### [2.7.3] - 2023-06-06

#### Changed
- Improve display of pickup orders on worksheet PDF and color-code pickup/delivery title
- Improve display of delivery fee on worksheet/invoice PDF

#### Removed
- Disable non-functioning `Mark as Delivered` option in Petals messaging

#### Fixed
- Fix display of delivery calendar when name contains double quotes
- Fix display of timeslots in correct timezone
- Redundancy for display of timeslots on orders if timeslot has been deleted in admin
- Escape remaining echoed localization strings

### [2.7.2] - 2023-05-21

#### Added
- Add 'Generator' meta tag in frontend
- Add countdown on characters for Card Message field

#### Changed
- Move order type field to `woocommerce_checkout_fields` hook
- Override most core emails to wrap them in WooCommerce template

#### Fixed
- Fix formatting of "Today's Deliveries" widget on admin dashboard
- Improve display of pickup orders in admin dashboard widgets
- Squash bug causing some localization fields to not save
- Fix HTML signature causing PHP error when being inserted
- Squash bug with blocked dates in delivery date metabox on order edit page
- Decentralize localization to improve compatibility

### [2.7.1] - 2023-05-15

#### Fixed
- Squash bug with closed dates causing calendar to fail on checkout

### [2.7.0] - 2023-05-13

#### Added
- Phone Order form
- "Suburb" field localization
- Australian address format for Gravity Forms

#### Changed
- Include currency symbol in fee amount fields
- "Delivery Suburbs" feature out of testing - removed option to disable
- Add 'Clear' button to delivery date filter field in admin orders list
- Move shared functions out of class and prepend with `bkf_`, plus add new functions
- Move all ajax functions to class `BkfAjax`, add new ajax functions
- Remove "local pickup" shipping methods from Delivery Suburbs options page
- Color-coding on blocked dates in admin area when admin role override is allowing dates to be selected
- Include date-specific fees in overnight purge
- Include currency symbol in date-specific fees calendar
- Hide methods with no timeslots in list on timeslots settings page
- Recurring blocked/closed days in calendar and datepicker now display only from current week forward
- Host font for BKF admin pages locally
- Color-code pickup vs. delivery on delivery calendar

#### Fixed
- Patch bug where timeslots field may not correctly display when only one delivery method is available
- Remove unnecessary `check()` function in `BkfSuburbs` class
- Additional localization on Petals options page
- Fix dashicon display on BKF admin pages
- Fix order count in admin menu to match 'Active' count
- Fix time detection in admin bar greeting
- Delete redundant `incl/petals/decision.php`
- Improve localization strings on same day cutoff settings page
- Fix localization on Petals options page
- Reduce padding on date lists on blocks/fees pages
- Fix bug with delivery method by weekday restrictions at checkout
- Switch to minified version of FullCalendar script
- Fix display of pickups on delivery calendar
- Fix display of delivery calendar PDF export
- Fix number of products in list on Petals Options page

#### Security
- Update Action Scheduler to v3.5.4

### [2.6.4] - 2023-03-30

#### Fixed
- Re-squash timeslot checkout validation bug

#### Security
- WP 6.2 tested

### [2.6.3] - 2023-03-29

#### Added
- Admin dashboard widget with most recent orders
- Admin dashboard widget with plugin news/updates

#### Changed
- Add `payment_complete()` to outbound Petals order processing
- Move some functions to a callable class (`BakkboneFloristCompanion`) to allow sharing - `get_rss_feed($url)`, `full_count()`, `all_count()`

#### Fixed
- Fix consistency of order statuses for "Active" filter
- Fix display of order notes on inbound Petals orders
- Optimize admin order list for inbound Petals orders

### [2.6.2] - 2023-03-28

#### Added
- Admin dashboard widget with today's deliveries
- Move "all" order filter to end of list, and make "active" the default filter on orders list.

#### Changed
- Hide completed/cancelled/refunded/failed/rejected orders from orders list by default
- Update order count in admin menu to include "New (Petals)" status
- De-clutter Petals message emails
- Add "petals_order_number" placeholder in some emails
- Hide Petals order notes from feed in admin dashboard widget

### [2.6.1] - 2023-03-27

#### Added
- Order Notifier feature

#### Changed
- Rename columns on order list
- Display "Delivered" action in Actions column for wc-collect status

#### Fixed
- Fix font consistency
- Change required capability for settings pages from `manage_options` to `manage_woocommerce`
- Localization inside inline js

### [2.6.0] - 2023-03-23

#### Added
- Petals inbound orders

#### Fixed
- Fix timeslot validation at checkout
- Fix undefined nonce indexes
- Fix post URL for Petals messaging

### [2.5.2] - 2023-03-19

#### Fixed
- Fix card message label in emails

### [2.5.1] - 2023-03-15

#### Fixed
- Patch localization at checkout

### [2.5.0] - 2023-03-12

#### Added
- Options page for localization

#### Changed
- Massive localization overhaul
- Move CS Heading and No-Ship to Localization settings
- Universal localization of "Download" for PDFs
- Change default pre-ordering period to 8 weeks

### [2.4.4] - 2023-03-11

#### Fixed
- Squash bug in timeslot field at checkout

### [2.4.3] - 2023-03-10

#### Fixed
- Fix time display on Method-Specific Timeslots page
- Fix time display on Timeslots page

### [2.4.2] - 2023-03-07

#### Fixed
- Fix delivery date field showing for virtual orders

### [2.4.1] - 2023-03-03

#### Fixed
- Fix CSS for "Order Type" field
- Fix POST URL for Petals decision
- Amend date() to wp_date() to resolve timezone issue

### [2.4.0] - 2023-03-02

#### Added
- "Order Type" field at top of checkout if a pickup method is available

#### Changed
- Move pickup features to own file for clarity
- Remove date_default_timezone_set()
- Improve wording of shipping field validation
- Improve localization throughout
- Identify some JS scripts
- Condense CS Heading code

#### Removed
- Remove unnecessary comment clutter in code

#### Fixed
- Fix variable names in base file
- Fix validation of shipping fields at checkout
- Fix translation of shipping to delivery
- Fix PHP error presenting when delivery weekday validated

#### Security
- DEV: Upgrade minimum PHP to 7.4

### [2.3.5] - 2023-02-25

#### Fixed
- Fix reported issue with delivery address fields at checkout (as relating to changing to/from local pickup methods)
- Fix typo in Petals new order email
- Fix divs on timeslot page

### [2.3.4] - 2023-02-07

#### Fixed
- Squash bug where same-day delivery cutoff was reflecting as passed on checkout regardless of time

### [2.3.3] - 2023-02-07

#### Fixed
- Fix hooks in filter.php
- Further fixes to timeslot metabox save fix from v2.3.2

### [2.3.2] - 2023-02-07

#### Added
- Filter orders by delivery date

#### Fixed
- Fix timeslot metabox save function
- Fix delivery date validation at checkout

### [2.3.1] - 2023-02-04

#### Fixed
- Fix display of timeslots page
- Fix fees options in menu

### [2.3.0] - 2023-02-04

#### Added
- Custom same-day delivery cutoffs per delivery method

#### Changed
- Remove unnecessary comment clutter

#### Fixed
- Fix function names in catblocks.php

### [2.2.8] - 2023-02-03

#### Added
- Add help tabs to all BKF pages and documentation link to plugin list

#### Fixed
- Fix reported bug in delivery calendar

### [2.2.7] - 2023-02-01

#### Fixed
- Reapply patch for previously reported Petals bug in v2.2.6

### [2.2.6] - 2023-01-31

#### Changed
- Add category blocks to nightly purge of past delivery date blocks

#### Fixed
- Fix reported bug where Petals orders crash on receipt

#### Security
- Update Dompdf to v2.0.2

### [2.2.5] - 2023-01-31

#### Fixed
- Fix reported bug where timeslots field stuck on checkout page if no time slots configured in backend, preventing checkout

### [2.2.4] - 2023-01-30

#### Fixed
- Fix reported bug where no delivery methods populate when adding a time slot in backend

### [2.2.3] - 2023-01-29

#### Fixed
- Fix reported bug where timeslots not validated at checkout

### [2.2.2] - 2023-01-28

#### Fixed
- Fix timeslot array in dd execution

### [2.2.1] - 2023-01-28

#### Fixed
- Fix display of delivery calendar

### [2.2.0] - 2023-01-27

#### Added
- Option for fee per weekday
- Option for fee per specific delivery date
- CSV/PDF exports on Delivery Calendar

#### Changed
- Reorganize submenus
- Apply priorities to actions for submenu functions
- Add localization to strings on DD Blocks settings page
- Add placeholder to fields on DD Blocks settings page

### [2.1.1] - 2023-01-25

#### Fixed
- Fix input fields on category blocks page
- Fix input fields on date blocks page

### [2.1.0] - 2023-01-25

#### Added
- Restrict delivery methods per day
- Option for fee per time slot
- Block delivery dates per product category

#### Fixed
- Fix over-capitalization of "delivery" in backend
- Fix delivery time slot display in order emails

### [2.0.2] - 2023-01-21

#### Changed
- Improve display of attribute values on PDF worksheet

### [2.0.1] - 2023-01-20

#### Added
- "Ready for Collection" order status and email

#### Changed
- Improve display of attribute keys on PDF worksheet
- Improved coloring of "Prepared" order status display on admin orders list
- Auto-created product for Petals orders "Private" so as not to display in category counts on frontend

### [2.0.0] - 2023-01-20

#### Added
- PDF Invoices and Worksheets
- Option to disable Order Comments field
- Delivery Dates feature - collect delivery dates and even timeslots
- Delivery Suburbs feature - restrict shipping methods by suburb instead of by postcode

#### Changed
- Petals Network integration is now direct - no BAKKBONE API subscription is required.
- Visual changes on settings pages and reorganized settings
- Card Message moved to own custom field, freeing up order comments field

#### Removed
- Deprecated Order Delivery Date Pro support as a result of above
- Deprecated WCFM support

#### Fixed
- Fire Gravity Forms features only if GF is activated
- Fire WooCommerce Address Book features only if WAB is activated

### [1.2.1] - 2022-12-22

#### Fixed
- Fix display of "Delivery details" header at checkout

### [1.2.0] - 2022-12-21

#### Added
- Order statuses "Scheduled", "Prepared", "Out for Delivery", "Relayed" (plus "New" "Accepted" and "Rejected" if Petals Network integration is enabled)
- Emails to customer for "Scheduled", "Prepared", "Out for Delivery" orders
- Settings and support links added to plugins list

#### Changed
- Rename "Completed" order status to "Delivered" and modify email to customer accordingly

### [1.1.0] - 2022-12-12

#### Added
- Ability to integrate Petals Network to receive/accept/reject orders in same format as your own orders
- Hide delivery address fields in checkout when pickup is selected
- Option to customize text displayed when customer enters a suburb for delivery that you do not service
- Automatically assign guest orders placed by registered customer to the matching user (so it appears in their order history when logged in)

#### Changed
- Improve display of delivery date in WCFM
- Set default values for freetext-based plugin options

### [1.0.8] - 2022-11-16

#### Changed
- Force valid recipient phone number on checkout

### [1.0.7] - 2022-10-20

#### Added
- Add delivery notes to order emails

### [1.0.6] - 2022-10-18

#### Added
- Delivery Suburb post type now supports custom text per suburb directly entered on the Suburb entry

#### Changed
- Tidies up admin menu
- Re-worded "delivery notes" field description in checkout
- Tweak card message display

### [1.0.5] - 2022-09-12

#### Added
- Improve compatibility directly with WCFM
- Add "Delivery Date" column to WCFM Orders List

### [1.0.4] - 2022-09-07

#### Security
- Escape syntax patching

### [1.0.3] - 2022-08-31

#### Added
- Adds native option to show Short Description of products in archives
- Option to change cross-sell header on cart page

#### Fixed
- Bugfixes

### [1.0.2] - 2022-08-31

#### Added
- Backwards compatibility for GF phone mask for existing customers

#### Fixed
- Fix WooCommerce Address Book integration

### [1.0.1] - 2022-08-31

#### Added
- Initial release