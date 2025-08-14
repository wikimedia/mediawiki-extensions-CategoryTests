<?php

class ExtCategoryTestsHooks implements
	\MediaWiki\Hook\ParserFirstCallInitHook
{
	/**
	 * @param Parser $parser
	 */
	public function onParserFirstCallInit( $parser ) {
		global $wgExtCategoryTests;

		$wgExtCategoryTests = new ExtCategoryTests();
		$parser->setFunctionHook( 'ifcategory', [ &$wgExtCategoryTests, 'ifcategory' ] );
		$parser->setFunctionHook( 'ifnocategories', [ &$wgExtCategoryTests, 'ifnocategories' ] );
		$parser->setFunctionHook( 'switchcategory', [ &$wgExtCategoryTests, 'switchcategory' ] );
	}
}
