<?php
/**
 * Piratenkleider 3 Theme Optionen
 *
 * @source http://github.com/xwolfde/Piratenkleider
 * @creator xwolf
 * @version 3.0
 * @licence GPL 2.0 
 */

require( get_template_directory() . '/inc/constants.php' );

$options = get_option('piratenkleider_theme_options');
$options = piratenkleider_compatibility($options);
    // adjusts variables for downwards comptability

if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  $xffaddrs = explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
  $_SERVER['REMOTE_ADDR'] = $xffaddrs[0];
}    
$_SERVER['REMOTE_ADDR'] = getAnonymIp($_SERVER['REMOTE_ADDR']);

if ($options['anonymize-user']==1) {
    /* IP-Adresse überschreiben */
    $_SERVER["REMOTE_ADDR"] = "0.0.0.0";
    /* UA-String überschreiben */
    $_SERVER["HTTP_USER_AGENT"] = "";    
    update_option('require_name_email',0);
}

  

require_once ( get_template_directory() . '/inc/theme-options.php' );     




if ( ! function_exists( 'piratenkleider_setup' ) ):
function piratenkleider_setup() {
     global $defaultoptions;
     global $options;


	if ( ! isset( $content_width ) )   $content_width = $defaultoptions['content-width'];
     
        // This theme styles the visual editor with editor-style.css to match the theme style.
        add_editor_style();
        // This theme uses post thumbnails
        add_theme_support( 'post-thumbnails' );
        // Add default posts and comments RSS feed links to head
        add_theme_support( 'automatic-feed-links' );
               
  
        $args = array(
            'width'         => 0,
            'height'        => 0,
            'default-image' => $defaultoptions['logo'],
            'uploads'       => true,
            'random-default' => false,                      
            'flex-height' => true,
            'flex-width' => true,
	    'header-text'   => false,
            'suggested-height' => $defaultoptions['logo-height'],
            'suggested-width' => $defaultoptions['logo-width'],
            'max-width' => 350,           
        );
       add_theme_support( 'custom-header', $args );
               
       $args = array(
	    'default-color' => $defaultoptions['background-header-color'],
	    'default-image' => $defaultoptions['background-header-image'],
	    'background_repeat'	=> 'repeat-x',
	    'background_position_x'  => 'left',
	    'background_position_y'  => 'bottom',
	    'wp-head-callback'       => 'piratenkleider_custom_background_cb',	
	);
       
	/**
	 * piratenkleider custom background callback.
	 *
	 */
	function piratenkleider_custom_background_cb() {
                 global $defaultoptions;
                 global $options;
	        // $background is the saved custom image, or the default image.
	        $background = set_url_scheme( get_background_image() );
	
	        // $color is the saved custom color.
	        // A default has to be specified in style.css. It will not be printed here.
	        $color = get_theme_mod( 'background_color' );
	
	        if ( ! $background && ! $color )
	                return;
                       
		
	        $style = $color ? "background-color: #$color;" : '';
	
	        if ( $background ) {
                        $image = " background-image: url('$background');";
                       
                       if ($background == $defaultoptions['background-header-image']) {
			   $style .= $image;
			} else {
			    $repeat = get_theme_mod( 'background_repeat', 'repeat-x' );
			    if ( ! in_array( $repeat, array( 'no-repeat', 'repeat-x', 'repeat-y', 'repeat' ) ) )
				    $repeat = 'repeat-x';
			    $repeat = " background-repeat: $repeat;";

			    $positionx = get_theme_mod( 'background_position_x', 'left' );
			    if ( ! in_array( $positionx, array( 'center', 'right', 'left' ) ) )
				    $positionx = 'left';
			    $positiony = get_theme_mod( 'background_position_y', 'bottom' );
			    if ( ! in_array( $positiony, array( 'top', 'bottom' ) ) )
				    $positiony = 'bottom';

			    $position = " background-position: $positionx $positiony;";

			    $attachment = get_theme_mod( 'background_attachment', 'scroll' );
			    if ( ! in_array( $attachment, array( 'fixed', 'scroll' ) ) )
				    $attachment = 'scroll';
			    $attachment = " background-attachment: $attachment;";

			    $style .= $image . $repeat . $position . $attachment;
		      }
		       
	        } 
		
	    
	    echo '<style type="text/css" id="custom-background-css">';
	    echo '.header { '.trim( $style ).' } ';
	    echo '</style>';
	}       
       
	add_theme_support( 'custom-background', $args );
       
	if ( function_exists( 'add_theme_support' ) ) {
	    add_theme_support( 'post-thumbnails' );
	    set_post_thumbnail_size( 150, 150 ); // default Post Thumbnail dimensions   
	}

	if ( function_exists( 'add_image_size' ) ) { 
	    add_image_size( 'teaser-thumb', $options['teaser-thumbnail_width'], $options['teaser-thumbnail_height'], $options['teaser-thumbnail_crop'] ); //300 pixels wide (and unlimited height)
	    if ($options['aktiv-linktipps']) {
		add_image_size( 'linktipp-thumb', $options['linktipp-thumbnail_width'], $options['linktipp-thumbnail_height'], $options['linktipp-thumbnail_crop'] ); //300 pixels wide (and unlimited height)
		 
	    }
	}
	
        
        // Make theme available for translation
        // Translations can be filed in the /languages/ directory
        load_theme_textdomain('piratenkleider', get_template_directory() . '/languages');
        $locale = get_locale();
        $locale_file = get_template_directory() . "/languages/$locale.php";
        if ( is_readable( $locale_file ) )
                require_once( $locale_file );

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus( array(
                'primary' => __( 'Hauptnavigation (Statische Seiten)', 'piratenkleider' ),
                'top' => __( 'Linkmenu (Links zu Webportalen wie Wiki, Forum, etc)', 'piratenkleider' ),
                'sub' => __( 'Technische Navigation (Kontakt, Impressum, etc)', 'piratenkleider' ),
        ) );


       if ($options['login_errors']==0) {
	    /** Abschalten von Fehlermeldungen auf der Loginseite */      
           add_filter('login_errors', create_function('$a', "return null;"));
       }        
        /** Entfernen der Wordpressversionsnr im Header */
        remove_action('wp_head', 'wp_generator');
	
	/* Zulassen von Shortcodes in Widgets */
	add_filter('widget_text', 'do_shortcode');
        
        if ($options['yt-alternativeembed']) {
        /* Filter fuer YouTube Embed mit nocookie: */     
    #    wp_oembed_remove_provider( '#https://(www\.)?youtube.com/watch.*#i' );
            wp_embed_register_handler( 'ytnocookie', '#https?://www\.youtube\-nocookie\.com/embed/([a-z0-9\-_]+)#i', 'wp_embed_handler_ytnocookie' );
            wp_embed_register_handler( 'ytnormal', '#https?://www\.youtube\.com/watch\?v=([a-z0-9\-_]+)#i', 'wp_embed_handler_ytnocookie' );
            wp_embed_register_handler( 'ytnormal2', '#https?://www\.youtube\.com/watch\?feature=player_embedded&v=([a-z0-9\-_]+)#i', 'wp_embed_handler_ytnocookie' );
        }

	function wp_embed_handler_ytnocookie( $matches, $attr, $url, $rawattr ) {
	    global $defaultoptions;    
	    $relvideo = '';
	    if ($defaultoptions['yt-norel']==1) {
		$relvideo = '?rel=0';
	    }
		$embed = sprintf(                                
				'<div class="embed-youtube"><p><img src="%1$s/images/social-media/youtube-24x24.png" width="24" height="24" alt="">YouTube-Video: <a href="https://www.youtube.com/watch?v=%2$s">https://www.youtube.com/watch?v=%2$s</a></p><iframe src="https://www.youtube-nocookie.com/embed/%2$s%5$s" width="%3$spx" height="%4$spx" frameborder="0" scrolling="no" marginwidth="0" marginheight="0"></iframe></div>',
				get_template_directory_uri(),
				esc_attr($matches[1]),
				$defaultoptions['yt-content-width'],
				$defaultoptions['yt-content-height'],
				$relvideo
				);

		return apply_filters( 'embed_ytnocookie', $embed, $matches, $attr, $url, $rawattr );

	}
	
	if ($options['aktiv-linktipps']) {
	    require( get_template_directory() . '/inc/custom-posts.php' );
	}
        if (isset($options['feed-overwriteauthorstring']) && (strlen(trim($options['feed-overwriteauthorstring']))>1 )) {
             add_filter( 'the_author', 'feed_author' );
              function feed_author($name) {
                global $options;  
                if( is_feed() && !is_admin()) {
                return $options['feed-overwriteauthorstring'];  
              }
        }
}
        
	

}
endif;
add_action( 'after_setup_theme', 'piratenkleider_setup' );

