<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions for use with the C5 dashboard.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteDashboardHelper {

	/** 
	 * Checks to see if a user has access to the C5 dashboard.
	 */
	public function canRead() {
		$c = Page::getByPath('/dashboard', 'ACTIVE');
		$cp = new Permissions($c);
		return $cp->canViewPage();
	}
	
	
	public function canAccessComposer() {
		$c = Page::getByPath('/dashboard/composer', 'ACTIVE');
		$cp = new Permissions($c);
		return $cp->canViewPage();
	}

	public function inDashboard($page = false) {
		if (!$page) {
			$page = Page::getCurrentPage();
		}
		return strpos($page->getCollectionPath(), '/dashboard') === 0;
	}
	
	public function getDashboardPaneFooterWrapper($includeDefaultBody = true) {
		$html = '</div></div></div></div>';
		if ($includeDefaultBody) {
			$html .= '</div>';
		}
		return $html;
	}
	
	public function getDashboardPaneHeaderWrapper($title = false, $help = false, $span = 'span12', $includeDefaultBody = true, $navigatePages = array(), $upToPage = false, $favorites = true) {
		
		$spantotal = 12;
		$offset = preg_match('/offset([0-9]+)/i', $span, $offsetmatches);
		if ($offset) {
			$offsettotal = $offsetmatches[1];
			$hasspan = preg_match('/span([0-9]+)/i', $span, $spanmatches);
			if ($hasspan) {
				$spantotal = $spanmatches[1];
				$gridtotal = ($offsettotal * 2) + $spantotal;
			}
		}
		
		if ($gridtotal > 12) {
			// we are working with legacy bootstrap 16-column grid
			// we take the offset and then we subtract from the span
			$spantotal = $spantotal - ($gridtotal - 12);
			$spantotal .= ' offset' . $offsettotal;
			$span = 'span' . $spantotal;
		}
		
		$html = '<div class="ccm-ui"><div class="row"><div class="' . $span . '"><div class="ccm-pane">';
		$html .= self::getDashboardPaneHeader($title, $help, $navigatePages, $upToPage, $favorites);
		if ($includeDefaultBody) {
			$html .= '<div class="ccm-pane-body ccm-pane-body-footer">';
		}
		return $html;
	}
	
	public function getDashboardPaneHeader($title = false, $help = false, $navigatePages = array(), $upToPage = false, $favorites = true) {
		$c = Page::getCurrentPage();
		$vt = Loader::helper('validation/token');
		$token = $vt->generate('access_quick_nav');

		$currentMenu = array();
		$nh = Loader::helper('navigation');
		$trail = $nh->getTrailToCollection($c);
		if (count($trail) > 1 || count($navigatePages) > 1 || is_object($upToPage)) { 
			$parent = Page::getByID($c->getCollectionParentID());
			if (count($trail) > 1 && (!is_object($upToPage))) {
				$upToPage = Page::getByID($parent->getCollectionParentID());
			}
			Loader::block('autonav');
			$subpages = array();
			if ($navigatePages !== -1) { 
				if (count($navigatePages) > 0) { 
					$subpages = $navigatePages;
				} else { 
					$subpages = AutonavBlockController::getChildPages($parent);
				}
			}
			
			$subpagesP = array();
			if(is_array($subpages)) {
				foreach($subpages as $sc) {
					$cp = new Permissions($sc);
					if ($cp->canViewPage()) { 
						$subpagesP[] = $sc;
					}
				}
			}
			
			if (count($subpagesP) > 0 || is_object($upToPage)) { 
				$relatedPages = '<ul id="ccm-page-navigate-pages-content" class="dropdown-menu">';
		
				foreach($subpagesP as $sc) { 
					if ($sc->getAttribute('exclude_nav')) {
						continue;
					}
		
					if ($c->getCollectionPath() == $sc->getCollectionPath() || (strpos($c->getCollectionPath(), $sc->getCollectionPath()) == 0) && strpos($c->getCollectionPath(), $sc->getCollectionPath()) !== false) {
						$class= 'nav-selected';
					} else {
						$class = '';
					}
					
					$relatedPages .= '<li class="' . $class . '"><a href="' . $nh->getLinkToCollection($sc, false, true) . '">' . t($sc->getCollectionName()) . '</a></li>';
				}
		
				if ($upToPage) { 
					$relatedPages .= '<li class="ccm-menu-separator"></li>';
					$relatedPages .= '<li><a href="' . $nh->getLinkToCollection($upToPage, false, true) . '">' . t('&lt; Back to %s', t($upToPage->getCollectionName())) . '</a></li>';
				}
				$relatedPages .= '</ul>';
				$navigateTitle = t($parent->getCollectionName());
			}
		}
		

		$html = '<div class="ccm-pane-header">';
		
		$class = 'icon-star';
		$qn = ConcreteDashboardMenu::getMine();
		$quicknav = $qn->getItems(false);
		if (in_array($c->getCollectionPath(), $quicknav)) {
			$class = 'icon-white icon-star';	
		}
		$html .= '<ul class="ccm-pane-header-icons">';
		if (!$help) {
			$ih = Loader::helper('concrete/interface/help');
			$pageHelp = $ih->getPages();
			if (isset($pageHelp[$c->getCollectionPath()])) {
				$help = $pageHelp[$c->getCollectionPath()];
			}
		}
		
		if (is_array($help)) {
			$help = $help[0] . '<br/><br/><a href="' . $help[1] . '" class="btn small" target="_blank">' . t('Learn More') . '</a>';
		}
		
		if (isset($relatedPages)) { 
			$html .= '<li><a href="" data-toggle="dropdown" title="' . $navigateTitle . '" id="ccm-page-navigate-pages"><i class="icon-share-alt"></i></a>' . $relatedPages . '</li>';
		}
		
		if ($help) {
			$html .= '<li><span style="display: none" id="ccm-page-help-content">' . $help . '</span><a href="javascript:void(0)" title="' . t('Help') . '" id="ccm-page-help"><i class="icon-question-sign"></i></a></li>';
		}
		
		if ($favorites) {
		$html .= '<li><a href="javascript:void(0)" id="ccm-add-to-quick-nav" onclick="CCMDashboard.toggleQuickNav(' . $c->getCollectionID() . ',\'' . $token . '\')"><i class="' . $class . '"></i></a></li>';
		}

		$html .= '<li><a href="javascript:void(0)" onclick="CCMDashboard.closePane(this)"><i class="icon-remove"></i></a></li>';
		$html .= '</ul>';
		if (!$title) {
			$title = $c->getCollectionName();
		}
		$html .= '<h3>' . $title . '</h3>';
		$html .= '</div>';
		return $html;
	}
	
	public function getDashboardBackgroundImage() {
		$feed = array();
		// this feed is an array of standard PHP objects with a SRC, a caption, and a URL
		// allow for a custom white-label feed
		$filename = date('Ymd') . '.jpg';
		$obj = new stdClass;
		$obj->checkData = false;
		$obj->displayCaption = false;
		
		if (defined('WHITE_LABEL_DASHBOARD_BACKGROUND_FEED') && WHITE_LABEL_DASHBOARD_BACKGROUND_FEED != '') {
			$image = WHITE_LABEL_DASHBOARD_BACKGROUND_FEED . '/' . $filename;
		} else if (defined('WHITE_LABEL_DASHBOARD_BACKGROUND_SRC') && WHITE_LABEL_DASHBOARD_BACKGROUND_SRC != '') {
			$image = WHITE_LABEL_DASHBOARD_BACKGROUND_SRC;
			if ($image == 'none') {
				$image = '';
			}
		} else {
			$obj->checkData = true;
			$imageSetting = Config::get('DASHBOARD_BACKGROUND_IMAGE');
			if ($imageSetting == 'custom') {
				$fo = File::getByID(Config::get('DASHBOARD_BACKGROUND_IMAGE_CUSTOM_FILE_ID'));
				if (is_object($fo)) {
					$image = $fo->getRelativePath();
				}
			} else if ($imageSetting == 'none') {
				$image = '';
			} else { 
				if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
					$image = DASHBOARD_BACKGROUND_FEED_SECURE . '/' . $filename;
				} else {
					$image = DASHBOARD_BACKGROUND_FEED . '/' . $filename;
				}
				$obj->displayCaption = true;
			}
		}
		$obj->filename = $filename;
		$obj->image = $image;
		return $obj;
	}
	
	public function addQuickNavToMenus($html) {
		$recent = '';
		ob_start();		
		
			$c = Page::getCurrentPage();
			if (!is_array($_SESSION['ccmQuickNavRecentPages'])) {
				$_SESSION['ccmQuickNavRecentPages'] = array();
			}
			if (in_array($c->getCollectionID(), $_SESSION['ccmQuickNavRecentPages'])) {
				unset($_SESSION['ccmQuickNavRecentPages'][array_search($c->getCollectionID(), $_SESSION['ccmQuickNavRecentPages'])]);
				$_SESSION['ccmQuickNavRecentPages'] = array_values($_SESSION['ccmQuickNavRecentPages']);
			}
			
			$_SESSION['ccmQuickNavRecentPages'][] = $c->getCollectionID();
	
			if (count($_SESSION['ccmQuickNavRecentPages']) > 5) {
				array_shift($_SESSION['ccmQuickNavRecentPages']);
			}
			

			if (count($_SESSION['ccmQuickNavRecentPages']) > 0) { ?>
				<ul class="ccm-dashboard-recent-pages">
				<?php
				$i = 0;
				$recentPages = array_reverse($_SESSION['ccmQuickNavRecentPages']); //display most-recent first
				foreach($recentPages as $_cID) {
					$_c = Page::getByID($_cID);
					$name = t('(No Name)');
					$divider = '';
					if (isset($_SESSION['ccmQuickNavRecentPages'][$i+1])) {
						$divider = '<span class="dashboard-divider">></span>';
					}
					if ($_c->getCollectionName()) {
						$name = $_c->getCollectionName();
					}
					?> <li><a id="ccm-recent-page-<?=$_c->getCollectionID()?>" href="<?=Loader::helper('navigation')->getLinkToCollection($_c)?>"><?=t($name)?></a><?=$divider?></li>
					<? $i++;
				}
				?>
				</ul>
				<?
			}
		$recent = ob_get_contents();
		ob_end_clean();
		$html = str_replace("<!--recent-->", $recent, $html);		
		return str_replace(array("\n", "\r", "\t"), "", $html);
	}

	public function getDashboardAndSearchMenus() {

		if (isset($_SESSION['dashboardMenus'])) {
			return $_SESSION['dashboardMenus'];
		}
				
		$d = ConcreteDashboardMenu::getMine();
		$items = $d->getItems();

		ob_start(); ?>
			<div id="ccm-intelligent-search-results">
			<?
			$page = Page::getByPath('/dashboard');
			$children = $page->getCollectionChildrenArray(true);
			
			$packagepages = array();
			$corepages = array();
			foreach($children as $ch) {
				$page = Page::getByID($ch);
				$pageP = new Permissions($page);
				if ($pageP->canRead()) { 
					if (!$page->getAttribute("exclude_nav")) {
						if ($page->getPackageID() > 0) {
							$packagepages[] = $page;
						} else {
							$corepages[] = $page;
						}
					}
				} else {
					continue;
				}
			
				if ($page->getAttribute('exclude_search_index')) {
					continue;
				}
				
				if ($page->getCollectionPath() == '/dashboard/system') {
					$ch2 = $page->getCollectionChildrenArray();
				} else {
					$ch2 = $page->getCollectionChildrenArray(true);
				}
				?>
				
				<div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-onsite">
				
				<h1><?=t($page->getCollectionName())?></h1>
				
				
				<ul class="ccm-intelligent-search-results-list">
				<? if (count($ch2) == 0) { ?>
					<li><a href="<?=Loader::helper('navigation')->getLinkTocollection($page, false, true)?>"><?=t($page->getCollectionName())?></a><span><?=t($page->getCollectionName())?> <?=$page->getAttribute('meta_keywords')?></span></li>
				<? } ?>
				
				<?
				if ($page->getCollectionPath() == '/dashboard/system') { ?>
					<li><a href="<?=Loader::helper('navigation')->getLinkTocollection($page, false, true)?>"><?=t('View All')?></a><span><?=t($page->getCollectionName())?> <?=$page->getAttribute('meta_keywords')?></span></li>
				<?				
				}
				
				foreach($ch2 as $chi) {
					$subpage = Page::getByID($chi); 
					$subpageP = new Permissions($subpage);
					if (!$subpageP->canRead()) {
						continue;
					}

					if ($subpage->getAttribute('exclude_search_index')) {
						continue;
					}
			
					?>
					<li><a href="<?=Loader::helper('navigation')->getLinkTocollection($subpage, false, true)?>"><?=$subpage->getCollectionName()?></a><span><? if ($page->getCollectionPath() != '/dashboard/system') { ?><?=t($page->getCollectionName())?> <?=$page->getAttribute('meta_keywords')?> <? } ?><?=$subpage->getCollectionName()?> <?=$subpage->getAttribute('meta_keywords')?></span></li>
					<? 
				}
				?>
				</ul>
				
				</div>
				<? }
				
				$custHome = Page::getByPath('/dashboard/home');
				$custHomeP = new Permissions($custHome);
				if ($custHomeP->canRead()) {
				?>
				
				<div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-onsite">
				
				<h1><?=t('Dashboard Home')?></h1>
				
				
				<ul class="ccm-intelligent-search-results-list">
					<li><a href="<?=View::url('/dashboard/home')?>"><?=t('Customize')?> <span><?=t('Customize Dashboard Home')?></span></a></li>
				</ul>
				
				</div>
				
				<? } ?>
				
				<div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-loading">
				<h1><?=t('Your Site')?></h1>
				<ul class="ccm-intelligent-search-results-list" id="ccm-intelligent-search-results-list-your-site">
				</ul>
				</div>
				
				<? if (ENABLE_INTELLIGENT_SEARCH_HELP) { ?>
				<div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-offsite ccm-intelligent-search-results-module-loading">
				<h1><?=t('Help')?></h1>
				<ul class="ccm-intelligent-search-results-list" id="ccm-intelligent-search-results-list-help">
				</ul>
				</div>
				<? } ?>
				
				<? if (ENABLE_INTELLIGENT_SEARCH_MARKETPLACE) { ?>
				<div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-offsite ccm-intelligent-search-results-module-loading">
				<h1><?=t('Add-Ons')?></h1>
				<ul class="ccm-intelligent-search-results-list" id="ccm-intelligent-search-results-list-marketplace">
				</ul>
				</div>
				<? } ?>				
			</div>
			
			<div id="ccm-toolbar-menu-dashboard" class="ccm-toolbar-hover-menu dropdown-menu">
			<div id="ccm-dashboard-dropdown-recent" class="ccm-dashboard-dropdown-inner">

			<h6><?=t('Recent')?></h6>
			<!--recent-->

			</div>
			<div id="ccm-dashboard-dropdown-favorites" class="ccm-dashboard-dropdown-inner">
				<h6><?=t('Favorites')?>
					<span class="dashboard-divider">|</span>
					<a href="<?=View::url('/dashboard')?>"><?=t('Full Dashboard')?></a>
				</h6>
				<ul>
				<?
				foreach($items as $path) { 
				$p = Page::getByPath($path, 'ACTIVE');
				$pc = new Permissions($p);
				if ($pc->canViewPage()) {
					$name = t($p->getCollectionName()); ?>
					<li><a href="<?=Loader::helper('navigation')->getLinkTocollection($p)?>"><?=$name?></a></li>
				<? } 

				} ?>
				</ul>
			</div>


			</div>
		<?
			$html = ob_get_contents();
			ob_end_clean();
			
		return str_replace(array("\n", "\r", "\t"), "", $html);
	
	}
}

