<?php
/*
Plugin Name: Network Posts Extended
Plugin URI: http://www.johncardell.com/plugins/network-posts-extended/
Description: Network Posts Extended plugin enables you to share posts over WP Multi Site network.  You can display on any blog in your network the posts selected by taxanomy from any blogs including main. 
Version: 0.0.1
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

function add_netsposts_toolpage()
{
	add_management_page( 'Network Shared Posts Tool', 'Network Shared Posts Tool', 'Administrator', 'netsposts_toolpage', 'netsposts_tool_page' );
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
	'menu_name' => '',
	'menu_class' => '',
	'container_class' => ''
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
		echo '<div id="block-wrapper">';
		if($title) echo '<span class="netsposts-title">'.$title.'</span><br />';
		foreach($colomn_data as  $data)
		{
			if($column > 1) echo '<div class ="netsposts-column" style="width:'.$col_width.'">';

			foreach($data as $key => $the_post)
			{
                $include_post2 = explode(",",$include_post);

                 if(isset($include_post)){

                     if(in_array($the_post['ID'], $include_post2)){
                         $blog_details = get_blog_details( $the_post['blog_id']);
                         $blog_name = $blog_details->blogname;
                         $blog_url = $blog_details->siteurl;
                         if($titles_only) $title_class = 'netsposts-post-titles-only'; else $title_class = 'netsposts-posttitle';
                         $html .= html_entity_decode($wrap_start).'<div class="netsposts-content"><span class="'.$title_class.'"><a href="'.$title_length.''.$the_post['guid'].'">'.ShortenText($the_post['post_title'],$title_length).'</a></span>';

                         if(!$titles_only)
                         {
                             $date = new DateTime(trim($the_post['post_date']));
                             $date_post = $date->format($date_format);
                             $html .= '<span class="netsposts-source"> '.__('Published','netsposts').' '.$date_post.' '.__('in','netsposts').'  <a href="'.$blog_url.'">'.$blog_name.'</a>';
                             ##  Full metadata
                             if( $show_author)
                             {
                                 if($column > 1) echo '<br />';
                                 $html .= ' ' . __('Author','netsposts'). ' ' . '<a href="'.$blog_url .'?author='.  $the_post['post_author'] .'">'. get_the_author_meta( 'display_name' , $the_post['post_author'] ) . ' </a>';
                             }
                             $html .= '</span>';
                             if($thumbnail)
                             {

                                 $html .= '<a href="'.$the_post['guid'].'">'.get_thumbnail_by_blog($the_post['blog_id'],$the_post['ID'],$size, $image_class).'</a><p class="netsposts-excerpt">';
                                 $the_post['post_content'] = preg_replace("/<img[^>]+\>/i", "", $the_post['post_content']);
                             }

                             if($auto_excerpt)  {$exerpt  = get_excerpt($excerpt_length, $the_post['post_content'], $the_post['guid']);}
                             else $exerpt  = $the_post['post_excerpt'];
                             if($full_text) $text = $the_post['post_content']; else $text = $exerpt;
                             $html .= strip_shortcodes( $text);
                             $html .= ' <a href="'.$the_post['guid'].'">read more→</a></p>';
                         }
                         $html .= '</div>';
                         $html .= "<br />";

                         $html .= html_entity_decode($wrap_end);

                         if($column > 1) echo '</div>';
                     }

                 }else{
                   // if(!in_array($the_post['ID'], $exclude_post2)){
                        $blog_details = get_blog_details( $the_post['blog_id']);
                              $blog_name = $blog_details->blogname;
                              $blog_url = $blog_details->siteurl;
                        if($titles_only) $title_class = 'netsposts-post-titles-only'; else $title_class = 'netsposts-posttitle';
                        $html .= html_entity_decode($wrap_start).'<div class="netsposts-content"><span class="'.$title_class.'"><a href="'.$the_post['guid'].'">'.ShortenText($the_post['post_title'],$title_length).'</a></span>';

                        if(!$titles_only)
                        {
                            $date = new DateTime(trim($the_post['post_date']));
                            $date_post = $date->format($date_format);
                            $html .= '<span class="netsposts-source"> '.__('Published','netsposts').' '.$date_post.' '.__('in','netsposts').'  <a href="'.$blog_url.'">'.$blog_name.'</a>';
                            ##  Full metadata
                            if( $show_author)
                            {
                            if($column > 1) echo '<br />';
                                $html .= ' ' . __('Author','netsposts'). ' ' . '<a href="'.$blog_url .'?author='.  $the_post['post_author'] .'">'. get_the_author_meta( 'display_name' , $the_post['post_author'] ) . ' </a>';
                            }
                            $html .= '</span>';
                            if($thumbnail)
                            {

                                $html .= '<a href="'.$the_post['guid'].'">'.get_thumbnail_by_blog($the_post['blog_id'],$the_post['ID'],$size, $image_class).'</a><p class="netsposts-excerpt">';
                            $the_post['post_content'] = preg_replace("/<img[^>]+\>/i", "", $the_post['post_content']);
                            }

                            if($auto_excerpt)  {$exerpt  = get_excerpt($excerpt_length, $the_post['post_content'], $the_post['guid']);}
                            else $exerpt  = $the_post['post_excerpt'];
                            if($full_text) $text = $the_post['post_content']; else $text = $exerpt;
                            $html .= strip_shortcodes( $text);
                            $html .= ' <a href="'.$the_post['guid'].'">read more→</a></p>';
                        }
                        $html .= '</div>';
                        $html .= "<br />";

                        $html .= html_entity_decode($wrap_end);

                        if($column > 1) echo '</div>';

                 //   }
                 }
            }
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

function get_thumbnail_by_blog($blog_id=NULL,$post_id=NULL,$size='thumbnail',$image_class)
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
		return   $content.'... <a href="'.$permalink.'">   '.__('read more&rarr;','trans-nlp').'</a>';
	}
}

function custom_sort($a,$b)
{
	return $a['post_date']<$b['post_date'];
}

###################  TOOL PAGE  #########################

function netsposts_tool_page()
{
	global $wpdb;
	$short_code = '[netsposts ';
	if (isset($_POST['blogs_selectbox']))
	{ 
	$value = (string)$_POST["radiogroup"];
	$short_code .= $value.'=\''. implode(',', $_POST['blogs_selectbox'])."'";
	} 
	
	if (isset($_POST['terms_selectbox']))	{ $short_code .= ' taxonomy=\''. implode(',', $_POST['terms_selectbox'])."'"; 	} 
	if (isset($_POST['days'])  and ($_POST['days']))	{ $short_code .= " days=". $_POST['days'];	} 
	if (isset($_POST['titles_only'])) { $short_code .= " titles_only=". $_POST['titles_only']; } 
	if (isset($_POST['show_author'])) { $short_code .= " show_author=". $_POST['show_author'];} 
	if (isset($_POST['post_type'])  and ($_POST['post_type']))	{ $short_code .= " post_type='". $_POST['post_type']."'";	} 
	if (isset($_POST['wrap_start'])  and ($_POST['wrap_start']))	{ $short_code .= " wrap_start='". $_POST['wrap_start']."'";	} 
	if (isset($_POST['wrap_end'])  and ($_POST['wrap_end']))	{ $short_code .= " wrap_end='". $_POST['wrap_end']."'";	} 	
	if (isset($_POST['full_text'])) { $short_code .= " full_text=". $_POST['full_text'];	} 
	if (isset($_POST['page_title_style'])  and ($_POST['page_title_style']))	{ $short_code .= " page_title_style='". $_POST['page_title_style']."'";	} 	
	if (isset($_POST['title'])  and ($_POST['title']))	{ $short_code .= " title='". $_POST['title']."'";	} 	
	if (isset($_POST['column'])  and ($_POST['column']))	{ $short_code .= " column='". $_POST['column']."'";	} 
	if (isset($_POST['menu_name'])  and ($_POST['menu_name']))	{ $short_code .= " menu_name='". $_POST['menu_name']."'";	} 		
	if (isset($_POST['menu_class'])  and ($_POST['menu_class']))	{ $short_code .= " menu_class='". $_POST['menu_class']."'";	} 		
	if (isset($_POST['container_class'])  and ($_POST['container_class']))	{ $short_code .= " container_class='". $_POST['container_class']."'";	} 		
	if (isset($_POST['date_format'])  and ($_POST['date_format']))	{ $short_code .= " date_format='". $_POST['date_format']."'";	} 	
	
	if (isset($_POST['auto_excerpt'])) { $short_code .= " auto_excerpt=". $_POST['auto_excerpt'];	} 
if (isset($_POST['excerpt_length'])  and ($_POST['excerpt_length']))	{ $short_code .= " excerpt_length=". $_POST['excerpt_length'];	} 
	
	if (isset($_POST['thumbnail'])) { $short_code .= " thumbnail=". $_POST['thumbnail'];	} 
	if (isset($_POST['size']) and ($_POST['size'][0]) and ($_POST['size'][1]))	{ $short_code .= " size=". implode(',', $_POST['size']); 	} 
	if (isset($_POST['image_class'])  and ($_POST['image_class']))	{ $short_code .= " image_class='". $_POST['image_class']."'";	} 
	
	
	if (isset($_POST['paginate'])) { $short_code .= " paginate=". $_POST['paginate'];	} 
	if (isset($_POST['list'])  and ($_POST['list']))	{ $short_code .= " list=". $_POST['list'];	} 
	if (isset($_POST['end_size'])  and ($_POST['end_size']))	{ $short_code .= " end_size='". $_POST['end_size']."'";	} 
	if (isset($_POST['mid_size'])  and ($_POST['mid_size']))	{ $short_code .= " mid_size='". $_POST['mid_size']."'";	} 
	if (isset($_POST['prev'])  and ($_POST['prev']))	{ $short_code .= " prev='". $_POST['prev']."'";	} 
	if (isset($_POST['next'])  and ($_POST['next']))	{ $short_code .= " next='". $_POST['next']."'";	} 
	if (isset($_POST['prev_next'])) { $short_code .= " prev_next=". $_POST['prev_next'];	} 
	
	
		
$short_code .= ']';
?>

<div class="wrap">
<div id="icon-users" class="icon32"><br /></div>
<h2>Network Shared Posts Ext Short Code Tool</h2>
<hr />
<div style="display:inline-block; ">
<div style ="display:inline-block; padding:15px; float:left;">
<font color=red><?php //if($status) echo "" . $status . ""; ?></font>
<form method="post" action="#">
<input type="radio" name="radiogroup" value="include_blog"  <?php if($_POST["radiogroup"] == "include_blog") echo ' checked '; ?>/>&nbsp;include
<input type="radio" name="radiogroup" value="exclude_blog"<?php if($_POST["radiogroup"] == "exclude_blog") echo ' checked '; ?> />&nbsp;exclude
<br />
<label>Sites</label><br />
<select MULTIPLE name="blogs_selectbox[]" >
<?php 
$BlogsTable = $wpdb->base_prefix.'blogs';
$blogs = $wpdb->get_results($wpdb->prepare(" select  blog_id  from $BlogsTable  "), ARRAY_A); 
$termsdata = array();
foreach($blogs as $val)
{
       if($val['blog_id'] == 1) { $OptionsTable = $wpdb->base_prefix.'options';   $TermsTable = $wpdb->base_prefix.'terms';  }
             else {$OptionsTable = $wpdb->base_prefix.$val['blog_id'] .'_options'; 	$TermsTable = $wpdb->base_prefix.$val['blog_id'] .'_terms';  
	}
          $blog_name  = $wpdb->get_var($wpdb->prepare(" select  option_value  from $OptionsTable  where option_name = 'blogname' ")); 
	echo '<option  value="'.$val['blog_id'].'"';
	if (isset($_POST['blogs_selectbox'])) {  if(in_array ($val['blog_id'], $_POST['blogs_selectbox'])) echo ' selected '; }
	echo '  >'.$blog_name.'</option>';
	 $terms = $wpdb->get_results($wpdb->prepare(" select  name, slug from $TermsTable  "), ARRAY_A); 
	$termsdata = array_merge_recursive($termsdata, $terms);
}
?>
</select><br />
<label>Taconomy (categories and tags)</label><br />
<?  //var_dump($termsdata)   ?>
<select MULTIPLE name="terms_selectbox[]" >
<?// echo '<br />';
foreach($termsdata as $val)
{
	echo '<option  value="'.$val['slug'].'"';
	if (isset($_POST['terms_selectbox']))	{ if(in_array ($val['slug'], $_POST['terms_selectbox'])) echo ' selected ';	}
	echo ' >'.$val['name'].'</option>';
	
}

 ?>
</select><br />
<label>CSS style for page title</label><input  type="text" name="page_title_style" value="<?php echo $_POST['page_title_style'] ?>"/><br />
<label>Title</label><input  type="text" name="title"  value="<?php echo $_POST['title'] ?>"/><br />
<label>Hpw many columns</label><input  type="text" name="column"  value="<?php echo $_POST['column'] ?>"/><br />
<label>Name of a menu</label><input  type="text" name="menu_name"  value="<?php echo $_POST['menu_name'] ?>"/><br />
<label>Menu CSS class</label><input  type="text" name="menu_class"  value="<?php echo $_POST['menu_class'] ?>"/><br />
<label>Menu container CSS class</label><input  type="text" name="container_class"  value="<?php echo $_POST['container_class'] ?>"/><br />

<label>Days (how old posts may be)</label><input  type="text" name="days"  value="<?php echo $_POST['days'] ?>"/><br />
<input type="checkbox" name="titles_only" value="true" <?php if($_POST['titles_only'] == true) echo ' checked '; ?>/><label>&nbsp;Titles only</label><br />
<input type="checkbox" name="show_author" value="true" <?php if($_POST['show_author'] == true) echo ' checked '; ?>/><label>&nbsp;Show author</label><br />
<label>Type of posts:</label><input  type="text" name="post_type"  value="<?php echo $_POST['post_type'] ?>"/><br />
<input type="checkbox" name="full_text" value="true"  <?php if($_POST['full_text'] == true) echo ' checked '; ?>/><label>&nbsp;Full text instead of excerpt</label><br />
<label>Format of the post date:</label><input  type="text" name="date_format"  value="<?php echo $_POST['date_format'] ?>"/><br />
<label>Wrap start:</label><input  type="text" name="wrap_start"   value="<?php echo $_POST['wrap_start'] ?>"/>&nbsp;<label>Wrap end:</label><input  type="text" name="wrap_end"   value="<?php echo $_POST['wrap_end'] ?>"/><br />


<input type="checkbox" name="auto_excerpt" value="false"  <?php if($_POST['auto_excerpt'] == 'false') echo ' checked '; ?>/><label>&nbsp;No auto excerpts</label><br />
<label>Length of excerpt</label><input  type="text" name="excerpt_length"  value="<?php echo $_POST['excerpt_length'] ?>"/><br />

<input type="checkbox" name="thumbnail" value="true"  <?php if($_POST['thumbnail'] == true) echo ' checked '; ?>/><label>&nbsp;Show thumbnails</label><br />
<label>Size of thumbnails (pixeks) </label> <br /><label>Width:</label><input  type="text" name="size[0]"   value="<?php echo $_POST['size'][0] ?>"/><label>&nbsp;Height:</label><input  type="text" name="size[1]"   value="<?php echo $_POST['size'][1] ?>"/><br />
<label>Name of CSS class thumbnails</label><input  type="text" name="image_class"   value="<?php echo $_POST['image_class'] ?>"/><br />

<input type="checkbox" name="paginate" value="true"  <?php if($_POST['paginate'] == true) echo ' checked '; ?>/><label>&nbsp;Paginate</label><br />
<label>How many posts per page:</label><input  type="text" name="list"    value="<?php echo $_POST['list'] ?>"/><br />
<label>How many numbers on either the start and the end list edges:</label><br />
<input  type="text" name="end_size"   value="<?php echo $_POST['end_size'] ?>"/><br />
<label>How many numbers to either side of current page:</label><br />
<input  type="text" name="mid_size"   value="<?php echo $_POST['mid_size'] ?>"/><br />
<input type="checkbox" name="prev_next" value="false"  <?php if($_POST['prev_next'] == 'false') echo ' checked '; ?>/><label>&nbsp;Do not include the previous and next links</label><br />
<label>The previous page link text:</label><input  type="text" name="prev"   value="<?php echo $_POST['prev'] ?>"/><br />
<label>The next page text:</label><input  type="text" name="next"  value="<?php echo $_POST['next'] ?>"/><br /><br />

<input type="submit" value="Get Short Code" style="color:#fff; background-color:#888; padding:10px; cursor:pointer;" />
</form>
</div>
<div style ="background-color:#eee; display:inline-block; padding:15px;float:right;">
<p><strong>Short Code</strong></p>
<textarea name="short_code" rows="10" cols="40" id="main"><?php  echo $short_code  ?></textarea><br />
	</div>
	</div></div>
<?php

}


?>