require( get_template_directory() . '/inc/widgets.php' );

function piratenkleider_scripts() {
    global $options;
    global $defaultoptions;

    /*
    'stylefile-position': 
     0 => __('Deaktiv (Nicht einbinden)', 'piratenkleider'),
      1 => __('Vor Standard-CSS-Dateien des Grunddesigns', 'piratenkleider'),
      2 => __('Nach Standard-CSS-Dateien des Grunddesigns', 'piratenkleider'),
      3 => __('Semi-Exklusiv (kein Laden des Grunddesign-CSS, jedoch optionale CSS (Farben, Schriften, Icons, ...)', 'piratenkleider'),
      4 => __('Exklusiv (kein Laden anderer CSS-Dateien)', 'piratenkleider'),

	     */
     $userstyle = 0;
     if ( !is_admin() ) {
	$userstyle = 0;
	if ((isset($options['aktiv-stylefile']) && ($options['aktiv-stylefile'] > 0) && (wp_get_attachment_url($options['aktiv-stylefile'])) )
		&& (isset($options['stylefile-position'])) && ($options['stylefile-position']>0)) {
	    $userstyle = 1;
	}
	 
	if (($userstyle==1) && ($options['stylefile-position']==1)) {
	    wp_enqueue_style( 'stylefile', wp_get_attachment_url($options['aktiv-stylefile']));	  
	}
	
	if (($userstyle==0) || (($userstyle==1) && ($options['stylefile-position']<3))) {
	    if ((isset($options['aktiv-alternativestyle'])) && ($options['aktiv-alternativestyle'] != 'style.css')) {
		 wp_enqueue_style( 'alternativestyle', get_template_directory_uri().'/css/'.$options['aktiv-alternativestyle'] );	     
	    } else {
		$theme  = wp_get_theme();
		wp_register_style( 'piratenkleider', get_bloginfo( 'stylesheet_url' ), false, $theme['Version'] );
		wp_enqueue_style( 'piratenkleider' );
	    }     		
	}
	if (($userstyle==1) && ($options['stylefile-position'] > 1)) {
	    wp_enqueue_style( 'stylefile', wp_get_attachment_url($options['aktiv-stylefile']));	  
	}
	
	if (($userstyle==0) || (($userstyle==1) && ($options['stylefile-position']!=4))) {
	    if ((isset($options['css-colorfile'])) && (strlen(trim($options['css-colorfile']))>1)) { 
		 wp_enqueue_style( 'color', get_template_directory_uri().'/css/'.$options['css-colorfile'] );	             
	    }        	  

	    if (isset($options['aktiv-mediaqueries-allparts']) && ($options['aktiv-mediaqueries-allparts']==1)) {
		wp_enqueue_style( 'basemod_mediaqueries_allparts', $defaultoptions['src-basemod_mediaqueries_allparts'] );
	    }

	    if ((isset($options['aktiv-linkicons'])) && ($options['aktiv-linkicons']==1)) { 
	       wp_enqueue_style( 'basemod_linkicons', $defaultoptions['src-linkicons-css'] );
	    }   
	
	    if ( is_singular() ) {
		$nosidebar = get_post_meta( get_the_ID(), 'piratenkleider_nosidebar', true );
		$custom_fields = get_post_custom(); 
		if ( ( !empty( $nosidebar ) && $nosidebar==1) 
		    || ((isset($custom_fields['fullsize'])) && ($custom_fields['fullsize'][0] == true)))  {
		   wp_enqueue_style( 'basemod_sidebarbottom', $defaultoptions['src-basemod_sidebarbottom'] ); 
		}
	    } 
	   if ((isset($options['position_sidebarbottom'])) && ($options['position_sidebarbottom'] ==1)) {
		   wp_enqueue_style( 'basemod_sidebarbottom', $defaultoptions['src-basemod_sidebarbottom'] ); 
	   }

	}

	wp_enqueue_script(
		    'layoutjs',
		    $defaultoptions['src-layoutjs'],
		    array('jquery'),
		    $defaultoptions['js-version']
	    );

	if (is_singular() && ($options['aktiv-commentreplylink']==1) && get_option( 'thread_comments' )) {        
		wp_enqueue_script(
		    'comment-reply',
		    $defaultoptions['src-comment-reply'],
		    false,
		    $defaultoptions['js-version']
	    );  
	 }        
      
     }
}
add_action('wp_enqueue_scripts', 'piratenkleider_scripts');
function piratenkleider_dequeue_fonts() {
         wp_dequeue_style( 'twentytwelve-fonts' );
      }
add_action( 'wp_enqueue_scripts', 'piratenkleider_dequeue_fonts', 11 );

