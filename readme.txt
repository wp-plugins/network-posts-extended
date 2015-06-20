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
1. screenshot-1.png
2. screenshot-2.png
3. screenshot-3.png

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