class ConcreteDashboardMenu {
	
	protected $items;
	public function getItems($sort = true) {
		if ($sort) {
			usort($this->items, array('ConcreteDashboardMenu', 'sortItems'));
		}
		return $this->items;
	}
	
	protected static function sortItems($a, $b) {
		$subpatha = substr($a, 11); // /dashboard
		$subpathb = substr($b, 11); // /dashboard
		$segmentsa = explode('/', $subpatha);
		$segmentsb = explode('/', $subpathb);
		$segmenta = substr($subpatha, 0, strpos($subpatha, '/'));
		$segmentb = substr($subpathb, 0, strpos($subpathb, '/'));
		if (count($segmentsa) == 3 && count($segmentsb) == 3) {
			$subpatha = $segmenta[0] . '/' . $segmenta[1];
			$subpathb = $segmentb[0] . '/' . $segmentb[1];
			$segmenta .= '/' . $segmentsa[1];
			$segmentb .= '/' . $segmentsb[1];
			
		}

		if (!$segmenta) {
			$segmenta = $subpatha;
		}
		if (!$segmentb) {
			$segmentb = $subpathb;
		}
		$db = Loader::db();
		$displayorderA = intval($db->GetOne('select cDisplayOrder from Pages p inner join PagePaths cp on p.cID = cp.cID where cPath = ?', array('/dashboard/' . $segmenta)));
		$displayorderB = intval($db->GetOne('select cDisplayOrder from Pages p inner join PagePaths cp on p.cID = cp.cID where cPath = ?', array('/dashboard/' . $segmentb)));

		if ($displayorderA > $displayorderB) {
			return 1;
		} else if ($displayorderA < $displayorderB) {
			return -1;
		} else {
			$displayorderA = intval($db->GetOne('select cDisplayOrder from Pages p inner join PagePaths cp on p.cID = cp.cID where cPath = ?', array('/dashboard/' . $subpatha)));
			$displayorderB = intval($db->GetOne('select cDisplayOrder from Pages p inner join PagePaths cp on p.cID = cp.cID where cPath = ?', array('/dashboard/' . $subpathb)));
			if ($displayorderA > $displayorderB) {
				return 1;
			} else if ($displayorderA < $displayorderB) {
				return -1;
			}
		}
	}
	
	public function contains($c) {
		return in_array($c->getCollectionPath(), $this->items);
	}
	
	public function add($c) {
		$this->items[] = $c->getCollectionPath();
	}

	public function remove($c) {
		unset($this->items[array_search($c->getCollectionPath(), $this->items)]);
	}

	public static function getMine() {
		$u = new User();
		$qn = unserialize($u->config('QUICK_NAV_BOOKMARKS'));
		if (is_object($qn)) {
			return $qn;
		}
		$qn = new ConcreteDashboardMenu();
		$qnx = new ConcreteDashboardDefaultMenu();
		$qn->items = $qnx->items;
		return $qn;	
	}
}

class ConcreteDashboardDefaultMenu extends ConcreteDashboardMenu {
	
	public $items = array(
		'/dashboard/composer/write',
		'/dashboard/composer/drafts',
		'/dashboard/sitemap/full',
		'/dashboard/sitemap/search',
		'/dashboard/files/search',
		'/dashboard/files/sets',
		'/dashboard/reports/statistics',
		'/dashboard/reports/forms'
	);

}
