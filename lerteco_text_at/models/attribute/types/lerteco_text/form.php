<?php
$fh = Loader::helper('form'); /* @var $fh FormHelper */
/* @var $controller LertecoTextAttributeTypeController */
?>
<?php echo $fh->text($fieldName, $value, array('class' => 'span3')) ?>

<?php if ($mustVal) {
	$field = str_replace(array('[', ']'), array('\\\\[', '\\\\]'), $fieldName);
?>
	<script type="text/javascript">
		$(function() {
			$('#<?php echo $field ?>').closest('form').validate();
			$('#<?php echo $field ?>').closest('form').bind('form-pre-serialize', function(event, form, opts, veto) {
				if (! form.valid()) {
					$($(this).closest('tr').find('a')[0]).trigger('click');
					veto.veto = true;
				}
			});

			<?php if ($textConfig['valReq']) { ?>
				$('#<?php echo $field ?>').rules('add', { required: true });
			<?php } ?>

			<?php if ($textConfig['valType'] == LertecoTextAttributeTypeController::TYPE_EMAIL) { ?>
				$('#<?php echo $field ?>').rules('add', { email: true });
			<?php } else if ($textConfig['valType'] == LertecoTextAttributeTypeController::TYPE_URL) { ?>
				$('#<?php echo $field ?>').rules('add', { url: true });
			<?php } else if ($textConfig['valType'] == LertecoTextAttributeTypeController::TYPE_REGEXP && $textConfig['valRegExp']) { ?>
				$('#<?php echo $field ?>').rules('add', { regex: '<?php echo str_replace('\\', '\\\\', $textConfig['valRegExp']) ?>'});
			<?php } ?>
		});
	</script>
<?php } ?>