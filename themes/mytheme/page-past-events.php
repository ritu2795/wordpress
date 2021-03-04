<?php 
get_header();
pageBanner(array(
  'title' => 'Past Events',
  'subtitle' => 'Recap of Past Events'
));
?>


  <div class="container container--narrow page-section">
      <?php 
      //having default custom queries we have to write our own pagination
        $today = date('Ymd');
        $pastEvents = new WP_Query(array(
            'paged' => get_query_var('paged','1'),
            //'posts_per_page' => 10,
            'post_type' => 'event',
            'meta_key' => 'event_date',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                'key' => 'event_date',
                'compare' => '<',
                'value' => $today,
                'type' => 'numeric'
                )
            )
        ));
        while($pastEvents->have_posts()) {
          $pastEvents->the_post(); 
          get_template_part('template-parts/content', 'event');
          }
          echo paginate_links(array(
              'total' => $pastEvents->max_num_pages
          ));
      ?>
    
</div>
<?php
get_footer();

?>