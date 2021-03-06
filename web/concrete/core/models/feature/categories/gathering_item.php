<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_GatheringItemFeatureCategory extends FeatureCategory {

	public function assignmentIsInUse(FeatureAssignment $fa) {
		$db = Loader::db();
		$num = $db->GetOne('select count(gaiID) as total from GatheringItemFeatureAssignments where faID = ?', array($fa->getFeatureAssignmentID()));
		return $num > 0;
	}
		
}
