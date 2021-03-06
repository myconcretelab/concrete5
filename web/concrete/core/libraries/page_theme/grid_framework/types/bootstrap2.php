<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_Bootstrap2PageThemeGridFramework extends PageThemeGridFramework {

	public function getPageThemeGridFrameworkName() {
		return t('Twitter Bootstrap');
	}

	public function getPageThemeGridFrameworkRowStartHTML() {
		return '<div class="row">';
	}

	public function getPageThemeGridFrameworkRowEndHTML() {
		return '</div>';
	}

	public function getPageThemeGridFrameworkColumnClasses() {
		$columns = array(
			'span1',
			'span2',
			'span3',
			'span4',
			'span5',
			'span6',
			'span7',
			'span8',
			'span9',
			'span10',
			'span11',
			'span12'
		);
		return $columns;	
	}

	public function getPageThemeGridFrameworkColumnOffsetClasses() {
		$offsets = array(
			'offset1',
			'offset2',
			'offset3',
			'offset4',
			'offset5',
			'offset6',
			'offset7',
			'offset8',
			'offset9',
			'offset10',
			'offset11',
			'offset12'
		);
		return $offsets;	
	}

	public function getGatheringGridItemMargin() {
		return 20;
	}

	public function getGatheringGridItemWidth() {
		return 146;
	}

	public function getGatheringGridItemHeight() {
		return 146;
	}

}
