﻿<?php

// This is a PLUGIN TEMPLATE.

// Copy this file to a new name like abc_myplugin.php.  Edit the code, then
// run this file at the command line to produce a plugin for distribution:
// $ php abc_myplugin.php > abc_myplugin-0.1.txt

// Plugin name is optional.  If unset, it will be extracted from the current
// file name. Uncomment and edit this line to override:
# $plugin['name'] = 'abc_plugin';

$plugin['version'] = '0.5';
$plugin['author'] = 'Graeme Porteous';
$plugin['author_uri'] = 'http://porteo.us/projects/textpattern/gbp_l10n/';
$plugin['description'] = 'Textpattern content localization.';
$plugin['type'] = '1';

@include_once('../zem_tpl.php');

if (0) {
?>
# --- BEGIN PLUGIN HELP ---
h1. Instructions

Under the content tab, there is a new localisation subtab. Here you can find a list of every article, category title and section titles which needs tobe localised.

To see your localised content you need to surround *everything* in all of your page and form templates with @<txp:gbp_localize>@ ... @</txp:gbp_localize>@

You can also use @<txp:gbp_localize section="foo" />@ or @<txp:gbp_localize category="bar" />@ to output localised sections and categories
# --- END PLUGIN HELP ---
<?php
}
# --- BEGIN PLUGIN CODE ---

// Constants
if (!defined('gbp_language'))
	define('gbp_language', 'language');
if (!defined('gbp_plugin'))
	define('gbp_plugin', 'plugin');

// require_plugin() will reset the $txp_current_plugin global
global $txp_current_plugin;
$gbp_current_plugin = $txp_current_plugin;
require_plugin('gbp_admin_library');
$txp_current_plugin = $gbp_current_plugin;

if( !defined( 'GBP_PREFS_LANGUAGES' ))
	define( 'GBP_PREFS_LANGUAGES', $gbp_current_plugin.'_languages' );

class LocalizationView extends GBPPlugin {

	var $gp = array(gbp_language);
	var $preferences = array(
		'languages' => array('value' => array('fr', 'de'), 'type' => 'gbp_array_text'),

		'articles' => array('value' => 1, 'type' => 'yesnoradio'),
		'article_vars' => array('value' => array('Title', 'Body', 'Excerpt'), 'type' => 'gbp_array_text'),
		'article_hidden_vars' => array('value' => array('textile_body', 'textile_excerpt'), 'type' => 'gbp_array_text'),

		'categories' => array('value' => 1, 'type' => 'yesnoradio'),
		'category_vars' => array('value' => array('title'), 'type' => 'gbp_array_text'),
		'category_hidden_vars' => array('value' => array(), 'type' => 'gbp_array_text'),

		// 'links' => array('value' => 0, 'type' => 'yesnoradio'),
		// 'link_vars' => array('value' => array('linkname', 'description'), 'type' => 'gbp_array_text'),
		// 'link_hidden_vars' => array('value' => array(), 'type' => 'gbp_array_text'),

		'sections' => array('value' => 1, 'type' => 'yesnoradio'),
		'section_vars' => array('value' => array('title'), 'type' => 'gbp_array_text'),
		'section_hidden_vars' => array('value' => array(), 'type' => 'gbp_array_text'),

		'plugins'	=> array('value' => 1, 'type' => 'yesnoradio'),
	
	);

	function preload() {

		global $gbp, $txp_current_plugin, $_GBP;
		$gbp[$txp_current_plugin] = &$this;
		$_GBP[0] = &$this;

		if ($this->preferences['articles']['value'])
			new LocalisationTabView('articles', 'article', $this);
		if ($this->preferences['categories']['value'])
			new LocalisationTabView('categories', 'category', $this);
		// if ($this->preferences['links']['value'])
		// 	new LocalisationTabView('links', 'link', $this);
		if ($this->preferences['sections']['value'])
			new LocalisationTabView('sections', 'section', $this);
		if ($this->preferences['plugins']['value'])
			new LocalisationTabView('plugins', 'plugin', $this);
		new GBPPreferenceTabView('preferences', 'preference', $this);

		$sql[] = 'CREATE TABLE IF NOT EXISTS `'.PFX.'gbp_l10n` (';
		$sql[] = '`id` int(11) NOT NULL AUTO_INCREMENT, ';
		$sql[] = '`table` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , ';
		$sql[] = '`language` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , ';
		$sql[] = '`entry_id` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci default NULL, ';
		$sql[] = '`entry_column` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci default NULL, ';
		$sql[] = '`entry_value` text CHARACTER SET utf8 COLLATE utf8_general_ci, ';
		$sql[] = '`entry_value_html` text CHARACTER SET utf8 COLLATE utf8_general_ci, ';
		$sql[] = 'PRIMARY KEY (`id`)';
		$sql[] = ') TYPE=MyISAM PACK_KEYS=1 AUTO_INCREMENT=1';

		safe_query(join('', $sql));
	}

	function main() {

		foreach ($this->preferences['languages']['value'] as $key)
			$languages['value'][$key] = gTxt($key);

		if (!gps(gbp_language))
			$_GET[gbp_language] = $this->preferences['languages']['value'][0];

		setcookie(gbp_language, gps(gbp_language), time() + 3600 * 24 * 365);

		$out[] = '<div style="padding-bottom: 3em; text-align: center; clear: both;">';
		$out[] = form(
			fLabelCell('Language: ').
			selectInput(gbp_language, $languages['value'], gps(gbp_language), 0, 1).
			'<br /><a href="'.hu.gps(gbp_language).'/">view localised site</a>'.
			$this->form_inputs()
		);
		$out[] = '</div>';

		echo join('', $out);
	}
}

class LocalisationTabView extends GBPAdminTabView {

	function preload() {

		$step = gps('step');
		if( $step )
			{
			switch( $step )
				{
				case 'gbp_save':				$this->save_post();
												break;
												
				# Called to save the stringset the user has been editing.
				case 'gbp_save_strings' : 		$this->save_strings();
												break;
												
				# Called if the user chooses to delete the string set for a removed plugin.
				case 'gbp_remove_stringset' :	$this->remove_strings();
												break;

				# Called if the user chooses to remove a specific languages' strings.
				# eg if they entered some french translations but later drop french from the site.
				case 'gbp_remove_languageset' :	$this->remove_strings();
												break;
				}
			}
	}

	function main() {

		switch ($this->event)
		{
			case 'article':
				if ($id = gps(gbp_id))
					$this->render_edit($this->parent->preferences['article_vars']['value'], $this->parent->preferences['article_hidden_vars']['value'], 'textpattern', "id = '$id'", $id);
				$this->render_list('ID', 'Title', 'textpattern', '1 order by Title asc');
			break;
			case 'category':
				if ($id = gps(gbp_id))
					$this->render_edit($this->parent->preferences['category_vars']['value'], $this->parent->preferences['category_hidden_vars']['value'], 'txp_category', "id = '$id'", $id);
				$this->render_list('id', 'title', 'txp_category', "name != 'root' order by title asc");
			break;
			// case 'link':
			// 	if ($id = gps(gbp_id))
			// 		$this->render_edit($this->parent->preferences['link_vars']['value'], $this->parent->preferences['link_hidden_vars']['value'], 'txp_link', "id = '$id'", $id);
			// 	$this->render_list('id', 'linkname', 'txp_link', '1 order by linkname asc');
			// break;
			case 'section':
				if ($id = gps(gbp_id))
					$this->render_edit($this->parent->preferences['section_vars']['value'], $this->parent->preferences['section_hidden_vars']['value'], 'txp_section', "name = '$id'", $id);
				$this->render_list('name', 'title', 'txp_section', "name != 'default' order by name asc");
			break;
			case 'plugin':
				$this->render_plugin_list();
				if ($plugin = gps(gbp_plugin))
					{
					$id = gps(gbp_id);
					$this->render_plugin_string_list( $plugin , $id );
					if( $id )
						$this->render_edit_string( $plugin , $id );
					}
			break;
		}
	}

	function render_list($key, $value, $table, $where) {

		$out[] = '<div style="float: left; width: 50%;" class="gbp_i18n_list">';

		// SQL used in both queries
		$sql = "FROM ".PFX."$table AS source, ".PFX."gbp_l10n AS l10n WHERE source.$key = l10n.entry_id AND l10n.entry_value != '' AND l10n.table = '".PFX."$table' AND l10n.language = '".gps(gbp_language)."' AND $where";

		// Localised
		$rows = startRows("SELECT DISTINCT source.$key as k, source.$value as v ".$sql);
		if ($rows) {

			$out[] = '<ul><h3>'.gTxt('gbp_l10n_localised').'</h3>';
			while ($row = nextRow($rows))
				$out[] = '<li><a href="'.$this->parent->url().'&#38;'.gbp_id.'='.$row['k'].'">'.$row['v'].'</a></li>';

			$out[] = '</ul>';
		}

		// Unlocalised
		$rows = startRows("SELECT DISTINCT $key as k, $value as v FROM ".PFX."$table WHERE $key NOT IN (SELECT DISTINCT source.$key $sql) AND $where");
		if ($rows) {

			$out[] = '<ul><h3>'.gTxt('gbp_l10n_unlocalised').'</h3>';
			while ($row = nextRow($rows))
				$out[] = '<li><a href="'.$this->parent->url().'&#38;'.gbp_id.'='.$row['k'].'">'.$row['v'].'</a></li>'.n;

			$out[] = '</ul>';
		}

		$out[] = '</div>';
		echo join('', $out);
	}

