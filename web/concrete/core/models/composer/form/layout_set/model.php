<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ComposerFormLayoutSet extends Object {

	public function getComposerFormLayoutSetID() {return $this->cmpFormLayoutSetID;}
	public function getComposerFormLayoutSetName() {return $this->cmpFormLayoutSetName;}
	public function getComposerFormLayoutSetDisplayOrder() {return $this->cmpFormLayoutSetDisplayOrder;}
	public function getComposerID() {return $this->cmpID;}
	public function getComposerObject() {return Composer::getByID($this->cmpID);}

	public static function getList(Composer $composer) {
		$db = Loader::db();
		$cmpFormLayoutSetIDs = $db->GetCol('select cmpFormLayoutSetID from ComposerFormLayoutSets where cmpID = ? order by cmpFormLayoutSetDisplayOrder asc', array($composer->getComposerID()));
		$list = array();
		foreach($cmpFormLayoutSetIDs as $cmpFormLayoutSetID) {
			$set = ComposerFormLayoutSet::getByID($cmpFormLayoutSetID);
			if (is_object($set)) {
				$list[] = $set;
			}
		}
		return $list;
	}

	public static function getByID($cmpFormLayoutSetID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from ComposerFormLayoutSets where cmpFormLayoutSetID = ?', array($cmpFormLayoutSetID));
		if (is_array($r) && $r['cmpFormLayoutSetID']) {
			$set = new ComposerFormLayoutSet;
			$set->setPropertiesFromArray($r);
			return $set;
		}
	}

	public function export($fxml) {
		$node = $fxml->addChild('set');
		$node->addAttribute('name', $this->getComposerFormLayoutSetName());
		$controls = ComposerFormLayoutSetControl::getList($this);
		foreach($controls as $con) {
			$con->export($node);
		}
	}

	public function updateFormLayoutSetName($cmpFormLayoutSetName) {
		$db = Loader::db();
		$db->Execute('update ComposerFormLayoutSets set cmpFormLayoutSetName = ? where cmpFormLayoutSetID = ?', array(
			$cmpFormLayoutSetName, $this->cmpFormLayoutSetID
		));
		$this->cmpFormLayoutSetName = $cmpFormLayoutSetName;
	}


	public function updateFormLayoutSetDisplayOrder($displayOrder) {
		$db = Loader::db();
		$db->Execute('update ComposerFormLayoutSets set cmpFormLayoutSetDisplayOrder = ? where cmpFormLayoutSetID = ?', array(
			$displayOrder, $this->cmpFormLayoutSetID
		));
		$this->cmpFormLayoutSetDisplayOrder = $displayOrder;
	}

	public function delete() {
		$controls = ComposerFormLayoutSetControl::getList($this);
		foreach($controls as $control) {
			$control->delete();
		}
		$db = Loader::db();
		$db->Execute('delete from ComposerFormLayoutSets where cmpFormLayoutSetID = ?', array($this->cmpFormLayoutSetID));
		$composer = $this->getComposerObject();
		$composer->rescanFormLayoutSetDisplayOrder();
	}

	public function rescanFormLayoutSetControlDisplayOrder() {
		$sets = ComposerFormLayoutSetControl::getList($this);
		$displayOrder = 0;
		foreach($sets as $s) {
			$s->updateFormLayoutSetControlDisplayOrder($displayOrder);
			$displayOrder++;
		}
	}


}