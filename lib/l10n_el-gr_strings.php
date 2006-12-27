<?php

#
#	The language of the strings in this file...
#
global $l10n_default_strings_lang;
$l10n_default_strings_lang = 'el-gr';	#	Ελληνικά


#
#	These strings are always needed, they will get installed in the language array...
#
global $l10n_default_strings_perm;
$l10n_default_strings_perm = array(
	'l10n-localisation'   => 'Α.Π.Τ.',
	'l10n-toggle'         => 'Εν. καταστάσεων',
	'l10n-snippets_tab'   => 'tεμάχια',
	'l10n-wizard'         => 'Wizards',
	'l10n-done'           => 'Τερμάτισε επιτυχός',
	'l10n-skipped'        => 'Skipped',
	'l10n-failed'         => 'Σφάλμα',
	'l10n-setup_1_main'   => 'Extend the `txp_lang.data` field from TINYTEXT to TEXT and add the `owner` field',
	'l10n-setup_1_title'  => 'Change the txp_lang table&#8230;',
	'l10n-setup_1_extend' => 'Extend `txp_lang.data` from TINYTEXT to TEXT',
	'l10n-setup_2_main'   => 'Insert the strings for the MLP Pack',
	'l10n-setup_3_main'   => 'Add `Lang` and `Group` fields to the textpattern table',
	'l10n-setup_3_title'  => 'Add fields to the "textpattern" table',
	'l10n-setup_4_main'   => 'Localise fields in content tables',
	'l10n-setup_6_main'   => 'Process {count} articles',
	'l10n-op_table'       => '{op} the {table} table',
	'l10n-op_tables'      => '{op} the {tables} tables',
	'l10n-comment_op'     => '{op} the default comment invitation',
	'l10n-add_field'      => 'Add the `{table}.{field}` field',
	'l10n-skip_field'     => 'Skip the `{table}.{field}` field - it already exists',
	'l10n-copy_defaults'  => 'Copy defaults to `{table}.{field}` field',
	'l10n-version_errors' => 'MLP Pack Detected Version Problems',
	'l10n-version_reason' => 'The MLP Pack cannot operate in this installation because&#8230;',
	'l10n-version_item'   => 'It requires <strong class="failure">{name} {min}</strong> or above, current install is {current}.',
	'l10n-setup'          => 'MLP Pack Setup',
	'l10n-setup_steps'    => 'The following steps will be taken to install configure the MLP Pack&#8230;',
	'l10n-setup_report'   => 'MLP Pack Setup Report&#8230;',
	);