	function render_edit($vars, $hidden_vars, $table, $where, $entry_id) {
		global $_GBP;

		$fields = trim(join(',', array_merge($vars, $hidden_vars)), ' ,');

		if ($rs1 = safe_row($fields, $table, $where)) {
			$out[] = '<div style="float: right; width: 50%;" class="gbp_l10n_edit">';

			foreach($rs1 as $field => $value) {

				$rs2 = safe_row(
					'id, entry_value',
					'gbp_l10n',
					"`language` = '".gps(gbp_language)."' AND `entry_id` = '$entry_id' AND `entry_column` = '$field' AND `table` = '".PFX."$table'"
				);

				$field_type = mysql_field_type(mysql_query("SELECT $field FROM ".PFX.$table), 0);

				if ($rs2)
					extract($rs2);

				if (!isset($entry_value))
					$entry_value = '';

				if (in_array($field_type, array('blob'))) {

					$out[] = '<p class="gbp_l10n_field">'.ucwords($field).'</p>';
					$out[] = '<div class="gbp_l10n_value_disable">'.text_area('" readonly class="', 200, 420, $value).'</div>';
					$out[] = '<div class="gbp_l10n_value">'.text_area($field, 200, 420, $entry_value).'</div>';

				} else if (in_array($field_type, array('string'))) {

					$out[] = '<p class="gbp_l10n_field">'.ucwords($field).'</p>';
					$out[] = '<div class="gbp_l10n_value_disable">'.fInput('text', '', $value, 'edit" readonly title="', '', '', 60).'</div>';
					$out[] = '<div class="gbp_l10n_value">'.fInput('text', $field, $entry_value, 'edit', '', '', 60).'</div>';

				} else
					$out[] = hInput($field, $value);
			}

			$out[] = '<div class="gbp_l10n_form_submit">'.fInput('submit', '', gTxt('save'), '').'</div>';
			$out[] = '</div>';

			$out[] = $this->parent->form_inputs();
			$out[] = sInput(((isset($id)) || (gps('step') == 'gbp_save')) ? 'gbp_save' : 'gbp_post');

			$out[] = hInput('gbp_table', $table);
			$out[] = hInput(gbp_language, gps(gbp_language));
			$out[] = hInput(gbp_id, $entry_id);

			echo form(join('', $out));
		}
	}

	function save_post() {

		global $txpcfg;
		extract(get_prefs());

		extract(gpsa($this->parent->preferences[$this->event.'_hidden_vars']['value']));
		$vars = gpsa($this->parent->preferences[$this->event.'_vars']['value']);

		$table = PFX.$_POST['gbp_table'];
		$language = $_POST[gbp_language];
		$entry_id = $_POST[gbp_id];

		include_once $txpcfg['txpath'].'/lib/classTextile.php';
		$textile = new Textile();

		foreach($vars as $field => $value) {

			if ($field == 'Body') {

				if (!isset($textile_body))
				$textile_body = $use_textile;

				if ($use_textile == 0 or !$textile_body)
					$value_html = trim($value);

				else if ($use_textile == 1)
					$value_html = nl2br(trim($value));

				else if ($use_textile == 2 && $textile_body)
					$value_html = $textile -> TextileThis($value);

			}

			if ($field == 'Title')
				$value = $textile->TextileThis($value, '', 1);

			if ($field == 'Excerpt') {

				if (!isset($textile_excerpt))
					$textile_excerpt = 1;

				if ($textile_excerpt) {
					$value_html = $textile -> TextileThis($value);
				} else {
					$value_html = $textile -> TextileThis($value, 1);
				}
			}

			if (!isset($id))
				$id = '';

			if (!isset($value_html))
				$value_html = '';

			if (phpversion() >= "4.3.0") {

				$value = mysql_real_escape_string($value);
				$value_html = mysql_real_escape_string($value_html);

			} else {

				$value = mysql_escape_string($value);
				$value_html = mysql_escape_string($value_html);
			}

			switch(gps('step'))
			{
				case 'gbp_post':
					$rs = safe_insert('gbp_l10n', "`id` = '$id', `table` = '$table', `language` = '$language', `entry_id` = '$entry_id', `entry_column` = '$field', `entry_value` = '$value', `entry_value_html` = '$value_html'");
				break;
				case 'gbp_save':
					$rs = safe_update('gbp_l10n', "`entry_value` = '$value', `entry_value_html` = '$value_html'",
						"`table` = '$table' AND `language` = '$language' AND `entry_id` = '$entry_id' AND `entry_column` = '$field'"
					);
				break;
			}
		}
	}

	/* ----------------------------------------------------------------------------
	Additional methods follow...
	---------------------------------------------------------------------------- */
	public static function insert_strings()
		{
		/*
		Adds this class' own strings to the string store. Add any strings you want to be able to localise for this
		plugin.
		*/
		$strings = array(
'gbp_l10n_delete_plugin'		=> 'This will remove ALL strings for this plugin.',
'gbp_l10n_explain_extra_lang'	=> '<p>* These languages are not specified in the site preferences.</p><p>If they are not needed for your site you can delete them.</p>',
'gbp_l10n_lang_remove_warning'	=> 'This will remove ALL plugin strings/snippets in $var1. ',
'gbp_l10n_localised'			=> 'Localised',
'gbp_l10n_missing'				=> ' missing.', 
'gbp_l10n_no_plugin_heading'	=> 'Notice&#8230;',
'gbp_l10n_orphans'				=> 'Orphans (Unused snippets.)',
'gbp_l10n_plugin_not_installed'	=> '<strong>*</strong> These plugins have registered strings but are not installed.<br/><br/>If you have removed the plugin and will not be using it again, you can strip the strings out.',
'gbp_l10n_registered_plugins'	=> 'Registered Plugins.' ,
'gbp_l10n_remove_plugin'		=> "This plugin is not installed.<br/><br/>If this plugin's strings are no longer needed you can remove them.",
'gbp_l10n_strings'				=> ' strings.',
'gbp_l10n_summary'				=> 'Language Stats.',
'gbp_l10n_textbox_title'		=> 'Type in the text here.',
'gbp_l10n_translations_for'		=> 'Translations for ',
'gbp_l10n_unlocalised'			=> 'Unlocalised',
);
		gbp_l10n_string_handler::InsertStringsForLang( $strings , 'en' , 'admin' );
		}

	function render_edit_string( $plugin ,$name )
		{
		/*
		Render the edit controls for all localizations of the chosen string.
		*/
		$out[] = '<div style="float: right; width: 50%;" class="gbp_i18n_values_list">';
		$out[] = '<h3>'.gTxt('gbp_l10n_translations_for').$name.'</h3>'.n.'<form action="index.php" method="post"><dl>';
		
		$string_event = '';
		$x = gbp_l10n_string_handler::GetStringSet( $name );
		$final_codes = array();
		if( count($x) )
			{
			#	Complete the set with any missing language codes and empty data...
			$lang_codes = gbp_l10n_language_handler::GetSiteLangs();
			foreach($lang_codes as $code)
				{
				if( array_key_exists( $code , $x ) )
					continue;
				$x[ $code ] = array( 'id'=>'', 'event'=>'', 'data'=>'' );
				}
			ksort( $x );
			foreach( $x as $code => $data )
				{
				$final_codes[] = $code;
				if( empty($string_event) and $data['event'] != $string_event )
					$string_event = $data['event'];
				$lang = gbp_l10n_language_handler::GetNativeNameOfLang($code);

				$out[] = '<dt>'.$lang.' ('.$code.').'.((empty($data['data'])) ? ' *' . gTxt('gbp_l10n_missing') : '' ).'</dt>';
				$out[] = '<dd><p>'.
							'<textarea name="' . $code . '-data" cols="60" rows="1" title="' . 
							gTxt('gbp_l10n_textbox_title') . '">' . $data['data'] . '</textarea>' .
							hInput( $code.'-id' , $data['id'] ) . 
							'</p></dd>';
				}
			}
		else{
			$out[] = '<li>'.gTxt('none').'</li>'.n;
			}
		
		$out[] = '</dl>';
		$out[] = '<div class="gbp_l10n_form_submit">'.fInput('submit', '', gTxt('save'), '').'</div>';
		$out[] = sInput('gbp_save_strings');
		$out[] = $this->parent->form_inputs();
		$out[] = hInput('codes', trim( join( ',' , $final_codes ) , ', ' ) );
		$out[] = hInput(gbp_language, gps(gbp_language));
		$out[] = hInput(gbp_plugin, $plugin);
		$out[] = hInput('string_event', $string_event);
		$out[] = hInput(gbp_id, $name);
		$out[] = '</form></div>';
		echo join('', $out);
		}
	
