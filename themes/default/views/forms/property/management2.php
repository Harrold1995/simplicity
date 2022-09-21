
<table class="tableGridTable" id="managementtable">

<thead>
  <tr>
    <th >Frequency</th>
    <th >Vendor</th>
    <th >Unit</th>
    <th >Amount</th>
    <th >Next Transaction Date</th>
    <th >End Date</th>
    <th >Income Accounts To Include</th>
    <th >Percentage/fixed</th>
    <th >Expense Account</th>
    <th ></th>
  </tr>
</thead>
<tbody>


</tbody>
<tfoot>
  <tr></tr>
</tfoot>
</table>

<script type="text/javascript">

        var template = function (id, data = {}) {
            if(_.isEmpty(data)) data = {name: '', frequency: '', amount: '', start_date: '', end_date: '', item_id: '', unit_id: '', vendor: ''};
        var newRow = '<tr index="' + id + '" ' + (data.id ? 'rid="' + data.id + '"' : '') + ' id="'+ data.id +'" class="" edit-mode="inputrow">' +
            '<td class="text-center">' + (data.fname ? data.fname : "") + '<select name="managements[' + id + '][frequency]" tsource="frequencies" default="' + data.frequency + '"/></td>' +
            '<td class="text-center">' + (data.name ? data.name : "") + '<select name="managements[' + id + '][vendor]" tsource="vendors" default="' + data.vendor + '"/></td>' +
            '<td class="text-center">' + (data.uname ? data.uname : "") + '<select name="managements[' + id + '][unit_id]" tsource="units" default="' + data.unit_id + '"/></td>' +
            '<td class="text-center">' + (data.amount ? data.amount : "") + '<input name="managements['+ id + '][amount]" type="hidden" value="' + data.amount + '"/></td>' +
            '<td class="text-center">' + (data.start_date ? data.start_date : "") + '<input name="managements[' + id + '][start_date]" type="hidden"  data-toggle="datepicker" value="' + data.start_date + '"/></td>' +
            '<td class="text-center">' + (data.end_date ? data.end_date : "") + '<input name="managements[' + id + '][end_date]" type="hidden" class="leaveEmpty" data-toggle="datepicker"  value="' + data.end_date + '"/></td>' +
            '<td class="text-center">' + (data.iname ? data.iname : "") + '<input class="multiple-select" name="managements[' + id + '][item_id]" tsource="accounts" value="' + data.item_id + '"/></td>' +
            '<td class="text-center">' + (data.percentage_fixed_text ? data.percentage_fixed_text : "") + '<select name="managements['+ id + '][percentage_fixed]" tsource="calc" default="' + data.percentage_fixed + '"/></td>' +
            '<td class="text-center">' + (data.aname ? data.aname : "") + '<select name="managements[' + id + '][account_id]" tsource="subexpenseAccounts" default="' + data.account_id + '"/></td>' +
            '<td class="text-center"><a href="#" class="delete"><i class="icon-x"></i></a></td>' +
            '</tr>';
        return newRow;
    };

    var tsources = {
        frequencies: <?php echo json_encode($frequencies); ?>,
         subexpenseAccounts: <?php echo json_encode($accounts); ?>,
         accounts: <?php echo json_encode($accounts); ?>,
         units: <?php echo json_encode($units); ?>,
         vendors: <?php echo json_encode($vendors); ?>,
         calc: [{"id":"1","name":"Percentage"}, {"id":"2","name":"Fixed"}]
    };

    var parseData = function (json) {
        var data = {id: json.id, vendor: json.vendor, name: json.vendor_text, unit_id: json.unit_id, uname: json.unit_id_text, frequency: json.frequency, fname: json.frequency_text, amount: json.amount, start_date: json.start_date, end_date: json.end_date, item_id: json.item_id, iname: json.item_id_text, percentage_fixed: json.percentage_fixed, percentage_fixed_text: json.percentage_fixed_text, account_id: json.account_id, aname: json.account_id_text};
        return data;
    };

    var table = $('.modal').last().find('#managementtable').tableGrid({
        template: template,
        mode: 'inputrow',
        name: 'managements',
        parseData: parseData,
        tsources: tsources,
        data: <?php echo $managements && count($managements) ? json_encode($managements) : 0 ?>
    });


    </script>