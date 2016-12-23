=== Cherry Testimonials ===
Contributors: TemplateMonster 2002
Tags: testimonials, reviews, custom post type, slider, swiper, cherry framework
Requires at least: 4.5
Tested up to: 4.7
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A testimonials management plugin for WordPress.

== Description ==

Unveil customers' feedback on your services to look more reliable to prospects. This easy-to-use plugin will let you add testimonials to any post and page of your site via shortcodes.

With Cherry Testimonials, you can prove your testimonials aren't fake and were written by real people. Besides the testimonial itself, it's possible to reveal a lot of details about its author. They include his name, photo, email, company he works in, his position, and link to the site of that company or his personal blog.

Note that you can hide the email, position, company's name, and photo, if a customer doesn't want you to reveal any of that data.

This WordPress plugin gives you freedom to customize testimonials the way you like. Its rich set of configuration options allows you to perform the following actions:

* choose the layout type (list or slider);
* set the number of testimonials to display;
* specify the title and subtitle of the testimonial;
* add a divider between the title and testimonial;
* set the length of testimonials in words;
* set the width of authors' photos in pixels;
* order testimonials by a specific attribute;
* define the category to display testimonials from;
* show testimonials with certain IDs.

If you choose a slider as the layout type to display testimonials, you can configure it in many ways. Here's what you can do:

* set the time between slide transitions;
* enable a slider loop;
* set a transition effect;
* show pagination and navigation buttons;
* set the number of slides per view;
* set the structure of testimonials presentation using a template with macros.

Give a try to Cherry Testimonials if you want to add and manage customers' reviews on your site with ease.

== Installation ==

1. Upload cherry-testi folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Testimonials->Settings screen to configure the plugin

== Frequently Asked Questions ==

= How to use? =

Insert a shortcode `[tm_testimonials]` to the post/page content.

== Screenshots ==

1. Testimonials Options.
2. Plugin Settings.
3. Example.

== Changelog ==

= 1.0.0 =
* Initial release

= 1.0.1 =
* UPD: slider settings for devices
* UPD: parameters list
* UPD: cherry-framework to 1.3.1 version

== Documentation ==

= Shortcode =

* type Layout type (list, slider)
* sup_title Specify the super title
* title Specify the title
* sub_title Specify the subtitle
* divider Show/hide divider between titles and testimonials
* limit Number of testimonials (limit="-1" – show all)
* orderby Order testimonials by a specific attribute
* order order testimonials
* category Define the category from which testimonials will be displayed
* ids Display testimonials with certain IDs (e.g ids="1721,1723")
* show_avatar Show/hide author avatar
* size Photo/avatar width (in px)
* content_length Content length (in words)
* show_email Show/hide author email
* show_position Show/hide testimonial author position
* show_company Show/hide company name
* template Template with macros which sets testimonial display structure
* custom_class Custom CSS-class

The following attributes are applied for slider (type="slider") only:

* autoplay Time between scrolled slides (ms)
* effect "slide" or "coverflow"
* loop Enable/disable slider "loop"
* pagination Show/hide pagination
* navigation Show/hide prev/next buttons
* slides_per_view Number of slides per view
* space_between Ppadding between slides (px)

To use testimonials slider use [Swiper script](http://idangero.us/swiper/)
Default script values can be changed with the help of **tm_testimonials_slider_data_atts** filter.

= Templates =

* __/templates/__ -- subdirectory with templates for pages (single, archive)
* __/templates/shortcodes/testimonials/__ -- subdirectory with _*.tmpl_ files

If you need to change the template content, you need to rewrite it in the theme keeping the folder structure. For instance:

* __wp-content/themes/twentysixteen/templates/__
* __wp-content/themes/twentysixteen/templates/shortcodes/testimonials/__