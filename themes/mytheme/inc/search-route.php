<?php 

add_action('rest_api_init', 'universityRegisterSearch');

function universityRegisterSearch(){
    //localhost/wp-json/wp/v2/posts
    //register_rest_route(namespace(is wp/version1), route(ending part of the url e.g posts/professors), array to describe when someone visits the url);
    register_rest_route('university/v1', 'search', array(
        //GET and in other words WP_REST_SERVER::READABLE for any host services
        'methods' => WP_REST_SERVER::READABLE, //CRUD methods in this we want to read or load data and to do that by sending GET request
        'callback' => 'universitySearchResults'
    ));
}

function universitySearchResults($data){
    //WP will automatic converts PHP data into JSON data
   $mainQuery = new WP_Query(array(
       'post_type' => array('professor','post','page', 'program', 'event'),
       ///wp-json/university/v1/search?term=Dr. Yok (s for search & data for ?term)
       's' => sanitize_text_field($data['term']) 
   ));

   $results = array(
       'generalInfo' => array(),
       'professors' => array(),
       'programs' => array(),
       'events' => array()
   );
//till when to loop itself
   while($mainQuery->have_posts()){
        $mainQuery->the_post();
        if(get_post_type() == 'post' OR get_post_type() == 'page'){
            array_push($results['generalInfo'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'id' => get_the_ID(),
                'postType' => get_post_type(),
                'authorName' => get_the_author() 
            ));
        }

        if(get_post_type() == 'professor'){
            array_push($results['professors'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'id' => get_the_ID(),
                'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
            ));
        }

        if(get_post_type() == 'program'){
            array_push($results['programs'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'id' => get_the_ID()
            ));
        }

        if(get_post_type() == 'event'){
            $eventDate = new DateTime(get_field('event_date'));
            $description = null;
            if(has_excerpt()) {
                $description = get_the_excerpt();
            } else{
                $description = wp_trim_words(get_the_content(), 18);
            }
            array_push($results['events'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'id' => get_the_ID(),
                'month' => $eventDate->format('M'),
                'day' => $eventDate->format('d'),
                'description' => $description
            ));
        }
       
   }

   //if statement if someone has written gibbrish words and shold return empty programs
   if($results['programs']){
    //if we have more than one programs then we have to loop through every program id.
        $programMetaQuery = array('relation' => 'OR');

        foreach($results['programs'] as $item){
            array_push($programMetaQuery, array(
                'key' => 'related_program',
                'compare' => 'LIKE',
                'value' => '"' . $item['id'] . '"'
            ));
        }

        $programRelationshipQuery = new WP_Query(array(
            'post_type' => array('professor','event'),
                'meta_query' => $programMetaQuery,

        ));

        while($programRelationshipQuery->have_posts()){
                $programRelationshipQuery->the_post();
                if(get_post_type() == 'professor'){
                    array_push($results['professors'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink(),
                        'id' => get_the_ID(),
                        'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
                    ));
                }

                if(get_post_type() == 'event'){
                    $eventDate = new DateTime(get_field('event_date'));
                    $description = null;
                    if(has_excerpt()) {
                        $description = get_the_excerpt();
                    } else{
                        $description = wp_trim_words(get_the_content(), 18);
                    }
                    array_push($results['events'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink(),
                        'id' => get_the_ID(),
                        'month' => $eventDate->format('M'),
                        'day' => $eventDate->format('d'),
                        'description' => $description
                    ));
                }

        }
        //will not duplicate if same words are present
        //array_values to remove the numbers 0, 1
        $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
        $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
    }
   
   return $results;

  // return $professors->posts; will return everything to return some of the data create an array
}