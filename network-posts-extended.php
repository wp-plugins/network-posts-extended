<?php
/*
Plugin Name: Network Posts Extended
Plugin URI: http://www.johncardell.com/plugins/network-posts-extended/
Description: Network Posts Extended plugin enables you to share posts over WP Multi Site network.  You can display on any blog in your network the posts selected by taxanomy from any blogs including main. 
Version: 0.0.4
Author: John Cardell
Author URI: http://www.johncardell.com

Copyright 2014 John Cardell

*/
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
	exit('Please don\'t access this file directly.');
}
############  SETUP  ####################
add_action("plugins_loaded","net_shared_posts_init");
add_shortcode('netsposts','netsposts_shortcode');
add_action('admin_menu', 'add_netsposts_toolpage');

// Setup functions

function ShortenText($text, $limit)

{

    $chars_limit = $limit;

    $chars_text = strlen($text);

    $text = $text." ";

    $text = substr($text,0,$chars_limit);

    $text = substr($text,0,strrpos($text,' '));

    if ($chars_text > $chars_limit)

    {

        $text = $text."...";

    }

    return $text;

}
// Add settings link on plugin page
function netsposts_plugin_settings_link($links) {
    $settings_link = '<a href="tools.php?page=netsposts_toolpage">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'netsposts_plugin_settings_link' );

function add_netsposts_toolpage()
{
	add_management_page( 'Network Shared Posts Extended Tool', 'Network Shared Posts Extended Tool', 'Administrator', 'netsposts_toolpage', 'netsposts_tool_page' );
}

function net_shared_posts_init()
{
	register_uninstall_hook(__FILE__, 'net_shared_posts_uninstall');
	wp_register_style( 'netspostscss', plugins_url('/net_posts_extended.css', __FILE__) );
	wp_enqueue_style( 'netspostscss' );
	load_plugin_textdomain('netsposts', false, basename( dirname( __FILE__ ) ) . '/language');
}

function net_shared_posts_uninstall()
{
	remove_shortcode('netsposts');
}

function netsposts_shortcode($atts)
{
	extract(shortcode_atts(array(
	'limit' => '',
	'days' => 0,
	'page_title_style' => '',
	'title' => '',
	'titles_only' => false,
	'wrap_start' => null,
	'wrap_end' => null,
	'thumbnail' => false,
	'post_type' => 'post',
	'include_blog' => null,
	'exclude_blog' => null,
    'exclude_post' => null,
    'include_post' => null,
    'title_length' => 999,
	'taxonomy' => '',
	'paginate' => false,
	'pages' => null,
	'list' => 10,
	'excerpt_length' => 400,
	'auto_excerpt' => false,
	'show_author' => false,
	'full_text' =>  false,
	'size' => 'thumbnail',
	'image_class' => 'post-thumbnail',
	'date_format' => 'n/j/Y',
	'end_size'     => '',
	'mid_size'  => '',
	'prev_next' => false,
	'prev' => '&laquo; Previous',
	'next' =>  'Next &raquo;',
	'column' => '1',
    'column_width' => '200',
    'title_color' => '',
    'text_color' => '',
    'meta_info' => 'true',
    'wrap_title_start' => '',
    'wrap_title_end' => '',
    'wrap_image_start' => '',
    'wrap_image_end' => '',
    'wrap_text_start' => '',
    'wrap_text_end' => '',
    'meta_width' => '100%',
	'menu_name' => '',
	'menu_class' => '',
	'container_class' => '',
    'post_height' => null,
    'manual_excerpt_length' => null
	), $atts));

########  OUTPUT STAFF  #################### 
$titles_only = strtolower($titles_only) == 'true'? true: false;
$thumbnail = strtolower($thumbnail) == 'true'? true: false;
$paginate = strtolower($paginate) == 'true'? true: false;
$auto_excerpt = strtolower($auto_excerpt) == 'true'? true: false;
$show_author = strtolower($show_author) == 'true'? true: false;
$full_text = strtolower($full_text) == 'true'? true: false;
$prev_next = strtolower($prev_next) == 'true'? true: false;
	global $wpdb;
	global $table_prefix;
        if($limit) $limit = " LIMIT 0,$limit ";
	## Params for taxonomy
	if($cat)
	{
		if ($tag)
		{
			implode(',',$cat, $tag);
		}
		} else $cat = $tag;
	## Include blogs
	if($include_blog) {
	$include_arr = explode(",",$include_blog);
	$include = " AND (";
	foreach($include_arr as $included_blog)
	{
		$include .= " blog_id = $included_blog  OR";
	}
	$include = substr($include,0,strlen($include)-2);
	$include .= ")";
	} else {  if($exclude_blog)   {$exclude_arr = explode(",",$exclude_blog); foreach($exclude_arr as $exclude_blog)	{$exclude .= "AND blog_id != $exclude_blog  "; }}}
	$BlogsTable = $wpdb->base_prefix.'blogs';
	$blogs = $wpdb->get_col($wpdb->prepare(

    "SELECT blog_id FROM $BlogsTable WHERE public = %d AND archived = %d AND mature = %d AND spam = %d AND deleted = %d $include $exclude", 1, 0, 0, 0, 0

    ));

	## Getting posts
	$postdata = array();
	if ($blogs)
	{
		foreach ($blogs as $blog_id)
		{
			if( $blog_id == 1 )
			{
				$OptionsTable = $wpdb->base_prefix."options";
				$PostsTable = $wpdb->base_prefix."posts";
				$TermRelationshipTable = $wpdb->base_prefix."term_relationships";
				$TermTaxonomyTable = $wpdb->base_prefix."term_taxonomy";
				$TermsTable = $wpdb->base_prefix."terms";
			}
			else {
				$OptionsTableTable = $wpdb->base_prefix.$blog_id."_options";
				$PostsTable = $wpdb->base_prefix.$blog_id."_posts";
				$TermRelationshipTable = $wpdb->base_prefix.$blog_id."_term_relationships";
				$TermTaxonomyTable = $wpdb->base_prefix.$blog_id."_term_taxonomy";
				$TermsTable = $wpdb->base_prefix.$blog_id."_terms";
			}
			         if ($days > 0) 	$old = "AND $PostsTable.post_date >= DATE_SUB(CURRENT_DATE(), INTERVAL $days DAY)"; else $old = "";
	
			## Taxonomy
			if($taxonomy )
			{ 
				$categories = explode(',',$taxonomy);
				$cat_arr = array();
				foreach($categories as $category)
				{
					$cat_id = $wpdb->get_var($wpdb->prepare("SELECT term_id FROM $TermsTable WHERE slug = '$category' "));
					if($cat_id) $cat_arr[] = $cat_id;
				}
				$taxonomy_arr = array();
				foreach($cat_arr as $cat_id)
				{
					$tax_id = $wpdb->get_var($wpdb->prepare("SELECT term_taxonomy_id FROM $TermTaxonomyTable WHERE  term_id = $cat_id"));
					if($tax_id) $taxonomy_arr[] = $tax_id;
				}
			
			foreach($taxonomy_arr as $tax_id)
			{
				$post_ids = $wpdb->get_results($wpdb->prepare("SELECT object_id FROM $TermRelationshipTable WHERE term_taxonomy_id = $tax_id"), ARRAY_A);
				if( !empty($post_ids) )
				{
					foreach($post_ids as $key=>$object_id)
					{
						$ids .=  " $PostsTable.ID = ".$object_id['object_id']. ' OR';
					}
				}
			}
			}
 
			if ($ids) {  $ids = ' AND  ('. substr($ids,0,strlen($ids)-2).')'; }  else { if($taxonomy) $ids = ' AND  ID=null';}
			
			$the_post = $wpdb->get_results( $wpdb->prepare(
                "SELECT $PostsTable.ID, $PostsTable.post_title, $PostsTable.post_excerpt, $PostsTable.post_content, $PostsTable.post_author, $PostsTable.post_date, $PostsTable.guid, $BlogsTable.blog_id
                FROM $PostsTable, $BlogsTable WHERE $BlogsTable.blog_id  =  $blog_id  AND $PostsTable.post_status = %s $ids  AND $PostsTable.post_type = '$post_type'  $old  $limit"
                , 'publish'
            ), ARRAY_A);
			$postdata = array_merge_recursive($postdata, $the_post);
			$ids='';
		}
		}  
	usort($postdata, "custom_sort");
	if($paginate)
	{
		if($column > 1)
		{
			$column_list =ceil($list/$column);    $list = $column_list*$column;  if(!$list)
			{
				$list=$column; $column_list = 1;
			}
		}
		$page = get_query_var('paged');
		if(!$page)  $page = get_query_var('page');
		if(!$page)  $page = 1;

        function super_unique($array,$key)

        {

            $temp_array = array();

            foreach ($array as &$v) {

                if (!isset($temp_array[$v[$key]]))

                    $temp_array[$v[$key]] =& $v;

            }

            $array = array_values($temp_array);

            return $array;



        }

        $postdata = super_unique($postdata,"ID");

        function removeElementWithValue($array, $key, $value){
            foreach($array as $subKey => $subArray){
                if($subArray[$key] == $value){
                    unset($array[$subKey]);
                }
            }
            return $array;
        }

        $exclude_post2 = explode(",",$exclude_post);

        foreach($exclude_post2 as $row){

            $postdata = removeElementWithValue($postdata, "ID", $row);

        }

        //if(!in_array($the_post['ID'], $exclude_post2)){

		$total_records = count($postdata);
		$total_pages = ceil($total_records/$list);
		$postdata = array_slice($postdata, ($page-1)*$list, $list);
	} 
	if($column > 1)
	{        $count = count($postdata);
	         if(!$paginate) $column_list = ceil($count/$column);
		for($i = 0; $i<$column; ++$i)
		{
			if($count < ($column_list*$column))  $column_list = ceil($count/$column);
			$colomn_data[$i] = array_slice($postdata, ($i )*$column_list, $column_list);
		}
	} else{
        $colomn_data[0] = $postdata;
    }
	## OUTPUT
	if($page_title_style) {
	?>
	
	<style type="text/css">
	h2.pagetitle
	{
        <?php echo  $page_title_style; ?>
        <?php echo get_option('net-style'); ?>
	}
	</style>
	<?
	}
	$html = '<div id="netsposts-menu">';
	if($menu_name)
	{
		$menu=array('menu'=>$menu_name, 'menu_class'=>$menu_class, 'container_class' => $container_class);
		wp_nav_menu($menu);
	}
	$html .= '</div>';
	if($postdata)
	{
        $html .= "<style>";
        $html .= get_option('net-style');
        $html .= "</style>";

        $html .= '<div id="block-wrapper">';

        if(isset($post_height)){
            $height_content = "height: ".$post_height."px;";
        }else{
            $height_content = "";
        }

		if($title) $html .= '<span class="netsposts-title">'.$title.'</span><br />';

		foreach($colomn_data as  $data)
		{

			if($column > 1) $html .= '<div class ="netsposts-column" style="width: '.$column_width.'px;">';

			foreach($data as $key => $the_post)
			{
                $include_post2 = explode(",",$include_post);

                 if(isset($include_post)){

                     if(in_array($the_post['ID'], $include_post2)){
                         $blog_details = get_blog_details( $the_post['blog_id']);
                         $blog_name = $blog_details->blogname;
                         $blog_url = $blog_details->siteurl;
                         if($titles_only) $title_class = 'netsposts-post-titles-only'; else $title_class = 'netsposts-posttitle';
                         $html .= html_entity_decode($wrap_start).'<div class="netsposts-content" style="'.$height_content.'">';
                         $html .= htmlspecialchars_decode($wrap_title_start);
                         $html .= '<span class="'.$title_class.'" style="color: '.$title_color.';">'.ShortenText($the_post['post_title'],$title_length).'</span>';
                         $html .= htmlspecialchars_decode($wrap_title_end);

                         if(!$titles_only)
                         {
                             $date = new DateTime(trim($the_post['post_date']));
                             $date_post = $date->format($date_format);
                             if($meta_info != "false"){
                                $html .= '<span class="netsposts-source"> '.__('Published','netsposts').' '.$date_post.' '.__('in','netsposts').'  <a href="'.$blog_url.'">'.$blog_name.'</a>';
                             }
                             ##  Full metadata
                             if( $show_author)
                             {
                                 if($column > 1) $html .= '<br />';
                                 $html .= ' ' . __('Author','netsposts'). ' ' . '<a href="'.$blog_url .'?author='.  $the_post['post_author'] .'">'. get_the_author_meta( 'display_name' , $the_post['post_author'] ) . ' </a>';
                             }
                             $html .= '</span>';
                             if($thumbnail)
                             {
                                 $html .= htmlspecialchars_decode($wrap_image_start);
                                 $html .= '<a href="'.$the_post['guid'].'">'.get_thumbnail_by_blog($the_post['blog_id'],$the_post['ID'],$size, $image_class, $column).'</a>';
                                 $html .= htmlspecialchars_decode($wrap_image_end);
                                 $html .= '<p class="netsposts-excerpt" style="color: '.$text_color.';">';
                                 $html .= htmlspecialchars_decode($wrap_text_start);
                                 $the_post['post_content'] = preg_replace("/<img[^>]+\>/i", "", $the_post['post_content']);
                             }

                             if($auto_excerpt)  {$exerpt  = get_excerpt($excerpt_length, $the_post['post_content'], $the_post['guid']);}
                             else $exerpt  = $the_post['post_excerpt'];
                             if($full_text){
                                 $text = $the_post['post_content'];
                             }else{
                                 if($manual_excerpt_length){
                                     $text = get_excerpt($manual_excerpt_length,$exerpt,$the_post['guid']);
                                 }else{
                                     $text = $exerpt;
                                 }
                             }
                             $html .= strip_shortcodes( $text);
                             $html .= htmlspecialchars_decode($wrap_text_end);
                            // $html .= ' <a href="'.$the_post['guid'].'">read more&rarr;</a></p>';
                         }

                         $html .= "<br />";

                         $html .= html_entity_decode($wrap_end);

                     }

                 }else{

                        $blog_details = get_blog_details( $the_post['blog_id']);
                              $blog_name = $blog_details->blogname;
                              $blog_url = $blog_details->siteurl;

                        if($titles_only) $title_class = 'netsposts-post-titles-only'; else $title_class = 'netsposts-posttitle';
                        $html .= html_entity_decode($wrap_start).'<div class="netsposts-content" style="'.$height_content.'">';
                        $html .= htmlspecialchars_decode($wrap_title_start);
                        $html .= '<span class="'.$title_class.'" style="color: '.$title_color.';">'.ShortenText($the_post['post_title'],$title_length).'</span>';
                        $html .= htmlspecialchars_decode($wrap_title_end);

                        if(!$titles_only)
                        {
                            $date = new DateTime(trim($the_post['post_date']));
                            $date_post = $date->format($date_format);
                            if($meta_info != "false"){

                                if($meta_width == "100%"){
                                    $width = 'width: 100%;';
                                }else{
                                    $width = "width: ".$meta_width."px;";
                                }

                                $html .= '<span class="netsposts-source" style="height: 24px; margin-bottom: 5px; overflow: hidden; '.$width.'"> '.__('Published','netsposts').' '.$date_post.' '.__('in','netsposts').'  <a href="'.$blog_url.'">'.$blog_name.'</a>';
                            }
                            ##  Full metadata
                            if( $show_author)
                            {
                            if($column > 1) $html .= '<br />';
                                $html .= ' ' . __('Author','netsposts'). ' ' . '<a href="'.$blog_url .'?author='.  $the_post['post_author'] .'">'. get_the_author_meta( 'display_name' , $the_post['post_author'] ) . ' </a>';
                            }
                            $html .= '</span>';
                            if($thumbnail)
                            {
                                $html .= htmlspecialchars_decode($wrap_image_start);
                                $html .= '<a href="'.$the_post['guid'].'">'.get_thumbnail_by_blog($the_post['blog_id'],$the_post['ID'],$size, $image_class, $column).'</a>';
                                $html .= htmlspecialchars_decode($wrap_image_end);
                                $html .= htmlspecialchars_decode($wrap_text_start);
                                $html .= '<p class="netsposts-excerpt" style="color: '.$text_color.';">';
                            $the_post['post_content'] = preg_replace("/<img[^>]+\>/i", "", $the_post['post_content']);
                            }

                            if($auto_excerpt)  {$exerpt  = get_excerpt($excerpt_length, $the_post['post_content'], $the_post['guid']);}
                            else $exerpt  = $the_post['post_excerpt'];
                            if($full_text){
                                $text = $the_post['post_content'];
                            }else{
                                if($manual_excerpt_length){
                                    $text = get_excerpt($manual_excerpt_length,$exerpt,$the_post['guid']);
                                }else{
                                    $text = $exerpt;
                                }
                            }
                            $html .= strip_shortcodes( $text);
                            $html .= ' <a href="'.$the_post['guid'].'">read more&rarr;</a></p>';
                            $html .= htmlspecialchars_decode($wrap_text_end);
                        }

                        $html .= "</div>";
                        $html .= "<br />";

                        $html .= html_entity_decode($wrap_end);

                 }

                 $html .= "<div style='clear: both;'></div>";

            }
            if($column > 1) $html .= '</div>';
		}
        $html .= '<div class="clear"></div>';
		if(($paginate) and ($total_pages>1))
		{
            $html .= '<div id="netsposts-paginate">';
			$big = 999999999;
            $html .= paginate_links( array(
	                  'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	                   'format' => '?paged=%#%',
	                     'current' => $page,
	                     'total' => $total_pages,
			    'prev_text'    => __($prev),
                              'next_text'    => __($next),
			    'end_size'     => $end_size,
                               'mid_size'     =>  $mid_size
) );

            $html .= '</div>';

		}
        $html .= '</div>';
	}

    return $html;
}
##########################################################

function get_thumbnail_by_blog($blog_id=NULL,$post_id=NULL,$size='thumbnail',$image_class, $column)
{
	if( !$blog_id  or !$post_id ) return;
	switch_to_blog($blog_id);
	$thumb_id = has_post_thumbnail( $post_id );
	if(!$thumb_id)
	{
		restore_current_blog(); return FALSE;
	}
	$blogdetails = get_blog_details( $blog_id );
	$size=explode(',',$size);
    if($column > 1){
        $image_class = $image_class." more-column";
    }
	$attrs = array('class'=> $image_class);
	$thumbcode = str_replace( $current_blog->domain . $current_blog->path, $blogdetails->domain . $blogdetails->path, get_the_post_thumbnail( $post_id, $size, $attrs ) );
	restore_current_blog();
	return $thumbcode;
}

function get_excerpt($length,$content,$permalink)
{
	if(!$length) return $content;
	else {
		$content = strip_tags($content);
		$content = substr($content, 0,  intval($length));
		$words = explode(' ', $content);
		array_pop($words);
		$content = implode(' ', $words);

/* Original Code return   $content.'... <a href="'.$permalink.'">   '.__('read more&rarr;','trans-nlp').'</a>'; */
/* Edited Code Turned argument 'read more&rarr;' to ''*/
		return   $content.'... <a href="'.$permalink.'">   '.__('','trans-nlp').'</a>';
	}
}

function custom_sort($a,$b)
{
	return $a['post_date']<$b['post_date'];
}

###################  TOOL PAGE  #########################

function netsposts_tool_page()
{
?>
<div class="wrap">
    <div id="icon-users" class="icon32"><br /></div>
    <h2>Network Posts Extended help</h2>
    <hr />
    If you like this plugin please donate:
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
        <input type="hidden" name="cmd" value="_donations">
        <input type="hidden" name="business" value="john@johncardell.com">
        <input type="hidden" name="lc" value="US">
        <input type="hidden" name="item_name" value="Network Shared Posts">
        <input type="hidden" name="no_note" value="0">
        <input type="hidden" name="currency_code" value="USD">
        <input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest">
        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>

    </br></br>

    <?php
     echo "Here is the link: For a complete tutorial please visit: <br> <a target='ejecsingle' href='http://www.johncardell.com/plugins/network-posts-extended/'>http://www.johncardell.com/plugins/network-posts-extended/</a>";
    ?>
    </br></br>
    <form method="post" action="options.php">
        <?php wp_nonce_field('update-options'); ?>
        <?php $styling  = get_option('net-style'); ?>
        Add extra css styling: <?php echo "Here is a good source for custom css styling: <a target='ejejcsingle' href='http://www.w3schools.com/css/css_id_class.asp'>w3schools class tutorial</a>"; ?></br>
        <textarea style="width: 500px; height: 500px;" name="net-style" ><?php echo $styling; ?></textarea>
        </br>
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="page_options" value="net-style" />
        <input type="submit" value="Save Changes" />
    </form>

</div>
<?php

}


?>
