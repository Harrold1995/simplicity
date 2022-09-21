<div id="wrapTableDivStyle">
<table class="tableGridTable" id="insurancetable">
      <thead >
        <tr>
            <th >Policy#</th>
            <th >Company</th>
            <th >Price</th>
            <th>Start Date</th>
            <th >End Date</th>
            <th >Policy type</th>
            <th>Coverage</th>
            <th >Payment Acct</th>
            <th >Broker</th>
            <th></th>
        </tr>

      </thead>
      <tbody>

      </tbody>
    </table>
</div>
    <script>

var template = function (id, data = {}) {
    if(_.isEmpty(data)) data = {policy: '', company:'', price: '', start_date: '', end_date: '', policy_type: '', coverage: '', payment_acct: '', broker: ''};
        var newRow = '<tr index="' + id + '" ' + (data.id ? 'rid="' + data.id + '"' : '') + ' id="'+ data.id +'" edit-mode="inputrow">' +
            '<td class="text-center">' + (data.policy ? data.policy : "") + '<input name="insurance['+ id + '][policy]" type="hidden" value="' + data.policy + '"/></td>' +
            '<td class="text-center">' + (data.company ? data.company : "") + '<input name="insurance[' + id + '][company]" type="hidden"  value="' + data.company + '"/></td>' +
            '<td class="text-center">' + (data.price ? data.price : "") + '<input name="insurance[' + id + '][price]" type="hidden"  value="' + data.price + '"/></td>' +
            '<td class="text-center">' + (data.start_date ? data.start_date : "") + '<input name="insurance[' + id + '][start_date]" type="hidden" data-toggle="datepicker" value="' + data.start_date + '"/></td>' +
            '<td class="text-center">' + (data.end_date ? data.end_date : "") + '<input name="insurance[' + id + '][end_date]" type="hidden" data-toggle="datepicker" value="' + data.end_date + '"/></td>' +
            '<td class="text-center">' + (data.policy_type ? data.policy_type : "") + '<input name="insurance[' + id + '][policy_type]" type="hidden"  value="' + data.policy_type + '"/></td>' +
            '<td class="text-center">' + (data.coverage ? data.coverage : "") + '<input name="insurance[' + id + '][coverage]" type="hidden" value="' + data.coverage + '"/></td>' +
            '<td class="text-center">' + (data.bankName ? data.bankName : "") + '<select name="insurance[' + id + '][payment_acct]" tsource="bankAccounts" default="' + data.payment_acct + '"/></td>' +
            '<td class="text-center">' + (data.brokerName ? data.brokerName : "") + '<select name="insurance[' + id + '][broker]" tsource="vendors" default="' + data.broker + '"/></td>' +
            '<td class="text-center"><a href="#" class="delete"><i class="icon-x"></i></a></td>' +
            '</tr>';
        return newRow;
    };

    var parseData = function (json) {
        var data = {id: json.id, policy: json.policy, company: json.company, price: json.price, start_date: json.start_date, end_date: json.end_date, policy_type: json.policy_type, coverage: json.coverage, bankName: json.payment_acct_text, brokerName: json.broker_text, broker: json.broker, payment_acct: json.payment_acct};
        return data;
    };

    var tsources = {
         bankAccounts: <?php echo json_encode($bankAccounts); ?>,
         vendors: <?php echo json_encode($vendors); ?>
    };

    var table = $('.modal').last().find('#insurancetable').tableGrid({
        template: template,
        mode: 'inputrow',
        name: 'insurance',
        parseData: parseData,
        tsources: tsources,
        data: <?php echo $insurance  && count($insurance)? json_encode($insurance) : 0 ?>
    });

</script>