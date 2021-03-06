<?php defined('C5_EXECUTE') or die("Access Denied.");

abstract class Concrete5_Model_ConversationRatingType extends Object {

	abstract public function outputRatingTypeHTML();
	abstract public function adjustConversationMessageRatingTotalScore(ConversationMessage $message);
	abstract public function rateMessage();
	
	public static function getList() {
		$db = Loader::db();
		$handles = $db->GetCol('select cnvRatingTypeHandle from ConversationRatingTypes order by cnvRatingTypeHandle asc');
		$types = array();
		foreach($handles as $handle) {
			$ratingType = ConversationRatingType::getByHandle($handle);
			if (is_object($ratingType)) {
				$types[] = $ratingType;
			}
		}
		return $types;
	}

	public static function add($cnvRatingTypeHandle, $cnvRatingTypeName, $cnvRatingTypeCommunityPoints, $pkgID = false) {
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db = Loader::db();
		$db->Execute('insert into ConversationRatingTypes (cnvRatingTypeHandle, cnvRatingTypeName, cnvRatingTypeCommunityPoints, pkgID) values (?, ?, ?, ?)', array($cnvRatingTypeHandle, $cnvRatingTypeName, $cnvRatingTypeCommunityPoints, $pkgID));
		return ConversationRatingType::getByHandle($cnvRatingTypeHandle);
	}

	public static function getByHandle($cnvRatingTypeHandle) {
		$db = Loader::db();
		$r = $db->GetRow('select cnvRatingTypeID, cnvRatingTypeHandle, cnvRatingTypeName, cnvRatingTypeCommunityPoints, pkgID from ConversationRatingTypes where cnvRatingTypeHandle = ?', array($cnvRatingTypeHandle));
		
		if (is_array($r) && $r['cnvRatingTypeHandle']) {
			$class = Loader::helper('text')->camelcase($r['cnvRatingTypeHandle']) . 'ConversationRatingType';
			$sc = new $class();
			$sc->setPropertiesFromArray($r);
			return $sc;
		}
	}
	
	public static function getByID($cnvRatingTypeID) {
		$db = Loader::db();
		$r = $db->GetRow('select cnvRatingTypeID, cnvRatingTypeHandle, cnvRatingTypeName, cnvRatingTypeCommunityPoints, pkgID from ConversationRatingTypes where cnvRatingTypeID = ?', array($cnvRatingTypeID));
		
		if (is_array($r) && $r['cnvRatingTypeHandle']) {
			$class = Loader::helper('text')->camelcase($r['cnvRatingTypeHandle']) . 'ConversationRatingType';
			$sc = new $class();
			$sc->setPropertiesFromArray($r);
			return $sc;
		}
	}

	public static function exportList($xml) {
		$list = self::getList();
		$nxml = $xml->addChild('conversationratingtypes');
		foreach($list as $sc) {
			$activated = 0;
			$type = $nxml->addChild('conversationratingtype');
			$type->addAttribute('handle', $sc->getConversationRatingTypeHandle());
			$type->addAttribute('name', $sc->getConversationRatingTypeName());
			$type->addAttribute('package', $sc->getPackageHandle());
			$type->addAttribute('points', $sc->getConversationRatingTypePoints());
		}
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$handles = $db->GetCol('select cnvRatingTypeHandle from ConversationRatingTypes where pkgID = ? order by cnvRatingTypeHandle asc', array($pkg->getPackageID()));
		$types = array();
		foreach($handles as $handle) {
			$ratingType = ConversationRatingType::getByHandle($handle);
			if (is_object($ratingType)) {
				$types[] = $ratingType;
			}
		}
		return $types;
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from ConversationRatingTypes where cnvRatingTypeHandle = ?', array($this->cnvRatingTypeHandle));
	}


	public function getConversationRatingTypeHandle() {return $this->cnvRatingTypeHandle;}
	public function getConversationRatingTypeName() {return $this->cnvRatingTypeName;}
	public function getConversationRatingTypeID() {return $this->cnvRatingTypeID;}
	public function getConversationRatingTypePoints() {return $this->cnvRatingTypeCommunityPoints;}
	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}
	public function getPackageObject() {return Package::getByID($this->pkgID);}

}