<?php

namespace MediaWiki\Extension\CategoryTests;

use MediaWiki\Parser\MagicWordFactory;
use MediaWiki\Parser\Parser;
use MediaWiki\Title\Title;
use Wikimedia\Rdbms\IConnectionProvider;

/**
 * CategoryTests extension by Ryan Schmidt
 * Functions for category testing
 * Check https://www.mediawiki.org/wiki/Extension:CategoryTests for more info on what everything does
 */
class CategoryTests {
	public function __construct(
		private readonly IConnectionProvider $dbProvider,
		private readonly MagicWordFactory $magicWordFactory,
	) {
	}

	public function ifcategory(
		Parser $parser,
		string $category = '',
		string $then = '',
		string $else = '',
		string $pagename = '',
	): string {
		if ( $category === '' ) {
			return $then;
		}
		if ( $pagename === '' ) {
			$title = $parser->getTitle();
			$page = $title->getDBkey();
			$ns = $title->getNamespace();
		} else {
			$title = Title::newFromText( $pagename );
			if ( !( $title instanceof Title ) || !$title->exists() ) {
				return $else;
			}
			$page = $title->getDBkey();
			$ns = $title->getNamespace();
		}
		$cattitle = Title::makeTitleSafe( NS_CATEGORY, $category );
		if ( !( $cattitle instanceof Title ) ) {
			return $else;
		}
		$catkey = $cattitle->getDBkey();
		$dbr = $this->dbProvider->getReplicaDatabase();
		$res = $dbr->newSelectQueryBuilder()
			->select( 'cl_from' )
			->tables( [ 'page', 'categorylinks' ] )
			->where( [
				'page_id=cl_from',
				'page_namespace' => $ns,
				'page_title' => $page,
				'cl_to' => $catkey,
			] )
			->limit( 1 )
			->caller( __METHOD__ )
			->fetchResultSet();
		if ( $res->numRows() === 0 ) {
			return $else;
		}
		return $then;
	}

	public function ifnocategories(
		Parser $parser,
		string $then = '',
		string $else = '',
		string $pagename = '',
	): string {
		if ( $pagename === '' ) {
			$title = $parser->getTitle();
			$page = $title->getDBkey();
			$ns = $title->getNamespace();
		} else {
			$title = Title::newFromText( $pagename );
			if ( !( $title instanceof Title ) || !$title->exists() ) {
				return $then;
			}
			$page = $title->getDBkey();
			$ns = $title->getNamespace();
		}
		$dbr = $this->dbProvider->getReplicaDatabase();
		$res = $dbr->newSelectQueryBuilder()
			->select( 'cl_from' )
			->tables( [ 'page', 'categorylinks' ] )
			->where( [
				'page_id=cl_from',
				'page_namespace' => $ns,
				'page_title' => $page,
			] )
			->limit( 1 )
			->caller( __METHOD__ )
			->fetchResultSet();
		if ( $res->numRows() === 0 ) {
			return $then;
		}
		return $else;
	}

	public function switchcategory(
		Parser $parser,
		string ...$args,
	): string {
		$found = false;
		$parts = [];
		$default = null;
		$page = '';
		foreach ( $args as $arg ) {
			$parts = array_map( 'trim', explode( '=', $arg, 2 ) );
			if ( count( $parts ) === 2 ) {
				$mwPage = $this->magicWordFactory->get( 'page' );
				if ( $mwPage->matchStartAndRemove( $parts[0] ) ) {
					$page = $parts[1];
					continue;
				}
				if ( $found || $this->ifcategory( $parser, $parts[0], '1', '', $page ) ) {
					return $parts[1];
				} else {
					$mwDefault = $this->magicWordFactory->get( 'default' );
					if ( $mwDefault->matchStartAndRemove( $parts[0] ) ) {
						$default = $parts[1];
					}
				}
			} elseif ( count( $parts ) === 1 ) {
				if ( $this->ifcategory( $parser, $parts[0], '1', '', $page ) ) {
					$found = true;
				}
			}
		}

		if ( count( $parts ) === 1 ) {
			return $parts[0];
		} elseif ( $default !== null ) {
			return $default;
		} else {
			return '';
		}
	}
}
