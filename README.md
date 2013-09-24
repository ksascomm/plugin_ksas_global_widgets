plugin_ksas_global_widgets
==========================
**Current Version:** 1.2

This is a network activated plugin.  Provides four different widgets and various shortcodes outlined below.

###Hub Widget and Shortcode
####Widget Options (default)
*    Title (From the Hub)
*	Quantity (3)
*	Keywords (krieger-school-arts-and-sciences)
*	Image Size (square_thumbnail)

####Shortcode
`[hub]` - shows default settings  
`[hub quantity="3" keywords="krieger-school-arts-and-sciences" image_size="square_thumbnail"]`

####Template tag
```
<?php hopkins_hub_shortcode(array(  
	'quantity'   => '3',  
	'keywords'     => 'krieger-school-arts-and-sciences',
	'image_size'     => 'square_thumbnail',
)); ?>
```
###Custom RSS Widget
Custom modifications to the default packaged Wordpress Widget.  Added intro text option and modified layout output: date, title, summary, author
####Widget Options
*   Title
*   Intro text (add text above feed display)
*   Feed URL
*   Quantity
*   Display Content/Excerpt (checkbox)
*   Display author (checkbox)
*   Display item date (checkbox)

###Site Executive Calendar Widget
Displays an agenda view of events from a Site Executive calendar instance. Calendar URL is set under "Theme Options"
####Widget Options
*   Title
*   View Type (Today, Week, Month)

###Recent News from Another Site Widget
Displays news stories from another site in the network install
####Widget Options
*   Title
*   Quantity
*   Site (dropdown choice)

###Search Form Shortcode
Displays search form.  Google Search Appliance collection is set under "Theme Options"  
`[search_form]`