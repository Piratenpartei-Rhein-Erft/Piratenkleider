
<div class="first-teaser-widget-area">
<?php if ( is_active_sidebar( 'first-teaser-widget-area' ) ) { ?>
        <?php dynamic_sidebar( 'first-teaser-widget-area' ); ?>
    <?php } else {        
          global $defaultoptions;
          global $defaultbilder_liste;
         $options = get_option( 'piratenkleider_theme_options' );
         $catname = $options['slider-catname'];
         $bilderoptions = get_option( 'piratenkleider_theme_defaultbilder' ); 
         $defaultbildsrc = $bilderoptions['slider-defaultbildsrc'];                        
         
         if (!isset($catname) ) $catname = get_cat_name(1);         
         $numberarticle = $options['slider-numberarticle'];
         if (!isset($numberarticle) )  $numberarticle =3;   
         if (!isset($options['url-mitgliedwerden'])) 
            $options['url-mitgliedwerden'] = $defaultoptions['url-mitgliedwerden'];
         if (!isset($options['teaser-subtitle'])) 
            $options['teaser-subtitle'] = $defaultoptions['teaser-subtitle'];
          if (!isset($options['teaser-title-maxlength'])) 
            $options['teaser-title-maxlength'] = $defaultoptions['teaser-title-maxlength'];        
          if (!isset($options['teaser-title-words'])) 
            $options['teaser-title-words'] = $defaultoptions['teaser-title-words'];
          
        query_posts( array( 'category_name' => "$catname", 'posts_per_page' => $numberarticle) );
        
        echo '<div class="flexslider">';
        echo '<h2 class="skip">Topthemen</h2>';
        echo "<ul class='slides'>";
        if ( have_posts() ) while ( have_posts() ) : the_post();
            echo "<li class='slide'>";
            if (has_post_thumbnail()) {
                the_post_thumbnail('full');
            } else {
                if (isset($defaultbildsrc)) {  
                    echo '<img src="'.$defaultbildsrc.'" width="'.$defaultoptions['thumb-width'].'" height="'.$defaultoptions['thumb-height'].'" alt="">';                
                } else {
                    $randombild = array_rand($defaultbilder_liste,2);
                    echo '<img src="'.$defaultbilder_liste[$randombild[0]]['src'].'" width="'.$defaultoptions['thumb-width'].'" height="'.$defaultoptions['thumb-height'].'" alt="">'; 
                    
                }
            }
            echo '<div class="caption">';
            echo '<p class="bebas">'.$options['teaser-subtitle'].'</p>';
            echo "<h3>";
            echo "<a href=";
            the_permalink();
            echo ">";
            echo short_title('&hellip;', $options['teaser-title-words'], $options['teaser-title-maxlength']);
            echo "</a>";
            echo "</h3>";
            echo "</div>";
            echo "</li>";
        endwhile;
        echo "</ul>";
        echo "</div>";
        wp_reset_query(); 
    } ?>
</div>
<div class="second-teaser-widget-area">
<div class="skin">
    <?php if ( is_active_sidebar( 'second-teaser-widget-area' ) ) { ?>
        <?php dynamic_sidebar( 'second-teaser-widget-area' ); ?>
    <?php } else { ?>
        <div class="textwidget teaserlinks">
            <ul>
                <li><a class="symbol symbol-idee" href="http://www.piratenpartei.de/politik/themen/">Informiere dich <span>über unsere Themen & Ziele!</span></a></li>
                <li><a class="symbol symbol-herz" href="http://www.piratenpartei.de/unterstutze-uns/">Unterst&uuml;tze uns <span>mit deinem Engagement!</span></a></li>
                <li><a class="symbol symbol-steuerrad" href="<?php echo $options['url-mitgliedwerden']; ?>">Werde Pirat! <span>Jetzt Mitglied werden!</span></a></li>
            </ul>
        </div>
    
    <!--
          <div class="textwidget">
            <ul>
            <li class="first"><a  href="http://www.piratenpartei.de/politik/themen/"><div>Informiere dich</div> Unsere Themen & Ziele!</a></li>
            <li class="second"><a href="http://www.piratenpartei.de/unterstutze-uns/"><div>Unterst&uuml;tze uns</div> Mit deinem Engagement!</a></li>
            <li class="third"><a  href="<?php echo $options['url-mitgliedwerden']; ?>"><div>Werde Pirat!</div> Jetzt Mitglied werden!</a></li>
            </ul>
        </div>
    -->
    <?php }  ?>
</div>
</div>
