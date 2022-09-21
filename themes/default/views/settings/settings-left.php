
<table class="tree-table">
    <tr <?=($page=='ltemplates')?' class="on" ':''?>><td><a href="<?=base_url('/users')?>">Users & Permissions</a></td></tr>
    <tr <?=($page=='seditor')?' class="on" ':''?>><td><a href="<?=base_url('/settings/seditor')?>">Settings Editor</a></td></tr>
    <tr <?=($page=='ltemplates')?' class="on" ':''?>><td><a href="<?=base_url('/settings/ltemplates')?>">Lease Templates</a></td></tr>
    <tr <?=($page=='ltemplates')?' class="on" ':''?>><td><a id="companySettings" href="#">Company Settings</a></td></tr>
    <tr id="recordsets_button" <?=($page=='ltemplates')?' class="on" ':''?>><td><a  href="#">recordsets</a></td></tr>
	<tr <?=($page=='pkeys')?' class="on" ':''?>><td><a href="<?=base_url('/settings/pkeys')?>">Property Keys</a></td></tr>
</table>
