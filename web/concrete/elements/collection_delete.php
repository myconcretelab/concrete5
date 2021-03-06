<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui">
<?
$sh = Loader::helper('concrete/dashboard/sitemap');
$numChildren = $c->getNumChildren();
$u = new User();
?>

<script type="text/javascript">
$(function() {
	$("#ccmDeletePageForm").ajaxForm({
		type: 'POST',
		iframe: true,
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		success: function(r) {
			var r = eval('(' + r + ')');
			if (r != null && r.rel == 'SITEMAP') {
				jQuery.fn.dialog.hideLoader();
				jQuery.fn.dialog.closeTop();
				$.fn.ccmsitemap('triggerEvent', 'deleteRequestComplete', [r]);
			} else {
				window.location.href = '<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=' + r.refreshCID;
			}
		}
	});
});
</script>

<? if ($c->getCollectionID() == 1) {  ?>
	<div class="error alert-message"><?=t('You may not delete the home page.');?></div>
	<div class="dialog-buttons"><input type="button" class="btn" value="<?=t('Cancel')?>" onclick="jQuery.fn.dialog.closeTop()" /></div>
<? }  else if ($numChildren > 0 && !$u->isSuperUser()) { ?>
		<div class="error alert-message"><?=t('Before you can delete this page, you must delete all of its child pages.')?></div>
		<div class="dialog-buttons"><input type="button" class="btn" value="<?=t('Cancel')?>" onclick="jQuery.fn.dialog.closeTop()" /></div>
		
	<? } else { 

		$request_rel = SecurityHelper::sanitizeString($_REQUEST['rel']);
		?>
		
		<div class="ccm-buttons">

		<form method="post" id="ccmDeletePageForm" action="<?=$c->getCollectionAction()?>">	
			<input type="hidden" name="rel" value="<?php echo h($request_rel); ?>" />

			<div class="dialog-buttons"><input type="button" class="btn pull-left" value="<?=t('Cancel')?>" onclick="jQuery.fn.dialog.closeTop()" />
			<a href="javascript:void(0)" onclick="$('#ccmDeletePageForm').submit()" class="btn btn-danger pull-right"><span><?=t('Delete')?></span></a>
			</div>

		<? if($c->isSystemPage()) { ?>
			<div class="alert alert-error"><?php echo t('Warning! This is a system page. Deleting it could potentially break your site. Please proceed with caution.') ?></div>
		<? } ?>
		<h3><?=t('Are you sure you wish to delete this page?')?></h3>
		<? if ($u->isSuperUser() && $numChildren > 0) { ?>
			<h4><?=t2('This will remove %s child page.', 'This will remove %s child pages.', $numChildren, $numChildren)?></h4>
		<? } ?>
		
		<? if (ENABLE_TRASH_CAN) { ?>
			<p><?=t('Deleted pages are moved to the trash can in the sitemap.')?></p>
		<? } else { ?>
			<p><?=t('This cannot be undone.')?></p>
		<? } ?>
		
			<input type="hidden" name="cID" value="<?=$c->getCollectionID()?>">
			<input type="hidden" name="ctask" value="delete">
			<input type="hidden" name="processCollection" value="1" />

			<?php 
			$display_mode = SecurityHelper::sanitizeString($_REQUEST['display_mode']);
			$select_mode = SecurityHelper::sanitizeString($_REQUEST['select_mode']);
			?>			
			<input type="hidden" name="display_mode" value="<?php echo h($display_mode); ?>" />
			<input type="hidden" name="select_mode" value="<?php echo h($select_mode); ?>" />
		</form>
		</div>
		
	<? }
?>