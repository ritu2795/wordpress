<?php get_header();

while(have_posts()){
    the_post(); 
    pageBanner();?>
     
  <div class="container container--narrow page-section">
  <div class="metabox metabox--position-up metabox--with-home-link">
         <p><a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('program'); ?>"><i class="fa fa-home" aria-hidden="true"></i> All Program</a> <span class="metabox__main"><?php the_title(); ?></span></p>
       </div>
    <div class="generic-content">
                <!-- to display the generic test some content -->
                <?php the_field('main_body_content'); ?>
                <?php 

                    $relatedProfessor = new WP_Query(array(
                        'posts_per_page' => 2,
                        'post_type' => 'professor',
                        //'meta_key' => 'event_date',
                        'orderby' => 'title',
                        'order' => 'ASC',
                        'meta_query' => array(
                        array(
                            'key' => 'related_program',
                            'compare' => 'LIKE',
                            'value' => '"' . get_the_ID() . '"'
                        )
                        )
                    ));


                    if($relatedProfessor->have_posts()) {
                    echo '<hr class="section-break">';
                    echo '<h2 class="headline headline--medium">' . get_the_title() . ' Professors</h2>';
                    echo '<ul class="professor-cards">';
                    while($relatedProfessor->have_posts()) {
                    $relatedProfessor->the_post(); ?>

                    <li class="professor-card__list-item">
                        <a class="professor-card" href="<?php the_permalink(); ?>">
                            <img class="professor-card__image" src="<?php the_post_thumbnail_url('professorLandscape'); ?>">
                            <span class="professor-card__name"><?php the_title(); ?></span>
                        </a>
                    </li>
                    
                    <?php }
                    echo '</ul>';
                    }
                    
                    wp_reset_postdata(); //will refresh the ID of the page and will show the upcoming events 
                    //it will reset the global post object and all of the function like title and ID return from above will be 
                    //reset to default url based query
            $today = date('Ymd');
            $homepagePosts = new WP_Query(array(
                    'posts_per_page' => 2,
                    'post_type' => 'event',
                    'meta_key' => 'event_date',
                    'orderby' => 'meta_value_num',
                    'order' => 'ASC',
                    'meta_query' => array(
                      array(
                        'key' => 'event_date',
                        'compare' => '>=',
                        'value' => $today,
                        'type' => 'numeric'
                      ),
                      array(
                          'key' => 'related_program',
                          'compare' => 'LIKE',
                          'value' => '"' . get_the_ID() . '"'
                      )
                    )
                ));

            
           if($homepagePosts->have_posts()) {
                echo '<hr class="section-break">';
                echo '<h2 class="headline headline--medium">Upcoming ' . get_the_title() . ' Events</h2>';
                while($homepagePosts->have_posts()) {
                $homepagePosts->the_post(); 
                get_template_part('template-parts/content', 'event');

                }
           }
            ?>


    </div>
</div>
    <?php 
}



get_footer();
?>