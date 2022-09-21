<main id="content" class="cols-a">
<article class="right-side">
    <div class="row mt-3">
        <div class="col-2">
            <a href="#" class="btn btn-primary mt-5" data-mode='add' data-type='custom' url='reports/getAddReportModal'>Add Report</a>
        </div>
        <div class="col-10">
            <table class="table table-d" style="max-height:500px; overflow:auto">
                <thead class="thead-light">
                    <tr>
                        <th width="50%">Report</th>
                        <th width="50%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($reports))
                        foreach ($reports as $report) {
                            echo "<tr>
                                          <td>" . $report->name . "</td>                                         
                                          <td><a href='#' class='reportLink' data-id='".$report->id."' title='(test)'>Modal</a>  <a href='" . base_url('/reports/edit/' . $report->id) . "'>Open in Report Editor</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class='deleteButton' refresh='true' href='".base_url('reports/deleteReport/' . $report->id)."'><i class='far fa-trash-alt'></i><a/></td>
                                      </tr>";
                        }
                    ?>
                </tbody>
            </table>
            <a href='#' class='reportLink' data-id='0' filters="1,3|3" defaults="297 Driggs ave.$$2017-07-03|2019-09-03" rtype="2" title='(test)'>Quick Report</a>
        </div>
    </div>
</article>
<aside class="left-side">
    <?php //require_once VIEWPATH . 'reports/reports-left.php'; ?>
</aside>
