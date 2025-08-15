<?php

namespace MediaWiki\Extension\CategoryTests;

use Parser;

class Hooks implements
	\MediaWiki\Hook\ParserFirstCallInitHook
{
	/**
	 * @param Parser $parser
	 */
	public function onParserFirstCallInit( $parser ) {
		global $wgExtCategoryTests;

		$wgExtCategoryTests = new CategoryTests();
		$parser->setFunctionHook( 'ifcategory', [ &$wgExtCategoryTests, 'ifcategory' ] );
		$parser->setFunctionHook( 'ifnocategories', [ &$wgExtCategoryTests, 'ifnocategories' ] );
		$parser->setFunctionHook( 'switchcategory', [ &$wgExtCategoryTests, 'switchcategory' ] );
	}
}