#
#	These are the regular mlp pack strings that will get installed into the txp_lang table...
#
global $l10n_default_strings;
$l10n_default_strings = array(
	'l10n-add_tags'                    => 'Προσθήκη ετικεττών τοπικοποίησης ?',
	'l10n-add_missing_rend'            => 'Προστέθηκε καιν. μορφοποίηση ($rendition) στο άρθρο $ID',
	'l10n-add_string_rend'             => '* Incomplete.<br>Add renditions in the missing {side} side languages.',
	'l10n-all_languages'               => 'όλες τις γλώσσες',
	'l10n-allow_search_delete'         => 'Να επιτραπεί η δισγραφή η ολική διαγραφή σε τεμάχια > αναζήτηση?',
	'l10n-allow_writetab_changes'      => 'Διαχειρηστές να προβούν σε τροποποίηση μορφοποιήσεων άρθρων/γλώσσας ?',
	'l10n-article_table_ok'            => 'Ο πίνακας άρθρων είναι εντάξι.',
	'l10n-by'                          => 'από',
	'l10n-by_content'                  => 'ανά πεπριεχόμενο',
	'l10n-by_name'                     => 'ονομαστική αναζήτηση',
	'l10n-cannot_export'               => 'Δεν μπωρέι να εξαχθεί $lang ,αφού δεν είναι εγκαταστημένη στόν ιστότοπο.',
	'l10n-clone'                       => 'Κλώνος',
	'l10n-clone_and_translate'         => 'Κλωνοποίηση "{άρθρου}" για μετάφραση',
	'l10n-cannot_delete_all'           => 'Πρέπει να έχουν 1+ Μορφοποίηση',
	'l10n-del_phantom'                 => 'Η πλασματική μορφοποίηση($rendition)  του άρθρου $ID διαγράφηκε.',
	'l10n-delete_plugin'               => 'Θα αφαιρεθούν όλες οι συμβολοσειρές τού αρθρώματος.',
	'l10n-delete_whole_lang'           => 'Να διαγραφούν όλες οι  συμβολοσειρές ($var2) στο $var1?',
	'l10n-edit_resource'               => 'Επεξεργασία  $type: $owner ',
	'l10n-email_xfer_subject'          => '[{sitename}] Ειδοποίηση: {count} μορφοποίηση{οιήσεις} μεταφέρθηκαν σε σάς.',
	'l10n-email_body_other'            => "{txp_username} έχει μεταφέρει τοίς ακόλουθες μορφοποιήσεις σε σάς...\r\n",
	'l10n-email_body_self'             => '\'Εχετε μεταφέρει τις ακόλουθες μορφοποιήσεις στο όνομά σας.',
	'l10n-email_end'                   => "Παρκαλείστε να σβήσετε τον τίτλο URL  όταν μεταφράζετε μια μορφοποίηση!\r\n\r\nΕυχαρειστούμε,\r\n--",
	'l10n-empty'                       => 'κενό',
	'l10n-explain_extra_lang'          => "<p>Αυτές οι γλώσσες δεν έχουν καθοριστεί στις ρυθμίσεις σας.</p>\r\n<p>Εαν δεν τα χρειάζεστε, μπωρούν να διαγραφούν.</p>",
	'l10n-explain_no_tags'             => '<p>* = Αυτές οι φόρμες/σελίδες έχουν συμβολοσειρές, αλλά δεν έχουν <em>ετικέττες</em> απααίτητα για να εμφανιστούν.</p><p>Αυτό μπωρεί να διορθωθεί με την προσθήκη αυτών τον ετικεττών στίς φόρμες/σελίδες.</p>',
	'l10n-explain_specials'            => 'Μια λίστα απο  συμβολοσειρές πού εμφανίζονται στο σύστημα TXP, οχι όμως σε φόρμες και σελίδες.',
	'l10n-export'                      => 'Εξαγωγή',
	'l10n-export_title'                => '<h2>Εξαγωγή {type} Συμβολοσειρών {help}</h2><br/><p>Επιλέξτε Γλώσσες να συμπεριληφθούν, καί πατήστε το κουμπί.</p>',
	'l10n-import'                      => 'Εισαγωγή',
	'l10n-import_count'                => 'Εισήχθησαν {count} {type} συμβολοσειρές.',
	'l10n-import_title'                => '<h2>Εισαγωγή{type} αρθρωμάτων</h2><br/><p>Εισάγετε το κείμενό σας εδώ, καί πατήστε το κουμπί.</p>',
	'l10n-import_warning'              => 'Θα εισαχθούν ή θα διαγραφούν όλες οι εμφανιζόμενες συμβολοσειρές.',
	'l10n-inline_editing'              => 'Ευθύγραμμη επιμέλεια σε φόρμες/σελίδες',
	'l10n-into'                        => 'σε',
	'l10n-inout'                       => 'Εις/Εξαγωγή',
	'l10n-invalid_import_file'         => '<p><strong>Το αρχείο δεν είναι νόμιμο αρχείο συμβολοσειρών</strong></p>',
	'l10n-lang_remove_warning'         => 'Προειδοποίηση: Όλες οι συμβολοσειρές αρθρωμάτων στο $var1 θα διαγραφούν. ',
	'l10n-lang_remove_warning2'        => 'Θα αφαιρεθούν όλες οι συμβολοσειρές στο $var1. Θα πρέπει να εγκαταστήσετε πάλι όλες τις συμβολοσειρές για $var1 εαν το χρησιμοποιήσετε στο μέλλον, ακόμη και στης ρυθμίσεις διαχείρησης. ',
	'l10n-language_not_supported'      => 'Προσπέλαση: Αυτή η γλώσσα σεν υποστιρίζεται.',
	'l10n-languages'                   => 'Γλώσσες',
	'l10n-legend_warning'              => 'Προειδοποίηση/Σφάλμα',
	'l10n-legend_fully_visible'        => 'Εμφάνηση σε όλες τις Γλώσσες',
	'l10n-localised'                   => ' Τοπικοποιημένο',
	'l10n-ltr'                         => 'Α.π.Δ. >',
	'l10n-matches'                     => 'συμβολοσειρές ταιριάζουν.',
	'l10n-missing'                     => 'λείπει.',
	'l10n-missing_rendition'           => 'Άρθρο {id} λείπει μια μορφοποίηση',
	'l10n-no_langs_selected'           => 'Δεν ηπάρχουν προεπιλεγμένες γλώσσες για κλώνοποίηση.',
	'l10n-no_plugin_heading'           => 'Ειδοποίηση...',
	'l10n-pageform-markup'             => '<p><strong>Bold</strong> = Τοπικοποιημένο.<br/>(Δεν είναι αναγκαίο να τοπικοποιηθούν ολα τα τεμάχια.)<br/>[#] = μέτρηση συμβολοσειρών.</p>',
	'l10n-plugin'                      => '\'Αρθρωμα',
	'l10n-registered_plugins'          => 'Καταχωρημένα Αρθρώματα.',
	'l10n-remove_plugin'               => 'Αυτό το άρθρωμα δεν είναι ποια εγκατεστημένο, ή τρέχει απο περιοχή Cache.<br/><br/>Εάν η συμβολοσειρές δεν είναι ποια απαραίτητα,μπωρούν να διαγραούν χορίς προβλήματα.',
	'l10n-renditions'                  => 'Μορφοποιήσεις',
	'l10n-rendition_delete_ok'         => 'Μορφοποίηση {rendition} διαγράφηκε.',
	'l10n-renditions_for'              => 'Μορφοποιήσεις για ',
	'l10n-rtl'                         => '< Δ.π.Α.',
	'l10n-sbn_rubrik'                  => 'Εισάγετε το κειμενό σας εδώ.',
	'l10n-sbn_title'                   => 'Εισάγετε τον όρο αναζήτησης εδώ.',
	'l10n-search_for_strings'          => 'Αναζήτηση συμβολοσειρών',
	'l10n-send_notifications'          => 'Να σταλεί μήνημα ηλεκτρονικού Ταχυδρομείου όταν μεταφέρθηκε μορφοποίηση σε Χρήστη?',
	'l10n-send_notice_to_self'         => '… ακόμα όταν μεταφέρετε στον εαυτό σας?',
	'l10n-send_notice_on_changeauthor' => '… ακόμα όταν συντάκτης άλλαξε στην λίστα περιεχόμενο > μορφοποιήσεις?',
	'l10n-show_langs'                  => 'Εμφάνηση γλωσσών…',
	'l10n-show_legends'                => 'Να εμφανιστεί  ενημερωτικός πίνακας άρθρων?',
	'l10n-skip_rendition'              => 'Προσπελάστηκε η μορφοποίηση($rendition) στην επεξεργασία του άρθρου($ID) αφού χρησιμοποιεί $lang που δεν υποστηρίζεται.',
	'l10n-snippet'                     => 'Τεμάχιο',
	'l10n-snippets'                    => 'τεμάχια',
	'l10n-special'                     => 'Ειδικά',
	'l10n-specials'                    => 'Ειδηκά',
	'l10n-statistics'                  => 'Εμφάνηση μέτρησης',
	'l10n-strings'                     => ' συμβολοσειρές',
	'l10n-strings_match'               => 'συμβολοσειρές βρέθηκαν…',
	'l10n-summary'                     => 'Μέτρηση',
	'l10n-table_rebuilt'               => 'Ο Πίνακς άρθρων διορθώθηκε. Προσπαθείστε πάλι.',
	'l10n-textbox_title'               => 'Εισάγετε το κείμενό σας εδώ.',
	'l10n-total'                       => 'Σύνολο',
	'l10n-unlocalised'                 => 'Μη τοπικοποιημένο',
	'l10n-view_site'                   => 'Εμφάνηση τοπικοποιημένης σελίδας',
	'l10n-warn_section_mismatch'       => 'Σφάλμα Σχέσης τομέων',
	'l10n-warn_lang_mismatch'          => 'Μη ορθολογικότητα σχέσης Γλωσσών',
	'l10n-xlate_to'                    => 'Μετάφραση σε:  ',
	'l10n-cleanup_steps'               => 'The following steps will be taken to cleanup the MLP Pack&#8230;',
	'l10n-drop_field'                  => 'Drop the `{table}.{field}` field',
	'l10n-clean_2_main'                => 'Remove all MLP strings and unregister plugins',
	'l10n-clean_2_unreg'               => 'Unregistered plugin \'{name}\'',
	'l10n-clean_2_remove_all'          => 'Remove plugin strings',
	'l10n-clean_2_remove_count'        => 'Removed {count} strings',
	'l10n-clean_3a_main'               => 'Drop the `Lang` and `Group` fields from the textpattern table',
	'l10n-clean_3a_main_2'             => 'Check this if you do not want to re-install the MLP Pack',
	'l10n-clean_4a_main'               => 'Remove Localised content from tables',
	'l10n-clean_8_main'                => 'Delete cookies',
	'l10n-delete_cookie'               => 'Delete the {lang} cookie',
	'l10n-cleanup'                     => 'MLP Pack Cleanup',
	'l10n-cleanup_report'              => 'MLP Pack Cleanup Report&#8230;',
	'l10n-cleanup_next'                => 'The MLP Pack l10n plugin can now be disabled and/or uninstalled.',
	);

?>
