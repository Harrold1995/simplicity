var NestedTable = function (body, data, columns, options = {}) {
    this.body = body;
    this.data = data;
    this.columns = columns;
    this.grid = null;
    this.dataView = null;
    this.options = options;
    this.corder = options.corder || null;
    this.hidden = options.hidden || [];
    this.id = options.id;
    this.tr = options.tr;
    this.parent = this.options.parent || $(this.body).parent().parent();
    this.collapsed = false;
    this.init();
};
/** class methods **/
NestedTable.prototype = {
    init: function () {
        $(this.body).data('nestesobj', this);
        this.initFormatters();
        this.initGrid();
        this.initEvents();        
        //this.print2();
    },

    getPrintData: function(l = false){
        return {columns: this.grid.getColumns(), rows: JSON.stringify(this.dataView.getFilteredItems()), tr: this.tr};
    },

    print2: function(l = false){
        var that = this;
        $.post(JS.baseUrl+'reports/print', {columns: this.grid.getColumns(), rows: JSON.stringify(this.dataView.getFilteredItems()), tr: that.parent.find('#slick-truncate:checked').val()}, function(data) {
                accountName = $(that.body).closest('.modal').find("#report-header").html();
                footer = $(that.body).closest('.modal').find("footer").html();
                $("#print_header").hide();
                $("#print_information").append(data);
                $("#print_information").find('#headerInfo').html(accountName);
                $("#print_information").find('#report_footer').html(footer);
                $("#page").addClass("print-section");
                that.printOrientation(l);
                window.print();
                $("#page").removeClass("print-section");
                $("#print_information").empty();
                $("#print_header").show();
          });
        //console.log(this.dataView.getFilteredItems());
    },

    printOrientation: function(l = true) {
        if(!l || l=='0') {
            $(window.printstyle).remove();
            window.printstyle = null;
            return;
        }
        var css = '@page { size: landscape; }',
            head = document.head || document.getElementsByTagName('head')[0],
            style = document.createElement('style');

        style.type = 'text/css';
        style.media = 'print';

        if (style.styleSheet){
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }
        head.appendChild(style);
        window.printstyle = style;
    },

    pdf: function(){
        $.post(JS.baseUrl+'reports/pdf', {header: this.parent.find('#report-header').html(),columns: this.grid.getColumns(), rows: JSON.stringify(this.dataView.getFilteredItems())}, function(url) {
            window.open(url);
        });
    },
    pdf2: function(){
        $.post(JS.baseUrl+'reports/pdf', {header: this.parent.find('#report-header').html(),columns: this.grid.getColumns(), rows: JSON.stringify(this.dataView.getFilteredItems())}, function(url) {
            //window.open(url);
            console.log('pdf 2');
            console.log(url);
        });
    },

    excel: function(){
        var rtitle = this.parent.find('#report-header').find('h2')[0].innerHTML;
        var filters = this.parent.find('#report-header').find('h4');
        var i;
        var text;
        var titleText;
        for (i = 0; i < filters.length; i++) { 
            text += `,\\0022&11\\000A&\\0022-\\,Italic\\0022`+filters[i].innerHTML+`\\` ;
            titleText += `_`+filters[i].innerHTML ;
            console.log(text);
          }
        //rtitle = rtitle.replace("&", "and");
        console.log(filters);
        $.post(JS.baseUrl+'reports/excel', {columns: this.grid.getColumns(), rows: JSON.stringify(this.dataView.getFilteredItems())}, function(data) {
            //console.log('excel');
            $("#print_information").append(data);

                    var uri = 'data:application/vnd.ms-excel;base64,'
                      , 
                      template = `<html xmlns:o="urn:schemas-microsoft-com:office:office" 
                      xmlns:x="urn:schemas-microsoft-com:office:excel" 
                      xmlns="http://www.w3.org/TR/REC-html40"><head>
                        <style>


                        @page
                        {
                        mso-page-orientation portrait;
                        margin:0.5in 0.551in 1.1437in 0in;
                        mso-footer-margin:0;
                        margin:1.36in .75in 1.0in .75in;
                        mso-header-margin:.5in;
                        mso-header-data:"&C&\\0022Times New Roman\\,Bold\\0022&18`+rtitle+`&\\0022-\\`+text+`&R&D";
                        mso-footer-data:"Simpli-city.com";
                        mso-horizontal-page-align:center;

                        }

                        </style>

                        <!--[if gte mso 9]><xml>
                        <x:ExcelWorkbook>
                        <x:ExcelWorksheets>
                        <x:ExcelWorksheet>
                            <x:Name>{worksheet}</x:Name>
                            <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>
                        </x:ExcelWorksheet>
                        </x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>`
                      , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                      , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
                   
                      table = $('.reportPrintTable')[0]
                      var ctx = {worksheet: name || rtitle, table: table.innerHTML}
                      //window.location.href = uri + base64(format(template, ctx))
                      //creating a temporary HTML link element (they support setting file names)
                        var a = document.createElement('a');
                        //getting data from our div that contains the HTML table
                        var data_type = 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64';


                        function s2ab(s) {
                            var buf = new ArrayBuffer(s.length);
                            var view = new Uint8Array(buf);
                            for (var i=0; i!=s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
                            return buf;
                          }

                          var blob = new Blob([s2ab(atob(base64(format(template, ctx))))], {
                            type: ''
                        });
                        a.href = URL.createObjectURL(blob);
                       
                       
                        //setting the file name
                        a.download = rtitle + titleText + '.xls';
                        //triggering the function
                        a.click();
                  
                $("#print_information").empty();
                
          });
       
    },

    initEvents: function() {
        var that = this;
        this.parent.find('.icon-excel').off('click').on('click', function(){
            that.excel('print_information' , 'tab1');
        })
        this.parent.find('.report-pdf').off('click').on('click', function(){
            that.pdf();
        })
        this.parent.find('.report-pdf2').off('click').on('click', function(){
            that.pdf2();
        })
        this.parent.find('.print').off('click').on('click', function(){
            that.print2($(this).closest('.modal').find('input[name="print-mode"]:checked').val() || false);
        })
        this.parent.on('change', '#slick-expanded', function(e){
            if($(this).is(':checked') && e.originalEvent)
                for(var i in that.data) {
                    if(that.data[i].cempty) that.data[i]._collapsed = that.data[i].expanded_def;
                }
            else
                for(var i in that.data) {
                    if(that.data[i].cempty) that.data[i]._collapsed = that.data[i].collapsed_def;
                }
            that.dataView.setItems(that.data);
            that.collapsed = !$(this).is(':checked');
        });
    },

    initGrid: function() {
        var that = this;
        var dataView;
        var grid;
        var options = {autosizeColsMode: 'FitViewportToCols', minWidth:4, syncColumnCellResize:true, rowHeight: 25, rowClasses: function(item){if(item.footer === true) return 'slick-footer';}};
        if(this.data.length == 0) return;
        if(this.data[0].cempty) this.toggleColumn('cempty', false); else this.toggleColumn('cempty', true);
        dataView = new Slick.Data.DataView({ inlineFilters: true });
        dataView.setFilterArgs(this.data);
        dataView.beginUpdate();
        if(this.id == '-1')
            dataView.setItems(this.expandAll(this.data));
        else
            dataView.setItems(this.data);
        dataView.setFilter(this.myFilter);
        dataView.endUpdate();
        this.dataView = dataView;
        this.orderColumns();
        grid = new Slick.Grid(this.body, dataView, this.parseColumns(this.columns), options);
        this.grid = grid;
        this.adjustHeaders(grid);
        grid.onCellChange.subscribe(function (e, args) {
            dataView.updateItem(args.item.id, args.item);
        });
        grid.onDblClick.subscribe(function (e, args) {
            var item = dataView.getItem(args.row);
            var column = grid.getColumns()[args.cell];
            that.tddata = {row: item, column: column};
            if ($(e.target).hasClass("toggle") || $(e.target).find(".toggle").length > 0) {
                $(that).trigger('nameclick');
            } else {
                $(that).trigger('tdclick');
            }
        });
        grid.onClick.subscribe(function (e, args) {
            if ($(e.target).hasClass("toggle") || $(e.target).find(".toggle").length > 0) {
                var item = dataView.getItem(args.row);
                if (item) {
                    if (!item._collapsed) {
                        item._collapsed = true;
                    } else {
                        item._collapsed = false;
                    }
                    window.setTimeout(() => dataView.updateItem(item.id, item), 200);
                }
            }
        });
        grid.onColumnsResized.subscribe(function () {that.adjustHeaders(grid);});
        dataView.onRowCountChanged.subscribe(function (e, args) {
            grid.updateRowCount();
            plugin = new Slick.AutoTooltips({ enableForHeaderCells: true });
            grid.registerPlugin(plugin);
            grid.render();
        });
        dataView.onRowsChanged.subscribe(function (e, args) {
            grid.invalidateRows(args.rows);
            plugin = new Slick.AutoTooltips({ enableForHeaderCells: true });
            grid.registerPlugin(plugin);
            grid.render();
        });
        grid.onColumnsReordered.subscribe(function(e, args){
            for(var i in args.impactedColumns) {
                that.corder[args.impactedColumns[i].field] = i;
            }
            that.adjustHeaders(grid);
            //console.log(args.impactedColumns);
        });
        $(window).on('resize', function(){
            grid.resizeCanvas();          
        })
        this.parent.find('.reportMax').off('click').on('click', function(e){
            $(this).closest('.modal').toggleClass('expanded');
            grid.resizeCanvas();
            that.adjustHeaders(grid);
        })
    },

    resizeCanvas: function() {
        this.grid.resizeCanvas();
        that.adjustHeaders(grid);
    },

    setData: function(data, columns, changed='grouping') {
        this.data = data;
        this.columns = columns;
        if(this.data[0] && this.data[0].cempty) this.toggleColumn('cempty', false); else this.toggleColumn('cempty', true);
        this.grid.setColumns(this.parseColumns(columns));
        this.dataView.setFilterArgs(this.data);
        if(this.collapsed)
            this.parent.find('#slick-expanded').trigger('change');
        else if(this.id == '-1')
            this.dataView.setItems(this.expandAll(data));
        else
            this.dataView.setItems(data);
        this.grid.invalidate();
        this.grid.render();
    },

    adjustHeaders: function(grid){
        var offset = (($(grid.getViewportNode()).width() - $(grid.getActiveCanvasNode()).width())/2) > 0 ? (($(grid.getViewportNode()).width() - $(grid.getActiveCanvasNode()).width())/2) : 0;
        $("."+ grid.getUID()+' .slick-header-column').each(function() {$(this).css('left', 1000 + offset);});
        $("."+ grid.getUID()+' .slick-header-columns').each(function() {$(this).width($(this).width() + offset);});

    },

    expandAll: function(data){
        for(var i in data) {
            if(data[i].cempty) {
                data[i]._collapsed = false;
                data[i].expanded_def = false;
            }
        }
        return data;
    },

    parseColumns: function(columns) {
        var newcolumns = [];
        var that = this;
        for(var i in columns){
            if(this.hidden.includes(columns[i].field)) continue;
            if(typeof columns[i].formatter !== 'function') columns[i].strformatter = columns[i].formatter;
            if(typeof columns[i].formatter === 'string') {
                columns[i].formatter = this.formatters[columns[i].formatter];
            }
            if(columns[i].link_type !== null){

            }
            newcolumns.push(columns[i]);
        }
        newcolumns.sort(function(a, b){return that.corder[a.field] - that.corder[b.field]});
        return newcolumns;
    },

    initFormatters: function() {
        var that = this;
        this.formatters = {
            NameFormatter: function (row, cell, value, columnDef, dataContext) {
                if (value == null || value == undefined || dataContext === undefined) { return ""; }
                value = (value+"").replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
                var spacer = "<span style='display:inline-block;height:1px;width:" + (15 * dataContext["indent"]) + "px'></span>";
                var idx = that.dataView.getIdxById(dataContext.id);
                if (that.data[idx + 1] && that.data[idx + 1].indent > that.data[idx].indent) {
                    if (dataContext._collapsed) {
                        return spacer + " <span class='toggle expand'></span>&nbsp;" + value;
                    } else {
                        return spacer + " <span class='toggle collapse'></span>&nbsp;" + value;
                    }
                } else {
                    return spacer + " <span class='toggle'></span>&nbsp;" + value;
                }
            },
            UsdFormatter: function (row, cell, value, columnDef, dataContext) {
                if (columnDef && columnDef.horizontal && dataContext.noshow) { return ""; }
                if (value == null || value == undefined || dataContext === undefined) { return columnDef && columnDef.horizontal && !dataContext['no0'] ? "$0.00" : ""; }
                value = '$'+number_format(value, 2);
                //console.log(dataContext);
                return value;
            },
            NumFormatter: function (row, cell, value, columnDef, dataContext) {
                if (columnDef && columnDef.horizontal && dataContext.noshow) { return ""; }
                if (value == null || value == undefined || dataContext === undefined) { return columnDef && columnDef.horizontal ? "0" : ""; }
                return value;
            },
            DateFormatter: function (row, cell, value, columnDef, dataContext) {
                if (value == null || value == undefined || dataContext === undefined) { return ""; }
                return moment(value).format('MM/DD/YYYY');
            },
            LinkFormatter: function (row, cell, value, columnDef, dataContext) {
                if (value == null || value == undefined || dataContext === undefined) { return ""; }
                field = columnDef.field+`-k`;
                return `<a class="${columnDef.link_type}" data-id="${dataContext[field]}" data-key="${columnDef.key_column}" data-pid="" data-type="${columnDef.source}" data-lid=""'>${value}</a>`; 
            }
        };
    },

    myFilter: function(item, data) {
        if (item.parent != null) {
            var parent = data[item.parent];
            while (parent) {
                if (parent._collapsed) {
                    return false;
                }
                parent = data[parent.parent];
            }
        }
        return true;
    },

    toggleColumn: function(val, def = null){
        var index = this.hidden.indexOf(val);
        if(def != null)
            if (def === false) {
                if(index > -1) this.hidden.splice(index, 1);
            } else {
                if(index == -1)this.hidden.push(val)
            }
        else
            if (index > -1) {
                this.hidden.splice(index, 1);
            } else {
                this.hidden.push(val)
            }

        if(def == null)this.grid.setColumns(this.parseColumns(this.columns));

    },

    orderColumns: function(){
        if(this.corder == null){
            this.corder = {};
            for(var i in this.columns){
                this.corder[this.columns[i].field] = i;
            }
        }
    },

    initColumnsPopup: function(body) {
        var that = this;
        var popup = $('<div class="cpopup c-left c-top" id="columnspopup"></div>');
        var popup2 = $('<div class="check-wrap" style = "overflow: auto; height:90%" </div>');
        for(var i in this.columns) {
            if(this.columns[i].field == 'cempty') continue;
            popup2.append('<div class="custom-control custom-checkbox form-group mb-0">' +
                '           <input type="checkbox" value="' + this.columns[i].field + '" ' + (!this.hidden.includes(this.columns[i].field) ? 'checked' : '') + ' class="custom-control-input" id="columns' + this.columns[i].field +'">' +
                '           <label class="custom-control-label checkbox-left text-left" for="columns' + this.columns[i].field + '">' + this.columns[i].name + '</label>' +
                '</div>'
            );
        }
        popup.append(popup2);
        popup.append('<div class="row justify-content-center mb-0 mt-1"><a href="#">Done</a></div>');
        body.prev().append(popup);
        popup.on('change', 'input', function(){
            that.toggleColumn($(this).val());
        });
        this.popup = popup;
    }
}