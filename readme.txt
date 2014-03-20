=== Network Posts Extended === 

Contributors: Code Ext, johnzenausa
Donate link: http://johncardell.com/plugins/network-posts-extended/
Tags: network global posts, network posts, global posts, multisite posts, shared posts.
Requires at least: 3.0
Tested up to: 3.8.1
Stable tag: 0.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Installation ==

1. Upload 'network-posts-extended' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Description ==

With Network Posts Extended plugin you can share posts over a WP Multi Site network. You can display the posts from all blogs in your network on any blog. You can select blogs to display posts or pages from.<br />
This plugin is very useful for a multi level network. For example city.state.country.com :<br />
'state' level site can collect posts from 'city' level sites and/or its own posts, 
'country' level site can collect posts from 'state' level sites and/or 'city' level sites and/or its own posts.<br />
You can specify categories and tags. All posts will be shown in the latest date order no matter from what blog they were taken. You can specify how old (in days) the collected posts may be. Also you can specify how many posts should be displayed from each blog. You can set thumbnails image size and style or disable thumbnails at all. You can adjust CSS styles editing CSS file. Plus you may include or exclude pages or posts by page_id. For example if a post has a page_id=2 and you would like to exclude it you would just add exclude_post=2.

== Screenshots ==
1. Network Shared Posts in demo action

== Frequently Asked Questions ==

= How to use Network Shared Posts ? = 
Craete post or page and put a short code [netsposts ] with desired arguments into your page content .<br />
Example: [netsposts include_blog='1,2,5' days='30' taxonomy='news' titles_only=false show_author=true thumbnail=true size='90,90' image_class=' alignleft' auto_excerpt=true excerpt_length=500 show_author=true paginate=true list=5]

= What short code arguments can I use ? = 
You can get the full list of short code arguments on plugin's web site http://www.johncardell.com/plugins/network-posts-extended/ 
<br />

== Changelog ==
=0.0.1=
Added 'exclude_blog' argument<br />
Added 'include_post' argument<br />
Added 'exclude_post' argument<br />
Added 'title_length' argument<br />
The pagination was enchanced with native WordPress pagination.<br />
Added argument for pagination: 'end_size', 'mid_size', 'prev', 'next', 'prev_next'<br />
Tool Page was added to admin menu<br />
This plugin was created by modifying the plugin 'Network Shared Posts' by Author: Code Ext which is located here: http://wordpress.org/plugins/network-shared-posts/

== Upgrade Notice ==
= 0.0.1 =
In this version you can use 'exclude_blog', 'include_post',exclude_post, and 'title_length' arguments. The pagination was enhanced with native WordPress pagination. The pages will count and be indexed as if using the <!--more--> tag. More features and styles will be available in future updates. This plugin as of now does not work with the column= argument but will be fixed in a future release. As of now it defaults to one column.

/* Network Posts Extended Shortcodes and Arguments */
[netsposts include_blog='1,2,5' days=30 taxonomy=news titles_only=false show_author=true thumbnail=true size='90,90' image_class=alignleft auto_excerpt=true excerpt_length=500 show_author=true paginate=true list=5]

include_post - list of posts/pages that you want to include (example: include_post=5 - include_post="5,8,153"
exclude_post - list of posts/pages that you want to exclude (example: exclude_post=5 - exclude_post="5,8,153"
title_length - Cuts off the title at X amount of characters so won't make long wrap around which look ugly. The length is in characters including spaces and symbols (Default 999).
include_blog – list of blogs, with the posts which will be displayed (default all blogs).
exclude_blog – list of excluded blogs (default none) (works only if include_blogs argument is not present).
days – how old in days the post can be (default 0' – no limit).
taxonomy – list of categories and/or tags for the posts selection (use slugs only) (default all).
titles_only – if true shows titles only (default false).
show_author – if true shows a posts author (default false).
thumbnail - if true shows thumbnails (default false).
size - size of thumbnail (width, height) (default thumbnail).
image_class – CSS class for image (default post-thumbnail)..
auto_excerpt - if true an excerpt will be taken from post content, if false a post excerpt will be used (default false)..
excerpt_length – the length of excerpt (auto_excerpt should be true)(default 400'). .
paginate – if true the result will be paginated (default false).
list – how many posts per page (default 10')..
post_type – type of posts (default post).
full_text - full text instead of excerpt (default false).
date_format – format of the post date (default n/j/Y).
wrap_start, wrap_end - you can wrap the posts (for example wrap_start=<div class=myclass> wrap_end=</div>),
end_size – how many numbers on either the start and the end list edges (used for pagination).
mid_size – how many numbers to either side of current page, but not including current page (used for pagination).
prev_next – Whether to include the previous and next links in the list or not (used for pagination. Default: true).
prev – the previous page link text. Works only if prev_next argument is set to true. (Default:« Previous)
next- The next page text. Works only if prev_next argument is set to true. (Default:Next »)
page_title_style – style for the page title (default: none)
title – custom title (default: none),
column – number of columns (default: 1),
menu_name – name of the menu (should be created in Appearance > Menu)(default: none),
menu_class – CSS class for the menu,
container_class – the CSS class that is applied to the menu container .

For a complete tutorial please visit: http://www.johncardell.com/plugins/network-posts-extended/