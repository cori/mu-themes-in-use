<?php
/*
Plugin Name: MU Themes In Use
Plugin URI:	http://kinrowan.net/wordpress/mu-templates-in-use
Description: 	Displays a list of the templates currently in use by Wordpress MU Blogs
		Only shows data from blogs that are not archived, spam, or deleted.
Author: Cori Schlegel
Version: 0.1
Author URI: http://kinrowan.net
*/

/*  Copyright 2010

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!class_exists('mu_themes_in_use')) {
	class mu_themes_in_use {

		//Class Functions
		/**
		* PHP 4 Compatible Constructor
		*/
		function mu_themes_in_use(){$this->__construct();}

		/**
		* PHP 5 Constructor
		*/
		function __construct(){

			global $current_user;

			//"Constants" setup
			$thispluginurl = PLUGIN_URL . dirname(plugin_basename(__FILE__)).'/';
			$thispluginpath = PLUGIN_PATH . dirname(plugin_basename(__FILE__)).'/';

			//Actions
			add_action("admin_menu", array(&$this,"admin_menu_link"));

		}

		/**
		* @desc Adds the menu link
		*/
		function admin_menu_link() {

			if ( is_site_admin() ) {
				add_submenu_page('wpmu-admin.php', 'Themes In Use', 'Themes In Use', 10, basename(__FILE__), array(&$this,"admin_page"));
			}
		}

		/**
		* Adds plugin page
		*/
		function admin_page() {

			global $wpdb;

			$arBlogs = $wpdb->get_results( $wpdb->prepare("SELECT blog_id, domain FROM wp_blogs WHERE archived = '0' and spam = '0' and deleted = '0' ORDER BY blog_id") );
			$themesUsedCount = array();
			foreach( $arBlogs as $theBlog ) {

				$theTheme = $wpdb->get_var( $wpdb->prepare("SELECT option_value FROM wp_{$theBlog->blog_id}_options WHERE option_name = 'template'") );
				$themesByBlogs[] = array("id"=>$theBlog->blog_id, "domain"=>$theBlog->domain, "theme"=>$theTheme);

				if( array_key_exists( $theTheme, $themesUsedCount ) ) {
					$themesUsedCount[$theTheme] += 1;
				} else {
					$themesUsedCount[$theTheme] = 1;
				}

			}

			arsort( $themesUsedCount );
			$themesUsedKeys = array_keys( $themesUsedCount );

?>
			<div class="wrap">
				<h2>Current Themes In Use (By Blog)</h2>
				<table class="editform">
					<tr valign="top">
						<th width="10%" align="left">Blog ID</th>
						<th width="15%" align="left">Blog Domain</th>
						<th align="left">Current Theme</th>
					</tr>
				<?php
					foreach( $themesByBlogs as $theme ) {
				?>
					<tr valign="top">
						<td width="10%">
							<?php echo "<a href=\"/wp-admin/wpmu-blogs.php?action=editblog&id={$theme['id']}\" title=\"Edit blog {$theme['id']}\">{$theme['id']}</a>"; ?>
						</td>
						<td width="15%">
							<?php echo $theme['domain']; ?>
						</td>
						<td>
							<?php echo $theme['theme']; ?>
						</td>
					</tr>
				<?php
					}
				?>
				</table>
				<h2>Current Themes (totals)</h2>
				<table class="editform">
					<tr valign="top">
						<th align="left">Theme Name</th>
						<th align="left">Total blogs in use</th>
					</tr>
					<?php foreach( $themesUsedKeys as $themeName ) { ?>
					<tr valign="top">
						<td><?php echo $themeName; ?></td>
						<td><?php echo $themesUsedCount[$themeName]; ?></td>
					</tr>
				<?php } ?>
				</table>
			</div>
<?php

		}

	} //End Class

} //End if class exists statement

//instantiate the class
if (class_exists('mu_themes_in_use')) {
	$mu_themes_in_use_var = new mu_themes_in_use();
}
?>