<style>.vg-sheet-editor-guests{text-align:center;margin-bottom:50px}.vg-sheet-editor-guests form{max-width:250px;margin:0 auto;text-align:left}.vg-sheet-editor-guests label{display:block}.vg-sheet-editor-guests form input[type=text],.vg-sheet-editor-guests form input[type=password],.vg-sheet-editor-guests form input[type=submit]{width:100%}.vg-sheet-editor-guests form input[type=submit]{padding:5px;border:0}</style>
<div class="vg-sheet-editor-guests">
	<?php echo wp_kses_post($login_message); ?>
	<?php echo $login_form; ?>
</div>