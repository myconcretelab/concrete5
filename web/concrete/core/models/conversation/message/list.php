<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_Conversation_Message_List extends DatabaseItemList {

	protected $sortBy = 'cnvMessageDateCreated';
	protected $sortByDirection = 'asc';
	protected $cnvID;

	public function __construct() {
		$this->setQuery('select cnvm.cnvMessageID from ConversationMessages cnvm');
	}

	public function filterByConversation(Conversation $cnv) {
		$this->filter('cnvID', $cnv->getConversationID());
	}

	public function sortByDateDescending() {
		$this->sortBy('cnvMessageDateCreated', 'desc');
	}

	public function filterByFlag(ConversationFlagType $type) {
		$this->addToQuery('inner join ConversationFlaggedMessages cnf on cnvm.cnvMessageID = cnf.cnvMessageID');
		$this->filter('cnf.cnvMessageFlagTypeID', $type->getConversationFlagTypeID());
	}
	
	public function sortByDateAscending() {
		$this->sortBy('cnvMessageDateCreated', 'asc');
	}

	public function filterByApproved() {
		$this->filter('cnvIsMessageApproved', 1);
	}

	public function filterByUnapproved() {
		$this->filter('cnvIsMessageApproved', 0);
	}
	
	public function filterByDeleted() {
		$this->filter('cnvIsMessageDeleted', 1);
	}

	public function filterByKeywords($keywords) {
		$this->addToQuery('inner join Conversations cnv on cnvm.cnvID = cnv.cnvID left join CollectionVersions cv on (cnv.cID = cv.cID and cv.cvIsApproved = 1)');

		$db = Loader::db();
		$qk = $db->quote('%' . $keywords . '%');
		$this->filter(false, "(cnvMessageSubject like $qk or cnvMessageBody like $qk or cvName like $qk)");		
	}
	
	public function get($num = 0, $offset = 0) {
		$r = parent::get($num, $offset);
		$messages = array();
		foreach($r as $row) {
			$messages[] = ConversationMessage::getByID($row['cnvMessageID']);	
		}
		return $messages;
	}

}
