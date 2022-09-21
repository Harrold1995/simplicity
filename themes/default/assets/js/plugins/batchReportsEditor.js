var BatchReportsEditor = function(obj, options) {
    this.obj = $(obj);
    this.body = $(obj).find(options.body);
    this.header = $(obj).find(options.header);
    this.filtersbody = $(obj).find('.br-filters');
    this.base = options.base;
    this.options = options;
    this.id = options.id;
    this.fieldIndex = 1;
    this.rsettings = {};
    this.reportId = null;
    this.reportIdCounter = [];
    this.init();
};
/** class methods **/
BatchReportsEditor.prototype = {
	init : function() {
	    var that = this;
        this.initEvents();
        $.post(this.base+'batchreports/getBatchReportAjax/'+this.id, function(result) {
            that.batch = result;
            that.generateReportsList();
            that.loadList();
            that.renderLeft();
        }, 'JSON');
	},

    saveBatch() {
	    var data = {};
        data.reports = [];
	    for(i in this.rsettings) {
	        data.reports.push(this.rsettings[i]);
        }
        data.fields = $('.bre-field-wrapper:not(.bre-template)').map(function(){
            return {id: $(this).attr('fieldid'), name:$(this).find('input:first').val(), condition:$(this).find('select:last').val(), dtype:$(this).find('select:first').val(), source:$(this).find('input:last').val()};
        }).get();
        data.name = $('#batchname').val();
        $.post(this.base+'batchreports/saveBatch/'+this.id, {data:data}, function(data) {
            if(data.redirect != null) window.location = data.redirect;
            JS.showAlert(data.type, data.message);
        }, 'JSON');
    },

    renderLeft() {
        var body = $('.bre-fields');
        for(var i in this.batch.fields) {
            var f = this.batch.fields[i];
            body.append(this.getFieldBlockHtml(f));
        }
    },

    getFieldBlockHtml(data = null){
        var template = $('.bre-field-wrapper.bre-template').clone().removeClass('bre-template');
        var newhtml = template[0].outerHTML.replace(/{index}/g, this.fieldIndex++);
        template = $(newhtml);
        if(!data) return template;
        template.find('input:first').val(data.name);
        template.find('input:last').val(data.source);
        template.find('select:first option[value="'+data.dtype+'"]').prop('selected', true);
        template.find('select:last option[value="'+data.condition+'"]').prop('selected', true);
        return template;
    },


    loadList : function() {
	    var that = this;
        $.post(this.base+'batchreports/loadReportsList/', function(result) {
            that.initReportsSettings();
            that.renderBottomList(result);
            that.renderTopList(result);
        }, 'JSON');
    },

    initReportsSettings : function() {
        for(var i in this.batch.reports) {
            var r = this.batch.reports[i];
            var id = this.getReportIdCounter(r.id);
            this.rsettings[id] = r;
        }
        this.reportIdCounter = [];
    },

    renderTopList : function(data) {
	    var newdata = [], tempdata = [], that = this;
        for(var i in this.batch.reports) {
            var temp = JSON.parse(JSON.stringify(data.data.find(x => x.report_id === that.batch.reports[i].id+'')));
            temp.id = this.getReportIdCounter(temp.report_id);
            newdata.push(temp);
        }
        var that = this;
        slickOptions = {rowHeight: 32,headerHeight: 32, forceFitColumns: true};
        var dataView = new Slick.Data.DataView({ inlineFilters: true });
        dataView.beginUpdate();
        dataView.setItems(newdata);
        dataView.setFilter(this.slickFilter);
        dataView.endUpdate();
        var CheckFormatter = function (row, cell, value, columnDef, dataContext) {
            return '<i class="fas fa-trash-alt"></i>';
        };
        data.columns[0].formatter = CheckFormatter;

        grid = new Slick.Grid('.reports-slick-top', dataView, data.columns, slickOptions);
        grid.onCellChange.subscribe(function (e, args) {
            dataView.updateItem(args.item.id, args.item);
        });
        grid.onClick.subscribe(function (e, args) {
            var item = dataView.getItem(args.row);

            if(args.cell == 0) {
                dataView.deleteItem(item.id);
                delete that.rsettings[item.id];
                return;
            }
            that.loadReport(item.id, item.report_id);

        });
        dataView.onRowCountChanged.subscribe(function (e, args) {
            grid.updateRowCount();
            grid.render();
        });
        dataView.onRowsChanged.subscribe(function (e, args) {
            grid.invalidateRows(args.rows);
            grid.render();
        });
        this.topDataView = dataView;
        $(window).on('resize', function(){grid.resizeCanvas();})
    },

    renderBottomList : function(data) {
	    var that = this;
        slickOptions = {rowHeight: 32,headerHeight: 32, forceFitColumns: true};
        var dataView = new Slick.Data.DataView({ inlineFilters: true });
        dataView.beginUpdate();
        dataView.setItems(data.data);
        dataView.setFilter(this.slickFilter);
        dataView.endUpdate();
        var PlusFormatter = function (row, cell, value, columnDef, dataContext) {
            return '<i class="fas fa-plus"></i>';
        };
        data.columns[0].formatter = PlusFormatter;

        grid = new Slick.Grid('.reports-slick-bottom', dataView, data.columns, slickOptions);
        grid.onCellChange.subscribe(function (e, args) {
            dataView.updateItem(args.item.id, args.item);
        });
        grid.onClick.subscribe(function (e, args) {
            var item = dataView.getItem(args.row);
            that.moveReportTop(item);
        });
        dataView.onRowCountChanged.subscribe(function (e, args) {
            grid.updateRowCount();
            grid.render();
        });
        dataView.onRowsChanged.subscribe(function (e, args) {
            grid.invalidateRows(args.rows);
            grid.render();
        });
        $(window).on('resize', function(){grid.resizeCanvas();})
    },

    moveReportTop: function(r) {
	    var r = JSON.parse(JSON.stringify(r));
	    r.id = this.getReportIdCounter(r.report_id);
        this.topDataView.addItem(r);
        this.topDataView.refresh();
        this.rsettings[r.id] = this.genRSettings(r);
    },

    slickFilter : function (item, es) {
        return true;
    },

    getFieldsList : function() {
	    var str = '<option value="0">Property</option>';
	    $('.bre-field-wrapper:not(.bre-template)').each(function(){
	        str += '<option value="'+$(this).attr('fieldid')+'">'+$(this).find('input:first').val()+'</option>';
        });
	    return str;
    },

    loadReport: function(id, rid) {
	    var that = this;
        this.filtersbody.html('');
        //console.log(id);
        //console.log(this.rsettings);
        $.post(this.base+'batchreports/getEditRightHtmlAjax/'+rid, {data: this.rsettings[id]}, function(result) {
            that.filtersbody.html(result);
            that.initFilters();
            that.reportId = id;
        });
    },

    genRSettings : function(r) {
	    var filters = [];
	    var params = [];
        var temp;
        var settings = JSON.parse(r.settings);
        for(var i in settings.ufilters) {
            temp = {column: settings.ufilters[i].column, ismapped: 1, isexact: 1, mapped: 0, value: ""};
            filters.push(temp);
        }
        for(var i in settings.params) {
            temp = {key: settings.params[i].key, ismapped: 1, isexact: 1, mapped: 0, value: ""};
            params.push(temp);
        }
        return {id: r.report_id, type: parseInt(settings.type), name: r.name, expanded: 0, truncated: settings.truncate, filters: filters, params: params};

    },

    initEvents : function() {
	    var that = this;
	    $('#savebatch').click(function(){
            that.saveBatch();
        });
        $('.bre-addfield').click(function(){
            $('.bre-fields').append(that.getFieldBlockHtml());
        });
        this.obj.on('change', '.bre-fields .bre-field-wrapper input', function(){
            that.updateFieldSelects();
        });
        this.obj.on('click', '.bre-delete', function(){
            $(this).parent().remove();
            that.updateFieldSelects();
        });
        this.filtersbody.on('change', 'input, select', function() {
            switch($(this).attr('id')) {
                case 'name' :
                    that.rsettings[that.reportId].name = $(this).val() || 0;
                    break;
                case 'expanded' :
                    that.rsettings[that.reportId].expanded = $(this).prop('checked') ? 1 : 0;
                    break;
                case 'truncated' :
                    that.rsettings[that.reportId].truncated = $(this).prop('checked') ? 1 : 0;
                    break;
                case 'cr' :
                    that.rsettings[that.reportId].cr = $(this).prop('checked') ? $(this).val() : 0;
                    break;
            }
            var parent = $(this).closest('.bre-filter-wrapper');
            var lines = parent.find('.bre-fe');
            switch($(this).attr('id')) {
                case 'ismapped' :
                    lines.eq(2).toggle($(this).val() == '1');
                    lines.eq(1).find('#value').toggle($(this).val() == '0');
                    lines.eq(1).find('#mapped').toggle($(this).val() == '1');
                    lines.eq(2).find('select:first').trigger('change');
                    if($(this).val() == '0') lines.eq(3).toggle(false);
                    break;
                case 'isexact' :
                    lines.eq(3).toggle($(this).val() == '0');
                    break;
                case 'mapped' :
                    $(this).attr('default', $(this).val());
                    break;
            }
            var column;
            if(parent.is('[fid]')) {
                column = parent.find('#column').val();
                that.rsettings[that.reportId].filters.find(x => x.column === column)[$(this).attr('id')] = $(this).val();
            }
            else if(parent.is('[pid]')) {
                column = parent.find('#key').val();
                that.rsettings[that.reportId].params.find(x => x.key === column)[$(this).attr('id')] = $(this).val();
            }
        });

    },

    updateFieldSelects : function() {
	    this.initFilters();
    },

    initFilters : function() {
	    var that = this;
	    var mappedoptions = that.getFieldsList();
        this.filtersbody.find('.bre-filter-wrapper').each(function(){
            var mapped = $(this).find('select#mapped');
            mapped.html(mappedoptions);
            mapped.find('option[value="'+mapped.attr('default')+'"]').prop('selected', true);
        });
    },

    print: function() {
	    var that = this;
	    var printArray = [];
	    printArray.data = [];
        printArray.headers = [];
	    var ajaxcalls = [];
        if(this.batch.reports) {
            var properties = $('#br-properties input[type="checkbox"]:checked').map(function(){return $(this).val()}).get();
            var index = 0;
            for(var j in properties) {
                var p = properties[j];
                for (var i in that.batch.reports) {
                    var r = that.reports[p][i];
                    var a = this.body.find('a[pid="'+p+'"][rid="'+i+'"]');
                    printArray['headers'][index] = '<h4 style="color: #878A89;text-align: center;">' + a.text() + '</h4>' + a.next().find('div:first').html();
                    ajaxcalls.push(
                        $.ajax({
                            type: "POST",
                            url: JS.baseUrl+'reports/print',
                            index: index,
                            data: that.reports[p][i].getPrintData(),
                            success: function(data){printArray['data'][this.index] = data}
                        })
                    );
                    index++;
                }
            }
        }
        $.when.apply(undefined, ajaxcalls).done(function(results){
            $(window.printstyle).remove();
            window.printstyle = null;
            $("#print_header").hide();
            var str;
            for(var i in printArray.data) {
                str = '';
                str = '<div class="page-break" style="min-height:1000px;break-after:page;page-break-after: always;page-break-before: always;display:block;"><div class="print-header">'+printArray.headers[i]+"</div>";
                str += '<div class="print-information">'+printArray.data[i]+'</div></div>';
                $("#print_information").append(str);
            }

            $("#page").addClass("print-section");
            window.print();
            $("#page").removeClass("print-section");
            $("#print_information").empty();
        });
    },

    generateReportsList: function() {
	    this.reports = {};
	    for(var i in this.batch.reports) {
	        var r = this.batch.reports[i];
	        this.reports[r.id] = r;
        }
    },

    getReportIdCounter: function(id) {
	    if(this.reportIdCounter[id])
	        this.reportIdCounter[id]++;
	    else
	        this.reportIdCounter[id] = 1;
        return id+'.'+this.reportIdCounter[id];
    }

};

/*var loader = operative({
            load: function(callback) {
                xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        callback(this.responseText);
                    }
                };
                xhttp.open("GET", "http://simplicity/batchreports/test", true);
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                xhttp.send();
            }
        });
        var time = Date.now();
        for(var i = 0; i < 10; i++) {
            loader.load(function(data){console.log('done '+(Date.now()-time)+' sec')});
        }
        for(var i = 0; i < 10; i++) {
            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    console.log('done '+(Date.now()-time)+' sec')
                }
            };
            xhttp.open("GET", "http://simplicity/test.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhttp.send();
        }*/