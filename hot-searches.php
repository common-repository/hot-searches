<?php
/*
 * Plugin Name:   Hot Searches
 * Version:       0.06
 * Plugin URI:    http://www.miscgarden.com/hs/
 * Description:   Show hot searches in your blog
 * Author:        cleanerleon
 * Author URI:    http://www.miscgarden.com
 */
function insert_search_code()
{
?>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script>

$(document).ready(function() {
    $('.googlesearch').click(function(){
        $('#sbi').attr("value",$(this).find('input').attr("value"));
        $('#sbb').click();
    });
})
</script>

<?php
}
$options = get_option('hot_searches_widget');
if ( !is_array($options) ) {
        $options = array('title'=>'Hot Searches', 'count'=>10, 'showcount'=>true,'showauthor'=>true,'searchtype'=>0);
}
if($options['searchtype']==1)
    add_action('wp_head', 'insert_search_code');
add_action('init','get_search_key');
function get_search_key()
{

    $term = $_GET['s'];
    if(empty($term))
        $term = $_GET["q"];

    if(empty($term))
    {
        $refer = $_SERVER["HTTP_REFERER"];
        if(empty($refer))
            return;
        $pattern = "/http(s)?:\/\/[^\/]*google[^\/]*\/([^q]*)q=([^\&]*)/i";
        if(preg_match($pattern, $refer,$matches))
            $term = $matches[3];
        if(empty($refer))
            return;
    }

    $taxonomy = "search-key";
    if(!is_taxonomy($taxonomy))
        register_taxonomy( $taxonomy, 'search', array('hierarchical' => false, 'update_count_callback' => 'update_key_count','label' => __('Hot Searches'), 'query_var' => false, 'rewrite' => false) ) ;
    if ( !$term_info = is_term($term, $taxonomy) )
            $term_info = wp_insert_term($term, $taxonomy);

    if ( is_wp_error($term_info) )
        return;
    wp_update_term_count_now(array($term_info["term_taxonomy_id"]), $taxonomy);
}

function update_key_count($terms)
{
 	global $wpdb;

        foreach ( (array) $terms as $term) {
           // $wpdb->prepare( "SELECT COUNT FROM $wpdb->$term_taxonomy WHERE term_taxonomy_id = %d", $term);
               $count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT FROM $wpdb->term_taxonomy WHERE term_taxonomy_id = %d", $term) );
               $count++;
               $wpdb->update( $wpdb->term_taxonomy, compact( 'count' ), array( 'term_taxonomy_id' => $term ) );
        }
}

function get_keys()
{
    $taxonomy = "search-key";
    if(!is_taxonomy($taxonomy))
        register_taxonomy( $taxonomy, 'search', array('hierarchical' => false, 'update_count_callback' => 'update_key_count','label' => __('Hot Searches'), 'query_var' => false, 'rewrite' => false) ) ;

    $terms = get_terms($taxonomy);
    if ( is_wp_error($terms) )
        return array();
    $keylist = array();
    foreach($terms as $term)
    {
        $keylist["$term->name"] = $term->count;
    }
    arsort($keylist);
    return $keylist;
}

require_once('hot-searches-widget.php');
$hsWidget = new HotSearchesWidget();
?>
