<?php
/**
 * Copyright 2013 Vistaprint Schweiz GmbH.
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
 *
 * @file
 * @ingroup Extensions
 *
 */

use MediaWiki\MediaWikiServices;

/**
 * CategoryTagSorter - sorts categories for easy reading
 *
 */
class CategoryTagSorter {

	const PREF_NAME = 'categorysortdisable';

	/**
	 * Sorts the categories ascii-betically
	 *
	 * @param Parser $parser
	 * @param mixed $text
	 * @return bool
	 */
	public static function sort( Parser $parser, $text ) {
		$userOptionsLookup = MediaWikiServices::getInstance()->getUserOptionsLookup();
		if ( $userOptionsLookup->getOption( $parser->getUserIdentity(), self::PREF_NAME ) === null ) {
			ksort( $parser->getOutput()->getCategories() );
		}
		return true;
	}

	/**
	 * Adds a user-preference to turn off this extension's behaviour
	 *
	 * @param User $user
	 * @param array &$prefs
	 * @return bool
	 */
	public static function prefs( User $user, array &$prefs ) {
		$prefs[self::PREF_NAME] = [
			'type' => 'toggle',
			'section' => 'rendering/advancedrendering',
			'label-message' => 'tog-categorysortdisable',
		];
		return true;
	}

}
