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
 */

/**
 * @group Extensions
 * @group VistaprintExtensions
 * @group CategoryTagSorter
 * @group Database
 * @covers CategoryTagSorter
 */
class CategoryTagSorterTest extends MediaWikiIntegrationTestCase {

	// ------ properties ------------------------------------------------

	const TEST_USERNAME = 'UnitTestor';

	const TEST_PAGENAME = 'UnitTestingArticle';

	/** @var string[] */
	private static $categoriesWikitext = [
		'[[Category:ZZZ]]',
		'[[Category:.NET collections]]',
		'[[Category:SiteBuilder]]',
		'[[Category:Wiki regression tests]]',
		'[[Category:Google Mini]]',
		'[[Category:Architecture]]'
	];

	/**
	 * Note: Notice the spaces have been changed to underscores,
	 *       and the categories are the keys of the array.
	 * @var string[]
	 */
	private static $categoriesInOrder = [
		'.NET_collections'      => '',
		'Architecture'          => '',
		'Google_Mini'           => '',
		'SiteBuilder'           => '',
		'Wiki_regression_tests' => '',
		'ZZZ'                   => ''
	];

	/** @var string[] */
	private static $categoriesOutOfOrder = [
		'ZZZ'                   => '',
		'.NET_collections'      => '',
		'SiteBuilder'           => '',
		'Wiki_regression_tests' => '',
		'Google_Mini'           => '',
		'Architecture'          => '',
	];

	// ------ helper methods ------------------------------------------------

	/**
	 * Compares the keys and values of two arrays making sure the order
	 * is the same.
	 *
	 * @param array $a
	 * @param array $b
	 * @protected
	 * @return array
	 */
	protected function compareTwoArrays( array $a, array $b ) {
		$expectedIterator = new ArrayIterator( $a );
		$actualIterator   = new ArrayIterator( $b );

		$isSortedCorrectly = true;
		$message = "";

		while ( $expectedIterator->valid() ) {

			if ( $expectedIterator->key() !== $actualIterator->key() ) {
				$isSortedCorrectly = false;
				$message = sprintf(
					"Array keys are different: %s, %s",
					$expectedIterator->key(),
					$actualIterator->key()
				);
				break;
			}
			if ( $expectedIterator->current() !== $actualIterator->current() ) {
				$isSortedCorrectly = false;
				$message = sprintf(
					"Array values are different: %s, %s",
					$expectedIterator->current(),
					$actualIterator->current()
				);
				break;
			}
			$actualIterator->next();
			$expectedIterator->next();
		}
		return [ $isSortedCorrectly, $message ];
	}

	/**
	 * Get an instance of the parser;
	 *
	 * @return Parser
	 */
	protected function getNewParser() {
		$parserFactory = $this->getServiceContainer()->getParserFactory();
		return $parserFactory->create();
	}

	// ------ tests ----------------------------------------------------------

	/**
	 * Makes sure the sorting is done correctly
	 *
	 * @return void
	 */
	public function testSortIsCorrect() {
		RequestContext::getMain()->setUser( User::newFromName( self::TEST_USERNAME ) );
		RequestContext::getMain()->getUser()->load();

		// Parse the "page"
		$wikitext = implode( "\n", self::$categoriesWikitext );
		$parser = $this->getNewParser();
		$parserOutput = $parser->parse(
			$wikitext,
			Title::newFromText( self::TEST_PAGENAME ),
			ParserOptions::NewFromUser( RequestContext::getMain()->getUser() )
		);
		$parsedCategories = $parserOutput->getCategories();

		$this->assertSameSize(
			self::$categoriesInOrder,
			$parsedCategories
		);

		[ $isSortedCorrectly, $message ] = $this->compareTwoArrays(
			self::$categoriesInOrder,
			$parsedCategories
		);
		$this->assertTrue( $isSortedCorrectly, $message );
	}

	/**
	 * The user-preference turns off this extension's behaviour. This means that
	 * the sorting should remain in the order written in the wikitext.
	 *
	 * @return void
	 */
	public function testPreferencesIsRespected() {
		RequestContext::getMain()->setUser( User::newFromName( self::TEST_USERNAME ) );
		RequestContext::getMain()->getUser()->load();

		// Turn off this extension's behaviour
		$userOptionsManager = $this->getServiceContainer()->getUserOptionsManager();
		$userOptionsManager->setOption(
			RequestContext::getMain()->getUser(),
			CategoryTagSorter::PREF_NAME,
			1
		);

		// Parse the "page"
		$wikitext = implode( "\n", self::$categoriesWikitext );
		$parser = $this->getNewParser();
		$parserOutput = $parser->parse(
			$wikitext,
			Title::newFromText( self::TEST_PAGENAME ),
			ParserOptions::NewFromUser( RequestContext::getMain()->getUser() )
		);
		$parsedCategories = $parserOutput->getCategories();

		$this->assertSameSize(
			self::$categoriesOutOfOrder,
			$parsedCategories
		);

		[ $isSortedCorrectly, $message ] = $this->compareTwoArrays(
			self::$categoriesOutOfOrder,
			$parsedCategories
		);
		$this->assertTrue( $isSortedCorrectly, $message );
	}

}