	function render_plugin_string_list( $plugin , $string_name )
		{
		/*
		Show all the strings and localizations for the given plugin.
		*/
		$stats 			= array();
		$strings 		= gbp_l10n_string_handler::GetPluginStrings( $plugin , $stats );
		$strings_exist 	= ( count( $strings ) > 0 );
		if( !$strings_exist )
			return '';

		$site_langs 	= gbp_l10n_language_handler::GetSiteLangs();

		$out[] = '<div style="float: left; width: 25%;" class="gbp_i18n_plugin_list">';
		$out[] = '<h3>'.$plugin.gTxt('gbp_l10n_strings').'</h3>'.n;	
		$out[] = '<ol>';
		if( $strings_exist )
			{
			$complete_langs = gbp_l10n_string_handler::GetFullLangsString();
			foreach( $strings as $string=>$langs )
				{
				$complete = ($complete_langs === $langs);
				$guts = $string . ' ['.$langs.']';
				if( !$complete )
					$guts = '<strong>'. $guts . '</strong>';
				$out[]= '<li><a href="'.$this->parent->url().'&#38;'.gbp_plugin.'='.$plugin.'&#38;'.gbp_id.'='.$string.'">' . 
						$guts .
						'</a></li>';
				}
			}
		else{
			$out[] = '<li>'.gTxt('none').'</li>'.n;
			}
		$out[] = '</ol>';
		$out[] = '</div>';
		
		if( empty( $string_name ) )
			{
			//	Render default view details in right hand pane...
			//
			$out[] = '<div style="float: right; width: 50%;" class="gbp_i18n_values_list">';

			//
			//	Render stats summary for the strings...
			//
			$out[] = '<h3>'.gTxt('gbp_l10n_summary').'</h3>'.n;
			$out[] = '<ul>';
			$extras_found = false;
			foreach( $stats as $iso_code=>$count )
				{
				$name = gbp_l10n_language_handler::GetNativeNameOfLang( $iso_code );
				$guts = $count . ' ' . $name;
				$remove = '';
				if( !in_array( $iso_code , $site_langs ) )
					{
					$extras_found = true;
					$remove[] = '<span class="gbp_l10n_form_submit">'.fInput('submit', '', gTxt('delete'), '').'</span>';
					$remove[] = sInput( 'gbp_remove_languageset');
					$remove[] = $this->parent->form_inputs();
					$remove[] = hInput( 'lang_code' , $iso_code );
					$remove[] = hInput(gbp_plugin, $plugin);
					$guts = form( $guts . ' * ' . join( '' , $remove ) , 
//								'border: 1px solid grey; padding: 0.5em; margin: 1em;' ,
									'' ,
									"verify('" . gbp_gTxt('gbp_l10n_lang_remove_warning' , array('$var1'=>$name ) ) . 
									 gTxt('are_you_sure') . "')");
					}
				$out[]= '<li>'.$guts.'</li>';
				}
			$out[]= '<li style="border-top: 1px solid gray; margin-right: 1em;">'.array_sum($stats).' '.gTxt('gbp_l10n_strings').'</li>';
			$out[] = '</ul>';
			if( $extras_found )
				$out[] = gTxt('gbp_l10n_explain_extra_lang');

			//
			//	If the plugin is not present start with a box offering to delete the lot!
			//
			$installed_plugins = safe_column( 'name', 'txp_plugin', '1=1 order by name' );
			if( !in_array( $plugin, $installed_plugins ) )
				{
				$out[] = '<h3>'.gTxt('gbp_l10n_no_plugin_heading').'</h3>'.n;
				$del[] = graf( gTxt('gbp_l10n_remove_plugin') );
				$del[] = '<div class="gbp_l10n_form_submit">'.fInput('submit', '', gTxt('delete'), '').'</div>';
				$del[] = sInput('gbp_remove_stringset');
				$del[] = $this->parent->form_inputs();
				$del[] = hInput(gbp_plugin, $plugin);

				$out[] = form(	join('', $del) , 
								'border: 1px solid grey; padding: 0.5em; margin: 1em;' ,
								"verify('".gTxt('gbp_l10n_delete_plugin').' '.gTxt('are_you_sure')."')");
				}

			$out[] = '</div>';
			}

		echo join('', $out);
		}
	
	function render_plugin_list()
		{
		/*
		Lists all plugins that have registered common, admin or public strings using the string store.
		*/
		$out[] = '<div style="float: left; width: 20%;" class="gbp_i18n_plugin_list">';
		$out[] = '<h3>'.gTxt('gbp_l10n_registered_plugins').'</h3>'.n.'<ul>';

		$plugins = gbp_l10n_string_handler::DiscoverRegisteredPlugins();
		if( count( $plugins ) )
			{
			//	Get an array of installed plugins. Not all of them will have registered for 
			// string support...
			$installed_plugins = safe_column( 'name', 'txp_plugin', '1=1 order by name' );

			foreach( $plugins as $plugin )
				{
				$marker = '';
				if( !in_array( $plugin, $installed_plugins )  )
					$marker = ' <strong>*</strong>';
				$out[] = '<li><a href="'.$this->parent->url().'&#38;'.gbp_plugin.'='.$plugin.'">'.$plugin.$marker.'</a></li>';
				}
			}
		else{
			$out[] = '<li>'.gTxt('none').'</li>'.n;
			}
		$out[] = '</ul>';
		$out[] = '</div>';
		echo join('', $out);
		}


	function remove_strings()
		{
		$plugin 		= gps(gbp_plugin);
		$remove_langs 	= gps('lang_code');

		gbp_l10n_string_handler::RemovePluginStrings( $plugin , $remove_langs );
		}
	
	function save_strings() 
		{
		$string_name 	= gps( gbp_id );
		$event       	= gps( 'string_event' );
		$codes			= gps( 'codes' );
		$lang_codes		= explode( ',' , $codes );

		foreach($lang_codes as $code)
			{
			$translation 	= gps( $code.'-data' );
			$id 			= gps( $code.'-id' );
			$exists			= !empty( $id );
			if( !$exists and empty( $translation ) )
				continue;

			gbp_l10n_string_handler::StoreTranslationOfString( $string_name , $event , $code , $translation , $id );
			}
		}

}

