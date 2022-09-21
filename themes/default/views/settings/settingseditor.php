<article class="right-side">
<div class="row mt-3">
    <div class="col-2">
      <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
          <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">General</a>
          <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">Validation</a>
      </div>
        <a href="#" class="btn btn-primary mt-5" data-mode='add' data-type='setting'>Add Setting</a>
     </div>
  <div class="col-10">
    <div class="tab-content" id="v-pills-tabContent">
      <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
      <table class="table table-d">
        <thead class="thead-light">
          <tr>
            <th width="50%">Setting</th>
            <th width="40%">Values</th>
            <th width="10%">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (isset($settings))
              foreach ($settings as $key => $setting) {
                if (strpos($key, "Validation") !== false){
                  echo "<tr data-mode='edit' data-type='setting' data-id='" . $key . "'>
                          <td>" . $key . "</td>
                          <td>" . count($setting) . "</td>
                          <td><i class='far fa-trash-alt'></i></td>
                      </tr>";
                      unset($settings[$key]);
              }
            }
          ?>
        </tbody>
      </table>
      </div>
      <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
      <table class="table table-d">
        <thead class="thead-light">
          <tr>
            <th width="50%">Setting</th>
            <th width="40%">Values</th>
            <th width="10%">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (isset($settings))
              foreach ($settings as $key => $setting) {
                  echo "<tr data-mode='edit' data-type='setting' data-id='" . $key . "'>
                          <td>" . $key . "</td>
                          <td>" . count($setting) . "</td>
                          <td><a href='settings/deleteSetting/" . $key . "' refresh = 'true' class='deleteButton'><i class='far fa-trash-alt'></i></a></td>
                      </tr>";
              }
          ?>
        </tbody>
      </table>
      </div>
    </div>

    </div>
</div>
</article>
<aside class="left-side noajax">
    <?php require_once VIEWPATH . 'settings/settings-left.php'; ?>
</aside>
