var BatchReports = function(obj, options) {
    this.obj = $(obj);
    this.body = $(obj).find(options.body);
    this.header = $(obj).find(options.header);
    this.filtersbody = $(obj).find('.br-filters');
    this.base = options.base;
    this.options = options;
    this.view = 'list';
    this.properties = {};
    this.init();
};
/** class methods **/
BatchReports.prototype = {
	init : function() {
	    var that = this;
        $.each($('#br-properties input[type="checkbox"]'), function (k, v) {that.properties[$(v).val()] =$(v).closest('label').text().trim()});
        this.initEvents();
	    this.loadList();
	},

    loadList : function() {
	    var that = this;
        $.post(this.base+'batchreports/loadList/', function(result) {
            that.renderList(result);
        }, 'JSON');
    },

    renderList : function(data) {
	    var that = this;
        slickOptions = {rowHeight: 32,headerHeight: 32, forceFitColumns: true};
        var dataView = new Slick.Data.DataView({ inlineFilters: true });
        dataView.getItemMetadata = function(index)
        {
            var item = dataView.getItem(index);
            if(item.id == 'add') {
                return { cssClasses: 'fes-add' };
            }
            else if(item.id == 'setup') {
                return { cssClasses: 'fes-setup' };
            } else {
                return { cssClasses: 'common' };
            }
        };
        dataView.beginUpdate();
        dataView.setItems(data.data);
        dataView.setFilter(this.slickFilter);
        dataView.endUpdate();
        var CheckFormatter = function (row, cell, value, columnDef, dataContext) {
            if(value) return '<label class="check active"></label>'; else return '<label class="check"></label>';
        };
        data.columns[0].formatter = CheckFormatter;

        grid = new Slick.Grid('#batches-slick', dataView, data.columns, slickOptions);
        grid.onCellChange.subscribe(function (e, args) {
            dataView.updateItem(args.item.id, args.item);
        });
        grid.onClick.subscribe(function (e, args) {
            var item = dataView.getItem(args.row);
            var old = item.check;
            var items = dataView.getItems();
            for(var i in items) {
                items[i].check = false;
                dataView.updateItem(items[i].id, items[i]);
            }
            item.check = !old;
            that.batchPreload(item.check ? item.id : null);
            dataView.updateItem(item.id, item);
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

    slickFilter : function (item, es) {
        return true;
    },

    batchPreload: function(id) {
	    if(id) {
            this.batchId = id;
            this.loadBatch(id);
            $('#runbatch').show();
        } else {
            $('#runbatch').hide();
        }
    },

    initEvents : function() {
	    var that = this;
	    $('#runbatch').click(function(){
            that.renderBatch();
        });
        this.body.on('click', 'a.reporttablelink', function(){
            if($(this).hasClass('expanded')) {
                $(this).removeClass('expanded');
                $(this).find('span').removeClass('expanded');
                $(this).next().slideUp(500);
            } else {
                $(this).addClass('expanded');
                $(this).find('span').addClass('expanded');
                $(this).next().slideDown(500);
            }
        });
        this.obj.on('click', 'a.br-print', function(){
            that.print();
        });
        this.filtersbody.on('change', 'input', function() {
            if(that.view == 'list') return;
            var fid = parseInt($(this).attr('fid'));
            if(that.batch.reports) {
                var properties = $('#br-properties input[type="checkbox"]:checked').map(function(){return $(this).val()}).get();
                for(var j in properties) {
                    var p = properties[j];
                    for (var i in that.batch.reports) {
                        var r = that.batch.reports[i];
                        for (var j in r.filters) {
                            var f = r.filters[j];
                            if (f.mapped == fid) that.reports[p][i].refreshBatchReport(that.generateReportFilters(r, p));
                        }
                        for (var j in r.params) {
                            var f = r.params[j];
                            if (f.mapped == fid) that.reports[p][i].refreshBatchReport(that.generateReportFilters(r, p));
                        }
                    }
                }
            }
        });
        $('#br-properties input').on('change', function() {
            if(that.view == 'list') return;
            var fid = 0;
            var add = $(this).is(':checked');
            var p = $(this).val();
            if(add) {
                that.addToLeftColumn(p);
                for (var i in that.batch.reports) {
                    var r = that.batch.reports[i];
                    that.batch.reports[i].tableid = i;
                    that.body.append('<a href="#" rid="'+i+'" pid="'+p+'" class="reporttablelink expanded"><span class="slick-group-toggle expanded"></span>' + r.name + '</a>');
                    that.body.append('<div><div></div><div class="reporttable" id="reporttable' + p + '-' + i + '"></div></div>');
                    if(!that.reports[p]) that.reports[p] = [];
                    that.reports[p][i] = new Reports('', '#reporttable' + p + '-' + i, '', "#reportfilters",
                        {base: that.base, batch: true, bsettings: r, bfilters: that.generateReportFilters(r, p), id: r.id, type: r.type}
                    );
                }
            }else{
                that.addToLeftColumn(p, true);
                for (var i in that.batch.reports) {
                    var r = that.batch.reports[i];
                    that.reports[p][i].destroy();
                    that.reports[p][i] = null;
                }
            }
        });
        $('.br-propslide').on('click', function(){
            if($('.br-outer>header').hasClass('collapsed')) {
                that.toggleColumn(true, false);
            } else {
                that.toggleColumn(true, true);
            }
        });
        $('.br-filterslide').on('click', function(){
            if($('.br-outer>footer').hasClass('collapsed')) {
                that.toggleColumn(false, false);
            } else {
                that.toggleColumn(false, true);
            }
        });

        this.obj.on('click', '.lefttrigger', function(){
            var pid = $(this).attr('pid');
            var rid = $(this).attr('rid') || 0;
            var body = that.body.closest('#br-main').find('.column-right');
            //console.log($('a[pid="'+pid+'"][rid="'+rid+'"]').offset().top);
            body.animate({
                scrollTop: body.scrollTop() + $('a[pid="'+pid+'"][rid="'+rid+'"]').offset().top - body.offset().top
            }, 500);
        });
    },

    toggleColumn : function(header = true, collapse = true) {
        var target = 0;
	    if(header) {
            if(collapse) {
                target = -280;
                $('.br-outer>header').addClass('collapsed');
            } else {
                $('.br-outer>header').removeClass('collapsed');
            }
            $('.br-outer>header').animate({
                'margin-left': target + 'px'
            }, {
                complete: function( now, fx ) {
                    $(window).trigger('resize');
                }
            });
        } else {
            if(collapse) {
                target = -280;
                $('.br-outer>footer').addClass('collapsed');
            } else {
                $('.br-outer>footer').removeClass('collapsed');
            }
            $('.br-outer>footer').animate({
                'margin-right': target + 'px'
            }, {
                complete: function( now, fx ) {
                    $(window).trigger('resize');
                }
            });
        }
    },

    loadBatch : function(id = null) {
	    var that = this;

	    if(!id) {
            this.filtersbody.html('');
        }
        $.post(this.base+'batchreports/getBatchReportAjax/'+id, function(result) {
            that.batch = result;
            that.renderFields();
        }, 'JSON');
    },

    initFilters() {
        this.filtersbody.find('input').datepicker( "destroy" );
        this.filtersbody.find('input.dinput').attr('autocomplete', 'new-password').datepicker();
        this.replaceInputs();
    },

    renderFields : function() {
	    var that = this;
        this.filtersbody.html('');
        $.post(this.base+'batchreports/loadRight/', {id: this.batch.id}, function(result) {
            that.filtersbody.html(result);
            that.initFilters();
        });
    },

    renderBatch : function() {
	    var that = this;
        this.toggleColumn(true, true);
        this.toggleColumn(false, true);
	    this.view = 'batch';
	    this.body.html('');
	    this.fields = {};
	    for(var i in this.batch.fields) {
            var f = this.batch.fields[i];
            this.fields[f.id] = f;
        }
	    this.reports = [];
	    this.header.html('<h2>'+this.batch.name+'</h2>');
	    this.header.append('<div class="br-buttons"><a href="#" class="br-print"><i id="printIdRR" class="icon-print"></i></a></div>');
	    var properties = $('#br-properties input[type="checkbox"]:checked').map(function(){return $(this).val()}).get();
        $.post(this.base+'batchreports/loadBodyWrapper/', function(result) {
            that.body.html(result);
            that.body = that.body.find('.column-right');
            for(var j in properties) {
                var p = properties[j];
                that.addToLeftColumn(p);
                for (var i in that.batch.reports) {
                    var r = that.batch.reports[i];
                    that.batch.reports[i].tableid = i;
                    that.body.append('<a href="#" rid="'+i+'" pid="'+p+'" class="reporttablelink expanded"><span class="slick-group-toggle expanded"></span>' + r.name + '</a>');
                    that.body.append('<div><div></div><div class="reporttable" id="reporttable' + p + '-' + i + '"></div></div>');
                    if(!that.reports[p]) that.reports[p] = [];
                    that.reports[p][i] = new Reports('', '#reporttable' + p + '-' + i, '', "#reportfilters",
                        {base: that.base, batch: true, bsettings: r, bfilters: that.generateReportFilters(r, p), id: r.id, type: r.type}
                    );
                }
            }
        });

    },

    addToLeftColumn: function(p, remove = false) {
        var body = this.body.prev().find('.column-left-inner');
        var div;
        if(!remove && body.find('.left-property-wrapper[pid="'+p+'"]').length == 0) {
            div = $('<div class="left-property-wrapper" pid="'+p+'"></div>');
            div.append('<div pid="'+p+'" class="lefttrigger main">'+this.properties[p]+'</div>');
            for (var i in this.batch.reports) {
                var r = this.batch.reports[i];
                div.append('<div pid="'+p+'" rid="'+i+'" class="lefttrigger">'+r.name+'</div>');

            }
            body.append(div);
        }
        if(remove && body.find('.left-property-wrapper[pid="'+p+'"]').length) {
            body.find('.left-property-wrapper[pid="'+p+'"]').remove();
        }

    },

    generateReportFilters : function(r, p) {
	    var result = {};
        for(var i in r.filters) {
            var f = r.filters[i];
            if(f.ismapped == 1) {
                if(f.mapped == 0)
                    result[f.column] = {value: p || null};
                else
                    result[f.column] = {value: this.filtersbody.find('input[fid="'+f.mapped+'"]').attr('sel-value') || this.filtersbody.find('input[fid="'+f.mapped+'"]').val() || null, name1: this.filtersbody.find('input#name1-'+f.mapped).val() || null, name2: this.filtersbody.find('input#name2-'+f.mapped).val() || null};
            }else{
                result[f.column] = {value: result[f.value]};
            }
            if(this.fields[f.mapped] && this.fields[f.mapped].condition == 3 && f.map_key !='value')
                result[f.column].value = this.filtersbody.find('input#'+f.map_key+'-'+f.mapped).val() || null;

            if(f.isexact == 0) {
                result[f.column].value = moment(result[f.column].value, 'MM/DD/YYYY').add(f.map_shift_value, f.map_shift_type).format('MM/DD/YYYY');
                result[f.column].name1 = moment(result[f.column].name1, 'MM/DD/YYYY').add(f.map_shift_value, f.map_shift_type).format('MM/DD/YYYY');
                result[f.column].name2 = moment(result[f.column].name2, 'MM/DD/YYYY').add(f.map_shift_value, f.map_shift_type).format('MM/DD/YYYY');

            }

            if(this.fields[f.mapped] && this.fields[f.mapped].condition == 3) {
                if(f.map_key != 'value') {
                    result[f.column].value = this.filtersbody.find('input#'+f.map_key+'-'+f.mapped).val() || null;
                    result[f.column].text = result[f.column].value;
                } else
                    result[f.column].text = (result[f.column].name1 || 'undefined') + ' - ' + (result[f.column].name2 || undefined);
            } else if(this.fields[f.mapped] && this.fields[f.mapped].condition == 6) {
                result[f.column].text = this.filtersbody.find('input[fid="'+f.mapped+'"]').closest('.field-wrapper').find('.items span').map(function(){return $(this).text()}).get().join(', ');
            } else if(this.fields[f.mapped] && this.fields[f.mapped].source)
                result[f.column].text = this.filtersbody.find('input[fid="'+f.mapped+'"]').val();
            else
                result[f.column].text = result[f.column].value;
            if(f.mapped == 0)
                result[f.column].text = $('#br-properties input#p'+p).closest('label').text();
            if(!result[f.column].text) result[f.column].text = 'undefined';
        }
        for(var i in r.params) {
            var f = r.params[i];
            if(f.ismapped == 1) {
                if(f.mapped == 0)
                    result[f.key] = {value: p || null};
                else
                    result[f.key] = {value: this.filtersbody.find('input[fid="'+f.mapped+'"]').attr('sel-value') || this.filtersbody.find('input[fid="'+f.mapped+'"]').val() || null, name1: this.filtersbody.find('input#name1-'+f.mapped).val() || null, name2: this.filtersbody.find('input#name2-'+f.mapped).val() || null};
            }else{
                result[f.key] = {value: result[f.value]};
            }
            if(this.fields[f.mapped] && this.fields[f.mapped].condition == 3 && f.map_key !='value')
                result[f.key].value = this.filtersbody.find('input#'+f.map_key+'-'+f.mapped).val() || null;

            if(f.isexact == 0) {
                result[f.key].value = moment(result[f.key].value, 'MM/DD/YYYY').add(f.map_shift_value, f.map_shift_type).format('MM/DD/YYYY');
                result[f.key].name1 = moment(result[f.key].name1, 'MM/DD/YYYY').add(f.map_shift_value, f.map_shift_type).format('MM/DD/YYYY');
                result[f.key].name2 = moment(result[f.key].name2, 'MM/DD/YYYY').add(f.map_shift_value, f.map_shift_type).format('MM/DD/YYYY');

            }
            if(this.fields[f.mapped] && this.fields[f.mapped].condition == 3) {
                if(f.map_key !='value') {
                    result[f.key].text = result[f.key].value;
                } else
                    result[f.key].text = (result[f.key].name1 || 'undefined') + ' - ' + (result[f.key].name2 || undefined);
            } else if(this.fields[f.mapped] && this.fields[f.mapped].condition == 6) {
                result[f.key].text = this.filtersbody.find('input[fid="'+f.mapped+'"]').closest('.field-wrapper').find('.items span').map(function(){return $(this).text()}).get().join(', ');
            } else if(this.fields[f.mapped] && this.fields[f.mapped].source)
                result[f.key].text = this.filtersbody.find('input[fid="'+f.mapped+'"]').val();
            else
                result[f.key].text = result[f.key].value;
            if(f.mapped == 0)
                result[f.key].text = $('#br-properties input#p'+p).closest('label').text();
            if(!result[f.key].text) result[f.key].text = 'undefined';
        }
        //console.log(result);
        return result;
    },

    replaceInputs: function(){
        var that = this;
        this.filtersbody.find('input[source]').each(function(){
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
            var cond = parent.find('input.fieldcond').val();
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

    generateMultipleSelect: function(source, parent, value = null) {
        var def = value || parent.find('input:first').val();

        var select = '<input class="multiple-select" fid="'+parent.find('input:first').attr('fid')+'" name="name" value="'+def+'">';
        var opts = JS.sdata[source];
        parent.html(select);

        parent.find('.multiple-select').selectize({
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

    generateSelect: function(source, parent, value = null) {
        var select = '<select class="editable-select" fid="'+(parent.find('input:first').attr('fid'))+'" name="'+(parent.find('input:first').attr('name') || 'name')+'" key="'+(parent.find('input:first').attr('key'))+'">';
        var def = parent.find('input:first').val();
        select = select+'</select>';
        parent.html(select);
        parent.find('.editable-select').fastSelect({type: source, default: value || def});
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
                    var t = a.text();
                    printArray['headers'][index] = '<h4 style="color: #878A89;text-align: center;">' + t.toUpperCase() + '</h4>' + a.next().find('div:first').html();
                    ajaxcalls.push(
                        $.ajax({
                            type: "POST",
                            url: JS.baseUrl+'reports/print',
                            index: index,
                            data: that.reports[p][i].getPrintData(),
                            success: function(data){
                                printArray['data'][this.index] = data
                            }
                            
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
                if (printOrientation == 'landscape'){
                    str = '<div class="page-break" style="min-width:1000px;break-after:page;page-break-after: always;page-break-before: always;display:block;-webkit-transform: rotate(-90deg); transform: rotate(-90deg);">';
                } else {
                    str = '<div class="page-break" style="min-height:1000px;break-after:page;page-break-after: always;page-break-before: always;display:block;">';
                }
                
                str += '<div class="print-information">'+printArray.data[i]+'</div></div>';
                console.log(printArray.data[i]);
                var strn = new DOMParser().parseFromString(str, "text/html");
                $(strn).find('#headerInfo').html(printArray.headers[i]);
                //$("#print_information").find('#report_footer').html(footer);
                
                $("#print_information").append($(strn.body).html());
            }

            $("#page").addClass("print-section");
            window.print();
            //$("#page").removeClass("print-section");
            //$("#print_information").empty();
        });
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