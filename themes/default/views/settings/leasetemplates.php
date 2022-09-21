<?php
$baseurl = 'https://docs.simpli-city.com';
$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';
$thisurl = $_SERVER['HTTP_HOST']; ?>
<article class="right-side">
    <div class="row mt-3">
        <div class="col-2">
            <a href="#" class="btn btn-primary mt-5" data-mode='add' data-type='custom' url='settings/getLTemplateModal'>Add Template</a>
        </div>
        <div class="col-10">
            <table class="table table-d">
                <thead class="thead-light">
                    <tr>
                        <th width="50%">Template</th>
                        <th width="50%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($templates))
                        foreach ($templates as $template) {
                            echo "<tr data-mode='edit' data-type='custom' url='settings/getLTemplateModal' data-id='" . $template->id . "'>
                                          <td>" . $template->name . "</td>                                         
                                          <td><a class='fblink' target='_blank' href='" . $baseurl. '?url=' . $thisurl . '&id='. $template->id . "'>Open in FormBuilder</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class='deleteButton' refresh='true' href='".base_url('settings/deleteLTemplate/' . $template->id)."'><i class='far fa-trash-alt'></i><a/></td>
                                      </tr>";
                        }
                    ?>
                </tbody>
            </table>

        </div>
    </div>
</article>
<aside class="left-side noajax">
    <?php require_once VIEWPATH . 'settings/settings-left.php'; ?>
</aside>
<iframe id="receiver" src="<?php echo $baseurl.'?token='.$token;?>>" width="0" height="0">
	<p>Your browser does not support iframes.</p>
</iframe>