function piratenkleider_addfonts() {
  global $options;
  global $default_fonts;  
  $output = "";
  $setfont = "";

  if ((isset($options['fonts-content'])) && ($options['fonts-content'] != 'none')) {
      $setfont = $options['fonts-content'];
            $seturl=0;

      if (isset($default_fonts[$setfont]['webfont'])
              && ($default_fonts[$setfont]['webfont']==1)) {
        $output .= '@font-face { font-family: FontPiratenkleiderDefault; local: '.$setfont.'; src: ';
          if (isset($default_fonts[$setfont]['eot'])) {
              $output .= 'url('.get_template_directory_uri().$default_fonts[$setfont]['eot'].') format("embedded-opentype")';
              $seturl = 1;
          }
          if (isset($default_fonts[$setfont]['ttf'])) {
              if ($seturl==1) $output .= ", ";
              $output .= 'url('.get_template_directory_uri().$default_fonts[$setfont]['ttf'].') format("truetype")';
              $seturl = 1;
          }
          if (isset($default_fonts[$setfont]['woff'])) {
              if ($seturl==1) $output .= ", ";
              $output .= 'url('.get_template_directory_uri().$default_fonts[$setfont]['woff'].') format("woff")';
              $seturl = 1;
          }
          if (isset($default_fonts[$setfont]['svg'])) {
              if ($seturl==1) $output .= ", ";
              $output .= 'url('.get_template_directory_uri().$default_fonts[$setfont]['svg'].') format("svg")';                                              
          }          $output .= ";}\n";    
          $output .= "body,.defaultfont,.teaserlinks ul li a span { font-family: FontPiratenkleiderDefault; }\n";
  
      } else {
        $output .= 'body,.defaultfont,.teaserlinks ul li a span { font-family: '.$default_fonts[$setfont]['family'].'; }';
        $output .= "\n";  
      }
  }  
  if ((isset($options['fonts-headers'])) && ($options['fonts-headers'] != 'none')) {
      $setfont = $options['fonts-headers'];
            $seturl=0;

      if (isset($default_fonts[$setfont]['webfont'])
              && ($default_fonts[$setfont]['webfont']==1)) {
        $output .= '@font-face { font-family: FontPiratenkleiderHeadlines; local: '.$setfont.'; src: ';
          if (isset($default_fonts[$setfont]['eot'])) {
              $output .= 'url('.get_template_directory_uri().$default_fonts[$setfont]['eot'].') format("embedded-opentype")';
              $seturl = 1;
          }
          if (isset($default_fonts[$setfont]['ttf'])) {
              if ($seturl==1) $output .= ", ";
              $output .= 'url('.get_template_directory_uri().$default_fonts[$setfont]['ttf'].') format("truetype")';
              $seturl = 1;
          }
          if (isset($default_fonts[$setfont]['woff'])) {
              if ($seturl==1) $output .= ", ";
              $output .= 'url('.get_template_directory_uri().$default_fonts[$setfont]['woff'].') format("woff")';
              $seturl = 1;
          }
          if (isset($default_fonts[$setfont]['svg'])) {
              if ($seturl==1) $output .= ", ";
              $output .= 'url('.get_template_directory_uri().$default_fonts[$setfont]['svg'].') format("svg")';                                              
          }          $output .= ";}\n";    
          $output .= "h1,h2,h3,h4,h5,h6,.headlinefont,.tagcloud,.post-nav a,ol.az dl dt,.post .post-info .cal-icon .day,.first-startpage-widget-area li a { font-family: FontPiratenkleiderHeadlines; }\n";
  
      } else {
        $output .= 'h1,h2,h3,h4,h5,h6,.headlinefont,.tagcloud,.post-nav a,ol.az dl dt,.post .post-info .cal-icon .day,.first-startpage-widget-area li a { font-family: '.$default_fonts[$setfont]['family'].'; }';
        $output .= "\n";  
      }
      
  }
  if ((isset($options['fonts-menuheaders'])) && ($options['fonts-menuheaders'] != 'none')) {
      $setfont = $options['fonts-menuheaders'];
      $seturl=0;
      if (isset($default_fonts[$setfont]['webfont'])
              && ($default_fonts[$setfont]['webfont']==1)) {
        $output .= '@font-face { font-family: FontPiratenkleiderMenuHeadlines; local: '.$setfont.'; src: ';
          if (isset($default_fonts[$setfont]['eot'])) {
              $output .= 'url('.get_template_directory_uri().$default_fonts[$setfont]['eot'].') format("embedded-opentype")';
              $seturl = 1;
          }
          if (isset($default_fonts[$setfont]['ttf'])) {
              if ($seturl==1) $output .= ", ";
              $output .= 'url('.get_template_directory_uri().$default_fonts[$setfont]['ttf'].') format("truetype")';
              $seturl = 1;
          }
          if (isset($default_fonts[$setfont]['woff'])) {
              if ($seturl==1) $output .= ", ";
              $output .= 'url('.get_template_directory_uri().$default_fonts[$setfont]['woff'].') format("woff")';
              $seturl = 1;
          }
          if (isset($default_fonts[$setfont]['svg'])) {
              if ($seturl==1) $output .= ", ";
              $output .= 'url('.get_template_directory_uri().$default_fonts[$setfont]['svg'].') format("svg")';                                              
          }
          $output .= ";}\n";    
          $output .= ".menufont, .nav-main ul.menu li a,.cifont,.sticker ul li,.teaserlinks ul li a { font-family: FontPiratenkleiderMenuHeadlines; }\n";  
      } else {
        $output .= '.menufont,.nav-main ul.menu li a,.cifont,.sticker ul li,.teaserlinks ul li a { font-family: '.$default_fonts[$setfont]['family'].'; }';
        $output .= "\n";  
      }
  }
 
  if ((isset($output)) && (strlen($output)>1)) {
      $out = "<style>";
      $out .= $output;
      $out .= "</style>\n";
  }
  echo $out;
}
add_action('wp_head', 'piratenkleider_addfonts');


function piratenkleider_addmetatags() {
  global $options;

    $output = "";
    $output .= "\t".'<meta charset="'.get_bloginfo('charset').'">'."\n";
    $output .= "\t".'<!--[if IE]> <meta http-equiv="X-UA-Compatible" content="IE=9"> <![endif]-->'."\n";
    $output .= "\t".'<meta name="viewport" content="width=device-width, initial-scale=1.0">'."\n";
    
    if ((isset( $options['meta-description'] )) && ( strlen(trim($options['meta-description']))>1 )) {
	 $output .= "\t".'<meta name="description" content="'.$options['meta-description'].'">'."\n";
    }
    if ((isset( $options['meta-author'] )) && ( strlen(trim($options['meta-author']))>1 )) {
	$output .= "\t".'<meta name="author" content="'.$options['meta-author'].'">'."\n";
     }
    if ((isset( $options['meta-verify-v1'] )) && ( strlen(trim($options['meta-verify-v1']))>1 )) {
	$output .= "\t".'<meta name="verify-v1" content="'.$options['meta-verify-v1'].'">'."\n";
     }

    $csv_tags = '';
    $tags = '';
    if ($options['aktiv-autokeywords']) {   
	$posttags = get_tags(array('number'=> $maxwords, 'orderby' => 'count', 'order'=> 'DESC'));
	$tags = '';
	    if (isset($posttags)) {
		foreach($posttags as $tag) {
		    $csv_tags .= $tag->name . ',';
		}	
		$tags = substr( $csv_tags,0,-2);
	    }
	if ((isset($options['meta-keywords'])) && (strlen(trim($options['meta-keywords']))>1 )) {
	    $tags = $options['meta-keywords'].', '.$tags;
	}
    } else {
	if ((isset($options['meta-keywords'])) && (strlen(trim($options['meta-keywords']))>1 )) {
	    $tags = $options['meta-keywords'];
	}	
    }
    if ((isset($tags)) && (strlen(trim($tags))>2 )) {
	if (strlen(trim($tags))>$maxlength) {
	    $tags = substr($tags,0,strpos($tags,",",$maxlength));
	}	
	$output .= "\t".'<meta name="keywords" content="'.$tags.'">'."\n";
    }
    
    if ((isset($options['favicon-file'])) && ($options['favicon-file']>0 )) {	 
	$output .=  "\t".'<link rel="shortcut icon" href="'.wp_get_attachment_url($options['favicon-file']).'">'."\n";
    } else {
	$output .=  "\t".'<link rel="apple-touch-icon" href="'.get_template_directory_uri().'/apple-touch-icon.png">'."\n";
	$output .=  "\t".'<link rel="shortcut icon" href="'.get_template_directory_uri().'/favicon.ico">'."\n";
    }
    echo $output;
}

add_action('wp_head', 'piratenkleider_addmetatags');





