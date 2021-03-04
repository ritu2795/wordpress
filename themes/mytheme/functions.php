<?php

require get_theme_file_path('/inc/search-route.php');
add_action('rest_api_init','university_custom_rest');

function university_custom_rest(){
    //adding a new custom property in json format in rest api
    //('posttype','fieldname',;'output')
    register_rest_field('post','authorName', array(
        'get_callback' => function() {
            return get_the_author();
        }
    ));

     register_rest_field('note','userNoteCount', array(
        'get_callback' => function() {
            return count_user_posts(get_current_user_id(), 'note');
        }
    )); 
}

function pageBanner($args = NULL){
    if(!$args['title']) {
        $args['title'] = get_the_title();
    }
    if(!$args['subtitle']) {
        $args['subtitle'] = get_field('page_banner_subtitle');
    }
    if(!$args['photo']) {
        if(get_field('page_banner_background_image') AND !is_archive() AND !is_home()){
            $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
        } else {
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }
 ?>   
  <div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>);"></div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
      <div class="page-banner__intro">
        <p><?php echo $args['subtitle']; ?></p>
      </div>
    </div>  
  </div>
<?php }


function university_files() {
    //wp_enqueue_script('main-javascript', get_theme_file_uri('/js/scripts-bundled.js'), NULL, '1.0', true);
    wp_enqueue_style('custom-google-fonts','//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome','//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    //wp_enqueue_style('university_main_files', get_stylesheet_uri());
   
    if(strstr($_SERVER['SERVER_NAME'], 'my-first-wordpress.local')) {
        wp_enqueue_script('main-javascript', 'http://localhost:3000/bundled.js', NULL, '1.0', true);
    } else {
        wp_enqueue_script('our-vendors-js', get_theme_file_uri('/bundled-assets/vendors~scripts.9678b4003190d41dd438.js'), NULL, '1.0', true);
        wp_enqueue_script('main-javascript', get_theme_file_uri('/bundled-assets/scripts.eab1b158895e9d2e2542.js'), NULL, '1.0', true);
        wp_enqueue_style('our-main-styles', get_theme_file_uri('/bundled-assets/styles.eab1b158895e9d2e2542.css'), NULL, '1.0', true);
    }
    wp_localize_script('main-javascript', 'universityData', array(
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest')
    ));
}
add_action('wp_enqueue_scripts','university_files');

function university_features() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorPortrait', 480, 650, true);
    add_image_size('pageBanner', 1500, 350, true);
}


add_action('after_setup_theme','university_features');

function university_adjust_queries($query){
    if(!is_admin() AND is_post_type_archive('event') AND $query->is_main_query()){
        $today = date('Ymd');
        $query->set('meta_key','event_date');
        $query->set('orderby','meta_value_num');
        $query->set( 'order','ASC');
        $query->set('meta_query',array(
            array(
              'key' => 'event_date',
              'compare' => '>=',
              'value' => $today,
              'type' => 'numeric'
            )
            ));
    }

    if(!is_admin() AND is_post_type_archive('program') AND $query->is_main_query()){
        $query->set('orderby','title');
        $query->set( 'order','ASC');
        $query->set('posts_per_page',-1);
    }
   
}

    
add_action('pre_get_posts', 'university_adjust_queries');

//Redirect subcribers account out of admin onto home page
add_action('admin_init','redirectSubsToFrontend');

function redirectSubsToFrontend(){
    $ourCurrentUser = wp_get_current_user();
    if(count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber'){
        wp_redirect(site_url('/'));
        exit;
    }
}

//remove the above black admin bar when the subscriber is logged in
add_action('wp_loaded','noSubsAdminBar');

function noSubsAdminBar(){
    $ourCurrentUser = wp_get_current_user();
    if(count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber'){
        show_admin_bar(false);
    }
}

//customize log in screen
add_filter('login_headerurl', 'ourheaderUrl');

function ourheaderUrl(){
    return esc_url(site_url('/'));
}

//to tell wp to load the css and scripts for the login page so that we can edit the login page css and fonts
add_action('login_enqueue_scripts', 'ourLoginCss');

function ourLoginCss(){
    wp_enqueue_style('custom-google-fonts','//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('our-main-styles', get_theme_file_uri('/bundled-assets/styles.eab1b158895e9d2e2542.css'));
}


//to remove the default title "powered by wordpress" on the login page
add_filter('login_headertitle','ourLoginTitle');

function ourLoginTitle(){
    return get_bloginfo('name');
}

//force note post to be private
//add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2); //2 tell function to work with 2

 function makeNotePrivate($data, $postarr) {

    if($data['post_type'] == 'note'){ //dont let subscriber to even add basic html in the note form

         //user account cannot create more than 5 posts
         if(count_user_posts(get_current_user_id(), 'note') > 4 AND !$postarr['ID']) {
            die("You have reached your note limit");
         }
         
        $data['post_content'] = sanitize_textarea_field($data['post_content']);
        $data['post_title'] = sanitize_text_field($data['post_title']); 
    }
    //make the notes private instead of published
    if($data['post_type'] = 'note' AND $data['post_status'] != 'trash') {
        $data['post_status'] = "private";
    }
    return $data;
} 

?>