if (@txpinterface == 'admin') {
	
	/*
	SED: Extend the txp_lang table to allow text instead of tinytext in the data field.
	This adds one byte per entry but gives much more flexibility to the strings/snippets for uses such as full 
	paragraphs of static text with some xhtml markup.
	*/
	$sql = ' CHANGE `data` `data` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL';
	safe_alter( 'txp_lang' , $sql );

	/*
	SED: Insert our own interface strings into the string table. 
	Does not overwrite any changes the admin has made via editing/translating the strings.
	*/
	LocalisationTabView::insert_strings();

	/*
	SED: On the admin side, pull the language from the TxP language variable, chop off the country specific
	part to leave the ISO-693 language part and use that to load the strings from the store to the $textarray
	*/
	$lang = explode( '-' , LANG );
	gbp_l10n_string_handler::LoadStringsIntoTextArray( $lang[0] );

	// We are admin-side.
	new LocalizationView('localisation', 'l10n', 'content');

}
else{

	// We are publish-side.
	global $prefs, $gbp_language;

	if (!defined('rhu'))
		define("rhu", preg_replace("/http:\/\/.+(\/.*)\/?$/U", "$1", hu));

	$path = explode('/', trim(str_replace(trim(rhu, '/'), '', $_SERVER['REQUEST_URI']), '/'));

	$lang_codes = gbp_l10n_language_handler::GetSiteLangs();
	foreach($lang_codes as $code)
		{
		if ($path[0] == $code)
			{
			$gbp_language = $code;
			break;	# Stop on first match.
			}
		}

	/*
	SED:	Load the localised set of strings based on the selected language...	
		Our localize routine should now have all the strings it needs to do snippet localization
		Plugins should be able to call gTxt() or gbp_gTxt() to output localized content.
	*/
	gbp_l10n_string_handler::LoadStringsIntoTextArray( $gbp_language );

	/* ====================================================
	TAG HANDLERS FOLLOW
	==================================================== */
	function gbp_snippet($atts)
		/*
		Tag handler: Outputs a localised snippet. This is a strict alternative to using 
		direct snippets in pages/forms.
		Atts: 'name' the name of the snippet to output.
		*/
		{
		$out = '';
		if( array_key_exists('name', $atts) )
			{
			global $gbp_language;

			$out = gTxt( $atts['name'] );
			if( $out === $atts['name'] )
				$out = '('.(($gbp_language)?$gbp_language:'??').')'.$out;
			}
		return $out;
		}

	// -----------------------------------------------------
	function gbp_if_lang( $atts , $thing )
		/*
		Basic markup tag. Use this to wrap blocks of content you only want to appear 
		when the specified language is set or if the direction of the selected language matches
		what you want. (Output different css files for rtl layouts for example).
		*/
	    {
		global $gbp_language;
		$out = '';
		
		if( !$gbp_language )
			return $out;
		
		extract(lAtts(array(
							'lang' => $gbp_language ,
							'dir'  => '',
							'wraptag' => 'div' ,
							),$atts));

		if( !empty($dir) and in_array( $dir , array( 'rtl', 'ltr') ) )
			{
			#	Does the direction of the currently selected site language match that requested?
			#	If so, parse the contained content.
			if( $dir == gbp_l10n_language_handler::GetLangDir( $gbp_language ) )
				$out = parse($thing) . n;
			}
		elseif( $lang == $gbp_language )
			{
			#	If the required language matches the site language, output a suitably marked up block of content.
			$dir = gbp_l10n_language_handler::GetLangDirMarkup( $lang );
			$out = "<$wraptag lang=\"$lang\"$dir/>" . parse($thing) . "</$wraptag>" . n;
			}

		return $out;
	    }

	// ----------------------------------------------------
	function gbp_render_lang_list( $atts )
		/*
		Renders a list of links that can be used to switch this page to another supported language.
		*/
		{
		global $gbp_language;
	
		$result = array();
		
		$site_langs = gbp_l10n_language_handler::GetSiteLangs();
		if( !empty($site_langs) )
			{
			foreach( $site_langs as $code ) 
				{
				$native_name = gbp_l10n_language_handler::GetNativeNameOfLang( $code );
				$dir = gbp_l10n_language_handler::GetLangDirMarkup( $code );
				$class = ($gbp_language === $code) ? 'gbp_current_language' : '';
				$native_name = doTag( $native_name , 'span' , $class , ' lang="'.$code.'"'.$dir );
				$result[] = '<a href="'.hu.$code.$_SERVER['REQUEST_URI'].'">'.$native_name.'</a>'.n;
				}
			}
		
		return doWrap( $result , 'ul' , 'li' );
		}

	// -----------------------------------------------------
	function gbp_get_language( $atts )
		/*
		Outputs the current language. Use in page/forms to output the language needed by the doctype/html decl.
		*/
		{
		global $gbp_language;

		if( !$gbp_language )
			return '';
		return $gbp_language;
		}

	// -----------------------------------------------------
	function gbp_get_lang_dir( $atts )
		/*
		Outputs the direction (rtl/ltr) of the current language. 
		Use in page/forms to output the direction needed by xhtml elements.
		*/
		{
		global $gbp_language;
		
		$lang = $gbp_language; 
		if( !$gbp_language )
			$lang = gbp_l10n_language_handler::GetSiteDefaultLang();

		$dir = gbp_l10n_language_handler::GetLangDir( $lang );
		return $dir;
		}
	
	// ----------------------------------------------------
	function gbp_localize($atts, $thing = '') 
		/*
		Graeme's original localisation container tag. Still very much needed.
		Some mods to include direct snippet localization for any contained content.
		*/
		{
		global $gbp_language, $thisarticle, $thislink;

		if ($gbp_language) {
			if (array_key_exists('category', $atts)) {
				$id = $atts['category'];
				$table = PFX.'txp_category';
				$rs = safe_field('entry_value', 'gbp_l10n', "`entry_id` = '$id' AND `entry_column` = 'title' AND `table` = '$table' AND `language` = '$gbp_language'");

				if ($rs && !empty($rs))
					return $rs;
				else
					return ucwords($atts['category']);

			} else if (array_key_exists('section', $atts)) {

				$id = $atts['section'];
				$table = PFX.'txp_section';
				$rs = safe_field('entry_value', 'gbp_l10n', "`entry_id` = '$id' AND `entry_column` = 'title' AND `table` = '$table' AND `language` = '$gbp_language'");

				if ($rs && !empty($rs))
					return $rs;
				else
					return ucwords($atts['section']);

			} else if ($thing) {

				# SED: Process the direct snippet substitutions needed in the contained content.
				$thing = gbp_l10n_snippet_handler::SubstituteSnippets( $thing );
				
				if (isset($thisarticle)) {
					$rs = safe_rows('entry_value, entry_value_html, entry_column', 'gbp_l10n', "`language` = '$gbp_language' AND `entry_id` = '".$thisarticle['thisid']."' AND `table` = '".PFX."textpattern'");

					if ($rs) foreach($rs as $row) {
						if ($row['entry_value'])
							$thisarticle[strtolower($row['entry_column'])] = ($row['entry_value_html']) ? parse($row['entry_value_html']) : $row['entry_value'];
					}
				}
				$html = parse($thing);
				$html = preg_replace('#((href|src)=")(?!\/?(https?|ftp|download|images|'.$gbp_language.'))\/?#', '$1'.$gbp_language.'/', $html);
				return $html;
			}
		}

		if (array_key_exists('category', $atts)) {

			$rs = safe_field('title', 'txp_category', '`name` = "'.$atts['category'].'"');
			if ($rs && !empty($rs))
				return $rs;
			else
				return ucwords($atts['category']);

		} else if (array_key_exists('section', $atts)) {

			$rs = safe_field('title', 'txp_section', '`name` = "'.$atts['section'].'"');
			if ($rs && !empty($rs))
				return $rs;
			else
				return ucwords($atts['section']);

		} else if ($thing) {

			# SED: Process and string substitutions needed in the contained content.
			$thing = gbp_l10n_snippet_handler::SubstituteSnippets( $thing );
			return parse($thing);
		}

		return null;
	}
}


