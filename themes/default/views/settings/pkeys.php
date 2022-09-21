<?php
$baseurl = 'https://docs.simpli-city.com';
$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';
$thisurl = $_SERVER['HTTP_HOST']; ?>
<article class="right-side" style="overflow: scroll !important; padding-bottom: 40px;">
    <div class="row mt-3">
        <div class="col-12">
			<form method="post" action="<?php echo base_url('settings/savepkeys')?>">
				<div class="mb-2 w-50 d-inline-block p-2 text-success" style="font-size:16px;"><?php echo $this->session->flashdata('msg');?></div>
				<input class="btn btn-primary mb-3 w-50 bg-success text-white" type="submit" value="Save Keys"/>
				<table class="table table- noarrow">
					<thead class="thead-light">
						<tr>
							<th width="34%">Property</th>
							<th width="33%">Cardknox Key</th>
							<th width="33%">iFields Cardknox Key</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Default Key</td>
							<td><input type='text' name='pkeys[0][key]' value='<?php echo $defkey->key;?>'></td>
							<td><input type='text' name='pkeys[0][ikey]' value='<?php echo $defkey->ikey;?>'></td>
						</tr>
						<?php if (isset($properties))
							foreach ($properties as $property) {
								echo "<tr>
										  <td>" . $property->name . "</td>                                         
										  <td><input type='text' name='pkeys[".$property->id."][key]' value='".$property->key."'></td>
										  <td><input type='text' name='pkeys[".$property->id."][ikey]' value='".$property->ikey."'></td>
									  </tr>";
							}
						?>
					</tbody>
				</table>
			</form>

        </div>
    </div>
</article>
<aside class="left-side noajax">
    <?php require_once VIEWPATH . 'settings/settings-left.php'; ?>
</aside>
<iframe id="receiver" src="<?php echo $baseurl.'?token='.$token;?>>" width="0" height="0">
	<p>Your browser does not support iframes.</p>
</iframe>

