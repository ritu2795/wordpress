<?php 

function university_post_types() {

    //event post type
    register_post_type('event', array(
        'capability_type' => 'event', //for roles and permissions
        'map_meta_cap' => true, //automatic map and require right capabilities at the right time
        'show_in_rest' => true, //for modern classic page but if editor is not present in supoort then it will show the older page
        'rewrite' => array('slug' => 'events'),
        'supports' => array('title', 'editor', 'excerpt'),
        'has_archive' => true,
        'public' => true,
        'menu_icon' => 'dashicons-calendar',
        'labels' => array(
            'name' => 'Events',
            'add_new_item' => 'Add New Event',
            'edit_item' => 'Edit Event',
            'all_items' => 'All Events',
            'singular_name' => 'Event'
        ) 
    ));

    //program post type
    register_post_type('program', array(
        'show_in_rest' => true, //for modern classic page but if editor is not present in supoort then it will show the older page
        'rewrite' => array('slug' => 'programs'),
        'supports' => array('title'),
        'has_archive' => true,
        'public' => true,
        'menu_icon' => 'dashicons-awards',
        'labels' => array(
            'name' => 'Programs',
            'add_new_item' => 'Add New Program',
            'edit_item' => 'Edit Program',
            'all_items' => 'All Program',
            'singular_name' => 'Program'
        ) 
    ));

    //professor post type
    register_post_type('professor', array(
        'show_in_rest' => true, //for modern classic page but if editor is not present in supoort then it will show the older page
        //'rewrite' => array('slug' => 'professors'),
        'show_in_rest' => true, //custom url for rest api
        'supports' => array('title', 'editor', 'thumbnail'),
        //'has_archive' => true,
        'public' => true,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'labels' => array(
            'name' => 'Professor',
            'add_new_item' => 'Add New Professor',
            'edit_item' => 'Edit Professor',
            'all_items' => 'All Professor',
            'singular_name' => 'Professor'
        ) 
    ));


    //note post type
    register_post_type('note', array(
        'capability_type' => 'note', //for roles and permissions
        'map_meta_cap' => true,
        //for rest api
        //'rewrite' => array('slug' => 'notes'),
        'show_in_rest' => true, //custom url for rest api
        'supports' => array('title', 'editor'),
        //'has_archive' => true,
        'public' => false, //should be private for specific account and this will not show in admin
        'show_ui' => true, //as public is false write this to show the notes in the admin area
        'menu_icon' => 'dashicons-welcome-write-blog',
        'labels' => array(
            'name' => 'Note',
            'add_new_item' => 'Add New Note',
            'edit_item' => 'Edit Note',
            'all_items' => 'All Note',
            'singular_name' => 'Note'
        ) 
    ));

}
add_action('init','university_post_types');

?>
