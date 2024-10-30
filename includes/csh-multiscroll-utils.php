<?php 
function cshms_get_pages_active_slide(){
    $pages_active = array();
    $q = new WP_Query(array(
        'post_type' => 'pt-multiscroll'
    ));

    foreach ($q->posts as $p){
        $page_selected =  get_post_meta($p->ID, '_cshms-page-active', true);
        if (!empty($page_selected)){
            array_push($pages_active, $page_selected);
        }
    }
    return $pages_active;
}