/* Anonymize IP */
function getAnonymIp( $ip, $strongness = 2 ) {
    if ($strongness==2) {
        if( filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ){
             /* IPv4 - Strong BSI Norm: last two oktetts to 0 */        
            return preg_replace('/[0-9]+.[0-9]+\z/', '0.0', $ip);	
        } else {
            /* IPv6 */
             return preg_replace('/[a-z0-9]*:[a-z0-9]*:[a-z0-9]*:[a-z0-9]*:[a-z0-9]*\z/', '0:0:0:0:0', $ip);	
        }
    } elseif ($strongness==1) {
         if( filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ){
	/* Weak BSI Norm: last two oktetts to 0 */
            return preg_replace('/[0-9]+\z/', '0', $ip);	
         } else {
             /* IPv6 */
             return preg_replace('/[a-z0-9]*:[a-z0-9]*:[a-z0-9]*:[a-z0-9]*\z/', '0:0:0:0', $ip); 
         }
    } elseif ($strongness==0) {
	/* No anonymizing */
	return $ip;		
    } else {
	if( filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ){
             /* IPv4 - Strong BSI Norm: last two oktetts to 0 */        
            return preg_replace('/[0-9]+.[0-9]+\z/', '0.0', $ip);	
        } else {
            /* IPv6 */
             return preg_replace('/[a-z0-9]*:[a-z0-9]*:[a-z0-9]*:[a-z0-9]*:[a-z0-9]*\z/', '0:0:0:0:0', $ip);	
        }
    }
    
}     

function feed_lifetime_cb( ) {
            global $options;
            return $options['feed_cache_lifetime'];
}
add_filter( 'wp_feed_cache_transient_lifetime' , 'feed_lifetime_cb' );
        
function piratenkleider_avatar ($avatar_defaults) {
    global $defaultoptions;
    $myavatar =  $defaultoptions['src-default-avatar']; 
    $avatar_defaults[$myavatar] = "Piratenkleider";
    return $avatar_defaults;
}
add_filter( 'avatar_defaults', 'piratenkleider_avatar' );

/* Refuse spam-comments on media */
function filter_media_comment_status( $open, $post_id ) {
	$post = get_post( $post_id );
	if( $post->post_type == 'attachment' ) {
		return false;
	}
	return $open;
}
add_filter( 'comments_open', 'filter_media_comment_status', 10 , 2 );

/* Format list for Tagclouds also in widgets */
function edit_args_tag_cloud_widget($args) {
    $args = array('format' => 'list');
    return $args;
}
add_filter('widget_tag_cloud_args','edit_args_tag_cloud_widget');


if ( ! function_exists( 'get_piratenkleider_options' ) ) :
/*
 * Erstes Bild aus einem Artikel auslesen, wenn dies vorhanden ist
 */
function get_piratenkleider_options( $field ){
    global $defaultoptions;	    
    if (!isset($field)) {
	$field = 'piratenkleider_theme_options';
    }
    $orig = get_option($field);
    if (!is_array($orig)) {
        $orig=array();
    }
    $alloptions = array_merge( $defaultoptions, $orig  );	
    return $alloptions;
}
endif;


function piratenkleider_compatibility ($oldoptions) {
    global $defaultoptions;
    $doupdate = 0;
    
    $newoptions = array_merge($defaultoptions,$oldoptions);	        
    
    if ($doupdate==1) {
	update_option('piratenkleider_theme_options', $newoptions);
    }
    return $newoptions;
}

if ( ! function_exists( 'piratenkleider_get_image_attributs' ) ) :
    function piratenkleider_get_image_attributs($id=0) {
        $precopyright = __('Bild: ','piratenkleider');
        if ($id==0) return;
        
        $meta = get_post_meta( $id );
        if (!isset($meta)) {
         return;
        }
        $result = array();
        $result['alt'] = trim(strip_tags($meta['_wp_attachment_image_alt'][0]));
                
        if (isset($meta['_wp_attachment_metadata']) && is_array($meta['_wp_attachment_metadata'])) {        
         $data = unserialize($meta['_wp_attachment_metadata'][0]);
         if (isset($data['image_meta']) && is_array($data['image_meta']) && isset($data['image_meta']['copyright'])) {
                $result['copyright'] = trim(strip_tags($data['image_meta']['copyright']));
         }
        }
        $attachment = get_post($id);
        
        if (isset($attachment) ) {
         $result['beschriftung'] = trim(strip_tags( $attachment->post_excerpt ));
         $result['beschreibung'] = trim(strip_tags( $attachment->post_content ));
         $result['title'] = trim(strip_tags( $attachment->post_title )); // Finally, use the title
        }
        
        $displayinfo = $result['beschriftung'];
        if (empty($displayinfo)) $displayinfo = $result['beschreibung'];
        if (empty($displayinfo) && !empty($result['copyright'])) $displayinfo = $precopyright.$result['copyright'];
        if (empty($displayinfo)) $displayinfo = $result['alt'];
        $result['credits'] = $displayinfo;
        return $result;
                
    }
endif;

if ( ! function_exists( 'piratenkleider_filter_wp_title' ) ) :   
/*
 * Sets the title
 */    
function piratenkleider_filter_wp_title( $title, $separator ) {
        // Don't affect wp_title() calls in feeds.
        if ( is_feed() )
                return $title;
        global $paged, $page;

        if ( is_search() ) {
                $title = sprintf( __( 'Suchergebnisse f&uuml;r %s', 'piratenkleider' ), '"' . get_search_query() . '"' );
                if ( $paged >= 2 )
                        $title .= " $separator " . sprintf( __( 'Seite %s', 'piratenkleider' ), $paged );
                $title .= " $separator " . get_bloginfo( 'name', 'display' );
                return $title;
        }

        $title .= get_bloginfo( 'name', 'display' );

        $site_description = get_bloginfo( 'description', 'display' );
        if ( $site_description && ( is_home() || is_front_page() ) )
                $title .= " $separator " . $site_description;

        if ( $paged >= 2 || $page >= 2 )
                $title .= " $separator " . sprintf( __( 'Seite %s', 'piratenkleider' ), max( $paged, $page ) );

        return $title;
}
endif;
add_filter( 'wp_title', 'piratenkleider_filter_wp_title', 10, 2 );


function piratenkleider_excerpt_length( $length ) {
	global $defaultoptions;
        return $defaultoptions['teaser_maxlength'];
}
add_filter( 'excerpt_length', 'piratenkleider_excerpt_length' );

function piratenkleider_continue_reading_link() {
        return ' <a class="nobr" title="'.strip_tags(get_the_title()).'" href="'. get_permalink() . '">' . __( 'Weiterlesen <span class="meta-nav">&rarr;</span>', 'piratenkleider' ) . '</a>';
}

function piratenkleider_auto_excerpt_more( $more ) {
        return ' &hellip;' . piratenkleider_continue_reading_link();
}
add_filter( 'excerpt_more', 'piratenkleider_auto_excerpt_more' );


function piratenkleider_custom_excerpt_more( $output ) {
       if ( has_excerpt() && ! is_attachment() ) {      
                $output .= piratenkleider_continue_reading_link();
        }
        return $output;
}
add_filter( 'get_the_excerpt', 'piratenkleider_custom_excerpt_more' );



