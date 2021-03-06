<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
*
* When activating a theme, any file within the theme is loaded into the system as a Page Theme File. At that point
* the file can then be used to create a new page type. 
* @package Pages
* @subpackage Themes
*/
class Concrete5_Model_PageThemeFile {
	
	protected $filename;
	protected $type;
	
	/**
	 * Type of page corresponding to the view template (used by single pages in this theme). Typically that means this template file is "view.php"
	 */
	const TFTYPE_VIEW = 1;
	
	/**
	 * Type of page corresponding to the default page type. If a page type doesn't have a template in a particular theme, default is used. 
	 */
	const TFTYPE_DEFAULT = 2;

	/**
	 * If this is used to designate what type of template this is, this means it corresponds to a single page like "login.php"
	 */
	const TFTYPE_SINGLE_PAGE = 3;
	
	/**
	 * This is a template for a new page type - one that hasn't been previously created in the system.
	 */
	const TFTYPE_PAGE_TYPE_NEW = 4;
	
	/**
	 * This is a template for a page type that already exists in the system.
	 */
	const TFTYPE_PAGE_TYPE_EXISTING = 5;
	
	/** 
	 * Sets the filename of this object to the passed parameter.
	 * @params string $filename
	 * @return void
	 */
	public function setFilename($filename) { $this->filename = $filename;}
	
	/**
	 * Sets the type of file for this object to one of the constants.
	 * @params string $type
	 * @return void
	 */
	public function setType($type) { $this->type = $type; }
	
	
	/** 
	 * Gets the filename for this theme file object.
	 * @return string $filename
	 */
	public function getFilename() { return $this->filename;}
	
	/**
	 * Gets the type of file for this object.
	 * @return string $type
	 */
	public function getType() {return $this->type;}
	
	/**
	 * Returns just the part of the filename prior to the extension
	 * @return string $handle
	 */
	public function getHandle() {
		return substr($this->filename, 0, strpos($this->filename, '.'));
	}
	
}
