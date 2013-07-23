<?php
/**
 * Plugin Name: KSAS Global Widgets
 * Plugin URI: http://krieger.jhu.edu/communications/web/plugins
 * Description: Contains a widget for Hub articles based on tag, recent news stories from another site of your choice, Upcoming Events for both Site Executive and Google calendars
 * Version: 0.1
 * Author: Cara Peckens
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**************WIDGET*****************/
add_action( 'widgets_init', 'ksas_load_global_widgets' );

function ksas_load_global_widgets() {
	register_widget( 'Hopkins_Hub_Widget' );
	register_widget('Recent_News_Other_Site');
	register_widget('SE_Calendar_Widget');
}

class SE_Calendar_Widget extends WP_Widget {
	function SE_Calendar_Widget() {
		$widget_options = array( 'classname' => 'calendar', 'description' => __('Displays this weeks events', 'secal') );
		$control_options = array( 'width' => 300, 'height' => 350, 'id_base' => 'secal-widget' );
		$this->WP_Widget( 'secal-widget', __('SEcal Widget', 'secal'), $widget_options, $control_options );
	}
	

	/* Widget Display */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$theme_option = flagship_sub_get_global_options();
		$view_type = $instance['view_type'];
		$calendar_url = $theme_option['flagship_sub_calendar_address'];
		$url_for_script = "http://krieger.jhu.edu/calendar/calendar_holder.html?url=" . $calendar_url . "/list/" . $view_type;
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;{ ?>
				<div id="calendar_container"></div>
				<script src="<?php echo get_template_directory_uri() ?>/assets/javascripts/min.easyXDM.js"></script>
				<script>
				    new easyXDM.Socket({
				        remote: "<?php echo $url_for_script; ?>",
				        container: document.getElementById("calendar_container"),
				        onMessage: function(message, origin){
				            this.container.getElementsByTagName("iframe")[0].style.height = message + "px";
				        }
				    });
				    
				    var $j = jQuery.noConflict();
				    $j('td.SECalendarNoEvent').prev('td.SECalendarEventDate').css('display', 'none');
				</script>

		<?php } echo $after_widget;
	}

	/* Update/Save the widget settings. */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['view_type'] = $new_instance['view_type'];

		return $instance;
	}

	/* Widget Options */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Upcoming Events', 'secal'), 'view_type' => 'byweek' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>Set your calendar address under Site Settings</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<!-- Calendar View: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'view_type' ); ?>"><?php _e('Calendar View:', 'secal'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'view_type' ); ?>" name="<?php echo $this->get_field_name( 'view_type' ); ?>" class="widefat" style="width:100%;">
				<option value="byday" <?php if ( 'byday' == $instance['view_type'] ) echo 'selected="selected"'; ?>>Today's Events</option>
				<option value="byweek" <?php if ( 'byweek' == $instance['view_type'] ) echo 'selected="selected"'; ?>>This Week's Events</option>
				<option value="bymonth" <?php if ( 'bymonth' == $instance['view_type'] ) echo 'selected="selected"'; ?>>This Month's Events</option>

			</select>
		</p>


	<?php
	}
}	

class Recent_News_Other_Site extends WP_Widget {
	function Recent_News_Other_Site() {
		$widget_options = array( 'classname' => 'ksas_recent', 'description' => __('Displays news stories from another site', 'ksas_recent') );
		$control_options = array( 'width' => 300, 'height' => 350, 'id_base' => 'ksas_recent-widget' );
		$this->WP_Widget( 'ksas_recent-widget', __('Recent News from Another Site', 'ksas_recent'), $widget_options, $control_options );
	}