function piratenkleider_remove_gallery_css( $css ) {
        return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
add_filter( 'gallery_style', 'piratenkleider_remove_gallery_css' );


function honor_ssl_for_attachments($url) {
	$http = site_url(FALSE, 'http');
	$https = site_url(FALSE, 'https');
        return is_ssl() ? str_replace($http, $https, $url) : $url;
}
add_filter('wp_get_attachment_url', 'honor_ssl_for_attachments');

if ( ! function_exists( 'piratenkleider_comment' ) ) :
/**
 * Template for comments and pingbacks.
 */
function piratenkleider_comment( $comment, $args, $depth ) {
        $GLOBALS['comment'] = $comment;
        global $defaultoptions;
        global $options;         
        
        switch ( $comment->comment_type ) :
                case '' :
        ?>
        <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
                <div id="comment-<?php comment_ID(); ?>">
                <div class="comment-details">
                    
                <div class="comment-author vcard">
                    <?php if ($options['aktiv-avatar']==1) {
                        echo '<div class="avatar">';
                        echo get_avatar( $comment, 48, $defaultoptions['src-default-avatar']); 
                        echo '</div>';   
                    } 
                    printf( __( '%s <span class="says">meinte am</span>', 'piratenkleider' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); 
                    ?>
                </div><!-- .comment-author .vcard -->
                <?php if ( $comment->comment_approved == '0' ) : ?>
                        <em><?php _e( 'Der Kommentar wartet auf die Freischaltung.', 'piratenkleider' ); ?></em>
                        <br />
                <?php endif; ?>

                <div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
                   <?php
                          /* translators: 1: date, 2: time */
                       printf( __( '%1$s um %2$s', 'piratenkleider' ), get_comment_date(),  get_comment_time() ); ?></a> Folgendes:<?php edit_comment_link( __( '(Edit)', 'piratenkleider' ), ' ' );
                    ?>
                </div><!-- .comment-meta .commentmetadata -->
                </div>

                <div class="comment-body"><?php comment_text(); ?></div>
                <?php if ($options['aktiv-commentreplylink']) { ?>
                <div class="reply">
                        <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>                       
                </div> <!-- .reply -->
                <?php } ?>


        </div><!-- #comment-##  -->

        <?php
                        break;
                case 'pingback'  :
                case 'trackback' :
        ?>
        <li class="post pingback">
                <p><?php _e( 'Pingback:', 'piratenkleider' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'piratenkleider'), ' ' ); ?></p>
        <?php
                        break;
        endswitch;
}
endif;




function piratenkleider_remove_recent_comments_style() {
        global $wp_widget_factory;
        remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'piratenkleider_remove_recent_comments_style' );

if ( ! function_exists( 'piratenkleider_post_teaser' ) ) :
/**
 * Erstellung eines Artikelteasers
 */
function piratenkleider_post_teaser($titleup = 1, $showdatebox = 1, $showdateline = 0, $teaserlength = 200, $thumbfallback = 1, $usefloating = 0) {
  global $options;
  global $post;
  
  $post_id = $post->ID;
  $sizeclass='';
  $out = '';
  if ('linktipps'== get_post_type()  ) {
      $out = linktipp_display($post);
      return $out;
  }
 
  $leftbox = '';
  $sizeclass = 'p3-column withthumb';   
  if (($showdatebox>0)  && ($showdatebox<5)) {
          
       // Generate Thumb/Pic or Video first to find out which class we need

	    $leftbox .=  '<div class="infoimage">';	    
	    $sizeclass = 'p3-column withthumb'; 
	    $thumbnailcode = '';	
	    $firstpic = '';
	    $firstvideo = '';
	    if (has_post_thumbnail()) {
		$thumbnailcode = get_the_post_thumbnail($post->ID, 'teaser-thumb');
	    }
		   
		$firstpic = get_piratenkleider_firstpicture();
		$firstvideo = get_piratenkleider_firstvideo();
		$fallbackimg = '<img src="'.$options['src-teaser-thumbnail_default'].'" alt="">';
		if ($showdatebox==1) {
		    if (!isset($output)) { $output = $thumbnailcode;}
		    if (!isset($output)) { $output = $firstpic;}
		    if ((!isset($output)) && (isset($firstvideo))) { $output = $firstvideo; $sizeclass = 'p3-column withvideo'; }		    
		    if (!isset($output)) { $output = $fallbackimg;}		    
		    if ((isset($output)) && ( strlen(trim($output))<10 )) {$output = $fallbackimg;}		    
		} elseif ($showdatebox==2) {
		    if (!isset($output)) { $output = $firstpic;}
		    if (!isset($output)) { $output = $thumbnailcode;}
		    if ((!isset($output)) && (isset($firstvideo))) { $output = $firstvideo; $sizeclass = 'p3-column withvideo'; }		    
		    if (!isset($output)) { $output = $fallbackimg;}		    
		    if ((isset($output)) && ( strlen(trim($output))<10 )) {$output = $fallbackimg;}			    		    
		} elseif ($showdatebox==3) {
		    if ((!isset($output)) && (isset($firstvideo))) { $output = $firstvideo; $sizeclass = 'p3-column withvideo'; }		     		    
		    if (!isset($output)) { $output = $thumbnailcode;}
		    if (!isset($output)) { $output = $firstpic;}
		    if (!isset($output)) { $output = $fallbackimg;}
		    if ((isset($output)) && ( strlen(trim($output))<10 )) {$output = $fallbackimg;}		    		    
		} elseif ($showdatebox==4) {
		    if ((!isset($output)) && (isset($firstvideo))) { $output = $firstvideo; $sizeclass = 'p3-column withvideo'; }		    
		    if (!isset($output)) { $output = $firstpic;}
		    if (!isset($output)) { $output = $thumbnailcode;}
		    if (!isset($output)) { $output = $fallbackimg;}
		    if ((isset($output)) && ( strlen(trim($output))<10 )) {$output = $fallbackimg;}
		} else {
		    $output = $fallbackimg; 
		}	
	   
    	    
	    $leftbox .= $output;
	    $leftbox .=  '</div>'; 
  } else {
       $sizeclass = 'p3-column';
  }
  if ($usefloating==1) {
      $sizeclass .= " usefloating";
  }

  $out .= '<section class="'. implode(' ',get_post_class($sizeclass)).'" id="post-'.$post->ID.'" >';
  
        
     if ($titleup==1) {
        $out .= '<header class="post-title p3-cbox"><h2>';
	$out .= '<a href="'.get_permalink().'" rel="bookmark">';
	$out .= get_the_title();
        $out .= '</a></h2></header>';
	$out .= "\n";
	$out .= '<div class="p3-column">'; 
      }	

    if ($showdatebox<5) { 
	$out .= '<div class="post-info p3-col1"><div class="p3-cbox">';
	if ($showdatebox==0) {		 
	      $num_comments = get_comments_number();           
	      if (($num_comments>0) || ( $options['zeige_commentbubble_null'])) { 
                    $out .= '<div class="commentbubble">'; 
                    $link = get_comments_link();
                    $out .= '<a href="'.$link.'">'.$num_comments.'<span class="skip"> ';
                    if ($num_comments>0) {
                        $out .= __('Kommentare', 'piratenkleider' ).'</span></a>';
                    } else {
                        $out .= __('Kommentar', 'piratenkleider' ).'</span></a>';
                    }
                    $out .= "</div>\n"; 
	       }	
		$out .= '<div class="cal-icon">';
		$out .= '<span class="day">'.get_the_time('j.').'</span>';
		$out .= '<span class="month">'.get_the_time('m.').'</span>';
		$out .= '<span class="year">'.get_the_time('Y').'</span>';
		$out .= "</div>\n";

            } else {	
                $out .= $leftbox;
            } 
            $out .= "</div></div>\n";
            $out .= '<article class="post-entry p3-col3">';
            $out .= '<div class="p3-cbox';
            if ($usefloating==0) { $out .= ' p3-clearfix'; }
            $out .= '">';	
	} else {
	     $out .= '<article class="post-entry p3-cbox">';
	}
	if ($titleup==0) {  
	    $out .= '<header class="post-title"><h2>';          
	    $out .= '<a href="'.get_the_permalink().'" rel="bookmark">';
	    $out .= get_the_title(); 
            $out .= "</a></h2></header>\n";
	 }
	   
	 if (($showdatebox!=0) && ($showdateline==1)) {  
	    $out .= '<p class="pubdateinfo">';
	    $out .=  piratenkleider_post_pubdateinfo(0); 
	    $out .= "</p>\n";	  	  
	 }
	   
	 $out .= get_piratenkleider_custom_excerpt($teaserlength); 
	 if ($showdatebox<5) {	 
            $out .= "</div>\n";    	
            $out .= '<div class="p3-ie-clearing">&nbsp;</div>';	
	 } 
	$out .= "</article>\n";
        if ($titleup==1) { $out .= '</div>'; }       
    $out .= "</section>\n"; 
		
    return $out;
}
endif;

if ( ! function_exists( 'piratenkleider_post_datumsbox' ) ) :
/**
 * Erstellung der Datumsbox
 */
function piratenkleider_post_datumsbox() {
    global $options;

    
    $out = '<div class="post-info">';
    $num_comments = get_comments_number();           
     if (($num_comments>0) || ( $options['zeige_commentbubble_null'])) { 
        $out .= '<div class="commentbubble">'; 
        $link = get_comments_link();
        $out .= '<a href="'.$link.'">'.$num_comments.'<span class="skip"> ';
        if ($num_comments>0) {
            $out .= __('Kommentare', 'piratenkleider' ).'</span></a>';
        } else {
            $out .= __('Kommentar', 'piratenkleider' ).'</span></a>';
        }
        $out .= "</div>\n"; 
     } 
    $out .= '<div class="cal-icon">';
    $out .= '<span class="day">'.get_the_time('j.').'</span>';
    $out .= '<span class="month">'.get_the_time('m.').'</span>';
    $out .= '<span class="year">'.get_the_time('Y').'</span>';
    $out .= "</div>\n";
    $out .= '</div>';
    return $out;

}
endif;


if ( ! function_exists( 'piratenkleider_post_pubdateinfo' ) ) :
/**
 * Fusszeile unter Artikeln: Ver&ouml;ffentlichungsdatum
 */
function piratenkleider_post_pubdateinfo($withtext = 1) {
    $out = '';
    if ($withtext==1) {
	$out .= '<span class="meta-prep">';
        $out .= __('Ver&ouml;ffentlicht am', 'piratenkleider' );
	$out .= '</span> ';
    }
    $out .= '<span class="entry-date">';
    $out .= get_the_date();
    $out .= "</span>\n";
    return $out;
}
endif;

if ( ! function_exists( 'piratenkleider_post_autorinfo' ) ) :
/**
 * Fusszeile unter Artikeln: Autorinfo
 */
function piratenkleider_post_autorinfo() {
        printf( __( ' <span class="meta-prep-author">von</span> %1$s ', 'piratenkleider' ),               
                sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span> ',
                        get_author_posts_url( get_the_author_meta( 'ID' ) ),
                        sprintf( esc_attr__( 'Artikel von %s', 'piratenkleider' ), get_the_author() ),
                        get_the_author()
                )
        );
}
endif;

