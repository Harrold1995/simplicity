var CustomReports = function (body, modal) {
    this.type = '1';
    this.modal = modal;
    this.body = $(body);
    this.bodyId = body;
    this.names = {'1': 'Trial Balance', '2': 'Aged Payables', '3': 'Balance Sheet', '4': 'P&L'};
    this.dateFormat = 'MM/DD/YYYY';
    this.loaded = false;
    this.initEvents();

};
/** class methods **/
CustomReports.prototype = {
    initEvents: function () {
    },
    load: function(data, type) {
        this.type = type;
        this.body.parent().find('#custom_types').val(type).trigger('change');
        for(var i in data){
            this.body.find('[name="'+data[i].name+'"]').val(data[i].value);
        }
    },
    initType: function (type, fields) {
        this.fields = fields;
        this.type = type;
        if(!this.modal)this.body.html('');
        if(type == '0' || this.modal) return;
        $('a[href="' + this.bodyId + '"]').trigger('click');
        var block = this.getParamsBlockWrapper();
        switch (this.type) {
            case '1':
                block.find('.parameters').html(this.getTrialParamsHTML());
                break;
        }
        this.body.append(block);
    },

    initData: function (data, columns) {
        this.data = data;
        this.columns = columns;
    },

    getSave: function () {
        var result = [];
        this.body.find('select, input').each(function(){
            var label = '';
            if($(this).is('select')) label = $(this).find('option:first').text(); else label = $(this).prev().text();
            result.push({name: $(this).attr('name'), value:$(this).val(), label: label, user: $(this).is('[user]')});
        });
        return result;
    },

    getParamsBlockWrapper: function () {
        var block = '<div class="custom-settings"><h3>' + this.names[this.type] + '</h3><div class="parameters"></div><button id="apply-custom" class="mt-3 float-none">Apply parameters</button></div>';
        return $(block);
    },

    getTrialParamsHTML: function () {
        var block = '';
        block += '<span class="select">' +
            '        <select class="compare-field" name="param_c_credit">' +
            '            <option disabled selected>Choose Credit Field</option>';
        for (var i in this.fields) {
            if (this.fields[i].type == 'num')
                block += '<option value="' + this.fields[i].id + '">' + this.fields[i].name + '</option>';
        }
        block += '        </select>' +
            '     </span>';
        block += '<span class="select">' +
            '        <select class="compare-field" name="param_c_debit">' +
            '            <option disabled selected>Choose Debit Field</option>';
        for (var i in this.fields) {
            if (this.fields[i].type == 'num')
                block += '<option value="' + this.fields[i].id + '">' + this.fields[i].name + '</option>';
        }
        block += '        </select>' +
            '     </span><p></p>';
        return block;
    },


}