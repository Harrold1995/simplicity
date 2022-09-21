var SlickRight = function (body, options = {}) {
    this.body = $(body);
    this.grid = null;
    this.dataView = null;
    this.options = options;
    this.hidden = options.hidden || [];
    this.parent = this.body.closest('.tabcontent');
    this.visible = true;
    this.init();
};
/** class methods **/
SlickRight.prototype = {
    init: function () {
        JS.right_pid = 0;
        JS.onlyopen = false;
        this.body.data('grid', this);
        this.initFormatters();
        this.loadSettings();
        this.initEvents();
    },

    print2: function(){
        var that = this;
        $.post(JS.baseUrl+'reports/print', {columns: this.grid.getColumns(), rows: JSON.stringify(this.dataView.getFilteredItems())}, function(data) {
                header = that.options.pheader;
                console.log(header);
                $("#print_header").hide();
                if(header)  $("#print_information").find('#headerInfo').html(header);
                $("#print_information").append(data);
                $("#print_information").addClass("horizontal");
                $("#page").addClass("print-section");
                //that.printOrientation(true);
                window.print();
                $("#page").removeClass("print-section");
                $("#print_information").empty();
                $("#print_information").removeClass("horizontal");
                $("#print_header").show();
          });
        //console.log(this.grid.getColumns());
    },

    pdf: function(){
        $.post(JS.baseUrl+'reports/pdf', {header: this.options.pheader, columns: this.grid.getColumns(), rows: JSON.stringify(this.dataView.getFilteredItems())}, function(url) {
            window.open(url);
        });
    },

    excel: function(){
        $.post(JS.baseUrl+'reports/print', {columns: this.grid.getColumns(), rows: JSON.stringify(this.dataView.getFilteredItems())}, function(data) {
                $("#print_information").append(data);
                    var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>'
                      , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                      , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
                   
                      table = $('.reportPrintTable')[0]
                      var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                      //window.location.href = uri + base64(format(template, ctx))
                        var a = document.createElement('a');
                        //getting data from our div that contains the HTML table
                        var data_type = 'data:application/vnd.ms-excel;base64';
                        a.href = data_type + ', ' + base64(format(template, ctx));
                        //setting the file name
                        a.download = 'exported_table_' + "transaction_listing" + '.xls';
                        //triggering the function
                        a.click();
                        a.remove();

                        
                                
                $("#print_information").empty();
                
          });
       
    },

    initEvents: function() {
        var that = this;
        this.body.on("remove", function () {
            that.destroy();
        })
        this.parent.parent().find('#exportocsv:not(.slickButton), #exportopdf:not(.slickButton), #printId:not(.slickButton)').off('click').addClass('slickButton');
        this.parent.parent().find('#exportocsv').on('click', function(){
            if(that.parent.hasClass('active'))
                that.excel('print_information' , 'tab1');
        })

        this.parent.parent().find('#exportopdf').on('click', function(){
            if(that.parent.hasClass('active'))
                that.pdf();
        })
        
        this.parent.parent().find('#printId').on('click', function(){
            if(that.parent.hasClass('active'))
                that.print2();
        })

        this.parent.parent().find('#slick-expanded').on('change', function(e){
            return;
            if($(this).is(':checked'))
                for(var i in that.data) {
                    if(!that.data[i].indent) that.data[i]._collapsed = false;
                }
            else
                for(var i in that.data) {
                    if(!that.data[i].indent) that.data[i]._collapsed = true;
                }
            that.dataView.setItems(that.data);
            that.collapsed = !$(this).is(':checked');
        });

        this.propFilter().then(
             function() {
                $(that.parent.parent().parent().find('#property_filter')[0]).change(function (e) {
                    if (e.originalEvent !== undefined) return;
                        JS.right_pid = parseInt($(this).attr('sel-value'));
                        that.calculate = true;
                        that.dataView.refresh();
                });
             }
        );
        
        //if(this.options.openonly)
        $(this.parent.parent().parent().find('#onlyopen')[0]).change(function (e) {


            JS.onlyopen = $(this).is(':checked');
            that.calculate = true;
            that.dataView.refresh();
        });
    },

    propFilter: async function() {
        console.log('changed');
        $(this.parent.parent().parent().find('#property_filter')[0]).editableSelect();

    },

    destroy: function() {
        //console.log('destroy');
        this.visible = false;
        if(this.post1 && this.post1.readyState !== 4){
            this.post1.abort();
        }
        if(this.post2 && this.post2.readyState !== 4){
            this.post2.abort();
        }
    },

    initGrid: function() {
        var that = this;
        var dataView;
        var grid;
        var options = {forceFitColumns: true, rowHeight: 38, explicitInitialization: true, showHeaderRow: true,headerRowHeight: 1};
        dataView = new Slick.Data.DataView({ inlineFilters: true });
        this.orderColumns();
        dataView.beginUpdate();
        dataView.setItems(this.calculateBalance(this.data));
        dataView.endUpdate();
        this.dataView = dataView;
        grid = new Slick.Grid(this.body, dataView, this.parseColumns(this.columns), options);
        plugin = new Slick.AutoTooltips({ enableForHeaderCells: true });
        grid.registerPlugin(plugin);
        grid.columnFilters = {};
        this.grid = grid;
        grid.onCellChange.subscribe(function (e, args) {
            //dataView.updateItem(args.item.id, args.item);
        });
        grid.onDblClick.subscribe(function (e, args) {
            var item = that.dataView.getItem(args.row);
            JS.openDraggableModal(item.dtype, 'edit', item.did, null, {lease: item.dlease,profile: item.dprofile});

        });
        grid.onClick.subscribe(function (e, args) {
            if ($(e.target).closest('.slick-row').find(".toggle").length > 0) {
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
        grid.setSortColumn('date', true);
        grid.onSort.subscribe(function (e, args) {
            let tree = that.listToTree(null);

            tree = that.sortTree(tree, args.sortCol.field, args.sortAsc, that);
            const newData = that.treeToList(tree);
            that.calculate = true;
            dataView.setItems(newData);
        });
        dataView.onRowCountChanged.subscribe(function (e, args) {
            grid.updateRowCount();
            plugin = new Slick.AutoTooltips({ enableForHeaderCells: true });
            grid.registerPlugin(plugin);
            grid.render();
        });
        dataView.onRowsChanged.subscribe(function (e, args) {
            grid.invalidateRows(args.rows);
            if(that.calculate) {
                that.calculate = false;
                that.calculateBalance();
            }
            plugin = new Slick.AutoTooltips({ enableForHeaderCells: true });
            grid.registerPlugin(plugin);
            grid.render();
        });
        grid.onColumnsReordered.subscribe(function(e, args){
            for(var i in args.impactedColumns) {
                that.corder[args.impactedColumns[i].field] = i;
            }
            that.saveSettings();
        });
        $(grid.getHeaderRow()).delegate(":input", "change keyup", function (e) {
            var columnId = $(this).data("columnId");
            if (columnId != null) {
                grid.columnFilters[columnId] = $.trim($(this).val());
                dataView.refresh();
            }
        });
        grid.onHeaderRowCellRendered.subscribe(function(e, args) {
            $(args.node).empty();
            $(args.node).closest('.slick-headerrow-columns').hide();
            $("<input type='text'>")
                .data("columnId", args.column.id)
                .val(grid.columnFilters[args.column.id])
                .appendTo(args.node);
        });
        grid.init();
        dataView.setFilterArgs({data:this.data, grid:this.grid});
        dataView.beginUpdate();
        dataView.setFilter(this.myFilter);
        dataView.endUpdate();
        this.appendFilterButton();
    },

    setData: function(data, columns, changed='grouping') {
        this.data = data;
        this.columns = columns;
        if(this.data[0].cempty) this.toggleColumn('cempty', false); else this.toggleColumn('cempty', true);
        this.grid.setColumns(this.parseColumns(columns));
        this.dataView.setFilterArgs({data:this.data, grid:this.grid});
        if(this.collapsed)
            this.parent.find('#slick-expanded').trigger('change');
        else if(this.id == '-1')
            this.dataView.setItems(this.expandAll(data));
        else
            this.dataView.setItems(data);
        this.grid.invalidate();
        plugin = new Slick.AutoTooltips({ enableForHeaderCells: true });
        this.grid.registerPlugin(plugin);
        this.grid.render();
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
            if(typeof columns[i].formatter !== 'function') columns[i].strformatter = columns[i].formatter || "";
            if(typeof columns[i].formatter === 'string') {
                columns[i].formatter = this.formatters[columns[i].formatter];
            }
            newcolumns.push(columns[i]);
        }
        newcolumns.sort(function(a, b){return that.corder[a.field] - that.corder[b.field]});
        return newcolumns;
    },

    calculateBalance: function(idata = null) {
        var data;
        if(idata) data = idata; else {
            data = this.dataView.getItems();
            visible = this.dataView.getFilteredItems();
        }

        this.lastbalance = _.sumBy(data, function(o) {return o.header && (idata || visible.includes(o)) ? parseFloat(o.balance1) : 0;});
        for(var i in data) {
            if(!data[i].header || !idata && !visible.includes(data[i])) continue;
            data[i].balance = this.lastbalance;
            this.lastbalance -= parseFloat(data[i].balance1);

        }
        if(!idata) this.dataView.setItems(data); else return data;
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
                if (value == null || value == undefined || dataContext === undefined) { return columnDef && columnDef.horizontal ? "$0.00" : ""; }
                value = '$'+number_format(value, 2);
                return value;
            },
            ColorUsdFormatter: function (row, cell, value, columnDef, dataContext) {
                if (value == null || value == undefined || dataContext === undefined) { return columnDef && columnDef.horizontal ? "$0.00" : ""; }
                color = value > 0 ? '#1c9759' : '#e17777'; 
                value = '<span style ="color:'+color+'; font-weight: 700">$'+number_format(value, 2)+'</span>';
                return value;
            },
            BalanceFormatter: function (row, cell, value, columnDef, dataContext) {
                if (value == null || value == undefined || dataContext === undefined) { return ""; }
                var idx = row;
                if(idx == 0) that.lastbalance = _.sumBy(that.dataView.getFilteredItems(), function(o) {return o.header === true ? parseFloat(o.amount) : 0;});

                if(dataContext.header !== true) return value;
                value = '$'+number_format(that.lastbalance, 2);
                that.lastbalance -= parseFloat(dataContext.amount);
                return value;
            },
            NumFormatter: function (row, cell, value, columnDef, dataContext) {
                if (value == null || value == undefined || dataContext === undefined) { return columnDef && columnDef.horizontal ? "0" : ""; }
                return value;
            },
            DateFormatter: function (row, cell, value, columnDef, dataContext) {
                if (value == null || value == undefined || dataContext === undefined) { return ""; }
                return moment(value).format('MM/DD/YYYY');
            },
            checkFormatter: function (row, cell, value, columnDef, dataContext) {
                if (value == null || value == undefined || dataContext === undefined || value == 0) { return ""; }
                if (value == 1) { return '<i  class="icon-check" style="color: #04a904; font-size:16px"></i>'; }
                return '<i  class="fas fa-spinner" style="color:#f9bd11; font-size:20px"></i>';
            },
            matchFormatter: function (row, cell, value, columnDef, dataContext) {
                //dataContext.name =  dataContext.name.replace(/[\/\(\)\']/g, "\\$&");
                dataContext.merchant_name =  dataContext.merchant_name.replace(/[\/\(\)\']/g, "*");
                dataContext.name =  dataContext.name.replace(/[\/\(\)\']/g, "*");
                if(dataContext.pending == 1){
                    let button =  `<span>Pending...</span>`;
                    return button;
                }
                if (value == null || value == undefined || dataContext === undefined || value == 0) { 
                    let button =  `<button data-transInfo = '${JSON.stringify(dataContext)}' class='addtransas'>Add As</button>
                    <i  title="Ignore Transaction" data-url="ignoreTrans" id="ignoreTrans" data-id = '${dataContext.transaction_id}' class="lnkDeleteRec fas fa-ban"></i>`;
                    return button;
                }
                if (value == 1) { 
                    let button =  `<span class='ignored-label'><i class="fas fa-ban"></i> Ignored</span>
                    <i  title="Remove Ignore" data-url="unIgnoreTrans" id="deleteIgnore" data-id = '${dataContext.transaction_id}' class="lnkDeleteRec icon-trash"></i>`;
                    return button;
                }
                return `<button data-type="banktrans" rec-id="${value}" class='recDetails'>Match #${value}</button>
                <i  title="Delete match"  id="deleteRec" data-id = '${dataContext.transaction_id}' class="lnkDeleteRec icon-trash" rec-id="${value}" data-type="auto"></i>`;
            }
        };
    },

    myFilter: function(item, data) {
        if (item.parent != null) {
            var parent = data.data[item.parent];
            while (parent) {
                if (parent._collapsed) {
                    return false;
                }
                parent = data.data[parent.parent];
            }
        }
        const searchString = JS.right_pid || '';
        for (var columnId in data.grid.columnFilters) {
            if (columnId !== undefined && data.grid.columnFilters[columnId] !== "") {
                var c = data.grid.getColumns()[data.grid.getColumnIndex(columnId)];
                if (!item[c.field] || !item[c.field].toLowerCase().includes(data.grid.columnFilters[columnId].toLowerCase())) {
                    return false;
                }
            }
        }
        if(JS.onlyopen && parseFloat(item.amounts) <= 0) return false;
        if(JS.right_pid == 0) return true;
        return item.pid == JS.right_pid;
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
        this.saveSettings();
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

    saveSettings() {
        $.post('api/saveSlickSettings/'+this.options.key, {hidden: this.hidden, corder: this.corder});
    },

    loadSettings() {
        var that = this;
        var filters = that.options.data;
        this.post1 = $.post(JS.baseUrl+that.options.dataUrl, {filters}, function (result) {
            that.data = result.data;
            that.columns = result.columns;
        }, "JSON");
        this.post2 = $.post(JS.baseUrl+'api/getSlickSettings/'+this.options.key, {}, function(data){
            if(data) {
                that.hidden = data.hidden || [];
                that.corder = data.corder || null;
            }
        }, 'JSON');
        $.when(this.post1, this.post2).done(function () {
            if(that.visible) {
                that.initGrid();
                that.initColumnsPopup(that.parent.parent().find('#editColumns'),that.options.tableName);
            }
        });
    },

    initColumnsPopup: function(body, tableName ="") {
        var that = this;
        var popup = $('<div class="cpopup c-left c-top" style = "overflow: auto;" data-manual=true id="columnspopup'+tableName+'"></div>');
        var popup2 = $('<div class="check-wrap" style = "overflow: auto; height:90%;" </div>');
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
        body.append(popup);
        popup.on('change', 'input', function(){
            that.toggleColumn($(this).val());
        });
        this.body.popup = popup;
        this.initPopupTrigger();
    },

    initPopupTrigger: function() {
        var that = this;
        $('.cpopup-trigger[data-target="#columnspopup"]').on('click', function(){
            if(that.body.length >1) console.log('more than 1 grid');
            if(!that.body.is(':visible')) return;
            var popup = that.body.popup;
            //popup.closest('section').toggleClass('toggle');
            //$(this).closest('.modal').find('.cpopup').hide(100);
            popup.css({
                'left': $(this).position().left - popup.width(),
                'top': $(this).position().top - popup.height()
            });
            if(popup.hasClass('c-top')) {
                popup.css({'top': $(this).position().top});
            }else if(popup.hasClass('c-middle')) {
                popup.css({'top': $(this).position().top - popup.height()/2});
            }else if(popup.hasClass('c-bottom')) {
                popup.css({'top': $(this).position().top - popup.height()});
            }
            if(popup.hasClass('c-left')) {
                popup.css({'left': $(this).position().left - popup.width()});
            }else if(popup.hasClass('c-right')) {
                popup.css({'left': $(this).position().left+$(this).width()});
            }
            popup.show(100, function(){if(popup.offset().top + popup.outerHeight() + 50 > $(window).height()) popup.addClass('flex-wrapper').height($(window).height() - popup.offset().top - 50)});

        });

    },

    listToTree: function(item) {
        const id = item ? item.id : null;

        const children = this.data
            .filter((item) => item.parent == id)
            .map((item) => this.listToTree(item))

        return {
            item,
            children
        }
    },

    treeToList: function(tree) {
        const item = tree.item;
        const children = tree.children;
        const childrenList = children
            .map(child => this.treeToList(child))
            .reduce((sum, val) => sum.concat(val), [])

        if (item === null) {
            return childrenList
        } else {
            return [item].concat(childrenList)
        }
    },

    sortTree: function(tree, field, isAsk, that) {
        return {
            item: tree.item,
            children: tree.children
                .sort((a, b) => {
                    let result;
                    let field1 = that.columns.find(x => x.field === field);
                    if(field1.strformatter == "UsdFormatter" || field1.strformatter == "NumFormatter"){
                        result = (parseFloat(a.item[field].replace(/[$,]+/g,"")) > parseFloat(b.item[field].replace(/[$,]+/g,""))) ? 1 : -1;
                    }else{result = (a.item[field] > b.item[field]) ? 1 : -1;}
                       

                    return isAsk ? result : result * -1;
                })
        }
    },

    appendFilterButton: function() {
        var that = this;
        var button = $('<a href="#" class="slickFilter"><i class="fas fa-search"></i></a>');
        this.body.append(button);
        button.click(function(){
            that.body.find('.slick-headerrow-columns').height(30).toggle();
            that.grid.resizeCanvas();
        });
    },

	resizeCanvas: function() {
		this.grid.resizeCanvas();
	}
}
