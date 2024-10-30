<?php

/*******************************************************************************

    File: lib.php
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

require('translitlib.php');

/*==============================================================================
    Data
==============================================================================*/

function lexicographer_span_pattern() {
    static $result;

    if (!isset($result)) {
        $result = '/(< *span [^>]*class="(?:[^"]* )?lemma(?: [^"]*)?"[^>]*>)(.*?'
                . ')(< *\/ *span *>)/';
    }

    return $result;
}

/*==============================================================================
    Hooks
==============================================================================*/

function lexicographer_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'lexicographer';

    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
        $wpdb->query("DROP TABLE $table_name");
    }

    $wpdb->query("CREATE TABLE " . $table_name . " (
                  lemma_ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  lemma_post_ID bigint(20) unsigned NOT NULL default '0',
                  lemma_content tinytext NOT NULL,
                  lemma_content_sorted tinytext NOT NULL,
                  lemma_section varchar(3) NOT NULL,
                  PRIMARY KEY (lemma_ID),
                  KEY lemma_post_ID (lemma_post_ID),
                  KEY lemma_section (lemma_section)
                  ) CHARSET " . DB_CHARSET . ";");
    $table_name = $wpdb->prefix . 'posts';
    $posts = $wpdb->get_results("SELECT ID FROM $table_name
                                 WHERE (post_type = 'page'
                                 OR post_type = 'post')
                                 AND post_status = 'publish'");

    foreach($posts as $post) {
        lexicographer_publish($post->ID);
    }
}

function lexicographer_uninstall() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'lexicographer';

    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }
}

function lexicographer_transition_post_status($new_status, $old_status, $post) {
    if ($new_status == 'publish') {
        lexicographer_publish($post->ID);
    } else {
        lexicographer_unpublish($post->ID);
    }
}

function lexicographer_publish($post_ID) {
    global $wpdb;
    $post = get_post($post_ID);
    $content = $post->post_content;
    preg_match_all(lexicographer_span_pattern(), $content, $matches);
    $hits = sizeof($matches[0]);
    $table_name = $wpdb->prefix . 'lexicographer';
    $wpdb->query("DELETE FROM $table_name
                  WHERE lemma_post_ID = $post_ID");

    for ($i = 0; $i < $hits; $i++) {
        $content = lexicographer_extract_term($matches[2][$i]);
        $content_sorted = lexicographer_sortify($content);

        if (preg_match('/^[A-Za-z]/', $content_sorted)) {
            $section = substr($content_sorted, 0, 1);
        } else if (preg_match('/^[అఆఇఈఉఊఎఏఐఒఓఔఅంకఖగఘచఛజఝటఠడఢణతథదధనపఫబభమయరలవశషసహళక్షఱअआइईउऊएऐओऔकखगघचछजझटठडढतथदधनपफबभमयरलवशसह]/', $content_sorted)) {
            $section = substr($content_sorted, 0, 3);
        } else if (preg_match('/^[0-9]/', $content_sorted)) {
            $section = '#';
        } else {
            $section = '*';
        } 

        $content = $wpdb->escape($content);
        $content_sorted = $wpdb->escape($content_sorted);

        $wpdb->query("INSERT INTO $table_name
                      (lemma_post_ID, lemma_content, lemma_content_sorted,
                      lemma_section)
                      VALUES
                      ($post_ID, '$content', '$content_sorted', '$section')");
    }
}

function lexicographer_unpublish($post_ID) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'lexicographer';
    $wpdb->query("DELETE FROM $table_name WHERE lemma_post_ID = $post_ID");
}

function lexicographer_index($attributes) {
    extract(shortcode_atts(array(
        'anchorlinks' => true,
        'headerlevel' => 3,
    ), $attributes));
    return lexicographer_get_index($headerlevel, $anchorlinks);
}

function lexicographer_get_index($heading_level, $anchorlinks) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'lexicographer';
    $index = "<div class=\"lexicographer-index\">\n";

    foreach (array('*', '#', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I',
                   'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
                   'U', 'V', 'W', 'X', 'Y', 'Z', 
                   'అ', 'ఆ', 'ఇ', 'ఈ', 'ఉ', 'ఊ', 'ఎ', 'ఏ', 'ఐ',
                   'ఒ', 'ఓ', 'ఔ', 'అం', 'క', 'ఖ', 'గ', 'ఘ', 'చ', 'ఛ', 'జ',
                   'ఝ', 'ట', 'ఠ', 'డ', 'ఢ', 'ణ', 'త', 'థ', 'ద', 'ధ', 'న',
                   'ప', 'ఫ', 'బ', 'భ', 'మ', 'య', 'ర', 'ల', 'వ',
                   'శ', 'ష', 'స', 'హ', 'ళ', 'క్ష', 'ఱ',
                   'अ', 'आ', 'इ', 'ई', 'उ', 'ऊ', 'ए', 'ऐ', 'ओ', 'औ',
                   'क', 'ख', 'ग', 'घ', 'च', 'छ', 'ज', 'झ', 'ट', 'ठ', 'ड', 'ढ',
                   'त', 'थ', 'द', 'ध', 'न', 'प', 'फ', 'ब', 'भ', 'म',
                   'य', 'र', 'ल', 'व', 'श', 'स', 'ह') as $section) {
        $rows = $wpdb->get_results("SELECT lemma_post_ID, lemma_content
                                    FROM $table_name
                                    WHERE lemma_section = '$section'
                                    ORDER BY lemma_content_sorted ASC");

        if (!empty($rows)) {
            $index .= "<div class=\"lexicographer-index-section\">\n";
            $index .= sprintf("<h%d>$section</h%d>\n<ul>\n", $heading_level,
            $heading_level);
            $home = get_option('home');

            foreach($rows as $row) {
                $index .= "<li><a href=\"";
                $index .= get_page_link($row->lemma_post_ID);
                       
                if ($anchorlinks == "true") {
                    $index .= '#';
                    $index .= lexicographer_anchornamify($row->lemma_content);
                }
                       
                $index .= "\">";
                $index .= $row->lemma_content;
                $index .= "</a></li>\n";
            }

            $index .= "</ul>\n";
            $index .= "</div>\n";
        }
    }

    $index .= "</div>";
    return $index;
}

function lexicographer_the_content($content) {
   return preg_replace_callback(lexicographer_span_pattern(),
           'lexicographer_insert_anchor', $content);
}

function lexicographer_widget_text($content) {
   return preg_replace_callback(lexicographer_span_pattern(),
           'lexicographer_insert_anchor', $content);
}

/*==============================================================================
    Helpers
==============================================================================*/

function lexicographer_extract_term($span_content) {
    return trim(lexicographer_dic_strip(html_entity_decode(
            strip_tags($span_content, '<sub><sup>'), ENT_COMPAT, 'UTF-8')));
}

function lexicographer_insert_anchor($groups) {
    return $groups[1] . '<a name="' . lexicographer_anchornamify($groups[2]) .
            '"></a> ' . $groups[2] . $groups[3];
}

function lexicographer_sortify($string) {
    return lexicographer_multireplace(strip_tags($string),
            lexicographer_sort_table());
}

?>