/* ----------------------------------------------------------------------------
class gbp_l10n_language_handler implements ISO-693-1 language support.
---------------------------------------------------------------------------- */
class gbp_l10n_language_handler
	{
	# Comment out as much as you feel you will never need. (Lightens up the memory needed a little.)
	static $iso_693_1_langs = array( 
	'aa'=>array( 'aa'=>'Afaraf' ),	//	'en'=>'Afar'
	'ab'=>array( 'ab'=>'аҧсуа бызшәа' ),	//	'en'=>'Abkhazian' 
	'af'=>array( 'af'=>'Afrikaans' ),	//	'en'=>'Afrikaans' 
	'am'=>array( 'am'=>'አማርኛ' ),	//	'en'=>'Amharic' 
	'ar'=>array( 'ar'=>'العربية' , 'dir'=>'rtl' ),	//	'en'=>'Arabic' 
	'as'=>array( 'as'=>'অসমীয়া' ),	//	'en'=>'Assamese' 
	'ay'=>array( 'ay'=>'Aymar aru' ),	//	'en'=>'Aymara' 
	'az'=>array( 'az'=>'Azərbaycan dili' ),	//	'en'=>'Azerbaijani' 
	'ba'=>array( 'ba'=>'башҡорт теле' ),	//	'en'=>'Bashkir' 
	'be'=>array( 'be'=>'Беларуская мова' ),	//	'en'=>'Byelorussian' 
	'bg'=>array( 'bg'=>'Български' ),	//	'en'=>'Bulgarian' 
	'bh'=>array( 'bh'=>'भोजपुरी' ),	//	'en'=>'Bihari',
	'bi'=>array( 'bi'=>'Bislama' ),	//	'en'=>'Bislama' 
	'bn'=>array( 'bn'=>'বাংলা' ),	//	'en'=>'Bengali; Bangla'
	'bo'=>array( 'bo'=>'Bod Skad' ) ,	//	'en'=>'Tibetan' 
	'br'=>array( 'br'=>'ar Brezhoneg' ) ,	//	'en'=>'Breton' 
	'ca'=>array( 'ca'=>'Català' ) ,	//	'en'=>'Catalan' 
	'co'=>array( 'co'=>'Corsu' ) ,	//	'en'=>'Corsican' 
	'cs'=>array( 'cs'=>'Čeština' ) ,	//	'en'=>'Czech' 
	'cy'=>array( 'cy'=>'Cymraeg' ) ,	//	'en'=>'Welsh' 
	'da'=>array( 'da'=>'Dansk' ) ,	//	'en'=>'Danish' 
	'de'=>array( 'de'=>'Deutsch' ) ,	//	'en'=>'German' 
	'dz'=>array( 'dz'=>'Dzongkha' ) ,	//	'en'=>'Bhutani'
	'el'=>array( 'el'=>'Ελληνικά' ) ,	//	'en'=>'Greek' 
	'en'=>array( 'en'=>'English' ),
	'eo'=>array( 'eo'=>'Esperanto' ),	//	'en'=>'Esperanto' 
	'es'=>array( 'es'=>'Español' ),	//	'en'=>'Spanish' 
	'et'=>array( 'et'=>'Eesti Keel' ),	//	'en'=>'Estonian' 
	'eu'=>array( 'eu'=>'Euskera' ),	//	'en'=>'Basque' 
	'fa'=>array( 'fa'=>'Fārsī' ),	//	'en'=>'Persian' 
	'fi'=>array( 'fi'=>'Suomi' ),	//	'en'=>'Finnish' 
	'fj'=>array( 'fj'=>'vaka-Viti' ),	//	'en'=>'Fiji' 
	'fo'=>array( 'fo'=>'Føroyska' ),	//	'en'=>'Faroese' 
	'fr'=>array( 'fr'=>'Français' ),	//	'en'=>'French' 
	'fy'=>array( 'fy'=>'Frysk' ),	//	'en'=>'Frisian' 
	'ga'=>array( 'ga'=>'Gaeilge' ),	//	'en'=>'Irish' 
	'gd'=>array( 'gd'=>'Gàidhlig' ),	//	'en'=>'Scots Gaelic'
	'gl'=>array( 'gl'=>'Galego' ),	//	'en'=>'Galician' 
	'gn'=>array( 'gn'=>"Avañe'ẽ" ),	//	'en'=>'Guarani' 
	'gu'=>array( 'gu'=>'ગુજરાતી' ),	//	'en'=>'Gujarati' 
	'ha'=>array( 'ha'=>'حَوْسَ حَرْش۪' , 'dir'=>'rtl' ),	//	'en'=>'Hausa' 
	'he'=>array( 'he'=>'עברית / עִבְרִית' ,'dir'=>'rtl' ),	//	'en'=>'Hebrew' 
	'hi'=>array( 'hi'=>'हिन्दी' ),	//	'en'=>'Hindi' 
	'hr'=>array( 'hr'=>'Hrvatski' ),	//	'en'=>'Croatian' 
	'hu'=>array( 'hu'=>'Magyar' ),	//	'en'=>'Hungarian' 
	'hy'=>array( 'hy'=>'Հայերէն' ),	//	'en'=>'Armenian' 
	'ia'=>array( 'ia'=>'Interlingua' ),	//	'en'=>'Interlingua' 
	'id'=>array( 'id'=>'Bahasa Indonesia' ),	//	'en'=>'Indonesian' 
	'ie'=>array( 'ie'=>'Interlingue' ),	//	'en'=>'Interlingue' 
	'ik'=>array( 'ik'=>'Iñupiak' ),	//	'en'=>'Inupiak' 
	'is'=>array( 'is'=>'Íslenska' ),	//	'en'=>'Icelandic' 
	'it'=>array( 'it'=>'Italiano' ),	//	'en'=>'Italian' 
	'iu'=>array( 'iu'=>'ᐃᓄᒃᑎᑐᑦ' ),	//	'en'=>'Inuktitut' 
	'ja'=>array( 'ja'=>'日本語' ),	//	'en'=>'Japanese' 
	'jw'=>array( 'jw'=>'basa Jawa' ),	//	'en'=>'Javanese' 
	'ka'=>array( 'ka'=>'ქართული' ),	//	'en'=>'Georgian' 
	'kk'=>array( 'kk'=>'Қазақ' ),	//	'en'=>'Kazakh' 
	'kl'=>array( 'kl'=>'Kalaallisut' ),	//	'en'=>'Greenlandic' 
	'km'=>array( 'km'=>'ភាសាខ្មែរ' ),	//	'en'=>'Cambodian' 
	'kn'=>array( 'kn'=>'ಕನ್ನಡ' ),	//	'en'=>'Kannada' 
	'ko'=>array( 'ko'=>'한국어' ),	//	'en'=>'Korean' 
	'ks'=>array( 'ks'=>'काऽशुर' ),	//	'en'=>'Kashmiri' 
	'ku'=>array( 'ku'=>'Kurdí' ),	//	'en'=>'Kurdish' 
	'ky'=>array( 'ky'=>'Кыргызча' ),	//	'en'=>'Kirghiz' 
	'la'=>array( 'la'=>'Latine' ),	//	'en'=>'Latin' 
	'ln'=>array( 'ln'=>'lokótá ya lingála' ),	//	'en'=>'Lingala' 
	'lo'=>array( 'lo'=>'ລາວ' ),	//	'en'=>'Laothian' 
	'lt'=>array( 'lt'=>'Lietuvių Kalba' ),	//	'en'=>'Lithuanian' 
	'lv'=>array( 'lv'=>'Latviešu' ),	//	'en'=>'Latvian'
	'mg'=>array( 'mg'=>'Malagasy fiteny' ),	//	'en'=>'Malagasy' 
	'mi'=>array( 'mi'=>'te Reo Māori' ),	//	'en'=>'Maori' 
	'mk'=>array( 'mk'=>'Македонски' ),	//	'en'=>'Macedonian' 
	'ml'=>array( 'ml'=>'മലയാളം' ),	//	'en'=>'Malayalam' 
	'mn'=>array( 'mn'=>'Монгол' ),	//	'en'=>'Mongolian' 
	'mo'=>array( 'mo'=>'лимба молдовеняскэ' ),	//	'en'=>'Moldavian' 
	'mr'=>array( 'mr'=>'मराठी' ),	//	'en'=>'Marathi' 
	'ms'=>array( 'ms'=>'Bahasa Melayu' ),	//	'en'=>'Malay' 
	'mt'=>array( 'mt'=>'Malti' ),	//	'en'=>'Maltese' 
	'my'=>array( 'my'=>'ဗမာစကား' ),	//	'en'=>'Burmese' 
	'na'=>array( 'na'=>'Ekakairũ Naoero' ),	//	'en'=>'Nauru' 
	'ne'=>array( 'ne'=>'नेपाली' ),	//	'en'=>'Nepali' 
	'nl'=>array( 'nl'=>'Nederlands' ),	//	'en'=>'Dutch' 
	'no'=>array( 'no'=>'Norsk' ),	//	'en'=>'Norwegian' 
	'oc'=>array( 'oc'=>'lenga occitana' ),	//	'en'=>'Occitan' 
	'om'=>array( 'om'=>'Afaan Oromo' ),	//	'en'=>'(Afan) Oromo'
	'or'=>array( 'or'=>'ଓଡ଼ିଆ' ),	//	'en'=>'Oriya' 
	'pa'=>array( 'pa'=>'ਪੰਜਾਬੀ' ),	//	'en'=>'Punjabi' 
	'pl'=>array( 'pl'=>'Polski' ),	//	'en'=>'Polish' 
	'ps'=>array( 'ps'=>'پښتو' , 'dir'=>'rtl' ),	//	'en'=>'Pashto' 
	'pt'=>array( 'pt'=>'Português' ),	//	'en'=>'Portuguese' 
	'qu'=>array( 'qu'=>'Runa Simi/Kichwa' ),	//	'en'=>'Quechua' 
	'rm'=>array( 'en'=>'Rhaeto-Romance' ),
	'rn'=>array( 'rn'=>'Kirundi' ),	//	'en'=>'Kirundi' 
	'ro'=>array( 'ro'=>'Română' ),	//	'en'=>'Romanian' 
	'ru'=>array( 'ru'=>'Русский' ),	//	'en'=>'Russian' 
	'rw'=>array( 'rw'=>'Kinyarwandi' ),	//	'en'=>'Kinyarwanda' 
	'sa'=>array( 'sa'=>'संस्कृतम्' ),	//	'en'=>'Sanskrit' 
	'sd'=>array( 'sd'=>'سنڌي' , 'dir'=>'rtl' ),	//	'en'=>'Sindhi' 
	'sg'=>array( 'sg'=>'yângâ tî sängö' ),	//	'en'=>'Sangho' 
	'sh'=>array( 'sh'=>'Српскохрватски' ),	//	'en'=>'Serbo-Croatian'
	'si'=>array( 'si'=>'(siṁhala bʰāṣāva)' ),	//	'en'=>'Sinhalese' 
	'sk'=>array( 'sk'=>'Slovenčina' ),	//	'en'=>'Slovak' 
	'sl'=>array( 'sl'=>'Slovenščina' ),	//	'en'=>'Slovenian' 
	'sm'=>array( 'sm'=>"gagana fa'a Samoa" ),	//	'en'=>'Samoan' 
	'sn'=>array( 'sn'=>'chiShona' ),	//	'en'=>'Shona' 
	'so'=>array( 'so'=>'af Soomaali' ),	//	'en'=>'Somali' 
	'sq'=>array( 'sq'=>'Shqip' ),	//	'en'=>'Albanian' 
	'sr'=>array( 'sr'=>'Srpski' ),	//	'en'=>'Serbian' 
	'ss'=>array( 'ss'=>'siSwati' ),	//	'en'=>'Siswati' 
	'st'=>array( 'st'=>'seSotho' ),	//	'en'=>'Sesotho' 
	'su'=>array( 'su'=>'basa Sunda' ),	//	'en'=>'Sundanese' 
	'sv'=>array( 'sv'=>'Svenska' ),	//	'en'=>'Swedish' 
	'sw'=>array( 'sw'=>'Kiswahili' ),	//	'en'=>'Swahili' 
	'ta'=>array( 'ta'=>'தமிழ்' ),	//	'en'=>'Tamil' 
	'te'=>array( 'te'=>'తెలుగు' ),	//	'en'=>'Telugu' 
	'tg'=>array( 'tg'=>'زبان تاجکی' , 'dir'=>'rtl' ),	//	'en'=>'Tajik' 
	'th'=>array( 'th'=>'ภาษาไทย' ),	//	'en'=>'Thai' 
	'ti'=>array( 'ti'=>'ትግርኛ' ),	//	'en'=>'Tigrinya' 
	'tk'=>array( 'tk'=>'Türkmençe' ),	//	'en'=>'Turkmen' 
	'tl'=>array( 'tl'=>'Tagalog' ),	//	'en'=>'Tagalog' 
	'tn'=>array( 'tn'=>'Setswana' ),	//	'en'=>'Setswana' 
	'to'=>array( 'to'=>'Faka-Tonga' ),	//	'en'=>'Tonga' 
	'tr'=>array( 'tr'=>'Türkçe' ),	//	'en'=>'Turkish' 
	'ts'=>array( 'ts'=>'xiTsonga' ),	//	'en'=>'Tsonga' 
	'tt'=>array( 'tt'=>'تاتارچا' , 'dir'=>'rtl' ),	//	'en'=>'Tatar' 
	'tw'=>array( 'tw'=>'Twi' ),	//	'en'=>'Twi' 
	'ug'=>array( 'ug'=>'uyghur tili' ),	//	'en'=>'Uighur' 
	'uk'=>array( 'uk'=>"Українська" ),	//	'en'=>'Ukrainian' 
	'ur'=>array( 'ur'=>'اردو', 'dir'=>'rtl' ),	//	'en'=>'Urdu' 
	'uz'=>array( 'uz'=>"Ўзбек (o'zbek)" ),	//	'en'=>'Uzbek' 
	'vi'=>array( 'vi'=>'Tiếng Việt' ),	//	'en'=>'Vietnamese' 
	'vo'=>array( 'vo'=>"vad'd'a tšeel" ),	//	'en'=>'Volapuk' 
	'wo'=>array( 'wo'=>'Wollof' ),	//	'en'=>'Wolof' 
	'xh'=>array( 'xh'=>'isiXhosa' ),	//	'en'=>'Xhosa' 
	'yi'=>array( 'yi'=>'ײִדיש' , 'dir'=>'rtl' ),	//	'en'=>'Yiddish' 
	'yo'=>array( 'yo'=>'Yorùbá' ),	//	'en'=>'Yoruba' 
	'za'=>array( 'za'=>'Sawcuengh' ),	//	'en'=>'Zhuang' 
	'zh'=>array( 'zh'=>'中文(國語)' ),	//	'en'=>'Chinese' 
	'zu'=>array( 'zu'=>'isiZulu' ),	//	'en'=>'Zulu' 
	);
	
	// ----------------------------------------------------------------------------
	public static function IsValidISO693Code($code)
		/*
		LANGUAGE SUPPORT ROUTINE
		Check the given string is a valid 2-digit language code from the ISO-693-1 table.
		*/
		{
		$result = false;
		if( 2 == strlen( $code ) )
			{
			$result = array_key_exists( $code , self::$iso_693_1_langs );
			}
		return $result;
		}
	// ----------------------------------------------------------------------------
	public static function FindCodeForLanguage( $name )
		/*
		LANGUAGE SUPPORT ROUTINE
		Returns the ISO-693-1 code for the given native language.
		*/
		{
		$out = gTxt( 'none' );
		if( $name and !empty( $name ) )
			{
			foreach( self::$iso_693_1_langs as $code => $data )
				{
				if( in_array( $name , $data ) )
					{
					$out = $code;
					break;
					}
				}
			}
		return $out;
		}
	// ----------------------------------------------------------------------------
	public static function GetLangDirMarkup( $lang )
		/*
		LANGUAGE SUPPORT ROUTINE
		Builds the xhtml direction markup needed based upon the directionality of the language requested.
		*/
		{
		$dir = '';
		if( !empty($lang) and isset(self::$iso_693_1_langs[$lang]['dir']) and ('rtl' == self::$iso_693_1_langs[$lang]['dir']) )
			$dir = ' dir="rtl"';
		return $dir;
		}
	// ----------------------------------------------------------------------------
	public static function GetLangDir( $lang )
		/*
		LANGUAGE SUPPORT ROUTINE
		Builds the xhtml direction markup needed based upon the directionality of the language requested.
		*/
		{
		$dir = 'ltr';
		if( !empty($lang) and isset(self::$iso_693_1_langs[$lang]['dir']) and ('rtl' == self::$iso_693_1_langs[$lang]['dir']) )
			$dir = 'rtl';
		return $dir;
		}
	// ----------------------------------------------------------------------------
	public static function GetNativeNameOfLang( $code )
		/*
		LANGUAGE SUPPORT ROUTINE
		Returns the native name of the given language code.
		*/
		{
		return (self::$iso_693_1_langs[$code][$code]) ? self::$iso_693_1_langs[$code][$code] : self::$iso_693_1_langs[$code]['en'] ;
		}
	// ----------------------------------------------------------------------------
	public static function GetSiteLangs()
		/*
		LANGUAGE SUPPORT ROUTINE
		Returns an array of the ISO-693-1 languages the site supports.
		*/
		{
		global $prefs;
		
		if (!array_key_exists(GBP_PREFS_LANGUAGES, $prefs))
			$prefs[GBP_PREFS_LANGUAGES] = 'en,el';
		
		$lang_codes = explode(',', $prefs[GBP_PREFS_LANGUAGES]);
		return $lang_codes;
		}
	// ----------------------------------------------------------------------------
	public static function GetSiteDefaultLang()
		/*
		LANGUAGE SUPPORT ROUTINE
		Returns a string containing the ISO-693-1 language to be used as the site's default.
		*/
		{
		global $prefs;
		$lang_codes = explode(',', $prefs[GBP_PREFS_LANGUAGES]);
		return $lang_codes[0];
		}

	} // End of gbp_l10n_language_handler