if ( ! function_exists( 'piratenkleider_post_taxonominfo' ) ) :
/**
 * Fusszeile unter Artikeln: Taxonomie
 */
function piratenkleider_post_taxonominfo() {
         $tag_list = get_the_tag_list( '', ', ' );
        if ( $tag_list ) {
                $posted_in = __( 'unter %1$s und tagged %2$s. <br>Hier der permanente <a href="%3$s" title="Permalink to %4$s" rel="bookmark">Link</a> zu diesem Artikel.', 'piratenkleider' );
        } elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
                $posted_in = __( 'unter %1$s. <br><a href="%3$s" title="Permalink to %4$s" rel="bookmark">Permanenter Link</a> zu diesem Artikel.', 'piratenkleider' );
        } else {
                $posted_in = __( '<a href="%3$s" title="Permalink to %4$s" rel="bookmark">Permanenter Link</a> zu diesem Artikel.', 'piratenkleider' );
        }
        // Prints the string, replacing the placeholders.
        printf(
                ' '.$posted_in,
                get_the_category_list( ', ' ),
                $tag_list,
                get_permalink(),
                the_title_attribute( 'echo=0' )
        );
}
endif;

// this function initializes the iframe elements 
// maybe wont work on multisite installations. please use plugins instead.
function piratenkleider_change_mce_options($initArray) {
    $ext = 'iframe[align|longdesc|name|width|height|frameborder|scrolling|marginheight|marginwidth|src]';
    if ( isset( $initArray['extended_valid_elements'] ) ) {
        $initArray['extended_valid_elements'] .= ',' . $ext;
    } else {
        $initArray['extended_valid_elements'] = $ext;
    }
    // maybe; set tiny paramter verify_html
    $initArray['verify_html'] = false;
    return $initArray;
}
add_filter('tiny_mce_before_init', 'piratenkleider_change_mce_options');




class My_Walker_Nav_Menu extends Walker_Nav_Menu {
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0) {
        if ( '-' === $item->title ) {
            $output .= '<li class="menu_separator"><hr>';
        } else{
            parent::start_el( $output, $item, $depth, $args, $id);
        }
    }
    public function display_element($el, &$children, $max_depth, $depth = 0, $args = array(), &$output){
        $id = $this->db_fields['id'];
        if(isset($children[$el->$id]))
            $el->classes[] = 'has_children';

        parent::display_element($el, $children, $max_depth, $depth, $args, $output);
    }
}

if ( ! function_exists( 'get_piratenkleider_socialmediaicons' ) ) :
/**
 * Displays Social Media Icons
 */
function get_piratenkleider_socialmediaicons( $darstellung = 1 ){
    global $options;
    global $default_socialmedia_liste;
    $zeigeoption = $options['alle-socialmediabuttons'];
    
    if ($darstellung ==0) {
        /* Keine Links */
        return; 
    } 
    if ($darstellung!=$zeigeoption) {
        /* Nichts anzeigen, da wir im falschen Modus sind */
        return;
    }
 
    if ($zeigeoption ==2) {    
           /* Links an der Seite */
            echo '<div id="socialmedia_iconbar">';
    }
   
    echo '<ul class="socialmedia">';       
    foreach ( $default_socialmedia_liste as $entry => $listdata ) {        
        
        $value = '';
        $active = 0;
        if (isset($options['sm-list'][$entry]['content'])) {
                $value = $options['sm-list'][$entry]['content'];
        } else {
                $value = $default_socialmedia_liste[$entry]['content'];
         }
         if (isset($options['sm-list'][$entry]['active'])) {
                $active = $options['sm-list'][$entry]['active'];
        }        
        if (($active ==1) && ($value)) {
            echo '<li><a class="icon_'.$entry.'" href="'.$value.'">';
            echo $listdata['name'].'</a></li>';
        }
    }
    echo '</ul>';
       
    if ($zeigeoption ==2) {    
           /* Links an der Seite */
            echo '</div>';
    }
}
endif;


if ( ! function_exists( 'get_piratenkleider_seitenmenu' ) ) :
/*
 * Anzeige des Sidebar-Menus
 */
