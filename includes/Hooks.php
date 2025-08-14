<?php

namespace MediaWiki\Extension\CategoryTests;

use MediaWiki\Parser\MagicWordFactory;
use MediaWiki\Parser\Parser;
use Wikimedia\Rdbms\ILoadBalancer;

class Hooks implements
	\MediaWiki\Hook\ParserFirstCallInitHook
{
	private CategoryTests $categoryTests;

	public function __construct(
		ILoadBalancer $loadBalancer,
		MagicWordFactory $magicWordFactory
	) {
		$this->categoryTests = new CategoryTests(
			$loadBalancer,
			$magicWordFactory
		);
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
