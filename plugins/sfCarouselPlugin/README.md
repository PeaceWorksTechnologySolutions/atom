Carousel (front-page slideshow)
===============================

This plugin adds a slideshow to the AtoM front page which displays up to 30 of the most recently added/updated photos in the system.  Photos and their captions (if available) are displayed.  Users may click a photo to go to its archival description.


Implementation Notes
--------------------
The slideshow on the front page shows up to 30 of the most recently added/updated photos in the system.  The query for recent photos is implemented using elastic search.

The AtoM template "layout_2col.php" is tweaked to add a possible slot (page element) called 'before-title'.  

The plugin overrides the 'home' template to fill this slot.

The plugin is implemented in plugins/sfCarouselPlugin

The slideshow itself is implemented using [carouFredSel](http://docs.dev7studios.com/jquery-plugins/caroufredsel).
