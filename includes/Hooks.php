<?php

namespace MediaWiki\Extension\CategoryTests;

use Parser;

class Hooks implements
	\MediaWiki\Hook\ParserFirstCallInitHook
{
	private CategoryTests $categoryTests;

	public function __construct() {
		$this->categoryTests = new CategoryTests();
	}

	/**
	 * @param Parser $parser
	 */
	public function onParserFirstCallInit( $parser ) {
		$parser->setFunctionHook( 'ifcategory', [ $this->categoryTests, 'ifcategory' ] );
		$parser->setFunctionHook( 'ifnocategories', [ $this->categoryTests, 'ifnocategories' ] );
		$parser->setFunctionHook( 'switchcategory', [ $this->categoryTests, 'switchcategory' ] );
	}
}
