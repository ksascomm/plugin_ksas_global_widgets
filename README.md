plugin_ksas_global_widgets
==========================
This is a very simple plugin to query and display stories from hub.jhu.edu.  Includes a widget, shortcode, and template tag.  Options include quantity, keywords, and image size.

*******DEFAULTS**********
Quantity - 3
Keywords - krieger-school-arts-and-sciences
Image Size - square_thumbnail

*******WIDGET***********
Can customize title, number of stories, keywords, and image size

*******SHORTCODE**********
[hub] - shows default settings
[hub quantity="3" keywords="krieger-school-arts-and-sciences" image_size="square_thumbnail"]

******TEMPLATE TAG********
<?php hopkins_hub_shortcode(array(
	'quantity'   => '3',
	'keywords'     => 'krieger-school-arts-and-sciences',
	'image_size'     => 'square_thumbnail',
)); ?>