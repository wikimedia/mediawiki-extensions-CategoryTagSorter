<?php
/**
 * CategoryTagSorter - Sort the category tags on every article page.
 *
 *
 * @file
 * @ingroup Extensions
 *
 * @author Dan Barrett
 * @author Daniel Renfro
 *
 *
 * Copyright 2013 Vistaprint, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */



/**
 * This is not a valid point of entry.
 *
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	echo <<< EOT
		To install my extension, put the following line in Localsettings.php:
		require_once( "\$IP/extensions/CategoryTagSorter/CategoryTagSorter.php" );
EOT;
	exit( 1 );
}

/**
 * Credits
 *
 */
$wgExtensionCredits['other'][] = array(
	'name' => 'CategoryTagSorter',
	'author' => array(
		'[http://mediawiki.org/wiki/User:Maiden_taiwan Dan Barrett]',
		'[http://mediawiki.org/wiki/User:AlephNull Daniel Renfro]',
	),
	'url' => 'http://mediawiki.org/wiki/Extension:CategoryTagSorter',
	'descriptionmsg' => 'categorytagsorter-desc',
	'version' => '0.2',
);

/**
 * The body of the extension.
 *
 */
$wgAutoloadClasses['CategoryTagSorter'] = __DIR__ . '/CategoryTagSorter_body.php';


/**
 * Internationalization/localization
 *
 */
$wgExtensionMessagesFiles['CategoryTagSorter'] = __DIR__ . '/CategoryTagSorter.i18n.php';



/**
 * Hooks
 *
 */
$wgHooks['ParserBeforeTidy'][] = 'CategoryTagSorter::sort';
$wgHooks['GetPreferences'][] = 'CategoryTagSorter::prefs';
$wgHooks['UnitTestsList'][] = 'wfCategoryTagSorterUnitTests';



/**
 * Add our unit-tests to the list
 *
 */
function wfCategoryTagSorterUnitTests( array &$files ) {
	$files[] = dirname( __FILE__ ) . '/tests/CategoryTagSorterTest.php';
	return true;
} ;
