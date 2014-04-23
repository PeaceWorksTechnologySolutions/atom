TinyMCE visual editor
=====================

This customization allows all static pages (editable pages) in AtoM to be edited using TinyMCE visual editor.   This means that these pages can be editing and formatted without needing to know HTML.

Basic formatting can be done by using the menus and toolbar buttons provided by TinyMCE.

TinyMCE extensions are enabled to allow uploading and including images on the static pages as follows:
 * choose Insert/edit image button from toolbar (or use menu Insert > Insert Image)
 * click the Folder/magnifying glass icon 
 * “Roxy Fileman” window will open. 
 * To upload a new image from your computer, click “Add file” then “Choose files”.  Select the file from your computer and click “Upload”.  Now close upload window using “X”.
 * click to select one of your uploaded images to insert into the page, then click the Select button.
 * Enter a description (for accessibility / screen readers)
 * Dimensions can be left blank.  Click OK.
 * Once inserted, the image size can be adjusted by selecting the image, then dragging one of the edge knobs.
 * Tables can be used to arrange text and/or images in rows and/or columns.

Implementation notes
--------------------
Adjusted a few AtoM templates to include TinyMCE on staticpages (editable pages).

TinyMCE is included from cdn.jsdelivr.net/tinymce/4.0.18/tinymce.min.js

Roxy Filemanager is not available from CDN and has been added under vendor/roxy_fileman

TinyMCE and Roxy integration with AtoM takes place in js/staticpage_tinymce.js
