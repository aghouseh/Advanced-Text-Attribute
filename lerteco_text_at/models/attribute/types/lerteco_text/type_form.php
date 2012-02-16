<?php
$fh = Loader::helper('form'); /* @var $fh FormHelper */
/* @var $controller LertecoTextAttributeTypeController */
?>

<fieldset>
	<legend>Validation</legend>

	<div class="clearfix control-group">
		<label class="control-label">Input Type</label>
		<div class="input controls">
			<?php echo $fh->select('valType', $typeOptions, $textConfig['valType']); ?>
		</div>
	</div>
	<div id="control-regexp" class="clearfix control-group">
		<label class="control-label">Regular Expression</label>
		<div class="input controls">
			<?php echo $fh->text('valRegExp', $textConfig['valRegExp']); ?>
		</div>
	</div>
	<div class="clearfix control-group">
		<label class="control-label">Required</label>
		<div class="input controls">
			<?php echo $fh->checkbox('valReq', '1', $textConfig['valReq']); ?>
		</div>
	</div>
</fieldset>
<fieldset id="set-display">
	<legend>Display</legend>
	
	<div id="control-regexp" class="clearfix control-group">
		<label class="control-label">Format As Type</label>
		<div class="input controls">
			<?php echo $fh->checkbox('formatType', '1', $textConfig['formatType']); ?>
		</div>
	</div>
</fieldset>