/* ----------------------------------------------------------------------------
class gbp_l10n_snippet_handler implements localized "snippets" within page and
form templates. Uses the services of the string_handler to localise the
strings therein.
---------------------------------------------------------------------------- */
class gbp_l10n_snippet_handler
	{
	# Use the first snippet detection pattern for a simple snippet format that is visible when the substitution fails.
	# Use the second snippet detection pattern if you want unmatched snippets as xhtml comments.
	static $snippet_pattern = "/##([\w|\.|\-]+)##/";
//	static $snippet_pattern = "/\<\!--##([\w|\.|\-]+)##--\>/";

	# The following pattern is used to match any gbp_snippet tags in pages and forms.
	static $snippet_tag_pattern = "/\<txp:gbp_snippet name=\"([\w|\.|\-]+)\"\s*\/\>/";
	
	// ----------------------------------------------------------------------------
	public static function SubstituteSnippets( &$thing )
		/*
		PUBLIC LOCALIZATION SUPPORT ROUTINE for use by localization plugin.
		Replaces all snippets within the contained block with their text from the global textarray.
		Allows TxP devs to include snippets* in their forms and page templates.
		
		*A Snippet is a specially formatted marker in the page/form template that gets substituted by
		the localization routine.
		*/
		{
		$out = preg_replace_callback( 	self::$snippet_pattern , 
										create_function(
							           '$match',
								       'global $gbp_language;
										global $textarray;
										if( $gbp_language )
											$lang = $gbp_language;
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
	// ----------------------------------------------------------------------------
	public static function FindSnippetsInBlock( &$thing , $merge = false )
		/*
		ADMIN SUPPORT ROUTINE
		Scans the given block ($thing) for snippets and returns their names as the values of an array.
		If merge is true then these values are expanded to contain whatever data is found in the txp_lang table for 
		that snippet.
		*/
		{
		$out = array();
		$tags = array();

		# Match all directly included snippets...
		preg_match_all( self::$snippet_pattern , $thing , $out );
		# Match all snippets included as txp tags...
		preg_match_all( self::$snippet_tag_pattern , $thing , $tags );

		#	cleanup and trim the output arrays a little...
		array_shift( $out );
		$out = $out[0];
		$out = doArray( $out , 'strtolower' );
		array_shift( $tags );
		$tags = $tags[0];
		$tags = doArray( $tags , 'strtolower' );
		$out = array_merge( $out , $tags );
		
		if( $merge and count($out) )
			{
			#	Enlarge the array with details of any txp_lang entries that match that snippet name.
			#	The admin side can then manipulate and expand the returned array and stash it back in the 
			#	language table for future use.

			$temp = array();
			foreach( $out as $name )
				{
				#	Load details of named entries...
				$rs = safe_rows_start(
					'id , lang , data , lastmod', 
					'txp_lang', 
					"`name`='" . doSlash($name) . "' AND `event`='snippet'" );

				if( $rs and mysql_num_rows($rs) > 0 )
					{
					while( $a = nextRow($rs) )
						{
						$lng = $a['lang'];
						$temp[$name][$lng]['id'] 		= $a['id'];
						$temp[$name][$lng]['lastmod'] 	= $a['lastmod'];
						$temp[$name][$lng]['data'] 		= $a['data'];
						}
					}
				else{
					$temp[$name] = NULL;
					}
				}
			$out = &$temp;
			}
		
		return $out;
		}
	// ----------------------------------------------------------------------------
	public static function StoreSnippets( &$snippets )
		/*
		ADMIN SUPPORT ROUTINE
		Takes a full array of snippets (includes 1+ renditions) and stores them in the txp_lang table.
		*/
		{
		if( !$snippets or 0==count($snippets) )
			return;
			
		foreach( $snippet as $name=>$langs )
			{
			if( !$langs or 0==count($langs) )
				continue;
				
			$name = doSlash( $name );
			
			#	Pull apart the languages...
			foreach( $langs as $lang=>$meta )
				{				
				if( !$meta or 3!=count( $meta ) )
					continue;
				
				$lang		= doSlash( $lang );
				$id 		= doSlash( $meta['id'] );
				$data 		= doSlash( $meta['data'] );
				$lastmod	= date('YmdHis');
				$set 		= " `lang`='$lang', `name`='$name', `lastmod`='$lastmod', `event`='snippet', `data`='$data'" ;

				if( empty( $id ) )
					{
					#insert new entry.
					echo " Calling safe_insert($set). CHANGE ME!" , br;
//					@safe_insert( 'txp_lang' , $set );
					}
				else{
					#update existing entry (use the id).
					$where = " `id`='$id'";
					echo " Calling safe_update( $set , $where ). CHANGE ME!" , br;
//					safe_update( 'txp_lang', $set, $where );
					}
				}
			}	
		}
	// ----------------------------------------------------------------------------
	public static function RenderSnippetList( &$snippets , $listtype )
		/*
		ADMIN SUPPORT ROUTINE
		Takes a full array of snippets (includes 1+ translations) and renders them as a list.
		*/
		{
		$out = '';
		
		if( !$snippets or 0==count($snippets) )
			{
			$out[] = gTxt('none');
			return doWrap( $out , $listtype , 'li' );
			}
			
		foreach( $snippets as $name=>$langs )
			{
			if( !$langs or 0==count($langs) )
				{
				$out[] = $name . ' - '.gTxt('none');
				continue;
				}
				
			ksort( $langs );
			#	Pull apart the languages...
			$tmp = $name . ' - ';
			foreach( $langs as $lang=>$meta )
				{				
				if( !$meta or 3!=count( $meta ) )
					continue;
				
				$tmp .= "[$lang] ";
				}
			$out[] = $tmp;
			}	
		return doWrap( $out , $listtype, 'li' );
		}
	} // End of gbp_l10n_snippet_handler
	
/* ----------------------------------------------------------------------------
class gbp_l10n_string_handler implements localized string storage support.
---------------------------------------------------------------------------- */
class gbp_l10n_string_handler
	{
	// ----------------------------------------------------------------------------
	public static function StripLeadingSection( $string , $delim='.' )
		/*
		Simply removes anything that prefixes a string up to the delimiting character.
		So 'hello.world' -> 'world'
		*/
		{
		if( empty( $string ))
			return '';
			
		$i = strstr( $string , $delim );
		if( false === $i )
			return $string;
		$i = ltrim( $i , $delim );
		return $i;
		}	
	// ----------------------------------------------------------------------------
	public static function InsertStringsForLang( $strings , $lang , $event='' )
		/*
		PLUGIN SUPPORT ROUTINE
		Plugin authors: CALL THIS FROM THE IMMEDIATE PROCESSING SECTION OF YOUR PLUGIN'S ADMIN CODE.
		Adds the given array of aliased strings to an additional string table.
		*/
		{
		global	$txp_current_plugin;

		# 	Check we have valid arguments...
		if( empty($strings) or empty($lang) )
			return null;

		# if the plugin is known, store it as a suffix to any strings stored...
		if( !empty($txp_current_plugin) and ($event=='public' or $event=='admin' or $event=='common') )
			$event = $event.'.'.$txp_current_plugin;

		#	Iterate over the $strings and, for each that is not present, enter them into the sql table...
		$lastmod 	= date('YmdHis');
		$lang 		= doSlash( $lang );
		$event 		= doSlash( $event );
		foreach( $strings as $name=>$data )
			{
			$data = doSlash($data);
			$name = doSlash($name);
			mysql_query("INSERT INTO `".PFX."txp_lang` SET `lang`='$lang', `name`='$name', `lastmod`='$lastmod', `event`='$event', `data`='$data'");
			}
		mysql_query("DELETE FROM `".PFX."txp_lang` WHERE `data`=''");
//		mysql_query("FLUSH TABLE `".PFX."txp_lang`");
		}
	// ----------------------------------------------------------------------------
	public static function StoreTranslationOfString( $name , $event , $new_lang , $translation , $id='' )
		/*
		ADMIN SUPPORT ROUTINE
		For use by the localization plugin. 
		Can create or update row in the DB depending upon the calling arguments.
		*/
		{
		# 	Check we have valid arguments...
		if( empty($name) or empty($event) or empty($new_lang) )
			{
//			echo br, " Aborting Translation Storage -- Missing paramenter!";
			return null;
			}

		if( !empty($txp_current_plugin) and ($event=='public' or $event=='admin' or $event=='common') )
			$event = $event.'.'.$txp_current_plugin;

		#	Escape the lot for mySQL.
		$id 			= doSlash( $id );
		$event 			= doSlash( $event );
		$name  			= doSlash( $name );
		$translation	= doSlash( $translation );
		$new_lang 		= doSlash( $new_lang );
		$lastmod 		= date('YmdHis');

		$set 	= " `lang`='$new_lang', `name`='$name', `lastmod`='$lastmod', `event`='$event', `data`='$translation'" ;

		if( !empty( $id ) )
			{
			#	This is an update...
			$where	= " `id`='$id'";
			$result = @safe_update( 'txp_lang' , $set , $where );
			}
		else{
			#	Insert new row...
			$result = @safe_insert( 'txp_lang' , $set );
			}

		return $result;
		}
	// ----------------------------------------------------------------------------
	public static function StoreTranslationOfStringByID( $id , $new_lang , $translation )
		/*
		ADMIN SUPPORT ROUTINE
		For use by the localization plugin. Clones the entry with the given id and stores the 
		translation in the data and sets the lang and date as given.
		*/
		{
		# 	Check we have valid arguments...
		if( empty($id) or empty($translation) or empty($new_lang) )
			return null;

		#	Does the row to copy exist?
		$id = doSlash( $id );
		$row = safe_row( '*' , 'txp_lang' , "`id`=$id" );
		if( !$row )
			return false;
			
//		echo br.br.br.br , "String [id=$id] found.";
			
		extract( $row );
		$translation	= doSlash( $translation );
		$new_lang 		= doSlash( $new_lang );
		$lastmod 		= date('YmdHis');
		$set 			= " `lang`='$new_lang', `name`='$name', `lastmod`='$lastmod', `event`='$event', `data`='$translation'" ;

//		echo " Calling safe_insert($set)." , br;

		@safe_insert( 'txp_lang' , $set );
		}
	// ----------------------------------------------------------------------------
	public static function RemovePluginStrings( $plugin , $remove_lang , $debug = '' )
		/*
		PLUGIN SUPPORT ROUTINE
		Either: Removes all the occurances of plugin strings in the given langs...
		OR:		Removes all of the named plugin's strings.
		*/
		{
		if( $remove_lang and !empty( $remove_lang ) )
			{		
			$where = "(`lang` IN ('$remove_lang')) AND (`event` LIKE \"common.%\" OR `event` LIKE \"public.%\" OR `event` LIKE \"admin.%\" OR `event`='snippet')";
//echo br, $where;
			@safe_delete( 'txp_lang' , $where , $debug );
			@safe_optimize( 'txp_lang' , $debug );
			}
		elseif( $plugin and !empty( $plugin ) )
			{
			$where = "`event`=\"common.$plugin\" OR `event`=\"public.$plugin\" OR `event`=\"admin.$plugin\"";
//echo br, $where;
			@safe_delete( 'txp_lang' , $where , $debug );
			@safe_optimize( 'txp_lang' , $debug );
			}
		}
	// ----------------------------------------------------------------------------
	public static function RemoveStrings( $strings , $event = '' )
		/*
		PLUGIN SUPPORT ROUTINE
		Plugin authors: CALL THIS FROM THE IMMEDIATE PROCESSING SECTION OF YOUR PLUGIN'S ADMIN CODE.
		Removes all of the named strings in ALL languages. (It uses the keys of the strings array).
		*/
		{
		global	$txp_current_plugin;

		if( !$strings or !is_array( $strings ) )
			return null;

		if( !empty($txp_current_plugin) and ($event=='public' or $event=='admin' or $event=='common') )
			$event = $event.'.'.$txp_current_plugin;
		$event 	= doSlash( $event );

//		echo br.br.br.br, "In RemoveStrings. Event($event). ";

		if( count( $strings ) )
			{
			foreach( $strings as $name=>$data )
				{
				$name 	= doSlash($name);
				$where 	= " `name`='$name'";
				if( !empty($event) )
					$where .= " AND `event`='$event'";
//				echo br , "Deleting entry where($where).";
				@safe_delete( 'txp_lang' , $where );
				}
			@safe_optimize( 'txp_lang' , $debug );
			}		
		}
	// ----------------------------------------------------------------------------
	public static function LoadStringsIntoTextArray( $lang )
		/*
		PUBLIC/ADMIN INTERFACE SUPPORT ROUTINE
		Loads all strings of the given language into the global $textarray so that any plugin can call 
		gTxt on it's own strings. Can be used for admin and public work.
		*/
		{
		global $textarray;
		
		$extras = gbp_l10n_string_handler::LoadStrings($lang);
		$textarray = array_merge( $textarray , $extras );
		return count( $extras );
		}
	// ----------------------------------------------------------------------------
	public static function LoadStrings( $lang )
		/*
		PUBLIC/ADMIN INTERFACE SUPPORT ROUTINE
		Loads all strings of the given language into an array and returns them.
		*/
		{
		$extras = array();

		if( @txpinterface == 'admin' )
			$rs = safe_rows_start('name, data','txp_lang',"lang='".doSlash($lang)."'");
		else
			$rs = safe_rows_start(
				'name, data', 
				'txp_lang', 
				"lang='" . doSlash($lang) . "' AND ( event='snippet' OR event LIKE \"public.%\" OR event LIKE \"common.%\" )");

		if( $rs && mysql_num_rows($rs) > 0 )
			{
			while ( $a = nextRow($rs) )
				{
				$extras[$a['name']] = $a['data'];
				}
			}
		return $extras;
		}
	// ----------------------------------------------------------------------------
	public static function DiscoverRegisteredPlugins()
		/*
		ADMIN INTERFACE SUPPORT ROUTINE
		Gets an array of the names of plugins that have registered strings in the correct format. 
		No repeats!
		*/
		{
		$result = array();

		$rs = safe_rows_start( 	
							'distinct event', 
							'txp_lang', 
							' `event` like "public.%" or `event` like "admin.%" or `event` like "common.%"'
							);
		if( $rs && mysql_num_rows($rs) > 0 )
			{
			$set = array();
			while ( $a = nextRow($rs) )
				{
				$plugin = self::StripLeadingSection($a['event']);			
				$set[$plugin] = $plugin;
				}
			foreach( $set as $plugin )
				{
				$result[] = $plugin;
				}
			sort( $result );
			}
		return $result;		
		}
	// ----------------------------------------------------------------------------
	public static function GetPluginStrings( $plugin , &$stats )
		/*
		ADMIN INTERFACE SUPPORT ROUTINE
		Given a plugin name, will extract a list of strings the plugin has registered, collapsing all 
		the translations into one entry. Thus...
		name	lang	data
		alpha	en		Alpha
		alpha	fr		Alpha
		alpha	el		Alpha
		beta	en		Beta
		Gives...
		alpha => 'fr, el, en'  (Sorted order)
		beta  => 'en'
		*/
		{
		$result = array();
		
		$plugin = doSlash( $plugin );
		$where = ' `event` = "public.'.$plugin.'" or `event` = "admin.'.$plugin.'" or `event` = "common.'.$plugin.'"';
		$rs = safe_rows_start( 'lang, name', 'txp_lang', $where );
		if( $rs && mysql_num_rows($rs) > 0 )
			{
			while ( $a = nextRow($rs) )
				{
				$name = $a['name'];
				$lang = $a['lang'];
				
				if( !array_key_exists( $name , $result ) )
					$result[$name] = array();
				
				if( array_key_exists( $lang , $result[$name] ) )
					$result[$name][$lang] += 1;
				else
					$result[$name][$lang] = 1;
////				$result[$name][$lang] = $lang;
				}
			ksort( $result );
			foreach( $result as $name => $langs )
				{
				ksort( $langs );

				//
				//	Build the language stats for the plugin...
				//
				foreach( $langs as $lang=>$count )
					{
					if( array_key_exists( $lang, $stats ) )
						$stats[$lang] += $count;
					else
						$stats[$lang] = $count;
					}
				
				$string_of_langs = rtrim( join( ', ' , array_keys($langs) ) , ' ,' );
				$result[$name] = $string_of_langs;
				}
			ksort( $stats );
			}
		return $result;
		}
	// ----------------------------------------------------------------------------
	public static function GetFullLangsString( )
		/*
		ADMIN INTERFACE SUPPORT ROUTINE
		Returns a string of the site languages. Used to work out if a given string has a complete 
		set of translations.
		*/
		{
		$langs = gbp_l10n_language_handler::GetSiteLangs();
		sort( $langs );
		$langs = rtrim( join( ', ' , $langs ) , ' ,' );
		return $langs;
		}
	// ----------------------------------------------------------------------------
	public static function GetStringSet( $string_name )
		/*
		ADMIN INTERFACE SUPPORT ROUTINE
		Given a string name, will extract an array of the matching translations.
		translation_lang => string_id , event , data
		*/
		{
		$result = array();

		$where = ' `name` = "'.$string_name.'"';
		$rs = safe_rows_start( 'lang, id, event, data', 'txp_lang', $where );
		if( $rs && mysql_num_rows($rs) > 0 )
			{
			while ( $a = nextRow($rs) )
				{
				$lang = $a['lang'];
				if( gbp_l10n_language_handler::IsValidISO693Code( $lang ) )
					{
					unset( $a['lang'] );	# will be used as key, no need to store it twice.
					$result[ $lang ] = $a;
					}
				}
			ksort( $result );
			}
		return $result;
		}
	// ----------------------------------------------------------------------------
	public static function gTxt( $alias, $args=null )
		/*
		PUBLIC/ADMIN INTERFACE SUPPORT ROUTINE
		Given a string name, will pull the string out of the $textarray and perform any argument replacements needed.
		*/
		{
		global $textarray;
		global $gbp_language;
		
		$lang = $gbp_language;
		if( !$lang )
			$lang = gbp_l10n_language_handler::GetSiteDefaultLang();
			
		$out = @$textarray[ $alias ];
		if( !$out or ($out === $alias) )
			$out = "($lang) $alias";
			
		if( isset( $args ) and is_array( $args ) and count($args) )
			{
//			echo br , "Doing replacements ...";
			foreach( $args as $pattern=>$value )
				{
//				echo "$pattern -> $value ... ";
				$out = preg_replace( '/\\'.$pattern.'/' , $value , $out );
				}
			}
			
		return $out;
		}
	} // End class gbp_l10n_string_handler


/* ----------------------------------------------------------------------------
PUBLIC/ADMIN WRAPPER ROUTINES...
---------------------------------------------------------------------------- */
function gbp_gTxt( $name , $args = null )
	/*
	Extension to the gTxt routine to allow an optional parameter list.
	Plugin authors can define strings with embedded variables that get preg_replaced
	based on the the argument array.
	
	So a string : 'plugin_name_hello' => 'Hello there $name.'
	
	could be replaced like this from within the plugin...
		gbp_gTxt( 'plugin_name_hello' , array( '$name'=>$name ) );
	*/
	{
	return gbp_l10n_string_handler::gTxt( $name , $args );
	}
// ----------------------------------------------------------------------------

# --- END PLUGIN CODE ---

?>
