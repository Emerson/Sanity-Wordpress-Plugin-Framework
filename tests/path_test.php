<?php
require_once dirname(dirname(__FILE__)).'/vendor/autoload.php';
require_once dirname(dirname(__FILE__)).'/framework/sanity.php';


class PathTest extends PHPUnit_Framework_TestCase {

	public function testInitialPath() {
		$Sanity = new SanityPluginFramework();
		echo $Sanity->plugin_path;
		echo "hello";
	}

}


?>