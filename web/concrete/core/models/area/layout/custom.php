<?

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_CustomAreaLayout extends AreaLayout {

	protected $arLayoutType = 'custom';
	
	protected function loadDetails() {
		$db = Loader::db();
		$row = $db->GetRow('select arLayoutSpacing, arLayoutIsCustom from AreaLayouts where arLayoutID = ?', array($this->arLayoutID));
		$this->setPropertiesFromArray($row);
	}	

	public function getAreaLayoutSpacing() {
		return $this->arLayoutSpacing;
	}

	public function hasAreaLayoutCustomColumnWidths() {
		return $this->arLayoutIsCustom;
	}

	public function duplicate() {
		$db = Loader::db();
		$v = array($this->arLayoutSpacing, $this->arLayoutIsCustom);
		$db->Execute('insert into AreaLayouts (arLayoutSpacing, arLayoutIsCustom) values (?, ?)', $v);
		$newAreaLayoutID = $db->Insert_ID();
		if ($newAreaLayoutID) {
			$newAreaLayout = AreaLayout::getByID($newAreaLayoutID);
			$columns = $this->getAreaLayoutColumns();
			foreach($columns as $col) {
				$col->duplicate($newAreaLayout);
			}
			return $newAreaLayout;
		}
	}

	public function setAreaLayoutColumnSpacing($spacing) {
		if (!$spacing) {
			$spacing = 0;
		}
		$db = Loader::db();
		$db->Execute('update AreaLayouts set arLayoutSpacing = ? where arLayoutID = ?', array($spacing, $this->arLayoutID));
		$this->arLayoutSpacing = $spacing;
	}

	public function enableAreaLayoutCustomColumnWidths() {
		$db = Loader::db();
		$db->Execute('update AreaLayouts set arLayoutIsCustom = ? where arLayoutID = ?', array(1, $this->arLayoutID));
		$this->arLayoutIsCustom = true;
	}

	public function disableAreaLayoutCustomColumnWidths() {
		$db = Loader::db();
		$db->Execute('update AreaLayouts set arLayoutIsCustom = ? where arLayoutID = ?', array(0, $this->arLayoutID));
		$this->arLayoutIsCustom = false;
	}


	public static function add($spacing = 0, $iscustom = false) {
		if (!$spacing) {
			$spacing = 0; // just in case
		}
		if (!$iscustom) {
			$iscustom = 0;
		} else {
			$iscustom = 1;
		}

		$db = Loader::db();
		$db->Execute('insert into AreaLayouts (arLayoutSpacing, arLayoutIsCustom, arLayoutUsesThemeGridFramework) values (?, ?, ?)', array($spacing, $iscustom, 0));
		$arLayoutID = $db->Insert_ID();
		if ($arLayoutID) {
			$ar = CustomAreaLayout::getByID($arLayoutID);
			return $ar;
		}
	}

	public function addLayoutColumn() {
		$columnID = parent::addLayoutColumn();
		$db = Loader::db();
		$db->Execute('insert into AreaLayoutCustomColumns (arLayoutColumnID) values (?)', array($columnID));
		return CustomAreaLayoutColumn::getByID($columnID);
	}



}