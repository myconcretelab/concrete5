<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_RssFeedGatheringDataSourceConfiguration extends GatheringDataSourceConfiguration  {

	public function setRssFeedURL($url) {
		$this->url = $url;
	}

	public function getRssFeedURL() {
		return $this->url;
	}

}
