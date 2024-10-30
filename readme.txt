=== Lexicographer ===

Contributors: texttheater, jelsgaard, nadiminti
Tags: index, dictionary, words, lemmas, glossary
Requires at least: 2.8.4
Tested up to: 5.3.3
Stable tag: trunk

Lexicographer creates an alphabetical index of your blog, using keywords you choose. The index can be included in any page, post or text widget.

== Description ==

Lexicographer creates an alphabetical index of your blog, using keywords you choose. The index can be included in any page, post or text widget. Here's an [example](http://texttheater.net/woerterverzeichnis) of such an index.

I wrote this plugin because I like to invent words and define them on my blog, thereby creating a dictionary distributed over several posts. Not only was there no list of all the words available, it was also that Google wouldn't find the words because it couldn't deal with the stress marks I put in there, dictionary-style. Lexicographer solves both of these problems. 

Lexicographer's index is divided into sections, according to the initial characters of terms. Such sections are currently created for the Latin letters (A-Z, letters with diacritics are grouped with the non-diacriticized versions), Hindi characters, and Telugu characters. A single section "#" is created for all Arabic digits (0-9). All other initial characters go into a special section titled "*". Contributions to add support for other writing systems are welcome!

== Installation ==

Either:

1. Search for and install Lexicographer directly through the 'Plugins' menu in
   WordPress

Or:

1. Download and unzip Lexicographer
2. Upload the `lexicographer` directory to the `/wp-content/plugins` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

== Usage ==

Lexicographer does not add any new elements to the WordPress admin interface.  Instead, you use it as follows:

In the posts and pages where you define terms, [use "Edit as HTML"](https://en.support.wordpress.com/wordpress-editor/#configuring-a-block) and put the words and terms to index in spans of class `lemma`, like so: `<span class="lemma">ˌsu·per·ca·liˌfra·gi·lis·ticˌex·pi·a·liˈdo·cious</span>` (the stress and hyphenation marks are of course optional).

In the page (or post, or text widget) where you'd like the index to live, paste the following shortcode:

[lexicographer_index]

At this place, the index will be inserted. It will consist of the defined terms, linking to the definitions.

Shortcode attributes:

* anchorlinks [true/false, default: true]: If true, anchored links to the terms
  will be created. If false, links to the post(s) containing the term will be
  created.
* headerlevel [int, default: 3]: Which heading level to use for the capical
  characters in the index.

== Support ==

If you have questions or suggestions, contact me at poststelle ät texttheater döt net.

== Changelog ==

= 0.9 =

* Added support for Telugu and Hindi (contribution by Sriram Nadiminti).
* Tested with WordPress 5.1.1.

= 0.8 =

* Changed how the Lexicographer index is inserted in posts and pages. You must
  now use shortcodes instead of pasting the <p>{{Lexicographer index}}</p>
  string where you want the index to appear.
* Using the shortcode, you can now specify the heading level of the capital
  character and if you want anchoring links for the terms or just plain links
  to posts.
* Tested with WordPress 4.4.2.

= 0.7 =

* The individual index sections are now put into divs of class
  lexicographer-index-section for the benefit of those who want to style and/or
  script the index.

= 0.6. =

* The index is now put into a div of class lexicographer-index for the benefit
  of those who want to style and/or script the index.

= 0.5 =

* Transliteration of lemmas to ASCII both for creating anchor names and for
  sorting now uses the same transliteration table. The characters ÄäÖöÜü
  (graphemes corresponding to German umlauts) still receive special treatment
  in that they are expanded à la ä → ae for anchor names (but not for sorting),
  but this is now done as a preprocessing step.
* The transliteration table now covers almost every latin-derived letter in the
  Unicode blocks Latin-1 Supplement, Latin Extended-A, Latin Extended-B and
  Latin Extended Additional. Most transliterations are "glyph-oriented" in that
  they involve only removing diacritic marks, decomposing ligatures and
  rotating letters back. A few transliterations are more "usage-oriented", such
  as ß → ss, þ → th or Ɣ → g. Some effort was made to keep the transliteration
  table sane, consistent and language-neutral. Missing letters are indicated in
  comments. Suggestions for additions and improvement are more than welcome!
* Bugfix: generated links were broken if not using /%postname permalinks.
* Bugfix: was indexing unpublished posts/pages on installation.

= 0.4 =

* Now observing DB_CHARSET for creating the database table. This fixes a
  problem where non-ASCII characters get replaced by question marks when
  inserting into the table via a UTF-8 connection.
* Tested with WordPress 3.3.2.
* Updated documentation.

= 0.3 =

* Index can now be inserted in widgets.
* Tested with WordPress 3.3.
* Updated documentation.

= 0.2 =

* Lemmas are now removed from the index when the post/page containing them is
  deleted or otherwise unpublished.
* The index now uses absolute links.
* Tested with WordPress 2.9.1.
* Updated documentation.

= 0.1 =

* Initial release.
