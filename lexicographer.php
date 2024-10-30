<?php

/*
Plugin Name: Lexicographer
Plugin URI: http://wordpress.org/extend/plugins/lexicographer/
Description: Lexicographer creates an alphabetical index of your blog, using keywords you specify. The index can be included in any page, post or widget.
Version: 0.9.4
Author: Kilian Evang
Author URI: https://texttheater.net
*/

/*******************************************************************************

    File: lexicographer.php
    Copyright (C) 2009-2019 Kilian Evang and contributors

    This file is part of Lexicographer.

    Lexicographer is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Lexicographer is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Lexicographer; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*******************************************************************************/

require('lib.php');

# Installation
register_activation_hook(__FILE__, 'lexicographer_install');
register_deactivation_hook(__FILE__, 'lexicographer_uninstall');

# Maintain index
add_action('transition_post_status', 'lexicographer_transition_post_status',
        10, 3);
		
# Replace shortcode with index
add_shortcode('lexicographer_index', 'lexicographer_index');
add_filter('widget_text', 'do_shortcode');

# Insert anchor links into content
add_filter('the_content', 'lexicographer_the_content');
add_filter('widget_text', 'lexicographer_widget_text');

