<?php

$plugin['url'] = '$HeadURL$';
$plugin['date'] = '$LastChangedDate$';
$plugin['revision'] = '$LastChangedRevision$';

$revision = @$plugin['revision'];
if( !empty( $revision ) )
	{
	$parts = explode( ' ' , trim( $revision , '$' ) );
	$revision = $parts[1];
	if( !empty( $revision ) )
		$revision = '.' . $revision;
	}

$plugin['name'] = 'l10n';
$plugin['version'] = '0.6' . $revision;
$plugin['author'] = 'Graeme Porteous & Stephen Dickinson';
$plugin['author_uri'] = 'http://txp-plugins.netcarving.com/plugins/mlp-plugin';
$plugin['description'] = 'Multi-Lingual Publishing Package.';
$plugin['type'] = '1';

@include_once('../zem_tpl.php');

if (0) {
?>
<!-- CSS SECTION
# --- BEGIN PLUGIN CSS ---
	<style type="text/css">
	div#l10n_help td { vertical-align:top; }
	div#l10n_help code { font-weight:bold; font: 105%/130% "Courier New", courier, monospace; background-color: #FFFFCC;}
	div#l10n_help .code_tag{ font-weight:normal; border:1px dotted #999; background-color: #f0e68c; display:block; margin:10px 10px 20px; padding:10px; }
	div#l10n_help a:link, div#l10n_help a:visited { color: blue; text-decoration: none; border-bottom: 1px solid blue; padding-bottom:1px;}
	div#l10n_help a:hover, div#l10n_help a:active { color: blue; text-decoration: none; border-bottom: 2px solid blue; padding-bottom:1px;}
	div#l10n_help h1 { color: #369; font: 20px Georgia, sans-serif; margin: 0; text-align: center; }
	div#l10n_help h2 { border-bottom: 2px solid black; padding:10px 0 0; color: #369; font: 17px Georgia, sans-serif; }
	div#l10n_help h2 a { text-decoration: none; }
	div#l10n_help ul ul { font-size:85%; }
	div#l10n_help h3 { color: #693; font: bold 12px Arial, sans-serif; letter-spacing: 1px; margin: 10px 0 0;text-transform: uppercase;}
	</style>
# --- END PLUGIN CSS ---
-->
<!-- HELP SECTION
# --- BEGIN PLUGIN HELP ---
<div id="l10n_help">

h1(#top). l10n MLP Pack Help.

<br />

|_. Copyright 2006 Graeme Porteous and Stephen Dickinson. |

<br />

h2. Table Of Contents.

* "Introduction &amp; Setup/Cleanup":#intro
* "Terminology":#terms
* "Translation Paradigm":#paradigm
* "What the MLP(Multi-Lingual Publishing) Pack provides.":#features
* "Known Issues":#issues
* "Snippets":#snippets
* "Tag Directory":#tags
** "l10n_lang_list":#lang_list
** "l10n_if_lang":#if_lang
** "l10n_get_lang":#get_lang
** "l10n_feed_link":#feed_link
** "l10n_get_lang_dir":#get_lang_dir
** "l10n_localise":#localise
* "Preferences Help":#prefs
* "Supported Languages.":#langs
* "Credits":#credits

<br/>

h2(#intro). Introduction &amp; Setup/Cleanup

The MLP(Multi-Lingual Publishing) Pack is an add-on pack for Textpattern 4.0.4 that helps turn it into a productive MLP platform -- or at least, that is its intended aim.

It is *not* implemented as a 'pure' plugin as it&#8230;

* exceeds the plugin size limit
* uses an altered version of the txplib_db.php file

_If you are looking for a pure TxP plugin then this is not the option for you._

<br/>

Other things you might like to think about before installing the pack&#8230;

* It makes some extensive additions to the underlying database, notably a new 'textpattern' table per language you run the site in.
* The 'articles' tab output is filtered using a temporary SQL table that hides the underlying table and allows additional filtering by language.
* Changes are made to the basic txp_lang and textpattern tables.

All these are listed in the setup wizard (under the content > MLP tab).

 <form action="index.php?event=l10n&tab=wizard" method="post"><p><input value="Setup/Cleanup&#8230;" type="submit"></p></form>

 <span style="float:right"><a href="#top" title="Jump to the top">top</a></span>

h2(#terms). Terminology

|_. Term |_. Definition |
| Work | A collection of an author's (or authors') ideas/opinions/thoughts. |
| Rendition | The expression of an authors _work_ in a single language. |
| Article | The *set* of _renditions_ of a given author's _work_. An article always has at least one _rendition_. |
| Translate/Translation | The act of translating one rendition into a new rendition. This also covers the process of conversion of the initial _work_ into its first _rendition_. |
| Translator | The person or persons doing the translation (could be the author of the original _work_ but doesn't have to be.) |

 <br>

To avoid confusion, the noun 'translation' *always* refers to the act of translating, *never* to the result of translating something.

A 'rendition' *always* refers to the result of translating a work (or an existing rendition of a work) into a language.

Plain Textpattern makes no differentiation between articles and renditions because it only supports a single rendition of any work. It has no need to distinguish between multiple renditions of a work -- to plain Textpattern, a rendition _is_ an article. Not so with an MLP setup; each article can have multiple renditions.

This means that the old 'Articles' tab on the contents page has been renamed 'Renditions' and a new tab (under the MLP tab) is introduced to allow display and manipulation of articles (sets of renditions of a work) as a table.

Each _row_ in the table represents an article, each _column_ a language and each _cell_ a rendition of an article in a language. When a cell has a rendition, it will show title, section and author summary information and be colour coded according to its published status (draft,hidden,pending,live or sticky). There is an icon !/textpattern/txp_img/l10n_delete.png! in the top, right-hand, corner that allows the rendition to be deleted, and if the article is live or sticky there will be a "clone" icon !/textpattern/txp_img/l10n_clone.png! in the bottom, left-hand, corner. Pressing this allows the rendition to be cloned to other languages (as a draft) and assigned to a translator for translation. (See the following section for more details).

The content > write tab still allows the editing of individual renditions.

 <span style="float:right"><a href="#top" title="Jump to the top">top</a></span>

h2(#paradigm). Translation Paradigm.

Originally I wanted to allow the creation of new renditions by showing an exisiting rendition on one side of the screen and then allowing a translator to do the translating on the other side of the screen. This meant _big_ changes to the existing write tab, or replacing the write tab with a complicated substitute.

However, I happened upon Mary's 'Save New' plugin and that inspired the current solution that allows the write tab to remain virtually untouched and yet still allow translation. This is done by 'cloning' a source rendition and then translating the clone *in situ* in the write tab.

The translator simply edits the clone, replacing the source text as they go, until it is all replaced with the target language. At that point the clone is a new rendition of the original author's work.

It's much easier on the translators as they get to keep the interface they are used to.

 <span style="float:right"><a href="#top" title="Jump to the top">top</a></span>

h2(#features). What the MLP(Multi-Lingual Publishing) Pack provides.

On the admin side...
* Each TxP user can choose their own admin language -- and switch between admin languages at will.
* Support for localisation of plugin strings via the admin interface (at last, no editing of source files!)
* Ability to search for, and edit, any TxP language string by name or by content -- in any of the languages supported by the site.
** Also allows quick location of strings with missing renditions in any language.
* Support for 'snippets' to simplify page/form editing and writing.
* Snippets can be entered in RTL or LTR mode (JS to toggle between the two.)
* Write tab now allows title/body/excerpt and preview in RTL as well as LTR mode.
* Import/export of your plugin strings or snippets so you can upload to live sites or share with others.
* Export of TxP strings using the TxP language file format for distribution to other/devs.
* Support for articles as groups of renditions.
* Support for cloning of renditions and their translation into other languages using the existing write tab.
* Email notifications sent to translators when articles are cloned or have their author changed.
* Extra filtering of the list of renditions by language.
* No hijacking of existing fields (sections/categories/custom fields) to store language information, so you are free to use the section/categories/custom fields in your application.
* Full localisation of the following fields...
** Categories
** Sections
** Image alt text and captions
** Link descriptions
** File descriptions
* Setup and Cleanup wizards.

On the public side...
* Detection of the language the user wants to view a site in via the url or browser headers.
* URLs re-written so that browser caches know the difference between the renditions of articles in different languages.
* Automatic selection of the correct renditions of snippets in pages and forms.
* Fully functional search/commenting/feeds for each language the site supports.
* Localised (and direction adjusted) feeds.
* Localised categories, sections, file & link descriptions, image alt text & captions.
* 404 support for finding renditions that are not available in the requested language.
* A tag to list all available renditions of a given article and allow switching between them.
* Tags for accessing language codes and direction information.
* Conditional tag for testing the visitor's language or the directionality of the language.

 <span style="float:right"><a href="#top" title="Jump to the top">top</a></span>

h2(#issues). Known Issues

Here is a list of known issues...

<h3>No MLP interface for localising link and file descriptions.</h3>

These fields can still be localised using the normal Textpattern pages. However, you need to use the l10n_if_lang tag to wrap each translation within the description field.

For example, to provide localised descriptions in English and Greek you enter this into the description field...

 <div class="code_tag"><pre> &lt;txp:l10n_if_lang lang="en-gb"&gt;
 English (GB).
 &lt;/txp:l10n_if_lang&gt;
 &lt;txp:l10n_if_lang lang="el-gr"&gt;
 Ελληνικά.
 &lt;/txp:l10n_if_lang&gt;</pre></div>

<h3>Localisation of image alt and image caption attributes.</h3>

This is more problematic and the above method does not work.

 <span style="float:right"><a href="#top" title="Jump to the top">top</a></span>

h2(#snippets). Snippets

Snippets are named strings that you can reference within pages or forms.

They are very similar to strings that are output in pages and forms using TxP's 'text' tag. Indeed, the 'Snippets' tab (found under *Content > MLP*) will also detect and display the strings used in the TxP 'text' tag.

However, snippets differ a little from the 'text' tag as they are parsed before the rest of the page/form and thus, can be used to provide localised strings as attributes to other tags. They are also very easy to use *but* they will not work once MLP is uninstalled.

To add snippets to pages or forms...

# Make sure the page/form is wrapped with the @<txp:l10n_localise>@ ... @</txp:l10n_localise>@ statements.
# Within those statements type a string starting and ending with two hash characters, like this "##my_first_snippet##" (no need for the quotation marks.)
# On the *content > MLP > Snippets* tab, look for your page or form on the correct subtab.
# Click on the page/form name to bring up a list of all snippets it contains.
# You should see your snippet "my_first_snippet" listed with no renditions.
# Click on the name of your snippet to bring up the edit boxes.
# Supply appropriate renditions and hit the save button.
# Now looking at your site should give you the correct rendition according to the url you type.

 <span style="float:right"><a href="#top" title="Jump to the top">top</a></span>

h2(#tags). Tag Directory

|_. Tag |_. Description |
| "*l10n_lang_list*":#lang_list    | Outputs an un-ordered list of languages. <br/> On an article list page, this outputs all of the site's available languages.<br/>On individual articles it lists only those languages the article has renditions for. |
| "*l10n_if_lang*":#if_lang        | Conditional tag that tests the visitor's browse language against a target, or tests the visitor's language's _direction_ against the given direction. <br/> This is very useful for serving css files for Right-to-Left languages.  |
| "*l10n_get_lang*":#get_lang      | Outputs the language code and/or full native name of the language the visitor is browsing in.<br/>Typically used in the page header to specify the language the page is rendered in (E.g. In the DOCTYPE declaration.) |
| "*l10n_feed_link*":#feed_link    | Outputs a language specific feed link. |
| "*l10n_get_lang_dir*":#get_lang_dir | Outputs the direction of the visitor's browse language. <br/> Use this in the html @body@ tag to specify the default direction of a page. |
| "*l10n_localise*":#localise      | Used as a wrap tag on entire pages/forms to enable snippet support. |

<hr/>

h3(#lang_list). "l10n_lang_list(Jump to the tag list)":#tags

Outputs an un-ordered list of languages.

On an article list page, this outputs all of the site's available languages. On individual articles it lists only those languages the article has renditions for. At present it uses messy urls to avoid extra overhead on looking up multiple renditions and calling the permlink function on each just to populate the list. This might change in future versions.

You can also use this tag on 404 pages to output a list of closely matching renditions.

|_. Attribute |_. Default |_. Description |
| title | '' | (Optional) This string will be output as a paragraph before the list of languages. |
| on404 | '' | (Optional) If you want to use this tag on a 404 page to output a list of closely matching renditions and their titles (when possible) then set this to a non-blank value. |
| list_class | l10n_lang_list | CSS class for entire list . |
| current_class | l10n_current | (Optional) Names the css class to give to the language in the list that matches the language the visitor is browsing in. |
| language_class | long | (Optional) CSS class name to apply to all list items. Valid values are 'long' (giving the long code such as 'en-gb') or 'short' (giving 'en'.) |
| show_empty | '' | (Optional on single article pages) Set to non-blank value to force the output of all languages, even ones with no rendition. |
| link_current | '' | (Optional) Set to a non-blank value to make the current language an active hyperlink |
| display | native | (Optional) How the language is displayed on the web page. Valid values are 'native++', 'native+', 'native', 'long' and 'short'. |
| article_list | TXP's @$is_article_list@ variable | (Optional on single article pages) Set to a non-blank value to always output a site-wide list (even on single article pages).<br/>Be careful though as setting this option could lead to 404 page not found errors if the visitor then attempts to click through to pages that have no rendition in selected language. |

&nbsp;<br/>
&nbsp;<br/>

h3(#if_lang). "l10n_if_lang(Jump to the tag list)":#tags

Conditional tag that tests the visitor's browse language against a target, or tests the _direction_ of the visitor's language against the given direction.

This is very useful for serving css files for Right-to-Left languages or any other content you wish to make specific to language or language direction.

This is used on the demo site to output a second CSS file for RTL languages. As the file is output after the default LTR file, it's CSS rules will override the LTR rules and the page layout is setup for correct RTL rendering.

|_. Attribute |_. Default |_. Description |
| lang | @$l10n_language['short']@ | Set this to a valid ISO-693 language code to test against the visitor's browse language. |
| dir | '' | Leave blank if testing using the 'lang' attribute otherwise setting this to either 'rtl' or 'ltr' tests against the direction of the visitor's browse language. |
| wraptag | div | Wrapper for the resulting output. It is *only* used for tests against the browse language, not against direction. |

h3(#get_lang). "l10n_get_lang(Jump to the tag list)":#tags

Outputs the language code and/or full native name of the language the visitor is browsing in. I use this in each page's Doctype.

|_. Attribute |_. Default |_. Description |
| type | short | (Optional) How to format the resulting string. Valid values are 'long','short','native' |


h3(#feed_link). "l10n_feed_link(Jump to the tag list)":#tags

Outputs a language specific feed link.

|_. Attribute |_. Default |_. Description |
| code | The visitor's current browse language | (Optional) If you want to override the language of a given feed link, set this to the code of the language you want to output the feed in. |

h3(#get_lang_dir). "l10n_get_lang_dir(Jump to the tag list)":#tags

Outputs the direction of the visitor's browse language. <br/> Use this in the html @body@ tag to specify the default direction of a page.

|_. Attribute |_. Default |_. Description |
| type | short | (Optional) Which of the language's codes to use during the direction lookup.<br/>Valid values are 'long','short' <br/>In practice 'short' should be all you need. |

h3(#localise). "l10n_localise(Jump to the tag list)":#tags

Use this tag to wrap entire pages and forms in which you wish to use snippets.

|_. Attribute |_. Default |_. Description |
| page | none | (Optional) Set this to any non-blank value when wrapping TxP pages to cause injection of language codes into the page's permlinks and other internal hrefs.<br/>This can help stop browsers from apparantly "loosing" track of your browse language by caching pages with the same url that you previously visited when browsing in a different language. |

This tag has no attributes.

 <span style="float:right"><a href="#top" title="Jump to the top">top</a></span>

h2(#prefs). Preferences Help.

* "Languages":#l10n-languages
* "Show Article Table Legend":#l10n-show_legends
* "Email a user when assigning them a rendition?":#l10n-send_notifications
* "… even when assigning to yourself?":#l10n-send_notice_to_self
* "… even when author changed in content > renditions list?":#l10n-send_notice_on_changeauthor
* "Power users can change a rendition's language or article?":#l10n-allow_writetab_changes
* "Inline editing of pages and forms":#l10n-inline_editing

h3(#l10n-languages). "Languages":#prefs

When the setup wizard is run, this list will be populated with all the currently installed languages on the site (to install languages you need to go to Admin>Preferences>Languages).

The first language in this comma separated list of language codes is considered to be the site's 'default' language and will be used if the MLP pack cannot serve the language the visitor is requesting. We recommend that you use the full, 5 character, language codes where possible (eg. el-gr for Greek, en-gb for British English etc) because TxP itself uses these 5 character codes to identify the currently selected language.

You can use the basic 2 character code if you want but things don't work out as well with TxP's language strings when you do this.


h3(#l10n-show_legends). "Show Article Table Legend":#prefs

This option controls the visibility of the explanation that appears as the footer of the article table on the Content>MLP>Articles tab.

Setting this to 'no' can free up some screen space for you if you know what the colour scheme represents.


h3(#l10n-send_notifications). "Email a user when assigning them a rendition?":#prefs

Using the table of articles on the Content>MLP>Articles page it is possible to clone renditions for translation into other languages. When you do so, you must assign the translation work to a translator. If you set this option to 'yes' then the MLP pack will send a notification email to the translator telling them of their work assignment and providing a clickable link that takes them straight to that rendition to start work.


h3(#l10n-send_notice_to_self). "… even when assigning to yourself?":#prefs

Some users might even assign themselves as translator of a work, and in this case you can prevent the notification email from being sent to yourself by setting this option to 'no'. Set it to 'yes' if you still want to recieve an email notification.


h3(#l10n-send_notice_on_changeauthor). "… even when author changed in content > renditions list?":#prefs

The MLP pack can even send email notifications when a rendition's author is changed from the Contents>Renditions tab (that is the old 'article' tab.)


h3(#l10n-allow_writetab_changes). "Power users can change a rendition's language or article?":#prefs

Set this option to 'yes' allows some users (Publishers and Managing Editors) to change the language or article that a rendition is assigned to.


h3(#l10n-inline_editing). "Inline editing of pages and forms":#prefs

Setting this option to 'yes' allows pages and forms to be edited using a special link on the Content>MLP>Snippets>(Pages/Forms) tab. This allows you to work with snippets in pages and forms than if you had to keep swithing to the Presentation>(Pages/Forms) tabs.

It also allows you access to a feature that allows pages and forms to automatically be wrapped with the l10n_localise tag.

h3(#l10n-allow_search_delete). "Allow strings to be totally deleted on the snippet > search tab?":#prefs

Choose 'yes' to allow all renditions of a string to be deleted when edited via the Content>MLP>Snippets>Search tab. Just select a string to edit, manually delete all renditions of the string and then hit 'save'. This will remove the string from your installation. You will be left on the edit page for that string so that you can re-enter rendition data if your deletion was a mistake.

 <span style="float:right"><a href="#top" title="Jump to the top">top</a></span>

h2(#langs). Supported Languages.

Here is the full list of ISO-693 languages known to the MLP Pack. Note that it contains a few extra 5 character codes.

The array is located in the file @textpattern\lib\mlp_langs.php@.

You can extend existing entries as needed. See the entries for 'ar', 'en' or 'zn' for examples how to add 5 character (xx-yy) ISO-693-2 codes to the array. If you need a language that is not in the array (nor even in the ISO-693-2 code set) then you _could_ generate your own code for it.

_Generated codes *must* be in 2 character (xx) or 5 character (xx-yy) format: the MLP Pack understands no others._

For example, were I to add Malaysian English (commonly known as Manglish) to the array then I might extend the 'en' sub-array thus @'en-ms'=>'Manglish'@

None of these codes may be used for section names in the MLP installation if the permlink mode starts with a section (for example, section/id/title.)

*You can improve the efficiency of your MLP installation by editing the array and commenting out (or removing) all the entries you don't use on the public and admin sides of the site.*

<pre class="code_tag">
static $iso_693_1_langs = array(
	'aa'=>array( 'aa'=>'Afaraf' ),
	'ab'=>array( 'ab'=>'аҧсуа бызшәа' ),
	'af'=>array( 'af'=>'Afrikaans' ),
	'am'=>array( 'am'=>'አማርኛ' ),
	'ar'=>array( 'ar'=>'العربية' , 'ar-dz'=>'جزائري عربي' , 'dir'=>'rtl' ),
	'as'=>array( 'as'=>'অসমীয়া' ),
	'ay'=>array( 'ay'=>'Aymar aru' ),
	'az'=>array( 'az'=>'Azərbaycan dili' ),
	'ba'=>array( 'ba'=>'башҡорт теле' ),
	'be'=>array( 'be'=>'Беларуская мова' ),
	'bg'=>array( 'bg'=>'Български' ),
	'bh'=>array( 'bh'=>'भोजपुरी' ),
	'bi'=>array( 'bi'=>'Bislama' ),
	'bn'=>array( 'bn'=>'বাংলা' ),
	'bo'=>array( 'bo'=>'Bod Skad' ) ,
	'br'=>array( 'br'=>'ar Brezhoneg' ) ,
	'ca'=>array( 'ca'=>'Català' ) ,
	'co'=>array( 'co'=>'Corsu' ) ,
	'cs'=>array( 'cs'=>'Čeština' ) ,
	'cy'=>array( 'cy'=>'Cymraeg' ) ,
	'da'=>array( 'da'=>'Dansk' ) ,
	'de'=>array( 'de'=>'Deutsch' ) ,
	'dz'=>array( 'dz'=>'Dzongkha' ) ,
	'el'=>array( 'el'=>'Ελληνικά' ) ,
	'en'=>array( 'en'=>'English' , 'en-gb'=>'English (GB)' , 'en-us'=>'English (US)' ),
	'eo'=>array( 'eo'=>'Esperanto' ),
	'es'=>array( 'es'=>'Español' ),
	'et'=>array( 'et'=>'Eesti Keel' ),
	'eu'=>array( 'eu'=>'Euskera' ),
	'fa'=>array( 'fa'=>'Fārsī' ),
	'fi'=>array( 'fi'=>'Suomi' ),
	'fj'=>array( 'fj'=>'vaka-Viti' ),
	'fo'=>array( 'fo'=>'Føroyska' ),
	'fr'=>array( 'fr'=>'Français' ),
	'fy'=>array( 'fy'=>'Frysk' ),
	'ga'=>array( 'ga'=>'Gaeilge' ),
	'gd'=>array( 'gd'=>'Gàidhlig' ),
	'gl'=>array( 'gl'=>'Galego' ),
	'gn'=>array( 'gn'=>"Avañe'ẽ" ),
	'gu'=>array( 'gu'=>'ગુજરાતી' ),
	'ha'=>array( 'ha'=>'حَوْسَ حَرْش۪' , 'dir'=>'rtl' ),
	'he'=>array( 'he'=>'עִבְרִית' ,'dir'=>'rtl' ),
	'hi'=>array( 'hi'=>'हिन्दी' ),
	'hr'=>array( 'hr'=>'Hrvatski' ),
	'hu'=>array( 'hu'=>'Magyar' ),
	'hy'=>array( 'hy'=>'Հայերէն' ),
	'ia'=>array( 'ia'=>'Interlingua' ),
	'id'=>array( 'id'=>'Bahasa Indonesia' ),
	'ie'=>array( 'ie'=>'Interlingue' ),
	'ik'=>array( 'ik'=>'Iñupiak' ),
	'is'=>array( 'is'=>'Íslenska' ),
	'it'=>array( 'it'=>'Italiano' ),
	'iu'=>array( 'iu'=>'ᐃᓄᒃᑎᑐᑦ' ),
	'ja'=>array( 'ja'=>'日本語' ),
	'jw'=>array( 'jw'=>'basa Jawa' ),
	'ka'=>array( 'ka'=>'ქართული' ),
	'kk'=>array( 'kk'=>'Қазақ' ),
	'kl'=>array( 'kl'=>'Kalaallisut' ),
	'km'=>array( 'km'=>'ភាសាខ្មែរ' ),
	'kn'=>array( 'kn'=>'ಕನ್ನಡ' ),
	'ko'=>array( 'ko'=>'한국어' ),
	'ks'=>array( 'ks'=>'काऽशुर' ),
	'ku'=>array( 'ku'=>'Kurdí' ),
	'ky'=>array( 'ky'=>'Кыргызча' ),
	'la'=>array( 'la'=>'Latine' ),
	'ln'=>array( 'ln'=>'lokótá ya lingála' ),
	'lo'=>array( 'lo'=>'ລາວ' ),
	'lt'=>array( 'lt'=>'Lietuvių Kalba' ),
	'lv'=>array( 'lv'=>'Latviešu' ),
	'mg'=>array( 'mg'=>'Malagasy fiteny' ),
	'mi'=>array( 'mi'=>'te Reo Māori' ),
	'mk'=>array( 'mk'=>'Македонски' ),
	'ml'=>array( 'ml'=>'മലയാളം' ),
	'mn'=>array( 'mn'=>'Монгол' ),
	'mo'=>array( 'mo'=>'лимба молдовеняскэ' ),
	'mr'=>array( 'mr'=>'मराठी' ),
	'ms'=>array( 'ms'=>'Bahasa Melayu' ),
	'mt'=>array( 'mt'=>'Malti' ),
	'my'=>array( 'my'=>'ဗမာစကား' ),
	'na'=>array( 'na'=>'Ekakairũ Naoero' ),
	'ne'=>array( 'ne'=>'नेपाली' ),
	'nl'=>array( 'nl'=>'Nederlands' ),
	'no'=>array( 'no'=>'Norsk' ),
	'oc'=>array( 'oc'=>'lenga occitana' ),
	'om'=>array( 'om'=>'Afaan Oromo' ),
	'or'=>array( 'or'=>'ଓଡ଼ିଆ' ),
	'pa'=>array( 'pa'=>'ਪੰਜਾਬੀ' ),
	'pl'=>array( 'pl'=>'Polski' ),
	'ps'=>array( 'ps'=>'پښتو' , 'dir'=>'rtl' ),
	'pt'=>array( 'pt'=>'Português' ),
	'qu'=>array( 'qu'=>'Runa Simi/Kichwa' ),
	'rm'=>array( 'en'=>'Rhaeto-Romance' ),
	'rn'=>array( 'rn'=>'Kirundi' ),
	'ro'=>array( 'ro'=>'Română' ),
	'ru'=>array( 'ru'=>'Русский' ),
	'rw'=>array( 'rw'=>'Kinyarwandi' ),
	'sa'=>array( 'sa'=>'संस्कृतम्' ),
	'sd'=>array( 'sd'=>'سنڌي' , 'dir'=>'rtl' ),
	'sg'=>array( 'sg'=>'yângâ tî sängö' ),
	'sh'=>array( 'sh'=>'Српскохрватски' ),
	'si'=>array( 'si'=>'(siṁhala bʰāṣāva)' ),
	'sk'=>array( 'sk'=>'Slovenčina' ),
	'sl'=>array( 'sl'=>'Slovenščina' ),
	'sm'=>array( 'sm'=>"gagana fa'a Samoa" ),
	'sn'=>array( 'sn'=>'chiShona' ),
	'so'=>array( 'so'=>'af Soomaali' ),
	'sq'=>array( 'sq'=>'Shqip' ),
	'sr'=>array( 'sr'=>'Srpski' ),
	'ss'=>array( 'ss'=>'siSwati' ),
	'st'=>array( 'st'=>'seSotho' ),
	'su'=>array( 'su'=>'basa Sunda' ),
	'sv'=>array( 'sv'=>'Svenska' ),
	'sw'=>array( 'sw'=>'Kiswahili' ),
	'ta'=>array( 'ta'=>'தமிழ்' ),
	'te'=>array( 'te'=>'తెలుగు' ),
	'tg'=>array( 'tg'=>'زبان تاجکی' , 'dir'=>'rtl' ),
	'th'=>array( 'th'=>'ภาษาไทย' ),
	'ti'=>array( 'ti'=>'ትግርኛ' ),
	'tk'=>array( 'tk'=>'Türkmençe' ),
	'tl'=>array( 'tl'=>'Tagalog' ),
	'tn'=>array( 'tn'=>'Setswana' ),
	'to'=>array( 'to'=>'Faka-Tonga' ),
	'tr'=>array( 'tr'=>'Türkçe' ),
	'ts'=>array( 'ts'=>'xiTsonga' ),
	'tt'=>array( 'tt'=>'تاتارچا' , 'dir'=>'rtl' ),
	'tw'=>array( 'tw'=>'Twi' ),
	'ug'=>array( 'ug'=>'uyghur tili' ),
	'uk'=>array( 'uk'=>"Українська" ),
	'ur'=>array( 'ur'=>'اردو', 'dir'=>'rtl' ),
	'uz'=>array( 'uz'=>"Ўзбек (o'zbek)" ),
	'vi'=>array( 'vi'=>'Tiếng Việt' ),
	'vo'=>array( 'vo'=>"vad'd'a tšeel" ),
	'wo'=>array( 'wo'=>'Wollof' ),
	'xh'=>array( 'xh'=>'isiXhosa' ),
	'yi'=>array( 'yi'=>'ײִדיש' , 'dir'=>'rtl' ),
	'yo'=>array( 'yo'=>'Yorùbá' ),
	'za'=>array( 'za'=>'Sawcuengh' ),
	'zh'=>array( 'zh'=>'中文(简体)' , 'zh-cn'=>'中文(简体)' , 'zh-tw'=>'中文(國語)'  ),
	'zu'=>array( 'zu'=>'isiZulu' ),
	);
</pre>

 <span style="float:right"><a href="#top" title="Jump to the top">top</a></span>


h2(#credits). Credits.

Thanks go to Marios for making the initial plugin request and pledging support for the development. Destry also promised support very soon afterward.

Graeme provided v0.5 of what was then the gbp_l10n plugin which I have greatly extended (with his help). l10n MLP also uses his admin library to provide the tabbed admin interface.

 <span style="float:right"><a href="#top" title="Jump to the top">top</a></span>

<br />

-- _Stephen Dickinson_
November 2006.

</div>
# --- END PLUGIN HELP ---
-->
<?php
}
# --- BEGIN PLUGIN CODE ---

// require_plugin() will reset the $txp_current_plugin global
global $txp_current_plugin;
$l10n_current_plugin = $txp_current_plugin;
require_plugin('gbp_admin_library');
$txp_current_plugin = $l10n_current_plugin;

// Constants
if( !defined( 'L10N_PLUGIN_CONST' ))
	define('L10N_PLUGIN_CONST', 'plugin');
if( !defined( 'L10N_SEP' ))
	define( 'L10N_SEP' , '-' );
if( !defined( 'L10N_NAME' ))
	define( 'L10N_NAME' , 'l10n' );
if( !defined( 'L10N_PREFS_LANGUAGES' ))
	define( 'L10N_PREFS_LANGUAGES', $l10n_current_plugin.'_l10n-languages' );
if( !defined( 'L10N_ARTICLES_TABLE' ) )
	define( 'L10N_ARTICLES_TABLE' , 'l10n_articles' );
if( !defined( 'L10N_RENDITION_TABLE_PREFIX' ) )
	define( 'L10N_RENDITION_TABLE_PREFIX' , 'l10n_textpattern_' );
if( !defined( 'L10N_SNIPPET_IO_HEADER' ) )
	define( 'L10N_SNIPPET_IO_HEADER' , 'MDoibDEwbi1jbG9uZSI7czoxMjoi' );

if( !defined( 'L10N_SNIPPET_PATTERN' ) )
	define( 'L10N_SNIPPET_PATTERN' , "/##([\w|\.|\-]+)##/" );
global $txpcfg;

function _l10n_set_browse_language( $code , $long ,  $debug=false )
	{
	#
	#	Call this function with the SHORT language code.
	#
	#	Takes care of storing the global language variable and also tries to do extra stuff like
	#	setting up the correct locale for the requested language.
	#
	global $l10n_language;
	$result = false;

	if( $long )
		{
		$site_langs = LanguageHandler::get_installation_langs();
		$tmp = $code;
		}
	else
		{
		$site_langs = LanguageHandler::get_site_langs();
		$tmp = LanguageHandler::expand_code( $code );
		}

	if( $debug )
		echo br, "_l10n_set_browse_language( $code ) ... \$site_langs=", var_dump($site_langs),", \$tmp='$tmp'";

	if( isset( $tmp ) and in_array( $tmp , $site_langs ) )
		{
		if( $debug )
			echo " ... in IF() ... " ;
		$l10n_language = LanguageHandler::compact_code($tmp);

		if( empty( $l10n_language['long'] ) )
			$l10n_language['long'] = $tmp;

		$result = true;
		getlocale( $l10n_language['long'] );
		if( $debug )
			echo "\$tmp [$tmp] used to set \$l10n_language to " , var_dump($l10n_language['long']) , " returning TRUE", br ;
		}
	else
		{
		if( $debug )
			echo " ... in ELSE ... " ;
		if( !isset($l10n_language) or !in_array( $l10n_language['long'] , $site_langs ))
			{
			$l10n_language = LanguageHandler::compact_code( LanguageHandler::get_site_default_lang() );
			getlocale( $l10n_language['long'] );
			$result = (!empty($tmp));
			}
		}
	if( $debug )
		echo br , "Input='$code', Site Language set to " , var_dump( $l10n_language ) , " Returning ", var_dump($result),  br;

	return $result;
	}

function _l10n_process_url( $use_get_params=false )
	{
	global $l10n_language;

	$new_first_path = '';
	$debug = false;

	@session_start();
	$site_langs = LanguageHandler::get_site_langs();

	if (!defined('rhu'))
		define("rhu", preg_replace("/https?:\/\/.+(\/.*)\/?$/U", "$1", hu));
	$path = explode('/', trim(str_replace(trim(rhu, '/'), '', $_SERVER['REQUEST_URI']), '/'));

	$ssname = 'l10n_short_lang';
	$lsname = 'l10n_long_lang';
	if( $use_get_params )
		{
		#
		#	Admin session variables differ from public to stop crosstalk...
		#
		$ssname = 'l10n_admin_short_lang';
		$lsname = 'l10n_admin_long_lang';

		$temp = gps( 'adminlang' );
		$tmp = substr( $temp , 0 , 2 );

		#
		#	Admin side we use the installation languages, not just the more
		# restricive 'site' languages used on the public side...
		#
		$site_langs = LanguageHandler::get_installation_langs();
		if( !empty($temp) and in_array( $temp , $site_langs ) )
			{
			#
			#	Hit! We can serve this language...
			#
			$_SESSION[$ssname] = $tmp;
			$_SESSION[$lsname] = $temp;
			if( $debug )
				echo br , "L10N MLP: Set session vars ($ssname < $tmp) ($lsname < $temp).";
			}
		}

	if( !$use_get_params and !empty( $path ) )
		{
		if( $debug )
			echo br , "L10N MLP: Public - Checking URL ($path), LANG = " , LANG;
		#
		#	Examine the first path entry for the language request.
		#
		$tmp = array_shift( $path );
		$temp = LanguageHandler::expand_code( $tmp );
		$reduce_uri = true;
		$new_first_path = (isset($path[0])) ? $path[0] : '' ;

		if( !empty($temp) and in_array( $temp , $site_langs ) )
			{
			#
			#	Hit! We can serve this language...
			#
			if( $debug )
				echo br , "L10N MLP: Set session vars ($ssname < $tmp) ($lsname < $temp).";
			$_SESSION[$ssname] = $tmp;
			$_SESSION[$lsname] = $temp;
			}
		else
			{
			#
			#	Not a language this site can serve...
			#
			if( !LanguageHandler::is_valid_short_code( $tmp ) )
				{
				#
				#	And not a known language so don't reduce the uri and use
				# the original part of the path...
				#
				$reduce_uri = false;
				$new_first_path = $tmp;
				}
			}

		if( $reduce_uri )
			{
			$new_uri = '/' . join( '/' , $path );
			$_SERVER['REQUEST_URI'] = $new_uri;
			}
		}

	if( !isset($_SESSION[$ssname]) or empty($_SESSION[$ssname]) )
		{
		#
		#	If we are still missing a language for the session, try to get the prefered selection
		# from the user agent's HTTP header.
		#
		$req_lang = (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '' ;
		if( isset( $req_lang ) and !empty( $req_lang ) )
			{
			$chunks = split( ',' , $req_lang );
			if( count( $chunks ) )
				{
				foreach( $chunks as $chunk )
					{
					$info = split( ';' , $chunk );
					if( false === $info )
						{
						$info[] = $chunk;
						}
					$code = $info[0];
					if( $code )
						{
						$len = strlen( $code );
						if( $len === 2 )
							{
							$lang = LanguageHandler::expand_code( $info[0] );
							$lang = LanguageHandler::compact_code( $lang );
							}
						elseif( $len === 5 )
							$lang = LanguageHandler::compact_code( $info[0] );
						else
							continue;

						if( in_array( $lang['long'] , $site_langs ) )
							{
							$_SESSION[$ssname]  = $lang['short'];
							$_SESSION[$lsname] = $lang['long'];
							break;
							}
						}
					}
				}
			}
		}

	#
	#	If we are still missing a language for the session, use the site default...
	#
	if( !isset($_SESSION[$ssname]) or empty($_SESSION[$ssname]) )
		{
		$long = $site_langs[0];
		$short = substr( $long , 0 , 2 );
		$_SESSION[$ssname] = $short;
		$_SESSION[$lsname] = $long;
		}

	if( $use_get_params )
		_l10n_set_browse_language( $_SESSION[$lsname] , true , $debug );
	else
		_l10n_set_browse_language( $_SESSION[$ssname] , false , $debug );

	return $new_first_path;
	}



# -- Include the admin file only if needed...
if( @txpinterface === 'admin' )
	{
	include_once $txpcfg['txpath'].'/lib/l10n_base.php';

	global $l10n_language , $textarray , $prefs;
	global $l10n_view;

	#	Switch admin lang if needed...
	_l10n_process_url( true );
	if( LANG !== $l10n_language['long'] and LANG !== $l10n_language['short'] )
		{
		$textarray = load_lang( $l10n_language['long'] );
		$prefs['language'] = $l10n_language['long'];
		}

	#
	include_once $txpcfg['txpath'].'/lib/l10n_admin_classes.php';
	$l10n_view = new LocalisationView( 'l10n-localisation' , L10N_NAME, 'content' );

	#
	include_once $txpcfg['txpath'].'/lib/l10n_admin.php';
	if( gps( 'l10nfile' ) === 'mlp.js' )
		{
		ob_start();
		print _l10n_inject_js();
		exit;
		}
	}


# -- Public code section follows...
if (@txpinterface === 'public')
	{
	$installed = l10n_installed();
	if( !$installed )
		return '';

	include_once $txpcfg['txpath'].'/lib/l10n_base.php';

	global $prefs;
	$prefs['db_remap_tables_func'] = 'l10n_redirect_textpattern';
	$prefs['db_remap_fields_func'] = 'l10n_remap_fields';

	# register a routine to handle URLs until the permanent_links plugin is integrated.
	register_callback( '_l10n_pretext' 					, 'pretext' );
	register_callback( '_l10n_textpattern_comment_submit'	, 'textpattern' );
	register_callback( '_l10n_tag_feeds'					, 'rss_entry' );
	register_callback( '_l10n_tag_feeds'					, 'atom_entry' );

	function _l10n_tag_feeds()
		{
		global $l10n_language , $thisarticle;

		$syndicate_body_or_excerpt = $GLOBALS['prefs']['syndicate_body_or_excerpt'];

		$dir = LanguageHandler::get_lang_direction_markup( $l10n_language['short'] );
		$content = $thisarticle['body'];
		$summary = $thisarticle['excerpt'];

		if ($syndicate_body_or_excerpt)
			{
			# short feed: use body as summary if there's no excerpt
			if( !trim($summary) )
				$summary = $content;
			$content = '';
			}

		$thisarticle['excerpt'] = tag( $summary , 'div' , $dir );
		$thisarticle['body']    = (!empty($content)) ? tag( $content , 'div' , $dir ) : '';
		}

	function _l10n_textpattern_comment_submit()
		{
		global $pretext, $l10n_language;

		#
		#	Detect comment submission and update master textpattern table...
		#
		$commented = gps( 'commented' );
		if( $commented === '1' )
			{
			$id = isset($pretext['id']) ? $pretext['id'] : '' ;
			if( !empty($id) )
				{
				$thecount = safe_field('count(*)','txp_discuss','parentid='.doSlash($id).' and visible='.VISIBLE);

				#
				#	Update the l10n master table (which simply maps to the underlying 'textpattern' table)...
				#
				$updated = safe_update('l10n_master_textpattern',"comments_count='".doSlash($thecount)."'","ID='".doSlash($id)."'");
				}
			}
		}

	function _l10n_pretext()
		{
		function load_localised_pref( $name )
			{
			global $prefs,$pretext;
			$k = "snip-$name";
			$r = gTxt( $k );
			if( $r !== $k )
				{
				$GLOBALS[$name] = $r;
				$GLOBALS['prefs'][$name] = $r;
				$prefs[$name] = $r;
				$pretext[$name] = $r;
				}
			}
		global $l10n_language , $textarray , $prefs;

		$first_chunk = _l10n_process_url();

		#
		#	Now we know what language this user is browsing in.
		# If it is NOT the site's currently selected language then we need to re-load
		# the textarray with the right language (otherwise some strings used in comment forms
		# and older/newer tags will be wrong!
		#
		if( LANG !== $l10n_language['long'] and LANG !== $l10n_language['short'] )
			{
			trace_add( "L10N MLP: Switching to {$l10n_language['long']} from " . LANG );
			$textarray = load_lang( $l10n_language['long'] );
			$prefs['language'] = $l10n_language['long'];
			}

		load_localised_pref( 'site_slogan' );
		@$GLOBALS['prefs']['comments_default_invite'] = gTxt('comment');

		#
		#	Don't know why, but there seems to be some whitespace getting into the
		# output buffer. XHTML can cope but it causes a parse error in the feed xml
		#
		#	Simple solution is to make sure the output buffer is empty before
		# continuing the processing of rss or atom requests...
		#
		$ob_cleaning = array( 'rss' , 'atom' );
		if( in_array( $first_chunk , $ob_cleaning) )
			while( @ob_end_clean() );
		}
	function _l10n_get_article_members( $article_id , $exclude_lang , $status='4' )
		{
		#
		#	Returns an array of the lang->rendition mappings for all members of the
		# given article...
		#
		$result = array();
		$where = "`Group`='$article_id' and `Status` >= '$status' and `Lang`<>'$exclude_lang'";
		$rows = safe_rows_start( 'ID,Title,Lang' , 'l10n_master_textpattern' , $where );
		if( count( $rows ) )
			{
			while( $row = nextRow($rows) )
				{
				$lang = $row['Lang'];
				$result[$lang] = array( 'id' => $row['ID'] , 'title' => escape_title($row['Title']) );
				}
			}
		return $result;
		}
	function _l10n_get_alternate_mappings( $rendition_id , $exclude_lang , $use_master=false )
		{
		if( $use_master )
			$info = safe_row( '`Group`' , 'l10n_master_textpattern' , "`ID`='$rendition_id'" );
		else
			$info = safe_row( '`Group`' , 'textpattern' , "`ID`='$rendition_id'" );
		if( empty($info) )
			{
			return $info;
			}

		$article_id = $info['Group'];
		$alternatives = _l10n_get_article_members( $article_id , $exclude_lang );
		return $alternatives;
		}

	function _l10n_substitute_snippets( &$thing )
		{
		/*
		Replaces all snippets within the contained block with their text from the global textarray.
		Allows TxP devs to include snippets* in their forms and page templates.
		*/
		$out = preg_replace_callback( 	L10N_SNIPPET_PATTERN ,
										create_function(
							           '$match',
								       'global $l10n_language;
										global $textarray;
										if( $l10n_language )
											$lang = $l10n_language[\'long\'];
										else
											$lang = "??";
										$snippet = strtolower($match[1]);
										if( array_key_exists( $snippet , $textarray ) )
											$out = $textarray[$snippet];
										else
											$out = "($lang)$snippet";
										return $out;'
									), $thing );
		return $out;
		}


	/*
	TAG HANDLERS FOLLOW
	*/
	function l10n_lang_list( $atts )
		{
		global $thisarticle , $l10n_language, $is_article_list , $pretext, $prefs;

		extract(lAtts(array(
							'title'				=> '',					#	Title will be prepended as a paragraph.
							'on404'				=> '', 					#	Set this to non-blank to force special 404 processing
							'current_class'		=> 'l10n_current',		#	Literal class markup for the current language
							'language_class'	=> 'long',				#	How the class of the list item is marked up
																		#	'long' => long lang eg: en-gb | 'short' eg. 'en'
							'list_class'		=> 'l10n_lang_list',	#	Literal class markup for entire list
							'show_empty'  		=> '',					#	show all langs, even ones with no translation?
							'link_current'		=> '',					#	make the current language an active hyperlink?
							'display'			=> 'native',			# 	How the language is displayed on the web page
																		#	'native++' | 'native+' | 'native' | 'long' | 'short'
							'article_list' 		=> $is_article_list,	#	Set to '1' to always output a site-wide list in this location
							),$atts));

		$on404			= !empty($on404);	# User marked this list as a 404 special lookup list.
		$show_empty		= !empty($show_empty);
		$link_current	= !empty($link_current);

		$processing404	= ($pretext['status'] === '404');

		$list = array();
		static $alangs;
		$slangs = LanguageHandler::get_site_langs();
		$section = empty($pretext['s']) ? '' : $pretext['s'];
		$id = $pretext['id'];
		$url = trim($_SERVER['REQUEST_URI'] , '/');
		$parts = chopUrl($url);

		//echo br , "l10n_lang_list(" , var_dump($atts) , ") Section($section) ID($id)" ;
		//echo br , "url = " , $_SERVER['REQUEST_URI'];
		//echo br , "parts = " , var_dump( $parts );

		if( $on404 )
			{
			#
			#	Find the section and id of the faulting article (if possible)...
			#
			if( empty($id) )
				$id = gps('id');	# Try out a messy match first

			if( empty($id) )		# Try matching based on the standard permlink schemes...
				{
				extract( $parts );
				//echo br , 'permlink_mode = ' , $prefs['permlink_mode'];
				switch($prefs['permlink_mode'])
					{
					case 'section_id_title':
						$id = $u1;
						break;

					case 'year_month_day_title':
						$when = "$u0-$u1-$u2";
						$rs = safe_row("ID,Section","l10n_master_textpattern",	"posted like '".doSlash($when)."%' and url_title like '".doSlash($u3)."' and Status >= 4 limit 1");
						$id = (!empty($rs['ID'])) ? $rs['ID'] : '';
						break;

					case 'section_title':
						$rs = safe_row("ID,Section",'l10n_master_textpattern',"url_title like '".doSlash($u1)."' AND Section='".doSlash($u0)."' and Status >= 4 limit 1");
						$id = @$rs['ID'];
						break;

					case 'title_only':
						$rs = safe_row("ID",'l10n_master_textpattern',"url_title like '".doSlash($u0)."' and Status >= 4 limit 1");
						$id = @$rs['ID'];
						break;

					case 'id_title':
						$id = $u0;
						break;
					}
				}

			if( !empty($id) and is_numeric($id) )
				{
				$article_list = false;
				}
			else
				{
				return '';
				}
			#
			#	Make sure we show all alternatives, even if they are in the current language...
			#
			$link_current = true;
			}

		$show_title = !empty( $title );

		if( !$article_list )
			{
			if( !isset( $alangs ) or !is_array( $alangs ) )
				$alangs = _l10n_get_alternate_mappings( $id , 'nothing' , true );

			//echo br , 'alangs = ' , var_dump( $alangs );

			if( $show_title )
				$show_title = !empty( $alangs );
			}

		if( $show_title )
			$title = tag( $title , 'p' ) . n;
		else
			$title = '';

		foreach( $slangs as $lang )
			{
			$codes = LanguageHandler::compact_code($lang);
			$short = $codes['short'];
			$long  = $codes['long'];
			$dir   = LanguageHandler::get_lang_direction_markup($lang);

			switch( $display )
				{
				case 'short':
					$lname = $short;
					break;
				case 'long':
					$lname = $long;
					break;
				case 'native+':
					$lname = LanguageHandler::get_native_name_of_lang( $lang )." [$short]";
					break;
				case 'native++':
					$lname = LanguageHandler::get_native_name_of_lang( $lang )." [$long]";
					break;
				default:
					$lname = LanguageHandler::get_native_name_of_lang( $lang );
					break;
				}

			if( $article_list )
				{
				#
				#	No individual ID but we should be able to serve all the languages
				# so use the current url and inject the language component into each one...
				#
				$current = ($l10n_language['long'] === $lang);
				$text    = tag( $lname , 'span' , $dir);

				#
				#	Prep the line class...
				#
				$class = ('short'===$language_class) ? $short : $lang ;
				if( $current )
					$class .= ' '.$current_class;
				$class = ' class="'.$class.'"';

				if( !$current or $link_current )
					{
					$uri = rtrim( $_SERVER['REQUEST_URI'] , '/' );
					if( $processing404 )
						$uri = '';
					$line = '<a href="'.hu.$short.$uri.'">'.$text.'</a>';
					}
				else
					$line = $text;

				$list[] = tag( $line , 'li' , $class );
				}
			else
				{
				#
				#	If a translation exists for that language then we
				# build a valid url to it and make it active in the list, otherwise include it in the
				# list but wihtout the hyper-link.
				#
				#	The active page is marked up with a css class.
				#
				if( array_key_exists( $lang , $alangs ) )
					{
					$record = $alangs[$lang];
					$lang_rendition_title	= $record['title'];
					$lang_rendition_id		= $record['id'];
					$current 	= ($l10n_language['long'] === $lang);
					$text		= $lname;
					if( $processing404 )
						$text	= strong($text) . sp . ':' . sp . $lang_rendition_title;
					$text   	= tag( $text , 'span' , $dir);

					#
					#	Prep the line class...
					#
					$class = ('short'===$language_class) ? $short : $lang ;
					if( $current )
						$class .= ' '.$current_class;
					$class = ' class="'.$class.'"';

					if( !$current or $link_current )
						{
						#
						#	Use messy urls to avoid permlink pattern processing...
						#
						$line = '<a href="'.hu.$short.'/?id='.$lang_rendition_id.'">'.$text.'</a>';
						}
					else
						$line = $text;

					$list[] = tag( $line , 'li' , $class );
					}
				else
					{
					if( $show_empty )
						$list[] = tag( $lname , 'li' );
					}
				}
			}


		$list = tag( join( "\n\t" , $list ) , 'ul' , " class=\"$list_class\"" );
		return $title . $list;
		}

	function l10n_if_lang( $atts , $thing )
	    {
		/*
		Basic markup tag. Use this to wrap blocks of content you only want to appear
		when the specified language is set or if the direction of the selected language matches
		what you want. (Output different css files for rtl layouts for example).
		*/
		global $l10n_language;
		$out = '';

		if( !$l10n_language )
			return $out;

		extract(lAtts(array(
							'lang' => $l10n_language['short'] ,
							'dir'  => '',
							'wraptag' => 'div' ,
							),$atts));

		if( !empty($dir) and in_array( $dir , array( 'rtl', 'ltr') ) )
			{
			#	Does the direction of the currently selected site language match that requested?
			#	If so, parse the contained content.
			if( $dir == LanguageHandler::get_lang_direction( $l10n_language['short'] ) )
				$out = parse($thing) . n;
			}
		elseif( $lang == $l10n_language['short'] or $lang == $l10n_language['long'] )
			{
			#	If the required language matches the site language, output a suitably marked up block of content.
			$dir = LanguageHandler::get_lang_direction_markup( $lang );
			$out = "<$wraptag lang=\"$lang\"$dir/>" . parse($thing) . "</$wraptag>" . n;
			}

		return $out;
	    }

	function l10n_get_lang( $atts )
		{
		/*
		Outputs the current language. Use in page/forms to output the language needed by the doctype/html decl.
		*/
		global $l10n_language;

		extract( lAtts( array(
								'type'=>'short' , # valid values = 'long','short','native'
								) , $atts ) );

		if( !$l10n_language )
			return '';

		$type = strtolower( $type );
		switch( $type )
			{
			case 'native' :
				$result = LanguageHandler::get_native_name_of_lang( $l10n_language['long'] );
				break;
			case 'long' :
				$result = $l10n_language['long'];
				break;
			case 'short' :
			default :
				$result = $l10n_language['short'];
				break;
			}

		return $result;
		}

	function _l10n_feed_link_cb( $matches )
		{
		global $l10n_feed_link_lang;

		#
		#	$matches[0] is the entire pattern...
		#	$matches[1] is the href...
		#
		$path = trim( str_replace( trim(hu, '/'), '', $matches[1] ), '/' );
		$path = $l10n_feed_link_lang['short'].'/'.$path;
		$path = ' href="'.hu.$path.'"';
		return $path;
		}

	function l10n_feed_link( $atts )
		{
		global $l10n_language, $l10n_feed_link_lang;
		$l10n_feed_link_lang = $l10n_language;

		if( isset($atts['code']) )
			{
			$code = $atts['code'];
			unset( $atts['code'] );

			if( $code === 'none' )
				return feed_link( $atts );

			$l10n_feed_link_lang = LanguageHandler::compact_code( $code );
			}

		#
		#	Get the standard result...
		#
		$result = feed_link( $atts );

		#
		#	Inject the language code into the url...
		#
		$pattern = '/ href="(.*)" /';
		$result = preg_replace_callback( $pattern , '_l10n_feed_link_cb' , $result );

		return $result;
		}

	function l10n_get_lang_dir( $atts )
		{
		/*
		Outputs the direction (rtl/ltr) of the current language.
		Use in page/forms to output the direction needed by xhtml elements.
		*/
		global $l10n_language;

		extract( lAtts( array( 'type'=>'short' ) , $atts ) );

		if( !$l10n_language )
			$lang = LanguageHandler::compact_code( LanguageHandler::get_site_default_lang() );
		else
			$lang = $l10n_language;

		$dir = LanguageHandler::get_lang_direction( $lang[$type] );
		return $dir;
		}


	function _l10n_link_lang_cb( $matches )
		{
		global $l10n_language;

		$external = ( rtrim( $matches[1] , '/') !== rtrim( hu , '/') );

		$result = $matches[0];
		if( !$external )
			{
			$has_lang_code = LanguageHandler::is_valid_short_code( trim( $matches[2] , '/' ) );
			if( !$has_lang_code )
				{
				$result = rtrim( $matches[1] . '/' . $l10n_language['short'] . $matches[2] . $matches[3] , '/' );
				$result = ' href="'. $result . '"';
				}
			}

		return $result;
		}

	function l10n_localise($atts, $thing = '')
		{
		global $l10n_language;

		if ($l10n_language)
			{
			if ($thing)
				{
				# Process the direct snippet substitutions needed in the contained content.
				$thing = _l10n_substitute_snippets( $thing );
				$html = parse($thing);

				#
				#	Process all the urls on 'pages' and insert the correct language
				# marker (if needed)...
				#
				if( array_key_exists( 'page' , $atts) )
					{
					# Insert the language code into all permlinks...
					$pattern = '/ href="(https?:\/\/[\w|\.|\-|\_]*)(\/[\w|\-|\_]*)([\w|\/|\_|\?|\=|\-|\#|\%]*)"/';
					$html = preg_replace_callback( $pattern , '_l10n_link_lang_cb' , $html );
					}

				return $html;
				}
			}

		return null;
		}
	}

# --- END PLUGIN CODE ---
?>
