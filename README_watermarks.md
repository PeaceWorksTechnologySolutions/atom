Per-institution watermarks
==========================

AtoM includes the ability to apply a watermark to uploaded photos.  However there is only provision for a single global watermark which is applied to all images.

This customization allows each institution to have their own watermark.

The watermark image may be uploaded by going to the institution record, then choosing “Edit theme”.  There you can choose a file on your local computer to upload as the watermark.

Uploading a new watermark will not  affect photos already present in AtoM, but AtoM does provide a way for a developer to cause all watermarks to be re-applied (see “Regenerating Derivatives” in AtoM documentation for more details).

Implementation Notes
--------------------
The functionality is achieved by modifying a few core AtoM files, so that the watermark can be uploaded on the Edit Theme page.  The Edit Theme page also allows the institution to upload a logo file – the implementation for watermarks follows the same approach.

Watermarking takes place in lib/model/QubitDigitalObject.php

When watermarking a photo, the code first checks whether the archival record is related to an institution, and whether an institution-specific watermark file is available.  If not, it falls back to checking for a global watermark file.
