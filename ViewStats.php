<?php
/**
 * ViewStats extension
 *
 * For more info see http://mediawiki.org/wiki/Extension:ViewStats
 *
 * @file
 * @ingroup Extensions
 * @author Scott Caldwell, 2018
 * @author Steven Orvis, 2018
 * @license GNU General Public Licence 2.0 or later
 */
 
 # Alert the user that this is not a valid access point to MediaWiki if they try to access the special pages file directly.
if ( !defined( 'MEDIAWIKI' ) ) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/ViewStats/ViewStats.php" );
EOT;
	exit( 1 );
}

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'View Stats',
	'author' => array(
		'Scott Caldwell',
		'Steven Orvis',
	),
	'version'  => '0.0.1',
	'url' => '',
	'descriptionmsg' => 'viewstats-desc',
);

/* Setup */

$wgMessagesDirs['ViewStats'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['ViewStatsAlias'] = __DIR__ . '/ViewStats.alias.php';

// Autoload classes
$wgAutoloadClasses['SpecialViewStats'] = __DIR__ . '/SpecialViewStats.php'; # Location of the SpecialViewStats class (Tell MediaWiki to load this file)

// Register files
$wgAutoloadClasses['ViewStatsHooks'] = __DIR__ . '/ViewStats.hooks.php';

// Register hooks
$wgHooks['LoadExtensionSchemaUpdates'][] = 'ViewStatsHooks::onLoadExtensionSchemaUpdates';
$wgHooks['SpecialSearchCreateLink'][] = 'ViewStatsHooks::onSpecialSearchCreateLink';
$wgHooks['SpecialSearchNogomatch'][] = 'ViewStatsHooks::onSpecialSearchNogomatch';

// Register special pages
$wgSpecialPages['ViewStats'] = 'SpecialViewStats'; # Tell MediaWiki about the new special page and its class name

/* Configuration */