function get_piratenkleider_seitenmenu( $zeige_sidebarpagemenu = 1 , $zeige_subpagesonly =1 , $seitenmenu_mode = 0 ){
  global $post;
  $sidelinks = '';
    if ($zeige_sidebarpagemenu==1) {   
		if (($seitenmenu_mode == 1) || (!has_nav_menu( 'primary' ))) {
			if ($zeige_subpagesonly==1) {
				//if the post has a parent

				if($post->post_parent){
				   if($post->ancestors) {
						$ancestors = end($post->ancestors);
						$sidelinks = wp_list_pages("title_li=&child_of=".$ancestors."&echo=0");
					} else {                
						$sidelinks .= wp_list_pages("sort_column=menu_order&title_li=&echo=0&depth=5&child_of=".$post->post_parent);              
					} 
				}else{
					// display only main level and children
					$sidelinks .= wp_list_pages("sort_column=menu_order&title_li=&echo=0&depth=5&child_of=".$post->ID);
				}

				if ($sidelinks) { 
					echo '<ul class="menu">';                   
					echo $sidelinks; 
					echo '</ul>';         
				} 

			} else {
				echo '<ul class="menu">';   
					wp_page_menu( ); 
				echo '</ul>';                        
			} 
		} else {
				if ($zeige_subpagesonly==1) {
					wp_nav_menu( array('depth' => 0, 'container_class' => 'menu-header subpagesonly', 'theme_location' => 'primary', 'walker'  => new My_Walker_Nav_Menu()) );      
				} else { 
					wp_nav_menu( array('depth' => 0, 'container_class' => 'menu-header', 'theme_location' => 'primary', 'walker'  => new My_Walker_Nav_Menu()) );      
				}
		}
    }
  
}
endif;

if ( ! function_exists( 'get_piratenkleider_firstpicture' ) ) :
/*
 * Erstes Bild aus einem Artikel auslesen, wenn dies vorhanden ist
 */
