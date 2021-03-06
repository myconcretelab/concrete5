<?php defined('C5_EXECUTE') or die('Access Denied.');

class Concrete5_Model_System_ImageEditor_Filter {
	public $scsID;
	public $scsHandle;
	public $scsDisplayName;
	public $scsDisplayOrder;
	public $pkgID;

	/**
 	 * Retrieves a list of filter objects.
 	 */
	public static function getList() {
		$db = Loader::db();
		$q  = $db->query('SELECT * FROM SystemImageEditorFilters');
		$cs = self::getSortedListFromQuery($q);
		return $cs;
	}

	/**
	 * Retrieves a list of filter objects by package
	 * this is used in package uninstall.
	 */
	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$q  = $db->query('SELECT * FROM SystemImageEditorFilters
							WHERE scsHandle=?',array($scsHandle));
		$cs = self::getSortedListFromQuery($q);
		return $cs;
	}

	/**
	 * Fancy way to sort using the display order
	 * Not super useful right now.
	 *
	 * This method naturally sorts first by display order, then by the orderby
	 * on the query.
	 */
	public static function getSortedListFromQuery($q) {
		$unsorted = array();
		while ($row = $q->FetchRow()) {
			$cs = self::load($row);
			$oid = $cs->getImageEditorFilterDisplayOrder();
			if (!$unsorted[$oid]) $unsorted[$oid] = array();
			$unsorted[$oid][] = $cs;
		}
		$sorted = array();
		foreach ($unsorted as $arr) {
			foreach ($arr as $v) {
				$sorted[] = $v;
			}
		}
		return $sorted;
	}

	/**
	 * Get the basic object
	 */
	public static function getByID($scsID) {
		$db = Loader::db();
		$q  = $db->query('SELECT * FROM SystemImageEditorFilters
							WHERE scsID=?',array($scsID));
		return self::load($q->FetchRow());
	}
	public static function getByHandle($scsHandle) {
		$db = Loader::db();
		$q  = $db->query('SELECT * FROM SystemImageEditorFilters
							WHERE scsHandle=?',array($scsHandle));
		return self::load($q->FetchRow());
	}
	public static function load($arr) {
		$obj = new SystemImageEditorFilter;
		foreach((array) $arr as $key=>$val) {
			$obj->{$key} = $val;
		}
		return $obj;
	}

	/**
	 * Retrieve Data
	 */
	public function getImageEditorFilterID() {
		return $this->scsID;
	}
	public function getImageEditorFilterHandle() {
		return $this->scsHandle;
	}
	public function getImageEditorFilterName() {
		return $this->scsDisplayName;
	}
	public function getImageEditorFilterDisplayOrder() {
		return $this->scsDisplayOrder;
	}
	public function getPackageID() {
		return $this->pkgID;
	}
	public function getPackageHandle() {
		return $this->getPackageObject()->getPackageHandle();
	}
	public function getPackageObject() {
		return Package::getByID($this->getPackageID());
	}

	/**
	 * Basic management of these objects
	 */
	public static function add($scsHandle, $scsDisplayName, $pkg = false) {
		$db = Loader::db();
		$db->execute('INSERT INTO SystemImageEditorFilters
						(scsHandle,scsDisplayName,pkgID) VALUES (?,?,?)',
						array($scsHandle,$scsDisplayName,$pkg));
		return self::getByHandle($scsHandle);
	}

	public function delete() {
		$db = Loader::db();
		$db->execute('DELETE FROM SystemImageEditorFilters WHERE scsID=?',
			array($this->scsID));
		return true;
	}
}