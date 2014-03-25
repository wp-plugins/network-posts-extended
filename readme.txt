=== Network Posts Extended ===
Contributors: johnzenausa, DJManas
Tags: network global posts, network posts, global posts, multisite posts, shared posts, network posts extended
Donate link: http://johncardell.com/plugins/network-posts-extended/
Requires at least: 3.0
Tested up to: 3.8.1
Stable tag: 0.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Share posts, pages, or custom post types across entire network.

== Description ==
The plugin is designed to share posts, pages, and custom post types from across entire network on any given page. You may list them in single or double column mode. Add custom css styling to your text, images and title.

You can specify categories and tags. All posts will be shown in the latest date order no matter from what blog they were taken. You can specify how old (in days) the collected posts may be. Also you can specify how many posts should be displayed from each blog. You can set thumbnails image size and style or disable thumbnails at all. You can adjust CSS styles editing CSS file. Plus you may include or exclude pages or posts by page_id. For example if a post has a page_id=2 and you would like to exclude it you would just add exclude_post=2.

== Installation ==
You may install the plugin using one of the three following methods:

Unzip file and using an ftp program upload it to the wp-content/plugins/ directory then activate in plugins page.

Using the search field in the admin area type in 'network posts extended' (without quotes) then install from there.

Upload zip file through the plugins menu


Note: For multisite installations only. Do not Network Activate. Activate it individually on each site.

== Frequently Asked Questions ==
Q) Should I network activate the plugin?A) No.
Q) May I only include an x amount of posts that I choose?
A) Yes, use include_post= and put in your posts in comma separated format surrounded by double quotes. Example include_post="5,78,896".
Q) My title is too long and looks ugly, anyway I can shorten it?
A) You may shorten it using the argument title_length="10" will rounded it off to the last complete word before it reaches 10 characters.


== Screenshots ==
1. Single Column
2. Double Column 
3. Double Column with Custom Class

== Changelog ==
Added the function to be able to use your own custom classes in tools area.

Plus added the following arguments:
column_width (default (px): 200)
title_color (default: black)
text_color - color of text. Examples text_color="red" or text_color="#ff0000". Both will have the texts color turn red.
meta_info - Default true
wrap_title_start,wrap_title_end - wrap_image_start,wrap_image_end - wrap_text_start,wrap_text_end.
meta_width - Same as title length except in percentage to shorten long meta data.

== Upgrade Notice ==
To added latest features and minor bug fixes. Plus ability to add custom css styling in plugins tool page. May now create this custom class: .example { color: #000; float:left; }

== List of Arguments ==
/* Network Posts Extended Shortcodes and Arguments */
<br /><br />
[netsposts include_blog="1,2,5" days=30 taxonomy="news" titles_only="false" show_author="true" thumbnail="true" size="90,90" image_class="alignleft" auto_excerpt="true" excerpt_length="150" show_author="true" paginate="true" list="5"]
<br /><br />
include_post - list of posts/pages that you want to include (example: include_post=5 - include_post="5,8,153"<br />
exclude_post - list of posts/pages that you want to exclude (example: exclude_post=5 - exclude_post="5,8,153"<br />
title_length - Cuts off the title at X amount of characters so won't make long wrap around which look ugly. The length is in characters including spaces and symbols (Default 999)<br />
include_blog – list of blogs, with the posts which will be displayed (default all blogs)<br />
exclude_blog – list of excluded blogs (default none) (works only if include_blogs argument is not present)<br />
days – how old in days the post can be (default 0' – no limit)<br />
taxonomy – list of categories and/or tags for the posts selection (use slugs only) (default all)<br />
titles_only – if true shows titles only (default false)<br />
show_author – if true shows a posts author (default false)<br />
thumbnail - if true shows thumbnails (default false)<br />
size - size of thumbnail (width, height) (default thumbnail)<br />
image_class – CSS class for image (default post-thumbnail)<br />
auto_excerpt - if true an excerpt will be taken from post content, if false a post excerpt will be used (default false)<br />
excerpt_length – the length of excerpt (auto_excerpt should be true)(default 400')<br />
paginate – if true the result will be paginated (default false)<br />
list – how many posts per page (default 10)<br />
post_type – type of posts (default post)<br />
full_text - full text instead of excerpt (default false)<br />
date_format – format of the post date (default n/j/Y)<br />
wrap_start, wrap_end - you can wrap the posts (for example wrap_start=&#34;&lt;div style=&#39;font-weight:bold;vertical-align:middle;&#39; class=&#39;myclass&#39;&gt;&#34; wrap_end=&lt;/div&gt;)
wrap_title_start,wrap_title_end - wrap_image_start,wrap_image_end - wrap_text_start,wrap_text_end. Same as wrap_start,wrap_end above.<br />
end_size – how many numbers on either the start and the end list edges (used for pagination)<br />
mid_size – how many numbers to either side of current page, but not including current page (used for pagination)<br />
prev_next – Whether to include the previous and next links in the list or not (used for pagination. Default: true)<br />
prev – the previous page link text. Works only if prev_next argument is set to true. (Default:« Previous)<br />
next- The next page text. Works only if prev_next argument is set to true. (Default:Next »)<br />
page_title_style – style for the page title (default: none)<br />
title – custom title (default: none)<br />
column – number of columns (default: 1)<br />
menu_name – name of the menu (should be created in Appearance > Menu)(default: none)<br />
menu_class – CSS class for the menu<br />
container_class – the CSS class that is applied to the menu container<br />
<br /><br />
For a complete tutorial please visit: <a href='http://www.johncardell.com/plugins/network-posts-extended/' target='ejejcsingle'>http://www.johncardell.com/plugins/network-posts-extended/</a>