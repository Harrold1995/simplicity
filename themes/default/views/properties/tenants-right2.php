<div class="column-header">
  <div class="row">
    <div class="col-12 col-xl-7 mb-4 module-info" id="info">
      <div class="row">
        <div class="col-6 col-sm-4 align-self-center">
          <img data-src="holder.js/160x160" class="rounded float-left" alt="160x160" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_162b2febc6d%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_162b2febc6d%22%3E%3Crect%20width%3D%22200%22%20height%3D%22200%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2274.4296875%22%20y%3D%22104.5%22%3E200x200%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E"
                  data-holder-rendered="true" style="width: 140px; height: 140px;">
        </div>
        <div class="col-6 col-sm-4 align-self-center">
          <h5 class="text-center"><?= isset($tenant) ? $tenant->name : '';?>
            <br>
            <?= isset($tenant) ? $tenant->address_line_1 : '';?>
            <br>
            <?= isset($tenant) ? $tenant->unit : '';?>
          </h5>

        </div>
        <div class="col-12 col-sm-4 mt-3 mt-sm-0 align-self-center text-center">
          <p>
            <i class="fa fa-envelope" aria-hidden="true"></i><?= isset($tenant) ?'  '. $tenant->email : '';?>
          </p>
          <p>
            <i class="fa fa-phone" aria-hidden="true"></i> <?= isset($tenant) ? preg_replace('/\d{3}/', '$0-', str_replace('.', null, trim($tenant->phone)), 2) : '';?> <span class="badge badge-secondary">preffered</span>
          </p>
          <button type="button" class="btn btn-primary btn-lg btn-block">
            Message
          </button>

        </div>
      </div>

    </div>

    <div class="col-7 col-sm-7 col-md-7 col-lg-6 col-xl-3 mb-4 module-info text-center" id="balance">
      <div class="d-flex justify-content-center">
        <div class="align-self-center">
          <p>
            <b>Security Deposit</b>
            <h3 style="color:#df571b"><b><?= isset($lease) ? $lease->deposit : '';?></b></h3>
          </p>
          <p>
            <b>Last Month's Rent</b>
          </p>
          <h3 style="color:#df571b"><b><?= isset($lease) ? $lease->last_month : '';?></b></h3>
          <p>
          <button type="button" class="btn btn-xs" style="min-width: 125px">
            Refund/Apply
          </button>
          </p>
        </div>
      </div>
    </div>

    <div class="col-5 col-sm-5 col-md-5 col-lg-6 col-xl-2 mb-4" id="info-buttons">
      <div class="d-flex justify-content-center h-100">
        <ul class="list-box align-self-center">
          <li>
            <a href="./">Payment</a>
          </li>
          <li>
            <a href="./">Credit</a>
          </li>
          <li>
            <a href="./">New Charge</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="tabFunction">
    <nav class="double center">
                <ul class="list-horizontal nav" id="property-tabs2" role="tablist">
                  <li class = "tablinks" ><a href="#profile-tab" onclick="tabswitch(event, 'profile',$(this))">Profile</a></li>
                  <li class="tablinks" id="defaultOpen"><a  href="#transactions-tab" onclick="tabswitch(event, 'transactions',$(this))">Transactions</a></li>
                  <li class = "tablinks" > <a href="#transactions-tab" onclick="tabswitch(event, 'notes',$(this))">Notes</a></li>
                  <li class = "tablinks"> <a  href="#transactions-tab" onclick="tabswitch(event, 'conversations',$(this))">Conversations</a></li>
                  <li class="tablinks"><a  href="#transactions-tab" onclick="tabswitch(event, 'maintenance',$(this))">Maintenance</a></li>
                  <li class = "tablinks"> <a  href="#transactions-tab" onclick="tabswitch(event, 'conversations',$(this))">Conversations</a></li>
                </ul>
          </nav>
</div>
          


            <!-- Tab content -->
<div id="setup" class="tabcontent" style="display:none">

<?php if ($getCreditCard) {require_once VIEWPATH . 'accounts/accounts-setup2.php';} else {require_once VIEWPATH . 'accounts/accounts-setup.php';}?>
</div>

<div id="transactions" class="active tabcontent defaultOpenTab">
<?php require_once VIEWPATH . 'properties/properties-transactions.php';?>
</div>

<div id="profile" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'properties/properties-profile.php';?>
</div>

<div id="statements" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'accounts/accounts-statements.php';?>
</div>

<div id="maintenance" class="tabcontent" style="display:none">
	<?php require_once VIEWPATH . 'properties/tenants-maintenance.php';?>
</div>