	/* Widget Display */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$quantity = $instance['quantity'];
		$site_id = $instance['site_id'];
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
			global $switched;
			switch_to_blog($site_id);
		$recent_posts_query = new WP_Query(array(
					'post_type' => 'post',
					'posts_per_page' => $quantity));
		if ( $recent_posts_query->have_posts() ) :  while ($recent_posts_query->have_posts()) : $recent_posts_query->the_post(); ?>
				<article class="row">
						<a href="<?php the_permalink(); ?>">
							<?php if ( has_post_thumbnail()) { ?> 
								<?php the_post_thumbnail('thumbnail'); ?>
							<?php } ?>
							<h6><?php the_title(); ?></h6>
							<?php the_excerpt(); ?>
						</a>
				</article>
		<?php endwhile; endif; restore_current_blog(); echo $after_widget;
	}

	/* Update/Save the widget settings. */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['quantity'] = strip_tags( $new_instance['quantity'] );
		/* No need to strip tags for site_id and show_site_id. */
		$instance['site_id'] = $new_instance['site_id'];

		return $instance;
	}

	/* Widget Options */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Recent News', 'ksas_recent'), 'quantity' => __('3', 'ksas_recent'), 'site_id' => '1' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<!-- Number of Stories: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'quantity' ); ?>"><?php _e('Number of stories to display:', 'ksas_recent'); ?></label>
			<input id="<?php echo $this->get_field_id( 'quantity' ); ?>" name="<?php echo $this->get_field_name( 'quantity' ); ?>" value="<?php echo $instance['quantity']; ?>" style="width:100%;" />
		</p>


		<!-- Choose News Source: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'site_id' ); ?>"><?php _e('Choose Source of News:', 'ksas_recent'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'site_id' ); ?>" name="<?php echo $this->get_field_name( 'site_id' ); ?>" class="widefat" style="width:100%;">
			<?php global $wpdb;
				$sites = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->blogs WHERE spam = '0' AND deleted = '0' and archived = '0'"));
		    foreach($sites as $site){
		    	$site_id = $site->blog_id;
		        $site_details = get_blog_details($site_id);
		        if($site_details != false){
		            $site_url = $site_details->siteurl;
		            $site_title = $site_details->blogname;
		        } ?>
		       <option value="<?php echo $site_id; ?>" <?php if ( $site_id == $instance['site_id'] ) echo 'selected="selected"'; ?>><?php echo $site_title; ?></option>
		    <?php } ?>
			</select>
		</p>
	<?php
	}
}

class Hopkins_Hub_Widget extends WP_Widget {
	function Hopkins_Hub_Widget() {
		$widget_options = array( 'classname' => 'hub', 'description' => __('Displays articles from the Hub based on tags', 'hub') );
		$control_options = array( 'width' => 300, 'height' => 350, 'id_base' => 'hub-widget' );
		$this->WP_Widget( 'hub-widget', __('Hub Widget', 'hub'), $widget_options, $control_options );
	}

	/* Widget Display */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$quantity = $instance['quantity'];
		$keywords = $instance['keywords'];
		$image_size = $instance['image_size'];
		$hub_url = 'http://api.hub.jhu.edu/articles?v=0&key=bed3238d428c2c710a65d813ebfb2baa664a2fef&return_format=json&tags=' . $keywords . '&per_page=' . $quantity;
			$rCURL = curl_init();
				curl_setopt($rCURL, CURLOPT_URL, $hub_url);
				curl_setopt($rCURL, CURLOPT_HEADER, 0);
				curl_setopt($rCURL, CURLOPT_RETURNTRANSFER, 1);
		
