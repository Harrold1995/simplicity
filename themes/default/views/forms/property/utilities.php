<div id="wrapTableDivStyle">
 <table class="tableGridTable" id="utilitiestable">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Unit</th>
                                <th>Description</th>
                                <th>Account#</th>
                                <th>Meter#</th>
                                <th>Default Exp</th>
                                <th>Payee</th>
                                <th>DP?</th>
                                <th>Paid by</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
</div>
    <script type="text/javascript">
         var utilityTypes = <?php echo json_encode($utilityTypes); ?>;
        var subaccounts= <?php echo json_encode($subaccounts); ?>;
        var units = <?php echo json_encode($units); ?>;
        var vendors = <?php echo json_encode($vendors); ?>;
        console.log('utilities');

        var template = function (id, data = {}) {
            console.log('the data is utilities ' + data);
            if(_.isEmpty(data)) data = {utility_type: '', unit_id:0, description: '', account: '', meter: '', default_expense_acct: '', payee: '', direct_payment: '', paid_by: ''};
        var newRow = '<tr index="' + id + '" ' + (data.id ? 'rid="' + data.id + '"' : '') + ' id="'+ data.id +'" class="" edit-mode="inputrow">' +
            // '<input name="utilities[' + data.id + '][id]" type="hidden" value="' + data.id + '"/>' +
            '<td class="text-center">' + (data.utName ? data.utName : "")  + '<select name="utilities['+ id + '][utility_type]" tsource="utilityTypes" default="' + data.utility_type + '"/></td>' +
            '<td class="text-center">' + (data.unname ? data.unname : "") + '<select name="utilities[' + id + '][unit_id]" tsource="units" default="' + data.unit_id + '"/></td>' +
            '<td class="text-center">' + (data.description ? data.description : "") + '<input name="utilities[' + id + '][description]" type="hidden"  value="' + data.description + '"/></td>' +
            '<td class="text-center">' + (data.account ? data.account : "") + '<input name="utilities[' + id + '][account]" type="hidden"  value="' + data.account + '"/></td>' +
            '<td class="text-center">' + (data.meter ? data.meter : "") + '<input name="utilities[' + id + '][meter]" type="hidden"  value="' + data.meter + '"/></td>' +
            '<td class="text-center">' + (data.aname ? data.aname : "") + '<select name="utilities[' + id + '][default_expense_acct]" tsource="subaccounts" default="' + data.default_expense_acct + '"/></td>' +
            '<td class="text-center">' + (data.payeeName ? data.payeeName : "") + '<select name="utilities[' + id + '][payee]" tsource="vendors" default="' + data.payee + '"/></td>' +
            '<td class="text-center"><input type="checkbox" id="direct_payment" name="utilities[' + id + '][direct_payment]" ' + (data.direct_payment == '1' ? 'checked' : '') + '/></td>' +
            '<td class="text-center">' + (data.paid_by ? data.paid_by : "") + '<input name="utilities[' + id + '][paid_by]" type="hidden" value="' + data.paid_by + '"/></td>' +
            '<td class="text-center"><a href="#" class="delete"><i class="icon-x"></i></a></td>' +
            '</tr>';
        return newRow;
    };

    var tsources = {
         utilityTypes: <?php echo json_encode($utilityTypes); ?>,
         subaccounts: <?php echo json_encode($subaccounts); ?>,
         vendors: <?php echo json_encode($vendors); ?>,
         units: <?php echo json_encode($units); ?>
    };

    var parseData = function (json) {
        var data = {id: json.id, utName: json.utility_type_text, unname: json.unit_id_text, utility_type: json.utility_type, unit_id: json.unit_id, description: json.description, account: json.account, meter: json.meter, aname: json.default_expense_acct_text, default_expense_acct: json.default_expense_acct, payeeName: json.payee_text, payee: json.payee, direct_payment: json.direct_payment, paid_by: json.paid_by};
        return data;
    };

    var table = $('.modal').last().find('#utilitiestable').tableGrid({
        template: template,
        mode: 'inputrow',
        name: 'utilities',
        parseData: parseData,
        tsources: tsources,
        data: <?php echo $utilities && count($utilities) ? json_encode($utilities) : 0 ?>
    });
    </script>