<?
defined('C5_EXECUTE') or die("Access Denied.");

$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}

/*
$txt = Loader::helper('text');
$args = $_REQUEST;
array_walk_recursive($args, array($txt, 'entities'));

if (isset($select_mode)) {
	$args['select_mode'] = $select_mode;
}
$args['selectedPageID'] = $_REQUEST['cID'];
if (is_array($args['selectedPageID'])) {
	$args['selectedPageID'] = implode(',',$args['selectedPageID']);
}
$args['sitemapCombinedMode'] = $sitemapCombinedMode;
if (!isset($args['select_mode'])) {
	$args['select_mode'] = 'select_page';
}
if ($args['select_mode'] == 'select_page') {
	$args['reveal'] = $args['selectedPageID'];
}

$args['display_mode'] = 'full';
$args['instance_id'] = time();
*/

if (isset($_REQUEST['requestID']) && Loader::helper('validation/numbers')->integer($_REQUEST['requestID'])) {
	$requestID = $_REQUEST['requestID'];
}
?>

<div class="ccm-sitemap-overlay"></div>


<script type="text/javascript">
$(function() {
	$('.ccm-sitemap-overlay').ccmsitemap({
		<? if (isset($requestID)) { ?>
			'requestID': '<?=$requestID?>',
		<? } ?>
		<? if ($_REQUEST['display'] == 'flat') { ?>
			displaySingleLevel: true,
		<? } else { ?>
			displaySingleLevel: false
		<? } ?>
	});
});
</script>
