<?php
// .scoper.inc.php in plugin root.

use Symfony\Component\Finder\Finder;

include 'vendor/barn2/barn2-lib/.scoper.inc.php';

$config = get_lib_scoper_config( 'Barn2\\Plugin\\Posts_Table_Pro\\Dependencies' );

	// Find all assets that we need to append.
	$finder = Finder::create();

	$finder
		->in(
			[
				'vendor/barn2/datatables/assets/scss',
                'vendor/barn2/table-generator/assets/build'
			]
		)
		->filter( static function ( SplFileInfo $file ) {
			return in_array( $file->getExtension(), [ 'css', 'js', 'woff', 'scss', 'php' ], true );
		} );

	$assets = array_keys( \iterator_to_array( $finder ) );

$finder = Finder::create()->
	files()->
	ignoreVCS( true )->
	ignoreDotFiles( true )->
	notName( '/LICENSE|.*\\.md|.*\\.dist|Makefile|composer\\.(json|lock)/' )->
	exclude(
		[
		'doc',
		'test',
		'build',
		'test_old',
		'tests',
		'Tests',
		'vendor-bin',
		'wp-coding-standards',
		'squizlabs',
		'phpcompatibility',
		'dealerdirect',
		'bin',
		'vendor',
		'deprecated',
		'mu-plugins',
		'plugin-boilerplate',
		'templates'
		]
	)->
    append(
        $assets
    )->
	in( [
		'vendor/barn2/datatables', 
        'vendor/barn2/table-generator/assets', 
        'vendor/barn2/table-generator', 
        'vendor/berlindb/core'
	] )->
	name( [ '*.php' ] );
  
$config['finders'][] = $finder;

return $config;