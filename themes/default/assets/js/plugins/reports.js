var Reports = function (left, table, bottom, right, options) {
    this.left = $(left);
    this.tablebody = $(table);
    this.bottom = $(bottom);
    this.right = $(right);
    this.options = options;
    this.options.settingsURL = 'reports/getSettings';
    this.options.ajaxTableURL = 'reports/getAjaxTable';
    this.options.ajaxTableHeaderURL = 'reports/getAjaxTableHeader';

    this.modal = options.modal || false;
    this.batch = options.batch || false;
    this.fheight = 0;
    if(this.modal){
        this.body = $('body').find('.report-modal').last();
        this.left = this.body.find(left);
        this.table_id = this.body.find(table);
        this.bottom = this.body.find(bottom);
        this.right = this.body.find(right);
    }else if(this.batch){
        this.body = $('.br-outer');
        this.right = this.body.find('.br-filters');
        this.bfilters = options.bfilters;
    } else {
        this.body = $('#root');
    }
    this.type = options.type || 0;
    this.ctype = options.ctype || 0;
    this.id = options.id || 0;
    this.base = options.base;
    this.groupingind = 0;
    this.cnames = [];
    this.signs={num:[0,1,4,5],usd:[0,1,4,5], text:[0,1,2,6], date:[0,3,4,5]};
    this.typeNames = {2 : 'Leases', 8 : 'Transactions', 1 : 'Properties', 3: 'Units', 4: 'Accounts', 5: 'Tenants', 6: 'Vendors', 7: 'Inventory'};
    this.customReports = null;
    this.dFormat = 'MM/DD/YYYY';
    this.activecr = null;
    this.table = null;
    this.init();
};
/** class methods **/
Reports.prototype = {
    init: function () {
        if(this.modal) {
            this.initModalEvents();
        } else if(this.batch) {
            this.initBatchEvents();
        } else this.initEvents();
        this.customReports = new CustomReports(this.modal ? '#reports-filters' : '#custom-parameters > div.list-tree', this.modal);

    },

    save: function (ret = false) {
        var that = this;
        var grouping = $('.grouping-select option:selected, .sorting-select option:selected').map(function () {
            var obj = {}, li = $(this).closest('li');
            obj.column = $(this).val();
            obj.dtype = that.cnames[obj.column].type;
            if(li.find('.grouping-select').length > 0){
                console.log('group');
                obj.type = 'grouping';
                if(obj.dtype == 'date') obj.datef = li.find('.datef').val();
                obj.hg = li.find("input[name^='hg']:checked").val() || 0;
                obj.he = li.find("input[name^='he']:checked").val() || 0;
                obj.ht = li.find("input[name^='ht']:checked").val() || 0;
                obj.vt = li.find("input[name^='vt']:checked").val() || 0;
                obj.exp = li.find("input[name^='exp']:checked").val() || 0;
                obj.nexp = li.find("input[name^='nexp']:checked").val() || 0;
                obj.col = li.find("input[name^='col']:checked").val() || 0;
                obj.htotal = li.find(".hg-select").val() || null;
                obj.header = li.find("input[name^='header']:checked").map(function () { return $(this).val() }).get();
                obj.footer = li.find("input[name^='footer']:checked").map(function () { return $(this).val() }).get();
                obj.hide0 = li.find("input[name^='hide']:checked").map(function () { return $(this).val() }).get();
            }else{
                obj.type = 'sorting';
                obj.order = li.find('.order-select').val();
            }
            return obj;
        }).get();
        var filters = $('#filters-section>li').map(function () {
            var obj = {};
            obj.column = $(this).find('select.filtersel').val();
            obj.dtype = that.cnames[obj.column].type;
            obj.condition = $(this).find('select.filtercond').val();
            obj.fields = $(this).find('.field').map(function () {
                var o = {};
                o.value = $(this).find('input[name="name"]').val();
                o.name1 = $(this).find('input[name="name1"]').val();
                o.name2 = $(this).find('input[name="name2"]').val();
                return o;
            }).get();
            return obj;
        }).get();
        var ufilters = $('#ufilters-section>li').map(function () {
            var obj = {};
            obj.column = $(this).find('select.filtersel').val();
            obj.dtype = that.cnames[obj.column].type;
            obj.condition = $(this).find('select.filtercond').val();
            obj.fields = $(this).find('.field').map(function () {
                var o = {};
                o.value = $(this).find('input[name="name"]').val();
                o.name1 = $(this).find('input[name="name1"]').val();
                o.name2 = $(this).find('input[name="name2"]').val();
                return o;
            }).get();
            return obj;
        }).get();
        var params = $('#params-section li').map(function () {
            var obj = {};
            obj.name = $(this).find('input[name="param_name"]').val();
            obj.key = $(this).find('input[name="param_key"]').val();
            obj.type = $(this).find('select[name="param_type"]').val();
            obj.value = $(this).find('input[name="param_value"]').val();
            obj.source = $(this).find('input[name="param_source"]').val();
            return obj;
        }).get();

        var top = {};
        top.gtotal = $('.report-gmain #gtotal:checked').val() || 0;
        top.gt_custom = $('.report-gmain #gtotal_custom').val() || null;
        var cf = $('#cfields input').map(function () { return {id: $(this).val(), type: that.cnames[$(this).val()].type, name: that.cnames[$(this).val()].name, query: $(this).attr('query'), af: $(this).attr('af')} }).get();
        var columns = $('#report-columns input:checked').map(function () { return $(this).val() }).get();
        var cr = {id: that.right.find('select[name="cr[id]"]').val(), name1: that.right.find('input[name="cr[name1]"]').val(), name2: that.right.find('input[name="cr[name2]"]').val()};
        //console.log(cr);
        var data = { corder: that.table ? that.table.corder : null, name: $('#rname').val() || "no name", columns: columns, grouping: grouping, filters: filters, ufilters: ufilters, top: top, params: params, cf:cf, type: that.type, cr: cr, activecr: that.activecr, printmode: $('input[name="print-mode"]:checked').val(), truncate: $('input#slick-truncate:checked').val() || 0, ctype: that.ctype, custom: that.customReports.getSave()};
        if(ret) return data;
        $.post(that.base + that.options.saveURL, {data:data}, function (data) {
            if(data.redirect != null) window.location = data.redirect;
            JS.showAlert(data.type, data.message);
        }, 'JSON').fail(function (data) { console.log(data); });
    },

    parseReportSettings: function () {
        var that = this;
        var grouping = $('.grouping-select option:selected, .sorting-select option:selected').map(function () {
            var obj = {}, li = $(this).closest('li');
            obj.column = $(this).val();
            if ($(this).val() != "Choose"){
                obj.dtype = that.cnames[obj.column].type;
                if(li.find('.grouping-select').length > 0){
                    obj.type = 'grouping';
                    if(obj.dtype == 'date') obj.datef = li.find('.datef').val();
                    obj.hg = li.find("input[name^='hg']:checked").val() || 0;
                    obj.he = li.find("input[name^='he']:checked").val() || 0;
                    obj.ht = li.find("input[name^='ht']:checked").val() || 0;
                    obj.vt = li.find("input[name^='vt']:checked").val() || 0;
                    obj.exp = li.find("input[name^='exp']:checked").val() || 0;
                    obj.nexp = li.find("input[name^='nexp']:checked").val() || 0;
    
                    obj.col = li.find("input[name^='col']:checked").val() || 0;
                    obj.htotal = li.find(".hg-select").val() || null;
                    obj.header = li.find("input[name^='header']:checked").map(function () { return $(this).val() }).get();
                    obj.footer = li.find("input[name^='footer']:checked").map(function () { return $(this).val() }).get();
                    obj.hide0 = li.find("input[name^='hide']:checked").map(function () { return $(this).val() }).get();
                }else{
                    obj.type = 'sorting';
                    obj.order = li.find('.order-select').val();
                }
                return obj; 
            }
        }).get();
        var filters = $('#filters-section>li').map(function () {
            var obj = {};
            obj.column = $(this).find('select.filtersel').val();
            obj.dtype = that.cnames[obj.column].type;
            obj.condition = $(this).find('select.filtercond').val();
            obj.fields = $(this).find('.field').map(function () {
                var o = {};
                o.value = $(this).find('input[name="name"]').val();
                o.name1 = $(this).find('input[name="name1"]').val();
                o.name2 = $(this).find('input[name="name2"]').val();
                return o;
            }).get();
            return obj;
        }).get();
        var ufilters = $('#ufilters-section>li').map(function () {
            var obj = {};
            obj.column = $(this).find('select.filtersel').val();
            obj.dtype = that.cnames[obj.column].type;
            obj.condition = $(this).find('select.filtercond').val();
            obj.fields = $(this).find('.field').map(function () {
                var o = {};
                o.value = $(this).find('input[name="name"]').val();
                o.name1 = $(this).find('input[name="name1"]').val();
                o.name2 = $(this).find('input[name="name2"]').val();
                return o;
            }).get();
            return obj;
        }).get();
        var params = $('#params-section li').map(function () {
            var obj = {};
            obj.name = $(this).find('input[name="param_name"]').val();
            obj.key = $(this).find('input[name="param_key"]').val();
            obj.type = $(this).find('select[name="param_type"]').val();
            obj.value = $(this).find('input[name="param_value"]').val();
            obj.source = $(this).find('input[name="param_source"]').val();
            return obj;
        }).get();
        var cf = $('#cfields input').map(function () { return {id: $(this).val(), type: that.cnames[$(this).val()].type, name: that.cnames[$(this).val()].name, query: $(this).attr('query'), af: $(this).attr('af')} }).get();
        var top = {};
        top.gtotal = $('.report-gmain #gtotal:checked').val() || 0;
        top.gt_custom = $('.report-gmain #gtotal_custom').val() || null;

        var data = {grouping: grouping, filters: filters, ufilters: ufilters, params:params, cf:cf, type: that.type, ctype: that.ctype, top: top, ctype: that.ctype, custom: that.customReports.getSave()};
        return data;
    },

    load: function (type, id){
        this.id = id;
        if(type == '0') return;
        var that = this;
        $(this.options.recordSelect).val(type);
        this.type = type;
        $.post( this.base + this.options.settingsURL + "/" + this.id + "/" +this.type, function(data) {
            that.settings = data;
            that.cnames = that.settings ? that.settings.cnames : [];
            that.ctype = that.settings ? that.settings.ctype : 0;
            that.loadLeft();
            that.generateDates();
        }, 'JSON').fail(function(data){console.log(data);});
        //that.bottom.find('ul.list-tree').empty();
        //this.reloadTable();
    },

    generateDates: function(){
        this.right.find('input').datepicker( "destroy" );
        this.right.find('input.dinput').attr('autocomplete', 'new-password').datepicker();
    },

    initEvents: function () {
        var that = this;
        $(this.options.recordSelect).change(function () {
            that.type = $(this).val();
            that.ctype = null;
            that.left.find('ul#cfields').empty();
            that.bottom.find('.report-gline').remove();
            that.right.find('#filters-section, #ufilters-section').empty();
            that.customReports.body.empty();
            that.load(that.type, that.id);
            that.generateDates();
        });
        $(this.options.customSelect).change(function () {
            var old = that.ctype;
            that.ctype = $(this).val();
            that.customReports.initType(that.ctype, that.getSelectedColumns());
            that.generateDates();
            //that.generateSelect(source, parent.find('[name="param_value"]').parent());
            if(that.ctype != old){
                that.reloadTable('custom');
            }

        });
        $('input[name="print-mode"]').change(function(){
            $('ul.print-orientation li').removeClass('active');
            $(this).closest('li').addClass('active');
        });
        this.left.on('change', '.checklist-a input[type="checkbox"]', function () {
            that.table.toggleColumn('c'+$(this).val());
        });

        this.left.on('click', '#addFieldButton', function(){
            JS.openDraggableModal('custom', 'add', null, null, {url:'reports/addFieldModal'}, [{
                event: 'postsubmit',
                function: function (e, data) {
                    var data = JSON.parse(data['data']);
                    var ind = that.customind;
                    $('#cfields').append('<li><label for="cf'+ind+'" class="active"><input type="checkbox" id="cf'+(ind)+'" value="'+(ind)+'" dtype="'+data.type+'" query="'+data.query+'" af="'+(data.af || 0)+'" checked><span>'+data.name+'</span></label><a href="#" class="edit-field"><i class="fas fa-edit"></i></a><a href="#" class="delete-field"><i class="fas fa-times-circle"></i></a></li>');
                    var cb = $('#cfields').find('input:last');

                    that.cnames[cb.val()] = [];
                    that.cnames[cb.val()]['name'] = data.name;
                    that.cnames[cb.val()]['type'] = data.type;
                    that.cnames[cb.val()]['index'] = that.left.find("input").length;
                    that.customind++;
                    that.updateOptions(cb.val());
                    cb.after('<div class="input"></div>').addClass('hidden').attr('aria-hidden', true).on('click', function () {
                        $(this).parent('label').toggleClass('active');
                    });
                    that.reloadTable('cf');
                }
            }]);
        }).on('click', '.delete-field', function(){
            that.updateOptions($(this).closest('li').find('input').first().val(), true);
            $(this).closest('li').remove();
            that.reloadTable('cf');
        }).on('click', '.edit-field', function(){
            var input = $(this).closest('li').find('input');
            var data = {type: input.attr('dtype'), name: input.closest('li').find('span').text(), query: input.attr('query'), af: input.attr('af')};
            JS.openDraggableModal('custom', 'edit', input.val(), null, {url:'reports/addFieldModal', data: data}, [{
                event: 'postsubmit',
                function: function (e, data) {
                    var data = JSON.parse(data['data']);
                    input.attr('dtype', data.type);
                    input.attr('query', data.query);
                    input.attr('af', data.af || 0);
                    input.closest('li').find('span').text(data.name);
                    var val = input.val();

                    that.cnames[val]['name'] = data.name;
                    that.cnames[val]['type'] = data.type;
                    that.reloadTable('cf');
                }
            }]);
        });

        this.bottom.on('mouseenter', '.list-tree > li', function () {
            $(this).children('div').show();
            $(this).addClass('toggle');
        }).on('mouseleave', '.list-tree li', function () {
            if($(this).find('.cpopup.open').length > 0) return;
            $(this).children('div').hide();
            $(this).removeClass('toggle');
        }).on('click', '.close', function () {
            $(this).closest('li').remove();
            that.refreshGroupingMargins();
            that.reloadTable('grouping');
        });
        $('#add-group').click(function () {
            var columns = that.getSelectedColumns();
            $.post(that.base + that.options.groupingLineTemplateURL, { columns: columns, ind: that.groupingind}, function (data) {
                that.groupingind++;
                that.bottom.find('ul.list-tree').append(data);
                that.refreshGroupingMargins();
            }).fail(function (data) { console.log(data); });
        });
        $('#add-sort').click(function () {
            var columns = that.getSelectedColumns();
            $.post(that.base + that.options.sortingLineTemplateURL, { columns: columns, ind: that.groupingind}, function (data) {
                that.groupingind++;
                that.bottom.find('ul.list-tree').append(data);
                that.refreshGroupingMargins();
            }).fail(function (data) { console.log(data); });
        });
        this.bottom.on('change', 'ul.list-tree .grouping-select', function () {
            //if(that.left.find('input[value="'+$(this).val()+'"]').is(':checked')) that.left.find('input[value="'+$(this).val()+'"]').trigger('click');
            if(that.cnames[$(this).val()].type == 'date') $(this).closest('li').find('.datef-wrapper').show(); else $(this).closest('li').find('.datef-wrapper').hide();
            that.reloadTable('grouping');
        });
        this.bottom.on('change', 'ul.list-tree .datef', function () {
            //if(that.left.find('input[value="'+$(this).val()+'"]').is(':checked')) that.left.find('input[value="'+$(this).val()+'"]').trigger('click');
            that.reloadTable('dateformat');
        });
        this.bottom.on('change', 'input[name^="hg"]', function(){
            var li = $(this).closest('li');
            li.find('.hgtotals').toggle();
            li.find('.hftotals').toggle();
        });
        this.bottom.on('change', 'input[name="hg"], input[name="vt"], input[name="ht"], input[name="he"], .hg-select, #gtotal, #gtotal_custom, input[name="exp"], input[name="nexp"], input[name="col"]', function () {
            that.reloadTable('horizontal');
        });
        this.bottom.on('change', 'ul.list-tree .sorting-select, ul.list-tree .order-select', function () {
            that.reloadTable('sorting');
        });

        this.bottom.on('click', '.cpopup a', function () {
            that.reloadTable();
        }).on('click', '.arrow-up', function () {
            var e = $(this).closest('li.report-gline');
            e.prev().insertAfter(e);
            that.refreshGroupingMargins();
            that.reloadTable('grouping');
        }).on('click', '.arrow-down', function () {
            var e = $(this).closest('li.report-gline');
            e.next().insertBefore(e);
            that.refreshGroupingMargins();
            that.reloadTable('grouping');
        });


        this.right.on('change', 'select.filtercond', function(e){
            var li = $(this).closest('li');
            if($(this).val() == '3'){
                li.find('.date').show();
                li.find('.value').hide();
            }else{
                li.find('.value').show();
                li.find('.date').hide();
            }
            if(e.originalEvent)li.find('.filtersel').attr('cchanged','1').trigger('change');
        }).on('change', 'select.filtersel', function(){            
            var li = $(this).closest('li');
            var select = $(this);
            var value = $(this).val();
            if(select.attr("cchanged") != "1") {
                li.find('select.filtercond option').hide();
                var selected = select.attr("first");
                that.signs[that.cnames[value]['type']].forEach(function(sign){
                    li.find('.filtercond option[value="'+sign+'"]').show();
                    if(selected == "0"){
                        selected = "1";
                        li.find('.filtercond option[value="'+sign+'"]').prop('selected', true);
                    }
                });
                select.attr("first", "0");
                select.attr("cchanged", 0);
                li.find('select.filtercond').trigger('change');
            }
            var input = li.find('.value input');
            var cond = li.find('select.filtercond').val();
            input.each(function(){
                var input = $(this);
                if(that.cnames[value]['source'] && that.cnames[value]['source'] != null && (cond == "0" || cond == "1")) {
                    if(!input.hasClass('editable-select') && !input.parent().data('input'))
                        input.parent().data('input',input);
                    that.generateSelect(that.cnames[value]['source'], input.parent());

                }else if(that.cnames[value]['source'] && (cond == "6")) {
                    if(!input.hasClass('multiple-select') && !input.parent().data('input'))
                        input.parent().data('input',input);
                    that.generateMultipleSelect(that.cnames[value]['source'], input.parent());

                }else{
                    val = input.val();
                    if(input.parent().data('input')) input.parent().html(input.parent().data('input'));
                    if(that.cnames[value]['type'] == 'date'){
                        li.find('input').addClass('dinput');
                    }  else {
                        li.find('input').removeClass('dinput');
                    }
                    that.generateDates();
                }
            });

        });

        this.right.on('click', '#add-filter, #add-ufilter', function() {
            var columns = that.getSelectedColumns();
            var body = $('#filters-section');
            if($(this).attr('id') == 'add-ufilter') body = $('#ufilters-section');
            $.post(that.base + that.options.filterLineTemplateURL, { columns: columns, ind: that.filtersind}, function (data) {                
                body.append(data);
                body.find('select.filtersel:last').prop("selectedIndex", 0).trigger('change');
                that.generateDates();                
            }).fail(function (data) { console.log(data); });
            return false;
        });

        this.right.on('click', '#add-params', function() {
            var body = $('#params-section');
            body.append(that.getParamTemplate());
        }).on('click', '#apply-params', function() {
            that.reloadTable('params');
        }).on('click', '#apply-filter', function() {
            that.reloadTable('filter');
        }).on('change', 'select[name="param_type"]', function(){
            var li = $(this).closest('li');
            var select = $(this);
            if(select.val() == 'date') li.find('input[name="param_value"]').addClass('dinput'); else li.find('input[name="param_value"]').removeClass('dinput');
            that.generateDates();
        }).on('input', 'input[name="param_key"]', function(){
            var li = $(this).closest('li');
            var span = li.find('p span');
            span.text($(this).val());
        });

        this.right.on('click', '.delete-section', function() {
            $(this).parents('.section, li').first().remove();
            that.reloadTable('filter');
        }).on('click', '.add-or', function() {
            var field = $(this).closest('li').find('div.field').first().clone();
            field.prepend('<div class="or-block">or</div>');
            field.prepend('<a href="#" class="delete-or"><i class="fas fa-times-circle"></i></a>');
            $(this).before(field);
            if(field.find('.editable-select').length > 0){
               that.generateSelect(that.cnames[$(this).closest('li').find('select.filtersel').val()]['source'], field.find('.value'));
            }
        }).on('click', '.delete-or', function() {
            $(this).closest('.field').remove();
        });

        $('#saveButton').click(function () {
            that.save();
        });

        this.right.on('click', '#apply-filter, #apply-custom', function() {
            that.reloadTable('custom');
            return false;
        });
        this.right.on('input', '[name="param_source"]', function(){
            var parent = $(this).parent();
            var source = $(this).val();
            var input = parent.find('[name="param_value"]').parent().find('input:first');
            if(JS.sdata[source] && JS.sdata[source].length) {
                if(!input.hasClass('editable-select') && !input.parent().data('input'))
                    input.parent().data('input',input);
                if(!input.hasClass('editable-select') || !e.originalEvent){
                    if(input.hasClass('editable-select') && input.parent().data('input'))
                        input.parent().html(input.parent().data('input'));
                    that.generateSelect(source, parent.find('[name="param_value"]').parent());
                }
            }else{
                if(input.parent().data('input')) input.parent().html(input.parent().data('input'));
            }
        });

        setTimeout(function(){that.right.find('input[name="param_source"]').trigger('input');},2000);
    },

    generateSelect: function(source, parent, value = null) {
        var select = '<select class="editable-select" name="'+(parent.find('input:first').attr('name') || 'name')+'" key="'+(parent.find('input:first').attr('key'))+'">';
        ///console.log(parent);
        var def = parent.find('input:first').val();
        //console.log(def);
        if(ESC && !ESC.exists(source)) {
            var opts = JS.sdata[source];
            for(i in opts)
                select += '<option value="'+opts[i].id+'"data-id="'+ opts[i].id + '" data-parent-id="' + opts[i].parent_id + '" class="nested' + opts[i].step+'">'+opts[i].name+'</option>';

        }
        select = select+'</select>';
        parent.html(select);
        parent.find('.editable-select').fastSelect({type: source, default: value || def});
    },

    generateMultipleSelect: function(source, parent, value = null) {
        var def = value || parent.find('input:first').val();

        var select = '<input class="multiple-select" name="name" value="'+def+'">';
        var opts = JS.sdata[source];
        parent.html(select);
        parent.find('.multiple-select').selectize({
            plugins: ["remove_button"],
            persist: false,
            maxItems: null,
            valueField: 'id',
            labelField: 'name',
            searchField: ['name'],
            options: opts,
            render: {
                item: function(item, escape) {
                    return '<div>' +
                        (item.name ? '<span class="name">' + escape(item.name) + '</span>' : '') +
                        (item.email ? '<span class="email">' + escape(item.email) + '</span>' : '') +
                        '</div>';
                },
                option: function(item, escape) {
                    var label = item.name || item.email;
                    var caption = item.name ? item.email : null;
                    return '<div>' +
                        '<span class="label">' + escape(label) + '</span>' +
                        (caption ? '<span class="caption">' + escape(caption) + '</span>' : '') +
                        '</div>';
                }
                
            }
            
        });
    },

    getParamTemplate: function(){
        var li = '<li>\n' +
            '                    <label>Parameter name: </label><input type="text" name="param_name" value="" placeholder="Parameter name"/>\n' +
            '                    <label>Parameter key: </label><input type="text" name="param_key" value="" placeholder="Parameter key"/>\n' +
            '                    <p>Use it as {params.<span></span>}</p>\n' +
            '                    <span class="arrow-down">\n' +
            '                        <select name="param_type">\n' +
            '                            <option value="text">Text</option>\n' +
            '                            <option value="num">Number</option>\n' +
            '                            <option value="date">Date</option>\n' +
            '                        </select>\n' +
            '                    </span><br/>' +
            '                    <label>Parameter value: </label><input type="text" value="" name="param_value" placeholder="Parameter value"/>' +
            '                    <label>Parameter source: </label><input type="text" value="" name="param_source" placeholder="Parameter source"/>' +
            '                    <a href="#" class="delete-section">Delete</a>'+
            '                </li>';
        return li;
    },

    loadLeft: function () {
        var that = this;
        this.left.load(this.base + this.options.leftColumnURL + "/" + this.type + "/" + this.id, function () {
            that.loadBottom();
            $('input[type="checkbox"]:not(".no-js")').each(function (i) {
                if ($(this).is('[checked]')) {
                    $(this).prop('checked', true).parent('label').addClass('active');
                } else {
                    $(this).prop('checked', false).removeAttr('checked');
                }
                $(this).after('<div class="input"></div>').addClass('hidden').attr('aria-hidden', true).on('click', function () {
                    $(this).parent('label').toggleClass('active');
                });
            });
            that.customind = that.left.find("#cfields li input").last().val() ? parseInt(that.left.find("#cfields li input").last().val().slice(0)) + 1 : 1000001;
            //$('.accordion-a').semanticAccordion().children(':header.toggle').next().show();
            that.right.find('select.filtersel').trigger('change');
            that.right.find('select.filtercond').trigger('change');
            if(that.ctype) {
                that.customReports.initType(that.ctype, that.getSelectedColumns());
                that.customReports.load(that.settings.custom, that.ctype);
            }
        });
    },

    loadBottom: function () {
        var that = this;
        var columns = that.getSelectedColumns();
        $.post(that.base + that.options.bottomURL + "/" + this.type + "/" + this.id, { columns: columns }, function (data) {
            that.bottom.html(data);
            //that.doCheckboxes(that.bottom);
            that.groupingind = that.bottom.find('.report-gline').length;
            that.reloadTable();
            that.refreshGroupingMargins();
        }).fail(function (data) { console.log(data); });

    },

    reloadTable: function (changed = 'grouping') {
        var that = this;
        //var cf = $('#cfields input').map(function () { return {id: $(this).val(), type: that.cnames[$(this).val()].type, name: that.cnames[$(this).val()].name, query: $(this).attr('query'), af: $(this).attr('af')} }).get();
        var settings = this.parseReportSettings();
        this.horizontal = this.isHorizontal(settings); 
        $.post( this.base + this.options.ajaxTableURL + "/" + this.type+ "/" + this.id, {settings:JSON.stringify(settings)}, function(data) {
            if(!that.table) {
                var hidden = that.left.find('input[type="checkbox"]:not(:checked)').map(function () { return 'c'+$(this).val() }).get();
                that.table = new NestedTable('#reports-table', data['data'], data['columns'], {corder: that.settings ? that.settings.corder : null , hidden: hidden});
                that.initTableEvents();
            }else {
                that.table.setData(data['data'], data['columns'], changed);
            }
        }, 'JSON').fail(function(data){console.log(data);});
    },

    isHorizontal: function(settings) {
        return _.sumBy(settings.grouping, function(o) { return parseInt(o.hg || 0); }) > 0
    },

    initTableEvents: function() {
        var that = this;
        $(this.table).on('nameclick', function(){
            var data = that.table.tddata;
            var settings = that.save(true);
            if(that.modal) settings = JSON.parse(JSON.stringify(that.settings));
            if(that.horizontal) {_.find(settings.grouping, { hg: '1' }).hg = 0;}
            var filter = {column: data.row['grouped-by'].slice(1), dtype: 'text', condition: '0', fields: [{"value": data.row['grouped-key']}]}
            settings.topgroup = data.row['grouped-by'].slice(1);
            if(!settings.ufilters) settings.ufilters = [];
            settings.ufilters.push(filter);
            delete settings.columns;
            if(that.activecr) settings.newtype = that.activecr;
            console.log(data.row.report_id);
            JS.openDraggableModal('report', 'add', data.row.report_id || -1, null, {settings: settings, ftransfer: data.row.report_id != null, data: {type: that.type, filters: settings.ufilters, params: settings.params}});
        });
        $(this.table).on('tdclick', function(){
            var data = that.table.tddata;
            if(that.horizontal) {
                var settings = that.save(true);
                if(that.modal) settings = JSON.parse(JSON.stringify(that.settings));
                _.find(settings.grouping, { hg: '1' }).hg = 0;
                var filter = {column: data.row['grouped-by'].slice(1), dtype: 'text', condition: '0', fields: [{"value": data.row['grouped-key']}]}
                settings.topgroup = data.row['grouped-by'].slice(1);
                if(!settings.ufilters) settings.ufilters = [];
                settings.ufilters = _.filter(settings.ufilters, function(o){
                    return o.column !=data.row['grouped-by'].slice(1) && o.column!=data.column['h_column'];
                });
                settings.ufilters.push(filter);
                filter = {column: data.column['h_column'], dtype: data.column['h_type'], condition: data.column['h_cond'], fields: [{"value": data.column['h_value'], "name1": data.column['h_name1'], "name2": data.column['h_name2']}]}
                settings.ufilters.push(filter);
                delete settings.columns;
                if(that.activecr) settings.newtype = that.activecr;
                JS.openDraggableModal('report', 'add', '-1', null, {settings: settings});
            }else{
                var type=null;
                if(data.row['main-type'] && data.row['main-type']!='') type = data.row['main-type'];
                JS.openDraggableModal(type || that.body.parent().find('#record_types option:selected').attr('type') || that.body.parent().find('#record_types').val(), 'edit', data.row['main-id'], null, null);
            }
        });
    },

    getSelectedColumns: function () {
        var result = [];
        this.left.find('input[type="checkbox"]').each(function () {
            result.push({ id: $(this).val(), name: $(this).parent().text(), type: $(this).attr('dtype') == 'usd' ? 'num' : $(this).attr('dtype'), checked: $(this).is(":checked") });
        });
        return result;
    },

    updateOptions: function(value, del = false){
        if(del) {
            this.bottom.find('option[value="'+value+'"]').remove();
            this.bottom.find('input[value="'+value+'"]').first().closest('tr').remove();
            this.right.find('option[value="'+value+'"]').remove();
        }else{
            this.bottom.find('select.grouping-select, select.hg-select').append('<option value = "'+value+'">'+this.cnames[value].name+'</option>');
            this.right.find('select.filtersel').append('<option value = "'+value+'">'+this.cnames[value].name+'</option>');
            if(this.cnames[value].type == 'num' || this.cnames[value].type == 'usd')
                this.bottom.find('.cpopup table').append('<tr><td><div class="custom-control custom-checkbox form-group mb-0">'+
                    '<input type="checkbox" value="'+value+'" class="custom-control-input" name="footer[]" id="footer2_'+value+'">'+
                    '<label class="custom-control-label checkbox-left text-left" for="footer2_'+value+'"></label>'+
                    '</div></td>'+
                    '<td><div class="custom-control custom-checkbox form-group mb-0">'+
                    '<input type="checkbox" value="'+value+'" class="custom-control-input" name="header[]" id="header2_'+value+'">'+
                    '<label class="custom-control-label checkbox-left text-left" for="header2_'+value+'"></label>'+
                    '</div></td>'+
                    '<td><div class="custom-control custom-checkbox form-group mb-0">'+
                    '<input type="checkbox" value="'+value+'" class="custom-control-input" name="hide[]" id="hide2_'+value+'">'+
                    '<label class="custom-control-label checkbox-left text-left" for="hide2_'+value+'"></label>'+
                    '</div></td>'+
                    '<td><div class="mb-0">'+
                    '<label class="text-left">'+this.cnames[value].name+'</label>'+
                    '</div></td></tr>'
                );

        }
    },

    refreshGroupingMargins: function () {
        this.bottom.find('ul.list-tree > li').each(function (i) {
            $(this).css('margin-left', ((i * 25) - (i > 0) * 21) + 'px');
        });
    },

    refreshGroupingOptions: function (id, visible) {
        this.bottom.find('ul.list-tree .grouping-select').each(function () {
            if (visible) {
                $(this).find('option[value="' + id + '"]').show();
            } else {
                if ($(this).val() == id) $(this).closest('li').remove();
                else $(this).find('option[value="' + id + '"]').hide();
            }
        });
    },

    // --------------------------------------------------
    // ----------------- MODAL SECTION ------------------
    // --------------------------------------------------

    replaceModalInputs: function(){
        var that = this;
        this.body.find('input[source]').each(function(){
            if($(this).closest('.parameters').length > 0) {
                var parent = $(this).parent();
                var source = $(this).attr('source');
                var input = $(this);
                if(JS.sdata[source] && JS.sdata[source].length) {
                    that.generateSelect(source, input.parent());
                }
                return;
            }
            var value = $(this).val();
            $(this).val('');
            var parent = $(this).parent().parent();
            var input = $(this);
            var cond = parent.find('input.filtercond').val();
            if( cond == "0" || cond == "1") {
                that.generateSelect(input.attr('source'), input.parent(), value);
            }else if( cond == "6") {
                that.generateMultipleSelect(input.attr('source'), input.parent(), value);
            }else{
                val = input.val();
                if(input.parent().data('input')) input.parent().html(input.parent().data('input'));
            }
        });
    },

    initModalEvents: function (){
        var that = this;
        this.generateDates();
        this.replaceModalInputs();
        this.body.on('click', '.apply-button', function(){
            that.loadModal();
        });

        this.right.on('change', 'input[type="text"]', function(){
            that.loadModal();
        });

        this.right.on('change', '.multiple-select', function(){
            that.loadModal();
        });

        this.body.on('change', 'input[name="print-mode"]', function(){
            $('ul.print-orientation li').removeClass('active');
            $(this).closest('li').addClass('active');
        });

        this.body.on('click', '.hide-button', function(){
            var button = $(this);
            if(this.fheight == 0) this.fheight = this.right.height;
            if(that.right.hasClass('shown')) {
                that.right.slideUp().removeClass('shown');
                button.text('Show');
            } else {
                that.right.slideDown().addClass('shown');
                button.text('Hide');
            }
        });
        this.body.on('change', '.cr-trigger', function(){
            var index = $(this).is(':checked') ? 1 : 0;
            that.body.find('.cr-select')[0].selectedIndex = index;
            that.body.closest('.modal').find('#report-header h4').text(that.body.find('.cr-select option:eq('+index+')').text());
            that.body.find('.cr-select').trigger('change');
        });
        this.body.on('change', '.cr-select', function(){
            that.activecr = $(this).val();
            that.settings.newtype = $(this).val();
            that.loadModal();
        });

        $.post( this.base + this.options.settingsURL + "/" + this.id + "/" +this.type, function(data) {
            if(that.id != '-1') that.settings = data; else that.settings = that.options.settings;
            that.cnames = data.cnames;
            that.corder = that.settings.corder || [];
            that.loadModal();
        }, 'JSON').fail(function(data){console.log(data);});
    },

    parseModalSettings: function() {
        var str = '';
        var text = null;

        for(var i in this.settings.ufilters) {
            str += '<h4 style= "color: #878A89; text-align:center">';
            str += this.cnames[this.settings.ufilters[i].column].name + ": ";
            this.settings.ufilters[i].fields[0].value = this.right.find('#f'+i+' input[name="name"]').val();
            this.settings.ufilters[i].fields[0].name1 = this.right.find('#f'+i+' input[name="name1"]').val();
            this.settings.ufilters[i].fields[0].name2 = this.right.find('#f'+i+' input[name="name2"]').val();
            if(this.settings.ufilters[i].condition == '3')
                str += this.settings.ufilters[i].fields[0].name1 + '-' + this.settings.ufilters[i].fields[0].name2;
            else if(this.settings.ufilters[i].condition == '6'){
                text = this.right.find('#f'+i+' .selectize-input.items>div>span').map(function() {return $(this).text();}).get().join(', ');
                str += text;
            }else{
                text = this.right.find('#f'+i+' input[name="name"]').attr('text');
                if(text)
                    str += text;
                else
                    str += this.settings.ufilters[i].fields[0].value;
            }
            str += '</h4>';
        }
        for(var i in this.settings.params) {
            str += '<h4 style= "color: #878A89; text-align:center">';
            str += this.settings.params[i].name + ": ";
            if( this.settings.params[i].source){
               this.settings.params[i].value = this.right.find('.parameters input[key="'+this.settings.params[i].key+'"]').attr('sel-value');
               text = this.right.find('.parameters input[key="'+this.settings.params[i].key+'"]').val();
            } else {
               this.settings.params[i].value = this.right.find('.parameters input[key="'+this.settings.params[i].key+'"]').val(); 
               text = this.right.find('.parameters input[key="'+this.settings.params[i].key+'"]').attr('text'); 
            }

            
            if(text)
                str += text;
            else
                str += this.settings.params[i].value;
            str += '</h4>';
        }
        this.body.closest('.modal').find('#report-header div').html(str);
    },

    loadModal: function (){
        var that = this;
        this.parseModalSettings();
        this.horizontal = this.isHorizontal(this.settings);
        that.body.find('#reports-table').hide();
        that.body.find('#report-header').append('<div class="lds-roller" style="padding-top: 10px;  overflow:hidden;  padding-left: 500px; min-width:600px"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>');
        $.post( this.base + this.options.ajaxTableURL + "/" + this.settings.type + "/" + this.id, {settings:JSON.stringify(this.settings)}, function(data) {
            if(!that.table) {
                var hidden = [];
                //console.log(that.cnames);
                if(!that.horizontal)
                    _.forEach(data['columns'], function(c){
                        //console.log(c.field.slice(1));
                        if((that.id == -1 && c.field.slice(1)!='empty' && (that.cnames[parseInt(c.field.slice(1))] && that.cnames[parseInt(c.field.slice(1))].active!='1')) || (that.settings.columns && !that.settings.columns.includes(c.field.slice(1)) && c.field.slice(1) != 'empty')) hidden.push(c.field);
                    });
                that.body.find('.lds-roller').remove();
                that.body.find('#reports-table').show();
                that.table = new NestedTable(that.body.find('#reports-table'), data['data'], data['columns'], {corder: that.settings ? that.settings.corder : null , hidden: hidden, parent: that.body.closest('.modal'), id:that.id});
                that.initTableEvents();
                that.table.initColumnsPopup(that.right);
                
            }else {
                that.body.find('.lds-roller').remove();
                that.body.find('#reports-table').show();
                that.table.setData(data['data'], data['columns']);
                
            }
        }, 'JSON').fail(function(data){console.log(data);});
    },

    renderModal: function (columns = []){
        var that = this;
        var cf = this.settings.cf;
        $.post(this.base + this.options.ajaxTableURL + "/" + this.type,{cf:cf}, function (data) {
            if(_.sumBy(that.settings.grouping, function(o) { return parseInt(o.hg || 0); }) > 0) {
                that.initModalHG(data);
            }else {
                if(that.settings.kfilter) data['data'] = that.applyKfilter(data['data']);
                data['data'] = that.customReports.getNewPreData(data['data']);
                var hidden = [];
                _.forEach(columns, function(c){
                    if(that.settings.columns && !that.settings.columns.includes(c.data.slice(1)) && c.data.slice(1) != 'empty') hidden.push(c.data.slice(1));
                });
                var srt = _.remove(that.settings.grouping, function(g){
                    return g.type == 'sorting';
                });
                var filters = _.map(that.settings.grouping, function(g){
                    return 'c'+g.column;
                });
                var filters1 = _.map(that.settings.grouping, function(g){
                    if ('c' + g.column + '-k' in data['data'][0]) return 'c' + g.column + '-k'; else return 'c' + g.column;
                });
                var dfs = _.map(that.settings.grouping, function(g){
                    return g.datef || '0';
                });
                var sorting = [];
                _.forEach(srt, function(s){
                    sorting['c' + s.column] = s.order;
                });

                var filternames = $('.grouping-select option:selected').map(function () {
                    return $(this).text()
                }).get();
                var totals = [];
                _.forEach(that.settings.grouping, function(g){
                    var filter = 'c' + g.column;
                    var headers = g.header;
                    var footers = g.footer;
                    var temp = {key: filter, headers: headers, footers: footers};
                    totals[filter] = temp;
                });
                var afs = _.map(_.filter(cf, function (o) { return o.af == '1';}),function(o){return o.id;});
                var nested = that.applyModalFilters(data['data']);
                console.log(totals);
                nested = that.nest(nested, filters1, dfs, afs, totals, sorting, true);
                console.log(nested);

                var sort0 = [];
                if (sorting[filters[0]] != null) {
                    sort0 = [0, sorting[filters[0]]];
                }
                if(that.ctype && that.ctype != 0) {
                    that.customReports.initData(nested, columns);
                    columns = that.customReports.getNewColumns();
                    nested = that.customReports.getNewData();
                }
                that.table = new NestedTables(that.body.find('#reports-table'), nested, [], columns, filternames, sort0, hidden, {type: that.type, corder: that.corder, typeName: that.typeNames[that.type], groupcount: filters.length, horizontal: that.customReports.isHorizontal()});
                $(that.table).on('tdclick', function(){
                    data = that.table.tddata;
                    console.log(data);
                    var settings = that.settings;
                    var filter = {column: data.grouped_by.slice(1), dtype: 'text', condition: '0', fields: [{"value": data.grouped_key}]}
                    if(data.grouped_by.slice(2)[0] == '-') settings.kfilter = {column: data.grouped_by, value: data.grouped_key}; else {settings.filters.push(filter);settings.kfilter = null;}
                    if(settings.ctype != '3') {settings.ctype = 0;}
                    delete settings.columns;
                    JS.openDraggableModal('report', 'add', '-1', null, {settings: settings});
                });
            }

        }, 'JSON').fail(function (data) { console.log(data); });
    },

    applyKfilter: function(data) {
        var pcolumn = this.settings.kfilter.column.substring(0, this.settings.kfilter.column.indexOf('-'))+'-p';
        var kcolumn = this.settings.kfilter.column;
        var value = this.settings.kfilter.value;
        var result = [];
        console.log(data);
        var g = kfilter(data, [value], true);
        return result;
        function kfilter(data, values, first = false){
            var parents = [];
            for(var i in data) {
                if(first){
                    if(data[i][kcolumn] == values[0]){
                        parents = values;
                        result.push(data[i]);
                        //console.log(data[i]);
                    }
                    continue;
                }else if(values.includes(data[i][pcolumn])) {
                    result.push(data[i]);
                    parents.push(data[i][kcolumn])
                }
            }
            console.log(parents);
            if(parents.length > 0) return kfilter(data, parents);
        }
    },

    initModalHG: function(data){
        var that = this;
        var hgcolumnt = _.filter(this.settings.grouping, function(o){return o.hg == "1";})[0];
        var hgcolumn = hgcolumnt;
        var htotal = hgcolumnt.ht;
        var vtotal = hgcolumn.vt;
        var df = (this.cnames[hgcolumn.column].type == 'date') ? hgcolumn.datef : '0';
        hgcolumn = hgcolumn.column;
        var temp = this.right.find('.filtersel[value="'+hgcolumn+'"]').closest('.filter-wrapper');
        var range = {from: temp.find('input[name = "name1"]').val() || '', to:temp.find('input[name = "name2"]').val() || ''};

        if(!range.from || !range.to) {
            temp = _.filter(this.settings.filters, function (o) {
                return o.column == hgcolumn;
            })[0];
            if(temp) range = {from: temp.fields[0].name1 || '', to: temp.fields[0].name2 || ''};
            if (!range.from || !range.to) {
                range = that.getRange(data['data'], 'c' + hgcolumn);
            }
        }
        columns = that.generateColumns(range, df, htotal);

        var srt = _.remove(that.settings.grouping, function(g){
            return g.type == 'sorting';
        });
        var filters = _.map(that.settings.grouping, function(g){
            return 'c'+g.column;
        });
        var filters1 = _.map(that.settings.grouping, function(g){
            if ('c' + g.column + '-k' in data['data'][0]) return 'c' + g.column + '-k'; else return 'c' + g.column;
        });
        var dfs = _.map(that.settings.grouping, function(g){
            return g.datef || '0';
        });
        var sorting = [];
        _.forEach(srt, function(s){
            sorting['c' + s.column] = s.order;
        });

        var filternames = $('.grouping-select option:selected').map(function () {
            return $(this).text()
        }).get();
        var totals = [];
        var total = hgcolumnt.htotal;

        _.forEach(that.settings.grouping, function(g){
            var filter = 'c' + g.column;
            var headers = g.headers;
            var footers = g.footers;
            var temp = {key: filter, headers: headers, footers: footers};
            totals[filter] = temp;
        });

        var nested = that.applyModalFilters(data['data']);
        nested = that.hgnest(nested, filters1, dfs, totals, sorting, columns, 'c'+total, 'c'+hgcolumn);
        if(vtotal == '1') nested = that.getVTotals(nested, columns);
        console.log(nested);

        var sort0 = [];
        if (sorting[filters[0]] != null) {
            sort0 = [0, sorting[filters[0]]];
        }
        //console.log(that.typeNames[that.type]+' '+that.type);
        that.table = new NestedTables(that.body.find('#reports-table'), nested, [], columns, filternames, sort0, [], {horizontal: true, type: that.type, groupcount: filters.length, typeName: that.typeNames[that.type]});
    },

    applyModalFilters: function(data){
        var that = this;
        _.forEach(this.settings.filters, function(f){
            var column = f.column;
            var cond = f.condition;
            var values = _.map(f.fields, function(o){
                return {value: o.value, name1: o.name1, name2: o.name2}
            });
            if (values.length == 0) return data;
            data = that.filtersLogic(data, values, column, cond);
        });
        this.right.find('.filter-wrapper').each(function(){
            var column = $(this).find('.filtersel').val();
            var cond = $(this).find('.filtercond').val();
            var values = $(this).map(function(o){
                return {value: $(this).find('input[name="name"]').val(), name1: $(this).find('input[name="name1"]').val(), name2: $(this).find('input[name="name2"]').val()}
            }).get();
            if (values.length == 0) return data;
            data = that.filtersLogic(data, values, column, cond);
        });
        return data;
    },



    // --------------------------------------------------
    // ----------------- END MODAL SECTION --------------
    // --------------------------------------------------

    initBatchEvents: function (){
        var that = this;

        $.post( this.base + this.options.settingsURL + "/" + this.id + "/" +this.type, function(data) {
            if(that.id != '-1') that.settings = data; else that.settings = that.options.settings;
            that.cnames = data.cnames;
            that.corder = that.settings.corder || [];
            that.loadBatch();
        }, 'JSON').fail(function(data){console.log(data);});
    },

    loadBatch: function (first = false){
        var that = this;
        this.parseBatchSettings();
        this.horizontal = this.isHorizontal(this.settings);
        that.tablebody.css('text-align', 'center').css('height', '500px');
        that.tablebody.find('.lds-roller').remove();

        that.tablebody.append('<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>');
        if(this.ajaxcall && this.ajaxcall.readyState !== 4){
            this.ajaxcall.abort();
        }
        this.ajaxcall = $.post( this.base + this.options.ajaxTableURL + "/" + this.settings.type + "/" + this.id, {settings:JSON.stringify(this.settings)}, function(data) {
            if(data['data'].length == 0) return;
            if(that.options.bsettings.expanded == '1')
                for(var i in data['data']) {
                    if(data['data'][i].cempty) data['data'][i]._collapsed = data['data'][i].expanded_def;
                }
            else
                for(var i in data['data']) {
                    if(data['data'][i].cempty) data['data'][i]._collapsed = data['data'][i].collapsed_def;
                }
            if(!that.table) {
                var hidden = [];
                if(!that.horizontal)
                    _.forEach(data['columns'], function(c){
                        if((that.id == -1 && c.field.slice(1)!='empty' && (that.cnames[parseInt(c.field.slice(1))] && that.cnames[parseInt(c.field.slice(1))].active!='1')) || (that.settings.columns && !that.settings.columns.includes(c.field.slice(1)) && c.field.slice(1) != 'empty')) hidden.push(c.field);
                    });
                that.tablebody.find('.lds-roller').remove();
                that.tablebody.css('text-align', 'left').css('height', '500px');
                that.table = new NestedTable(that.tablebody, data['data'], data['columns'], {corder: that.settings ? that.settings.corder : null , hidden: hidden, parent: that.body.closest('.modal'), id:that.id, tr: that.options.bsettings.truncated});
                that.initTableEvents();
                that.table.initColumnsPopup(that.right);

            }else {
                that.tablebody.find('.lds-roller').remove();
                that.tablebody.css('text-align', 'left').css('height', '500px');
                that.table.setData(data['data'], data['columns']);

            }
        }, 'JSON').fail(function(data){});
    },

    parseBatchSettings: function() {
        var str = '';
        var text = null;
        if(this.options.bsettings) {
            if(this.options.bsettings.cr) {
                this.settings.newtype = this.options.bsettings.cr;
            }
        }
        for(var i in this.settings.ufilters) {
            str += '<h4 style= "color: #878A89; text-align:center">';
            str += this.cnames[this.settings.ufilters[i].column].name + ": ";
            if(this.bfilters && this.bfilters[this.settings.ufilters[i].column])
                this.settings.ufilters[i].fields[0].value = this.bfilters[this.settings.ufilters[i].column].value;
            if(this.bfilters && this.bfilters[this.settings.ufilters[i].column])
                this.settings.ufilters[i].fields[0].name1 = this.bfilters[this.settings.ufilters[i].column].name1;
            if(this.bfilters && this.bfilters[this.settings.ufilters[i].column])
                this.settings.ufilters[i].fields[0].name2 = this.bfilters[this.settings.ufilters[i].column].name2;

            text = this.bfilters[this.settings.ufilters[i].column].text;
            if(text)
                str += text;
            else
                str += this.settings.ufilters[i].fields[0].value;

            str += '</h4>';
        }
        for(var i in this.settings.params) {
            str += '<h4 style= "color: #878A89; text-align:center">';
            if(this.bfilters && this.bfilters[this.settings.params[i].key])
            this.settings.params[i].value = this.bfilters[this.settings.params[i].key].value;
            str += this.settings.params[i].name + ": ";
            text = this.bfilters[this.settings.params[i].key].text;
            if(text)
                str += text;
            else
                str += this.settings.params[i].value;
            str += '</h4>';
        }
        this.tablebody.prev().html(str);

    },

    refreshBatchReport: function(filters) {
        this.bfilters = filters;
        this.loadBatch();
    },

    destroy: function() {
        this.tablebody.parent().prev().remove();
        this.tablebody.parent().remove();
    },

    getPrintData() {
        if(this.table)
            return this.table.getPrintData();
        else return '';
    }
}
