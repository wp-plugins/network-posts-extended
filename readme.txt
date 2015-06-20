=== Network Posts Extended ===
Contributors: johnzenausa
Tags: network global posts, network posts, global posts, multisite posts, shared posts, network posts extended
Donate link: http://johncardell.com/plugins/network-posts-extended/
Requires at least: 4.0
Tested up to: 4.4.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

List posts and/or pages from across entire network.

== Description ==
The plugin is designed to share posts, pages, and custom post types from across entire network on any given page on main blog. You may list them in single or double column mode. Add custom css styling to your text, images, whole container and title.

You can specify categories and tags. All posts will be shown in the latest date order no matter from what blog they were taken. You can specify how old (in days) the collected posts may be. Also you can specify how many posts should be displayed from each blog. You can set thumbnails image size and style or disable them. You can adjust CSS styles with this plugins built in CSS editor located in the sidebar under Tools>Network Shared Posts Extended Tool.

You may also include or exclude pages or posts by page_id. For example if a post has a page_id=2 and you would like to exclude it you would just add exclude_post=2.

== Installation ==
You may install the plugin using one of the three following methods:
1. Unzip file and using an ftp program upload it to the wp-content/plugins/ directory then activate in plugins page.
2. Using the search field in the admin plugins area type in \'network posts extended\' (without quotes) then install from there.
3. Upload zip file through the standard plugins menu.

Note: For multisite installations only. Do not Network Activate. Activate on main site only. Due to WordPress security measures for subdomains it will not work properly. We recommend using Summary of Child Pages, or Child Pages Shortcode plugins to list the posts and pages of the given blog/subdomain.

== Frequently Asked Questions ==
Q) Should I network activate the plugin?

A) No. Activate on main blog and each subdomain individually.


Note: Custom CSS code will not work on subdomain unless user has Super Admin privelidges. I recommend you use the WP Custom CSS Plugin by Tips and Tricks


Q) May I only include an x amount of posts that I choose?

A) Yes, use include_post= and put in your posts in comma separated format surrounded by double quotes.
Example include_post=\"5,78,896\".


Q) My title is too long and looks ugly, anyway I can shorten it?

A) You may shorten it using the argument title_length=\"10\" will rounded it off to the last complete word before it reaches 10 characters.


Q) I would like to just show an X amount of random posts on the home page. Is it possible?

A) Use the following arguments: random=\"true\" and list=\"10\" will show ten different posts randomly whenever the page is loaded.


Q) May I order my posts in specific order by date or title?

A) Yes you may give specific ordering of your posts or pages via alphabetical order (by title), by date or page or post specific order.

== Screenshots ==
1. Single Column Default
2. Single Column with Blue Header and Thumbnail Dimmensions size="240,160"
3. Double Column with Red Header

== List of Arguments ==

/* Network Posts Extended Shortcodes and Arguments */

<br /><br />

[netsposts include_blog="1,2,5" days="30" taxonomy="news" titles_only="false" show_author="true" thumbnail="true" size="90,90" image_class="alignleft" auto_excerpt="true" excerpt_length="150" show_author="true" paginate="true" list="5"]

<br /><br />

include_post - list of posts/pages that you want to include (example: include_post="5" - include_post="5,8,153"<br />

exclude_post - list of posts/pages that you want to exclude (example: exclude_post="5" - exclude_post="5,8,153"<br />

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

wrap_start, wrap_end - you can wrap the posts for example: (wrap_start="&lt;div style='font-weight:bold;vertical-align:middle;' class='myclass'&gt;" wrap_end="&lt;/div&gt;")

wrap_title_start,wrap_title_end - wrap_image_start,wrap_image_end - wrap_text_start,wrap_text_end. Same as wrap_start,wrap_end above.<br />

end_size – how many numbers on either the start and the end list edges (used for pagination)<br />

manual_excerp_length - You can set the length of the manual excerpt. For example if someone has 500 words in the manual excerpt field it may be trimmed down to 400 like so: manual_excerpt_length="400" (defaul 9999)<br />

mid_size – how many numbers to either side of current page, but not including current page (used for pagination)<br />
order_by - Sort in ascending (default value) and descending order via the following arguments - Ascending: order_post_by='alphabetical_order' order_post_by='date_order' order_post_by='page_order' and descending: order_post_by='alphabetical_order desc' order_post_by='date_order desc' order_post_by='page_order desc' (note: descending must be surrounded by single or double quotes because of the empty space after page_order<br />

page_title_style – style for the page title (default: none)<br />

post_height - Sets the default height for all posts. Recommended for 2 column mode. For example if manual_excerpt_length="400" or excerpt_length="400" and you want posts with less of an excerpt to have same dimmensions use this feature. post_height="300" will give a standard height of 300 pixels. So if post has less characters of text will still keep square shape so titles line up nicely. <br />

prev_next – Whether to include the previous and next links in the list or not (used for pagination. Default: true)<br />

prev – the previous page link text. Works only if prev_next argument is set to true. (Default:« Previous)<br />

next - The next page text. Works only if prev_next argument is set to true. (Default:Next »)<br />

random - Set to true to show posts randomly. (Default: set to false)<br />

title – custom title (default: none) Example: title="Joe's Favorite Bicycles"<br />

title_color - Color of the title text. Example: title_color="red" or title="color:#ff0000" both will give you a color of red. (Default black)<br />

title_length - Cuts off the title at X amount of characters so won't make long wrap around which look ugly. The length is in characters including spaces and symbols (Default 999)<br />

column – number of columns (default: 1)<br />

column_width - Width of column in pixels. Example column_width="250". (Default: 200)<br />

meta_info - Example: meta_info="false" (Default 'true')<br />

meta_length - Example: meta_length="75%" (Default 100%)<br />

menu_name – name of the menu (should be created in Appearance > Menu)(default: none)<br />

menu_class – CSS class for the menu<br />

container_class – the CSS class that is applied to the menu container<br /><br />

For a complete tutorial please visit: <a href='http://www.johncardell.com/plugins/network-posts-extended/' target='ejejcsingle'>http://www.johncardell.com/plugins/network-posts-extended/</a>

== Changelog ==
= 1.2 =
Added custom class to plugins net_posts_extended.css file

.netsposts-read-more-link

This way you can remove the read more links from the excerpts by using the following attribute:

a.netsposts-read-more-link { visibility: hidden; }

= 1.1 =
Added ability to list posts in specific order by date, title or page (pertains to post_type=page only).

Arguments now work with paginate=false random=true

Fixed call to function error


= 1.0 =
Added two more arguments.

manual_excerpt_length=

post_height=


= 0.9 =
Added the function to be able to use your own custom classes in tools area.

Plus added the following arguments:

column_width (default (px): 200)

title_color (default: black)

text_color - color of text. Examples text_color=\"red\" or text_color=\"#ff0000\". Both will have the texts color turn red.

meta_info - Default true


= 0.8 =
wrap_title_start,wrap_title_end - wrap_image_start,wrap_image_end - wrap_text_start,wrap_text_end.

meta_width - Same as title length except in percentage to shorten long meta data.

Added the ability to show posts or pages randomly using the following argument: random=\"true\"

The list= argument works with pagination= true or false (default: false) 