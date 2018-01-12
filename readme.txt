=== f(x) Primary Term ===
Contributors: turtlepod
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=TT23LVNKA3AU2
Tags: Category,Primary Term,Term
Requires at least: 4.9
Requires PHP: 5.3
Tested up to: 4.9
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple plugin to enable primary term. As default only supports Post Category.

== Description ==

Simple plugin to enable primary term. As default only supports Post Category.

Developer can easily extend the plugin to support custom post types and custom taxonomy.

This plugin currently only supports hierarchical taxonomy.

This plugin is very similar with Yoast SEO plugin "Primary Term" functionality. You do not need this plugin if you already have Yoast SEO active.


== Installation ==

1. Navigate to "Plugins > Add New" Page from your Admin.
2. To install directly from WordPress.org repository, search the plugin name in the search box and click "Install Now" button to install the plugin.
3. To install from plugin .zip file, click "Upload Plugin" button in "Plugins > Add New" Screen. Browse the plugin .zip file, and click "Install Now" button.
4. Activate the plugin.

== Frequently Asked Questions ==

= How to get primary term? =

You can use `fx_primary_term_get_primary_term()` function. Here's an example usage.

```
$primary_category_id = fx_primary_term_get_primary_term( 'category', get_the_ID() );
if ( $primary_category_id ) {
	// Do something.
}
```

= How to add support to custom post type/taxonomy  =

Here's and example function for adding support for "genre" taxonomy in "book" cpt:

```
add_filter( 'fx_primary_term_taxonomies', function( $tax ) {
	$tax['book'] = array( 'genre' );
	return $tax;
} );
```

== Screenshots ==

1. No screenshots.

== Changelog ==

= 1.0.0 - 12 Jan 2018 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
initial relase.

== Other Notes ==

Notes for developer:

= Github =

Development of this plugin is hosted at [GitHub](https://github.com/turtlepod/fx-primary-term). Pull request and bug reports are welcome.
