<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_CollectionAttributeComposerControl extends ComposerControl {
	
	protected $akID;
	protected $ak = false;
	protected $cmpControlTypeHandle = 'collection_attribute';
	
	public function setAttributeKeyID($akID) {
		$this->akID = $akID;
		$this->setComposerControlIdentifier($akID);
	}

	public function composerFormControlSupportsValidation() {
		return true;
	}

	public function export($node) {
		$ak = $this->getAttributeKeyObject();
		$node->addAttribute('handle', $ak->getAttributeKeyHandle());
	}

	public function removeComposerControlFromDraft() {
		$ak = $this->getAttributeKeyObject();
		$c = $this->cmpDraftObject->getComposerDraftCollectionObject();
		$c->clearAttribute($ak);
	}

	public function getAttributeKeyObject() {
		if (!$this->ak) {
			$this->ak = CollectionAttributeKey::getByID($this->akID);
		}
		return $this->ak;
	}

	public function getAttributeKeyID() {
		return $this->akID;
	}

	public function getComposerControlCustomTemplates() {
		return array();
	}

	public function canComposerControlSetPageName() {
		$ak = $this->getAttributeKeyObject();
		if ($ak->getAttributeKeyHandle() == 'meta_title') {
			return true;
		}
		return false;
	}

	public function getComposerControlPageNameValue(Page $c) {
		$ak = $this->getAttributeKeyObject();
		return $c->getAttribute($ak->getAttributeKeyHandle());
	}

	public function getComposerControlDraftValue() {
		if (is_object($this->cmpDraftObject)) {
			$ak = $this->getAttributeKeyObject();
			$c = $this->cmpDraftObject->getComposerDraftCollectionObject();
			return $c->getAttributeValueObject($ak);
		}
	}
	
	public function shouldComposerControlStripEmptyValuesFromDraft() {
		return true;
	}

	public function isComposerControlDraftValueEmpty() {
		$ak = $this->getAttributeKeyObject();
		$c = $this->cmpDraftObject->getComposerDraftCollectionObject();
		return ($c->getAttribute($ak) == '');
	}

	public function render($label, $customTemplate) {
		$ak = $this->getAttributeKeyObject();
		$env = Environment::get();
		$set = $this->getComposerFormLayoutSetControlObject()->getComposerFormLayoutSetObject();
		$control = $this;
		$template = $env->getPath(DIRNAME_ELEMENTS . '/' . DIRNAME_COMPOSER . '/' . DIRNAME_COMPOSER_ELEMENTS_CONTROLS . '/' . $this->cmpControlTypeHandle . '.php');
		include($template);
	}

	public function publishToPage(ComposerDraft $d, $data, $controls) {
		$c = $d->getComposerDraftCollectionObject();
		// the data for this actually doesn't come from $data. Attributes have their own way of gettin data.
		$ak = $this->getAttributeKeyObject();
		$ak->saveAttributeForm($c);				
	}

	public function validate($data, ValidationErrorHelper $e) {
		$ak = $this->getAttributeKeyObject();
		$e1 = $ak->validateAttributeForm();
		if ($e1 == false) {
			$e->add(t('The field "%s" is required', $ak->getAttributeKeyName()));
		} else if ($e1 instanceof ValidationErrorHelper) {
			$e->add($e1);
		}
	}

	public function addAssetsToRequest(Controller $cnt) {
		$ak = $this->getAttributeKeyObject();
		$akc = $ak->getController();
		$akc->setupAndRun('composer');
	}

}