<div id="wrapTableDivStyle">
<table class="tableGridTable" id="taxestable">

                      <thead>
                        <tr>
                          <th >Borough</th>
                          <th >Block</th>
                          <th>Lot</th>
                          <th>Pay Frequency</th>
                          <th >Paymt Acct</th>
                          <th>Current Assessment</th>
                          <th >Allocate By</th>
                          <th >Payee</th>
                          <th >Last Paid On</th>
                          <th ></th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
</div>

<script type="text/javascript">
        console.log('taxes');

        var template = function (id, data = {}) {
            if(_.isEmpty(data)) data = {borough: '',block: '', lot:0, frequency: '', fname: '', payment_acct: '', bankName: '', assessment: '', allocate_by: '', abname: '', payee: '', payeename: '', last_pay_date: ''};
        var newRow = '<tr index="' + id + '" ' + (data.id ? 'rid="' + data.id + '"' : '') + ' id="'+ data.id +'" class="" edit-mode="inputrow">' +
            '<td class="text-center">' + (data.borough ? data.borough : "") + '<input name="taxes['+ id + '][borough]" type="hidden" value="' + data.borough + '"/></td>' +
            '<td class="text-center">' + (data.block ? data.block : "") + '<input name="taxes[' + id + '][block]" type="hidden"  value="' + data.block + '"/></td>' +
            '<td class="text-center">' + (data.lot ? data.lot : "") + '<input name="taxes[' + id + '][lot]" type="hidden"  value="' + data.lot + '"/></td>' +
            '<td class="text-center">' + (data.fname ? data.fname : "") + '<select name="taxes[' + id + '][frequency]" tsource="frequencies" default="' + data.frequency + '"/></td>' +
            '<td class="text-center">' + (data.bankName ? data.bankName : "") + '<select name="taxes[' + id + '][payment_acct]" tsource="bankAccounts"  default="' + data.payment_acct + '"/></td>' +
            '<td class="text-center">' + (data.assessment ? data.assessment : "") + '<input name="taxes[' + id + '][assessment]" type="hidden"  value="' + data.assessment + '"/></td>' +
            '<td class="text-center">' + (data.abname ? data.abname : "") + '<select name="taxes[' + id + '][allocate_by]" tsource="frequencies" default="' + data.allocate_by + '"/></td>' +
            '<td class="text-center">' + (data.payeename ? data.payeename : "") + '<select name="taxes[' + id + '][payee]" tsource="vendors" default="' + data.payee + '"/></td>' +
            '<td class="text-center">' + (data.last_pay_date ? data.last_pay_date : "") + '<input name="taxes[' + id + '][last_pay_date]" type="hidden" data-toggle="datepicker" value="' + data.last_pay_date + '"/></td>' +
            '<td class="text-center"><a href="#" class="delete"><i class="icon-x"></i></a></td>' +
            '</tr>';
        return newRow;
    };

    var tsources = {
        vendors: <?php echo json_encode($vendors); ?>,
        bankAccounts: <?php echo json_encode($bankAccounts); ?>,
        frequencies: <?php echo json_encode($frequencies); ?>
    };

    var parseData = function (json) {
        var data = {id: json.id, borough: json.borough, block: json.block, lot: json.lot, frequency: json.frequency, fname: json.frequency_text, payment_acct: json.payment_acct, bankName: json.payment_acct_text, assessment: json.assessment, meter: json.meter, allocate_by: json.allocate_by, abname: json.allocate_by_text, payee: json.payee, payeename: json.payee_text, last_pay_date: json.last_pay_date};
        return data;
    };

    var table = $('.modal').last().find('#taxestable').tableGrid({
        template: template,
        mode: 'inputrow',
        name: 'taxes',
        parseData: parseData,
        tsources: tsources,
        data: <?php echo $taxes && count($taxes) ? json_encode($taxes) : 0 ?>
    });
    </script>