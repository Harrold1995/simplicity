<article class="right-side h100">
    <div class="row mt-3 h100">
        <div class="col-2">
            <a href="#" class="btn btn-primary mt-5" data-mode='add' data-type='custom' url='recordsets/getModal'>Add Recordset</a>
        </div>
        <div class="col-10 h100">
            <table class="table table-d flex-table action-column">
                <thead class="thead-light">
                    <tr>
                        <th>Recordset</th>
                        <th class="pl-3 pr-3">Action</th>
                    </tr>
                </thead>
                <tbody     style ='max-height: calc(60vh);' >
                    <?php if (isset($recordsets))
                        foreach ($recordsets as $recordset) {
                            echo "<tr>
                                          <td>" . $recordset->name . "</td>                                         
                                          <td class='pl-3 pr-3'><a href='#' data-id='".$recordset->id."' data-mode='edit' data-type='custom' url='recordsets/getModal' ><i class='far fa-edit'></i></a>&nbsp;<a class='deleteButton' refresh='true' href='".base_url('recordsets/deleteRecordset/' . $recordset->id)."'><i class='far fa-trash-alt'></i><a/></td>
                                      </tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</article>
<aside class="left-side">
    <?php //require_once VIEWPATH . 'reports/reports-left.php'; ?>
</aside>
