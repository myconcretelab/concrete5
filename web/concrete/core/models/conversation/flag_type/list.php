<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_Conversation_FlagType_List extends DatabaseItemList {

	public function __construct() {
		$this->setQuery('select * from ConversationFlaggedMessageTypes');
		$this->sortBy('cnvMessageFlagTypeID', 'asc');
	}

	public function get() {
		$r = parent::get(0, 0);
		$flagTypes = array();
		foreach($r as $row) {
			$flagTypes[] = ConversationFlagType::getByID($row['cnvMessageFlagTypeID']);
		}
		return $flagTypes;
	}

}