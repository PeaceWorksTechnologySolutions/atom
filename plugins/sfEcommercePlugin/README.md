E-Commerce (sfEcommercePlugin)
==============================

Overview
--------
This plugin allows website visitors to select and purchase individual photos.  Payment for the photos happens via PayPal.  E-Commerce admins review each order and may approve or reject (remove) photos from the order.  After approval, the customer is sent an email containing a link which they can use to download the image(s).

E-Commerce Admin Users
----------------------
Each institution with photos for sale should have one or more E-Commerce admin users.  Admin users have the ability to view and process orders for the institution.

An e-commerce admin can be associated with only one institution.

To create an e-commerce admin user, create a normal user account in AtoM (Admin > Users > Add new).  You can add the user to one or more AtoM user groups (administrator, editor, contributor, translator, etc.) but this is not required.  

After saving, click the Ecommerce Settings tab, then Edit.  Set the Repository field to associate the user account with one of the institutions already in the system, then click Save.

Shopping cart
-------------
When browsing or viewing archival descriptions (records), a customer usually has the option to add the photo to their shopping cart (“Purchase this image”).

Photos from more than one institution may be added to the cart.

In the following circumstances, the system will not provide the “Purchase this image” button:
 * the archival description record does not have a digital object (photo) attached to it
 * the record has the Disseminate Right set to “Disallow”, so the system will instead display the message “This photo has restrictions and may not be ordered online. Please contact the archives for permission to order the photo.”  Note that in AtoM, rights are inherited from the parent record.  For example setting Disseminate to Disallow at the Collection level will prevent any photo within the Collection from being added to the cart – unless an individual photo record within the Collection has Disseminate set to Allow (i.e. at the Item level)
 * the archival description record is not related to an institution, or the institution does not have any pricing configuration.

After an item is added to the cart, the customer can click the “Cart” button to see the cart contents and subtotal.  From here they have the option to Edit the cart or Check out.  

Edit Cart shows the price of each item in the cart and allows individual items to be removed.


Check out
---------
When a user proceeds to Check out, they must fill in their name, address and email address.  They may optionally enter their phone number.

They must accept the terms (non-commercial usage only) by checking a box.  If they do not accept the terms, they are not allowed to submit the order.

Optionally, they may indicate that they would like to receive updates via email.  This information is recorded to the database for future use.

The system does not require the customer to create an account.  Returning customers use the same check out process each time.

Payment
-------
Payment is made via PayPal. There is a single paypal account shared by all institutions. 

PayPal allows the customer to pay either by  credit card or by using their PayPal account.

When the payment transaction is completed, the customer has the option to return to AtoM site.

Regardless of whether the customer returns to the AtoM site, the AtoM site receives notification directly from PayPal to indicate whether that payment was successful.  At this point:
 * The customer is notified via email that their payment has been received and that the order will be processed. 
 * Each institution involved in the transaction is sent a notification, including a link to the order so that they may process it.  If an institution has multiple e-commerce admins, the notification is sent to all of them.

If a customer cancels their order, the order record remains in the database with status 'cancelled'.
If payment is not successful, the order record remains in the database but has status 'pending_payment'.  Such orders are not visible on the Manage Orders page.

Viewing and Processing Orders
-----------------------------
An order may involve one or more institutions, depending on which photos the customer selected.

E-Commerce admins from each institution can view and process orders by logging in to AtoM, then clicking the “Manage” menu (pencil icon), then “Orders”.  

By default, orders with “paid” status are the ones shown in this report, since these are the ones needing to be processed.  Admin users can change to status filter to “all”, “refunded”, “cancelled” or “processed” to change which records are shown.

An e-commerce admin is linked to one institution, and the Manage Orders shows only orders where their institution is involved.  If a customer has purchased only photos from different institutions, their order will not show up.

Admins can click an order to view its details.  This shows the customer name, address and email and a list of photos.  The order may include photos from other institutions, but these are not shown.

For each photo, the admin may choose to “accept” the photo (allow it to be purchased) or “reject” (remove it from the order and refund the customer for this photo).  The admin may also include a note which will be included in the email notification sent to the customer.

If any photos are rejected, the refund happens automatically as soon as the “Process order” button is clicked.

An order will have the status “refunded” if ALL of the photos in the order were rejected (from all institutions).  Otherwise it will show as “processed”.

Download
--------
As soon as an individual institution processes their portion of the order, the customer will be sent an email with a link for downloading the images. 

The download page will include all photos from their order that have been approved so far, possibly including photos from multiple institutions.

The customer may download all photos (as a zip file), or may download photo files individually.

Customers are allowed to access these download links for 10 days after the last institution processes their portion of the order.  After 10 days the download link will no longer work.  

The reason for the expiry is to encourage customers to download their images and not to rely on the AtoM site to provide ongoing access to their purchased photos.

Vacation
--------
In the event that an e-commerce admin will be unavailable to process orders, they may set a vacation message which will be sent to customers (after they place an order).

To enable the vacation message, log in and then click your username at the upper right, then choose “profile”.  Then click the “Ecommerce Settings” tab.

Click “Edit”.  Enable the “Vacation enabled” checkbox and enter the vacation message.  

Then click “Save”.

If an institution has only one e-commerce admin, then the vacation message will be sent to any customer who places an order from this institution.  

If the institution has multiple e-commerce admins, the system will only send a vacation message if ALL e-commerce admins have their vacation message enabled (i.e. no one is available to process orders).  In this case the system will choose one of the e-commerce admins (arbitrarily) and will send their vacation message to the customer.

Removing personal information
-----------------------------
If a customer requests that their personal information be removed from AtoM, then this may be done by e-commerce admins who have access to that customers order(s).  

Since a customer may have placed multiple orders from different institutions, e-commerce admins from each institution should check for and remove personal information.

Click the “Manage” menu (pencil icon), then “Orders”.  

Search for the customer by name.  There may be multiple orders from that customer.

For each order, click it to view, then choose “Remove personal information” at the bottom and click “OK” to confirm.   The order record will remain in the system, but the name will be set to “anonymous” and the address, email and phone number fields will be blanked out.

Note that this option is not available for unprocessed orders (those with status “paid”).  First all involved institutions must process the order, then the system will allow you to remove personal information.

Sales Report
------------
An e-commerce user can use the Sales Report to see total orders and income for a date range.  The report includes only orders (or portions of orders) related to their institution.

Click the “Manage” menu (pencil icon), then “Sales Report”.

Optionally enter start and end dates.

The summary information at the top of the report shows total orders, total photos sold, taxes (if applicable), fees (deducted by PayPal) and net sales.

The itemized list shows the records (transaction events) and includes:
 * sales – item sales
 * sale fees (deducted by PayPal)
 * taxes – if applicable
 * refunds
 * fees refunded
 * taxes refund

It is possible to copy and paste this table into a spreadsheet for printing or further processing.

Pricing
-------

Each institution may set their own per-photo price.  Pricing cannot be set via the AtoM user interface, but is done by editing a configuration file on the server (plugins/sfEcommerce/config/ecommerce.yml).

Adding Institutions
-------------------
To add a new institution, it is necessary to:
 * add the institution to AtoM normally (add -> archival institution)
 * create a watermark image as a PNG file (recommended dimensions 400x400 pixels).  Upload it by viewing the institution record, then using "Edit theme" button at the bottom. For the Watermark field, select the watermark image on your local computer to upload it into AtoM
 * update plugins/sfEcommerce/config/ecommerce.yml to set the photo price for the new institution