function get_piratenkleider_firstpicture(){
    global $post;
    $first_img = '';
    ob_start();
    ob_end_clean();
    $matches = array();
    preg_match('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
   if ((is_array($matches)) && (isset($matches[1]))) {
        $first_img = $matches[1];
        if (!empty($first_img)){
            $site_link =  home_url();  
            $first_img = preg_replace("%$site_link%i",'', $first_img); 
            $imagehtml = '<img src="'.$first_img.'" alt="" >';
            return $imagehtml;    
        }
    }
}
endif;


if ( ! function_exists( 'get_piratenkleider_firstvideo' ) ) :
/*
 * Erstes Bild aus einem Artikel auslesen, wenn dies vorhanden ist
 */
function get_piratenkleider_firstvideo($width = 300, $height = 169, $nocookie =1, $searchplain =1){
    global $post;
    ob_start();
    ob_end_clean();
    $matches = array();
    preg_match('/src="([^\'"]*www\.youtube[^\'"]+)/i', $post->post_content, $matches);
    if ((is_array($matches)) && (isset($matches[1]))) {
        $entry = $matches[1];	
        if (!empty($entry)){
	    if ($nocookie==1) {
		$entry = preg_replace('/feature=player_embedded&amp;/','',$entry);
		$entry = preg_replace('/feature=player_embedded&/','',$entry);
		$entry = preg_replace('/youtube.com\/watch\?v=/','youtube-nocookie.com/embed/',$entry);
	    }
            $htmlout = '<iframe width="'.$width.'" height="'.$height.'" src="'.$entry.'" allowfullscreen="true"></iframe>';
            return $htmlout;    
        }
    }
    // Schau noch nach YouTube-URLs die Plain im text sind. Hilfreich fuer
    // Installationen auf Multisite ohne iFrame-Unterstützung
    if ($searchplain==1) {
       
       preg_match('/\b(https?:\/\/www\.youtube[\-a-z]*\.com\/(watch|embed)[\/a-z0-9\.\-&;\?_=]+)/i', $post->post_content, $matches);
        if ((is_array($matches)) && (isset($matches[1]))) {
            $entry = $matches[1];
            if (!empty($entry)){
                if ($nocookie==1) {
                    $entry = preg_replace('/feature=player_embedded&amp;/','',$entry);
		    $entry = preg_replace('/feature=player_embedded&/','',$entry);
                    $entry = preg_replace('/youtube.com\/watch\?v=/','youtube-nocookie.com/embed/',$entry);
                }
                $htmlout = '<iframe width="'.$width.'" height="'.$height.'" src="'.$entry.'" allowfullscreen></iframe>';
                return $htmlout;    
            }
         }  
    }
}
endif;



if ( ! function_exists( 'get_piratenkleider_custom_excerpt' ) ) :
/*
 * Erstellen des Extracts
 */
function get_piratenkleider_custom_excerpt($length = 0, $continuenextline = 1, $removeyoutube = 1, $alwayscontinuelink = 0){
  global $options;
  global $post;
  global $defaultoptions;
      
  if (has_excerpt()) {
      return  get_the_excerpt();
      
  } else {
      $excerpt = get_the_content();
       if (!isset($excerpt)) {
          $excerpt = __( 'Kein Inhalt', 'piratenkleider' );
        }
  }
  if ($length==0) {
      $length = $options['teaser_maxlength'];
      if ($length <=0) {
	  $length = 100;
      }
  }
  if ($removeyoutube==1) {
    $excerpt = preg_replace('/\s+(https?:\/\/www\.youtube[\/a-z0-9\.\-\?&;=_]+)/i','',$excerpt);
  }
  
  $excerpt = strip_shortcodes($excerpt);
  $excerpt = strip_tags($excerpt, $defaultoptions['excerpt_allowtags']); 
  
  
  if (mb_strlen($excerpt)<5) {
      $excerpt = '<!-- '.__( 'Kein textueller Inhalt', 'piratenkleider' ).' -->';
  }

  $needcontinue =0;
  if (mb_strlen($excerpt) >  $length) {
    $the_str = mb_substr($excerpt, 0, $length);
    $the_str .= "...";
    $needcontinue = 1;
  }  else {
      $the_str = $excerpt;
  }
  $the_str = '<p>'.$the_str;
  if (isset($options['continuelink']) && ($options['continuelink'] != $alwayscontinuelink)) {
      $alwayscontinuelink = $options['continuelink'];
  }
  if ($alwayscontinuelink < 2) {
      if (($needcontinue==1) || ($alwayscontinuelink==1)) {
	  if ($continuenextline==1) {
	      $the_str .= '<br>';
	  }
	  $the_str .= piratenkleider_continue_reading_link();
      }
  }
  $the_str .= '</p>';
  return $the_str;
}
endif;

if ( ! function_exists( 'short_title' ) ) :
/*
 * Erstellen des Kurztitels
 */
function short_title($after = '...', $length = 6, $textlen = 10) {
   $thistitle =   get_the_title();  
   $mytitle = explode(' ', get_the_title());
   if ((count($mytitle)>$length) || (mb_strlen($thistitle)> $textlen)) {
       while(((count($mytitle)>$length) || (mb_strlen($thistitle)> $textlen)) && (count($mytitle)>1)) {
           array_pop($mytitle);
           $thistitle = implode(" ",$mytitle);           
       }       
       $morewords = 1;
   } else {              
       $morewords = 0;
   }
   if (mb_strlen($thistitle)> $textlen) {
      $thistitle = mb_substr($thistitle, 0, $textlen);
      $morewords = 1;     
   }
   if ($morewords==1) {
       $thistitle .= $after;
   }   
   return $thistitle;
}
endif;

if ( ! function_exists( 'piratenkleider_fetch_feed' ) ) :
/*
 * Feet holen mit direkter Angabe der SimplePie-Parameter
 */
function piratenkleider_fetch_feed($url,$lifetime=0) {
    global $defaultoptions;
    global $options;


    if ($lifetime==0){
        $lifetime=  $options['feed_cache_lifetime'];
    }
    if ($lifetime < 600) $lifetime = 1800;
    // Das holen von feeds sollte auf keinen Fall haeufiger als alle 10 Minuten erfolgen

    require_once  (ABSPATH . WPINC . '/class-feed.php');

    $feed = new SimplePie();
    if ($defaultoptions['use_wp_feed_defaults']) {
        $feed->set_cache_class('WP_Feed_Cache');
        $feed->set_file_class('WP_SimplePie_File');
    } else {
        if ((isset($defaultoptions['dir_feed_cache'])) && (!empty($defaultoptions['dir_feed_cache']))) {
            if (is_dir($defaultoptions['dir_feed_cache'])) { 
                $feed->set_cache_location($defaultoptions['dir_feed_cache']);
            } else {
                mkdir($defaultoptions['dir_feed_cache']);
                if (!is_dir($defaultoptions['dir_feed_cache'])) {
                    echo "Wasnt able to create Feed-Cache directory";
                } else {
                    $feed->set_cache_location($defaultoptions['dir_feed_cache']);
                }
            }
        }  
    }
    $feed->set_feed_url($url);
    $feed->set_cache_duration($lifetime);
    
    do_action_ref_array( 'wp_feed_options', array( $feed, $url ) );
    $feed->init();
    $feed->handle_content_type();

    if ( $feed->error() )
        return new WP_Error('simplepie-error', $feed->error());

    return $feed;
}
endif;


function wpi_linkexternclass($content){
        return preg_replace_callback('/<a[^>]+/', 'wpi_linkexternclass_callback', $content);
    }
 
function wpi_linkexternclass_callback($matches) {
        $link = $matches[0];
        $site_link = home_url();  
        if ((strpos($link, 'class') === false)
		   && (strpos($link, 'mailto:') === false)
                   && (strpos($link, 'http') >0)
                   && (strpos($link, $site_link) === false)) {
            $link = preg_replace("%(href=\S(?!($site_link|#)))%i", 'class="extern" $1', $link);
        }       
        return $link;
    }
add_filter('the_content', 'wpi_linkexternclass');


 function wpi_relativeurl($content){
        return preg_replace_callback('/<a[^>]+/', 'wpi_relativeurl_callback', $content);
    }
 
function wpi_relativeurl_callback($matches) {
        $link = $matches[0];
        $site_link =  wp_make_link_relative(home_url());  
        $link = preg_replace("%href=\"$site_link%i", 'href="', $link);                 
        return $link;
    }
 add_filter('the_content', 'wpi_relativeurl');
   

function piratenkleider_breadcrumb() {
  global $defaultoptions;
  
  $delimiter	= $defaultoptions['breadcrumb_delimiter']; // = ' / ';
  $home		= $defaultoptions['breadcrumb_homelinktext']; // __( 'Startseite', 'piratenkleider' ); // text for the 'Home' link
  $before	= $defaultoptions['breadcrumb_beforehtml']; // '<span class="current">'; // tag before the current crumb
  $after	= $defaultoptions['breadcrumb_afterhtml']; // '</span>'; // tag after the current crumb
 
  echo '<div id="crumbs">'; 
  if ( !is_home() && !is_front_page() || is_paged() ) { 
    
    global $post;
    
    $homeLink = home_url('/');
    echo '<a href="' . $homeLink . '">' . $home . '</a>' . $delimiter;
 
    if ( is_category() ) {
	global $wp_query;
	$cat_obj = $wp_query->get_queried_object();
	$thisCat = $cat_obj->term_id;
	$thisCat = get_category($thisCat);
	$parentCat = get_category($thisCat->parent);
	if ($thisCat->parent != 0) 
	    echo(get_category_parents($parentCat, TRUE, $delimiter ));
	echo $before . __( 'Artikel der Kategorie ', 'piratenkleider' ). '"' . single_cat_title('', false) . '"' . $after;
 
    } elseif ( is_day() ) {
	echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a>' .$delimiter;
	echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a>' .$delimiter;
	echo $before . get_the_time('d') . $after; 
    } elseif ( is_month() ) {
	echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a>' . $delimiter;
	echo $before . get_the_time('F') . $after;
    } elseif ( is_year() ) {
	echo $before . get_the_time('Y') . $after; 
    } elseif ( is_single() && !is_attachment() ) {
	if ( get_post_type() != 'post' ) {
	  $post_type = get_post_type_object(get_post_type());
	  $slug = $post_type->rewrite;
	  echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a>' .$delimiter;
	  echo $before . get_the_title() . $after;
	} else {
	  echo $before . get_the_title() . $after;
	} 
    } elseif ( !is_single() && !is_page() && !is_search() && get_post_type() != 'post' && !is_404() ) {
	$post_type = get_post_type_object(get_post_type());
	echo $before . $post_type->labels->singular_name . $after;
    } elseif ( is_attachment() ) {
	$parent = get_post($post->post_parent);
	echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>'. $delimiter;
	echo $before . get_the_title() . $after;
    } elseif ( is_page() && !$post->post_parent ) {
	echo $before . get_the_title() . $after;
 
    } elseif ( is_page() && $post->post_parent ) {
	$parent_id  = $post->post_parent;
	$breadcrumbs = array();
	while ($parent_id) {
	    $page = get_page($parent_id);
	    $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
	    $parent_id  = $page->post_parent;
	}
	$breadcrumbs = array_reverse($breadcrumbs);
	foreach ($breadcrumbs as $crumb) echo $crumb . $delimiter;
	echo $before . get_the_title() . $after; 
    } elseif ( is_search() ) {
	echo $before . __( 'Suche nach ', 'piratenkleider' ).'"' . get_search_query() . '"' . $after; 
    } elseif ( is_tag() ) {
	echo $before . __( 'Artikel mit Schlagwort ', 'piratenkleider' ). '"' . single_tag_title('', false) . '"' . $after; 
    } elseif ( is_author() ) {
	global $author;
	$userdata = get_userdata($author);
	echo $before . __( 'Artikel von ', 'piratenkleider' ). $userdata->display_name . $after;
    } elseif ( is_404() ) {
	echo $before . '404' . $after;
    }
 /*
    if ( get_query_var('paged') ) {
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
      echo __('Page', 'piratenkleider') . ' ' . get_query_var('paged');
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
    }
 */
   
  } elseif (is_front_page() && $defaultoptions['zeige_breadcrump_frontpages']) {
	echo $before . $home . $after;
  } elseif (is_home() && $defaultoptions['zeige_breadcrump_frontpages']) {
	echo $before . get_the_title(get_option('page_for_posts')) . $after;
  }
   echo '</div>'; 
}

 

function piratenkleider_header_style() {} 


function piratenkleider_admin_style() {
    wp_register_style( 'themeadminstyle', get_template_directory_uri().'/css/admin.css' );	   
    wp_enqueue_style( 'themeadminstyle' );	
    wp_enqueue_media();
    wp_register_script('themeadminscripts', get_template_directory_uri().'/js/admin.js', array('jquery'));    
    wp_enqueue_script('themeadminscripts');	   
}
add_action( 'admin_enqueue_scripts', 'piratenkleider_admin_style' );


function custom_login() { 
    echo '<link rel="stylesheet" type="text/css" href="'.get_template_directory_uri().'/css/custom-login.css" />'; 
}
add_action('login_head', 'custom_login');

add_filter('upload_mimes', 'custom_upload_mimes');
function custom_upload_mimes ( $existing_mimes=array() ) {
    $existing_mimes['css'] = 'text/plain';
    $existing_mimes['ico'] = 'image/ico';
    return $existing_mimes;
}