		$hub_call = curl_exec($rCURL);
		curl_close($rCURL);
		$hub_results = json_decode ( $hub_call, true );
		$hub_articles = $hub_results['_embedded'];
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
		if (is_array($hub_articles['articles'])) {

		foreach($hub_articles['articles'] as $hub_article) { ?>
				<article class="row">
						<a href="<?php echo $hub_article['url']; ?>">
							<img src="<?php echo $hub_article['_embedded']['image_thumbnail'][0]['sizes'][$image_size]; ?>" />
							<h6><?php echo $hub_article['headline']; ?></h6>
							<p><?php echo $hub_article['subheadline']; ?>
							<?php if (empty($hub_article['subheadline'])) { 
								echo $hub_article['excerpt'];
							} ?>
							</p>
							
						</a>
				</article>
		<?php } } else { ?> 
			<article class="row">
				<p><b>No stories found</b></p>
			</article>
		<? } echo $after_widget;
	}

	/* Update/Save the widget settings. */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['quantity'] = strip_tags( $new_instance['quantity'] );
		$instance['keywords'] = strip_tags( $new_instance['keywords'] );
		/* No need to strip tags for image_size and show_image_size. */
		$instance['image_size'] = $new_instance['image_size'];

		return $instance;
	}

	/* Widget Options */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('From the Hub', 'hub'), 'quantity' => __('3', 'hub'), 'keywords' => 'student-life', 'image_size' => 'medium' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<!-- Number of Stories: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'quantity' ); ?>"><?php _e('Number of stories to display:', 'hub'); ?></label>
			<input id="<?php echo $this->get_field_id( 'quantity' ); ?>" name="<?php echo $this->get_field_name( 'quantity' ); ?>" value="<?php echo $instance['quantity']; ?>" style="width:100%;" />
		</p>

		<!-- Keywords: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'keywords' ); ?>"><?php _e('Enter keywords. Use hyphens instead of spaces (comma separated, no spaces) ie. physics,arts-and-sciences:', 'hub'); ?></label>
			<input id="<?php echo $this->get_field_id( 'keywords' ); ?>" name="<?php echo $this->get_field_name( 'keywords' ); ?>" value="<?php echo $instance['keywords']; ?>" style="width:100%;" />
		</p>

		<!-- Image Size: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php _e('Image Size:', 'hub'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'image_size' ); ?>" name="<?php echo $this->get_field_name( 'image_size' ); ?>" class="widefat" style="width:100%;">
				<option value="square_thumbnail" <?php if ( 'square_thumbnail' == $instance['image_size'] ) echo 'selected="selected"'; ?>>Square thumbnail</option>
				<option value="small" <?php if ( 'small' == $instance['image_size'] ) echo 'selected="selected"'; ?>>Small 200px wide</option>
				<option value="medium" <?php if ( 'medium' == $instance['image_size'] ) echo 'selected="selected"'; ?>>Medium 420px wide</option>
			</select>
		</p>


	<?php
	}
}


/**************SHORTCODE & TEMPLATE TAG*****************/
function hopkins_hub_shortcode($atts, $content=null) {
 
	extract(shortcode_atts(array(
	 
	'quantity'   => '3',
	'keywords'     => 'krieger',
	'image_size'     => 'square_thumbnail',	 
	), $atts));
	 	
		$hub_url = 'http://api.hub.jhu.edu/articles?v=0&return_format=json&tags=' . $keywords . '&per_page=' . $quantity;
		$rCURL = curl_init();
			curl_setopt($rCURL, CURLOPT_URL, $hub_url);
			curl_setopt($rCURL, CURLOPT_HEADER, 0);
			curl_setopt($rCURL, CURLOPT_RETURNTRANSFER, 1);
		
		$hub_call = curl_exec($rCURL);
		curl_close($rCURL);
		$hub_results = json_decode ( $hub_call, true );
		$hub_articles = $hub_results['_embedded']; ?>
		<div id="widget" class="widget row hub">
			<div class="widget_title"><h5>From the Hub</h5></div>
		<?php foreach($hub_articles['articles'] as $hub_article) { ?>
				<article>
						<a href="<?php echo $hub_article['url']; ?>">
							<img src="<?php echo $hub_article['_embedded']['image_thumbnail'][0]['sizes'][$image_size]; ?>" />
							<h6><?php echo $hub_article['headline']; ?></h6>
							<p><?php echo $hub_article['subheadline']; ?></p>
						</a>
				</article>
			
		<?php }	?>
		</div>
		
	<?php }
 
add_shortcode('hub','hopkins_hub_shortcode');

       
function search_form_shortcode( $attr, $content = null ) { 
	$theme_option = flagship_sub_get_global_options(); 
	$collection_name = $theme_option['flagship_sub_search_collection'];
?>
        <form class="search-form" action="<?php echo site_url('/search'); ?>" method="get">
                    <fieldset>
                        <input type="text" class="input-text" name="q" value="" />
                        <label>Search:</label>
                        <input type="radio" name="site" value="<?php echo $collection_name; ?>" checked>This site only
                        <input type="radio" name="site" value="krieger_collection">All of JHU
                        <input type="submit" class="button blue_bg" value="Search Again" />
                    </fieldset>
       </form>
       <?php 
}
add_shortcode('search_form', 'search_form_shortcode'); 


?>