+(function ($) {
    FormGridSlick = function (body, options) {
        this.options = options;
        this.showFooterRow = options.showFooterRow ? options.showFooterRow :false;
        this.body = $(body).find('.formGridSlickTable');
        this.header = $(body).find('.header, header');
        this.parent = $(body);
        this.slickFilters = {payBills: this.slickFilterPayBills, utilities: this.slickFilterUtilities, capital: this.slickFilter};
        this.cfilters = {};
        this.datefilter = false;
        this.searchstring = '';
        this.modal = this.body.closest('.modal');
        this.init();
        this.initEvents();
    };
    FormGridSlick.prototype = {
        init: function () {
            this.body.append('<div class="loadercontainer"><div></div><div></div><div></div><div></div><div></div></div>');
            var that = this;
            this.body.data('fsg', this);
            this.initFormatters();
            oldpost = $(this.body).data('post');
            if( oldpost && oldpost.readyState !== 4){
                oldpost.abort();
            }
            post = $.post(this.options.dataUrl, {}, function (result){
                $(this.body).find('.lds-roller').remove();
                that.data = result.data;
                that.columns = result.columns;
                that.initSlick();
            }, 'JSON');
            this.body.data('post', post);
        },

        initSlick: function () {
            var that = this;
            var dataView;
            var grid;
            var options = {forceFitColumns: true, rowHeight: 48, enableAsyncPostRender: true, createFooterRow: true, showFooterRow: this.showFooterRow, footerRowHeight: 32, enableCellNavigation: true};
            dataView = new Slick.Data.DataView({inlineFilters: true});
            dataView.setFilterArgs(this);
            dataView.beginUpdate();
            if (this.options.type in this.slickFilters){
                dataView.setFilter(this.slickFilters[this.options.type]);
            }
            dataView.setItems(this.data);
            dataView.endUpdate();
            this.dataView = dataView;
            grid = new Slick.Grid(this.body, dataView, this.parseColumns(this.columns), options);
            this.grid = grid;
            that.updateAllTotals();
            that.initFooter();
            grid.onCellChange.subscribe(function (e, args) {
                dataView.updateItem(args.item.id, args.item);
            });
            dataView.onRowCountChanged.subscribe(function (e, args) {
                grid.updateRowCount();
                that.updateAllTotals();
                grid.render();
            });
            dataView.onRowsChanged.subscribe(function (e, args) {             
                grid.invalidateRows(args.rows);
                that.updateAllTotals();
                grid.render();
            });
            grid.onClick.subscribe(function (e, args) {
                that.processCellClick(args);
            });
            grid.onDblClick.subscribe(function (e, args) {
                var item = dataView.getItem(args.row);
                JS.openDraggableModal(item.type, 'edit', item.th_id, null);
            });
            grid.onSort.subscribe(function (e, args) {
                let tree = that.listToTree(null);
                //console.log(args);
                tree = that.sortTree(tree, args.sortCol.field, args.sortAsc, args.sortCol.datatype || null);
                const newData = that.treeToList(tree);
                that.calculate = true;
                dataView.setItems(newData);
            });
            
            grid.onMouseEnter.subscribe(function(e, args) {
                var cell = args.grid.getCellFromEvent(e);
                var item = dataView.getItem(cell.row);

                if(item.error){
                    viewCell = e.target;
                    viewRow = viewCell.closest('.slick-row');
                    console.log(item.error);
                    console.log(viewCell.closest('.slick-row'));
                    if($(viewRow).find('.error-msg').length == 0){
                        $(viewRow).append('<span class="error-msg"> '+item.error +'</span>');
                    } 
                };
                
            });
        },

        slickFilter: function(item, that) {
            return !that.searchstring || Object.values(item).join('||').toLowerCase().indexOf(that.searchstring) > -1;
        },

        processCellClick: function(args) {
            switch(this.options.type) {
                case 'payBills':
                    if(this.columns[args.cell].id == 'check') {
                        var item = this.dataView.getItem(args.row);
                        item.check = item.check ? 0 : 1;
                        if(item.check) {
                            item['amount'] = item['open_balance'];
                        } else {
                            item['amount'] = 0;
                        }
                        this.updateAllTotals();
                        this.grid.invalidateRow(args.row);
                        this.grid.render();
                        if(item.check) {
                            cell = this.grid.getCellNode(args.row, 9);
                            $(cell).find('input').focus().select();
                        } 
                       
                    }
                    break;
                case 'utilities':
                    if(this.columns[args.cell].id == 'plus') {
                        var item = this.dataView.getItem(args.row);
                        if (item) {
                            if (!item._collapsed) {
                                item._collapsed = true;
                            } else {
                                item._collapsed = false;
                            }
                        }
                        if(!item.childrenCount || item.childrenCount == 0) {
                            this.addRowAfterItem(item, item);
                        } else {
                            this.dataView.updateItem(item.id, item);
                        }
                        break;
                    }
                    if(this.columns[args.cell].id == 'check') {
                        var item = this.dataView.getItem(args.row);
                        if(item.parent !== undefined) {
                            var parent = this.dataView.getItemByIdx(item.parent);
                            console.log(item);
                            this.dataView.deleteItem(item.id);
                            parent.childrenCount--;
                            if(parent.childrenCount == 0) parent._collapsed = true;
                            this.dataView.updateItem(parent.id, parent);

                        } else {
                            item.check = item.check ? 0 : 1;
                        }
                        this.grid.invalidateRow(args.row);
                        this.grid.render();

                    } 
                    break;

                    default:
                        if(this.columns[args.cell].id == 'check') {
                            var item = this.dataView.getItem(args.row);
                            item.check = item.check ? 0 : 1;
                            this.updateAllTotals();
                            this.grid.invalidateRow(args.row);
                            this.grid.render();
                           
                        }
            }
        },

        addRowAfterItem: function(item, parent) {
            var data = this.dataView.getItems();
            var currRowNum = this.dataView.getIdxById(item.id);
            parent._collapsed = false;
            parent.childrenCount !== undefined ? parent.childrenCount++ : parent.childrenCount = 1;
            parent.childrenIndex !== undefined ? parent.childrenIndex++ : parent.childrenIndex = 1;
            parent.check = 1;
            this.dataView.updateItem(parent.id, parent);
            var newrow = jQuery.extend({}, data[currRowNum]);
            newrow.parent = this.dataView.getIdxById(parent.id);
            newrow.mainid = parent.id;
            newrow.subid = parent.childrenIndex;
            newrow.id = parent.id+'_'+newrow.subid;
            newrow.check = 0;
            var position = (currRowNum+1);
            data.splice(position,0,newrow);
            this.dataView.setItems(data);
            this.dataView.refresh();
            this.grid.invalidateRows();
            this.grid.render();
        },

        parseColumns: function(columns) {
            for(var i in columns){
                if(typeof columns[i].formatter === 'string') {
                    columns[i].formatter = this.formatters[columns[i].formatter];
                }
                if(typeof columns[i].asyncPostRender === 'string') {
                    columns[i].asyncPostRender = this.renderers[columns[i].asyncPostRender];
                }
            }
            return columns;
        },

        slickFilterPayBills: function (item, grid) {
            for(var i in grid.cfilters) {
                if(!grid.cfilters[i].all) {
                    if(!grid.cfilters[i].data.includes(item[i])) return false;
                }
            }
            if(grid.datefilter && !moment(grid.datefilter).isAfter(item.transaction_date)) return false;
            return true;
        },

        slickFilterUtilities: function (item, grid) {
            if (item.parent != null) {
                var parent = grid.data[item.parent];
                while (parent) {
                    if (parent._collapsed) {
                        return false;
                    }
                    parent = grid.data[parent.parent];
                }
            }
            for(var i in grid.cfilters) {
                if(!grid.cfilters[i].all) {
                    if(!grid.cfilters[i].data.includes(item[i])) return false;
                }
            }
            if(grid.datefilter && !moment(grid.datefilter).isAfter(item.old_last_paid_date)) return false;
            return true;
        },

        initFormatters: function() {
            var that = this;
            this.formatters = {
                UsdFormatter: function (row, cell, value, columnDef, dataContext) {
                    if (value == null || value == undefined || dataContext === undefined) { return ""; }
                    value = '$'+number_format(value, 2);
                    return value;
                },
                DateFormatter: function (row, cell, value, columnDef, dataContext) {
                    if (value == null || value == undefined || dataContext === undefined) { return ""; }
                    return moment(value).format('MM/DD/YYYY');
                },
                CheckFormatter: function (row, cell, value, columnDef, dataContext) {
                    switch(that.options.type) {
                        case 'payBills':
                            if (value == null || value == undefined || dataContext === undefined || value == 0) { return ""; }
                            if (value == 1) { return '<i  class="icon-check" style="color: #04a904; font-size:16px"></i>'; }
                            return '<i  class="fas fa-spinner" style="color:#f9bd11; font-size:20px"></i>';
                            break;
                        case 'utilities':
                        case 'capital':
                            if(columnDef.id == 'check' && dataContext.parent !== undefined)
                                return '<i class="icon-x"></i>';
                            return '<label for="'+columnDef.id+'_'+dataContext.id+'" class="custom-checkbox">'+
                                        '<input type="checkbox" id="'+columnDef.id+'_'+dataContext.id+'" value="1" class="commonCheckbox" filter="profile_id" '+(value == '1' ? 'checked' : '')+'>'+
                                        '<div class="input"></div>'+
                                    '</label>';
                            break;
                    }
                },
                PlusFormatter: function (row, cell, value, columnDef, dataContext) {
                    if (dataContext.parent !== undefined) return "";
                    if (dataContext._collapsed || dataContext._collapsed === undefined) return '+'; else return '-';
                },
                ButtonFormatter: function (row, cell, value, columnDef, dataContext) {
                    return `<button data-id = '${row}' data-action ='${columnDef.field}'>${columnDef.field}</button>`;
                },
                AddAsButtonFormatter: function (row, cell, value, columnDef, dataContext) {
                    dataContext.num =  dataContext.num.replace(/[\/\(\)\']/g, "*");
                    return `<button data-id = '${row}' data-transInfo = '${JSON.stringify(dataContext)}' class='addtransas' >${columnDef.field}</button>`;
                },
                
                SelectFormatter: function (row, cell, value, columnDef, dataContext) {
                    return '<span class="select"><input type="text" class="es-input" value="'+dataContext[columnDef['namefield']]+'"></span>';
                },

                InputFormatter: function (row, cell, value, columnDef, dataContext) {
                    if(columnDef.format == 'date' && !value) {
                        value = moment(new Date()).format("MM/DD/YYYY");
                        dataContext[columnDef.field] = value;
                    }
                    return (columnDef.format && columnDef.format == 'usd' ? '$' : '')+'<input type="text" class="'+(columnDef.format && columnDef.format == 'usd' ? 'decimal' : '')+' slickinput" value="'+(columnDef.format && columnDef.format == 'usd' ? number_format(value) : (value || ''))+'">';
                }
            };
            this.renderers = {
                renderSelect: function (cellNode, row, dataContext, colDef) {
                    var def = dataContext[colDef['field']];
                    $(cellNode).html('<span class="select"><select class="editableSelect"></span>');
                    $(cellNode).find('.editableSelect').fastSelect({type: colDef.source, default: def});
                    $(cellNode).addClass('slickSelectCell').find('input.editableSelect').change(function(e){
                        dataContext[colDef['field']] = $(this).attr('sel-value');
                        dataContext[colDef['namefield']] = $(this).val();
                    });
                },
                renderCheckbox: function (cellNode, row, dataContext, colDef) {
                    var checkbox = $(cellNode).find('input[type="checkbox"]');
                    checkbox.change(function(e){
                        dataContext[colDef['field']] = checkbox.prop('checked') ? 1 : 0;
                    });
                },
                renderFormula: function (cellNode, row, dataContext, colDef) {
                    var item = that.dataView.getItemById(dataContext.id);
                    item['amount'] = eval(colDef['formula']);
                    //console.log(dataContext);
                    $(cellNode).html(number_format(eval(colDef['formula'])));
                },
                renderInput: function (cellNode, row, dataContext, colDef) {
                    if(colDef.format == 'date')
                        $(cellNode).find('input').datepicker();
                    $(cellNode).find('input.decimal').calculadora({decimals: 2, useCommaAsDecimalMark: false});
                    $(cellNode).find('input').off('input').off('focusin').off('focusout').on('input',function(e){

                        if(!that.validateInput($(this), dataContext, colDef)) {
                            switch(colDef.format) {
                                case 'usd': case 'num':
                                    $(this).val(number_format(dataContext[colDef['field']]));
                                    break;
                                case 'text': case 'date':

                                    break;
                            }

                            return true;
                        }
                        switch(colDef.format) {
                            case 'usd': case 'num':
                                dataContext[colDef['field']] = Number($(this).val().replace(',', ''));
                                break;
                            case 'text': case 'date':
                                dataContext[colDef['field']] = $(this).val();
                                break;
                        }
                        if(colDef.total) that.updateTotal($(cellNode).index());

                    }).on('focusin', function(){
                        
                        $(this).val(dataContext[colDef['field']] != 0 ? dataContext[colDef['field']] : '');
                        if(that.options.type == 'utilities') {
                            var next = that.dataView.getItemByIdx(that.dataView.getIdxById(dataContext.id)+1);
                            if(dataContext.parent !== undefined && dataContext.parent != next.parent) that.addRowAfterItem(dataContext, that.dataView.getItemByIdx(dataContext.parent));
                        }
                        $(this).select();

                    }).on('keyup', function(e){
                        
                        if(that.options.type == 'capital') {
                            var item = that.dataView.getItemById(dataContext.id);
                            
                            item['amount'] = newAmt;
                            cell = that.grid.getCellFromEvent(e);
                            row = cell.row;

                            var columnIdx = that.grid.getColumns().length;
                            while (columnIdx--) {
                                var column = that.grid.getColumns()[columnIdx];
                                if(column.formula) {
                                    var newAmt = eval(column['formula']);
                                    var cellnode = that.grid.getCellNode(row,columnIdx);
                                    item[column.name] = newAmt;
                                    $(cellnode).html("$" + number_format(newAmt,2));
                                }
                            }

                        }

                    }).on('focusout', function(){
                        switch(colDef.format) {
                            case 'usd': case 'num':
                                $(this).val(number_format(dataContext[colDef['field']]));
                                break;
                            case 'text': case 'date':

                                break;
                        }
                        if(colDef.instantUpdate){
                            var type = colDef.field;
                            var id = dataContext.id;
                            var value = $(this).val();
                            $.post(JS.baseUrl + that.options.iuUrl,
                                {
                                    'type': type,
                                    'value': value,
                                    'id': id
                                }, function (data) {
                                    console.log('success' + data);
                                }
                            );
                        }
                        if($(this).val() && !dataContext.check && colDef.format == 'usd') {
                            //console.log('ff');
                            var item = that.dataView.getItemById(dataContext.id);
                            item['check'] = true;
                            that.dataView.updateItem(dataContext.id, item);
                        }

                    });
                },
            };
        },

        validateInput: function(input, item, column) {
            var value = Number(input.val().replace(',', ''));
            switch (this.options.type) {
                case 'payBills':
                    if(column.id != 'amount') return true;
                    if(item.open_balance > 0 && value > item.open_balance){
                        JS.showAlert('danger', 'Payment amount can not exceed the open balance!')
                        return false;
                    }
                    if(item.open_balance < 0 &&  value != 0   ){
                        if(value > 0  || Math.abs(value) > Math.abs(item.open_balance)){
                            JS.showAlert('danger', 'Credit can not be greater then open balance!')
                            return false;
                        }
                    }
                    break;
            }
            return true;
        },

        initEvents: function () {
            var that = this;
            $(window).resize(function(){that.grid.resizeCanvas()});
            this.header.on('change', 'input[name="dueDate"]', function(e){
                if($(this).val() == '0')
                    that.datefilter = false;
                else
                    that.datefilter = that.header.find('#pay_bill_due_date').val();
                if(that.dataView) that.dataView.refresh();
            });
            this.header.on('change', '#pay_bill_due_date', function(e){
                if(that.datefilter !== false) {
                    that.datefilter = $(this).val();
                    if(that.dataView) that.dataView.refresh();
                }
            });
            this.header.on('change', '#paybill_accounts', function(){
                var id = $(this).attr('sel-value');
                var text = $(this).val();
                if(that.dataView) {
                    var data = that.dataView.getItems();
                    for (var i in data) {
                        data[i].account_id = id;
                        data[i].bank_name = text;
                    }
                    that.dataView.setItems(data);
                    that.grid.invalidate();
                    that.grid.render();
                }
            });
            that.modal.on('input', that.options.search, function() {
                that.searchstring = $(this).val().toLowerCase();
                if(that.dataView) that.dataView.refresh();
            });
            this.body.on('change', '#pay_bill_select_all', function(e) {
                var check = $(this).is(':checked');
                if(that.dataView) {
                    var data = that.dataView.getItems();
                    for (var i in data) {
                        data[i].check = check ? 1 : 0;
                        if(data[i].check) {
                            data[i]['amount'] = data[i]['open_balance'];
                        } else {
                            data[i]['amount'] = 0;
                        }
                    }
                    that.updateAllTotals();
                    that.dataView.setItems(data);
                    that.grid.invalidate();
                    that.grid.render();
                }
            });

            this.body.on('click', 'button', function(e) {
                e.preventDefault();
                var action = $(this).attr('data-action');
                
                

                switch(action){
                    case 'quickAdd' :
                        var rowid = $(this).attr('data-id');
                        var row = that.dataView.getItem(rowid);
                        if(!row.account_id || !row.property_id){
                            alert('You need to choose a property and account to enter this transaction!');
                            return;
                        }


                        $.post(JS.baseUrl + 'transactions/quickAdd',
                            {
                                'property_id': row.property_id,
                                'account_id': row.account_id,
                                'amount': row.amount,
                                'bankTransAmt': row.amount < 0 ? 0-row.amount: row.amount,
                                'bank_account': row.bank_account,
                                'date': row.date,
                                'bank_trans_id': row.id,
                                'profile_id': row.profile_id,
                                'description': row.num
                            }, function (data) {
                                selector = $(that.grid.getCellNode(rowid,1)).closest('.slick-row');
                                $(selector).stop()
                                .css("background-color","#4ec1fd63")
                                .hide(500, function() {
                                    that.dataView.deleteItem(row.id);
                                });
                                    
                                
                            }
                        );
                        break;
                    case 'addAs' :
                        break;
                }
            });

            if(this.options.type == 'payBills') {
                jQuery.expr[':'].icontains = function(a, i, m) {
                    return jQuery(a).text().toUpperCase()
                        .indexOf(m[3].toUpperCase()) >= 0;
                };
                this.header.on('input', '.paybillfilter', function(e) {
                    var body = $(this).closest('div');
                    if($(this).val()) {
                        body.find('li').hide();
                        body.find('label:icontains("'+$(this).val()+'")').each(function(){$(this).parent().show()});
                    } else{
                        body.find('li').show();
                    }
                })
            }
        },

        checkboxChanged: function(event) {
            var filter = event.filter;
            var obj = {data: event.data, all: event.all};
            if(filter) {
                this.cfilters[filter] = obj;
            }
            if(this.dataView) {
                this.updateAllTotals();
                this.dataView.refresh();
            }
        },

        updateAllTotals: function() {
            var columnIdx = this.grid.getColumns().length;
            while (columnIdx--) {
                this.updateTotal(columnIdx);
            }
        },

        updateTotal: function(cell) {
            var column = this.grid.getColumns()[cell];
            var columnId = column.id;
            if(!column.total) return;
            var total = 0;
            var data = this.dataView.getFilteredItems();
            var i = data.length;
            while (i--) {
                if(data[i].check) total += Number(data[i][columnId]) || 0;
            }
            var columnElement = this.grid.getFooterRowColumn(columnId);
            $(columnElement).html("$" + number_format(total,2));
        },

        initFooter: function() {
            var column;
            switch(this.options.type) {
                case 'payBills':
                    column = this.grid.getFooterRowColumn(0);
                    $(column).html('<label for="printPayBillChecks" class="custom-checkbox"><input type="hidden" name="printPayBillChecks" value="0"><input type="checkbox" name="printPayBillChecks" value="1" class="hidden" id="printPayBillChecks"><div class="input"></div></label>');
                    column = this.grid.getFooterRowColumn(1);
                    $(column).addClass('nooverflow').html('Print Checks');

                    break;
            }
        },

        getFormData: function() {
            var formData = new FormData(this.body.closest('form')[0]);
            var tempdata = [];
            var html = '';
            var parent = null;
            if(this.dataView) {
                var data = this.dataView.getFilteredItems();
                var pairs = [];
                for (var i in data) {
                    parent = null;
                    if(this.options.type == 'utilities' && data[i].parent !== undefined)
                        parent = this.dataView.getItemByIdx(data[i].parent);
                    if(data[i].check || (parent && parent.check)) {
                        pairs = this.options.template(data[i]);
                        for (var pair of pairs) {
                            formData.append(pair[0], pair[1]);
                        }
                    }
                }
            }
            return formData;
        },

        listToTree: function(item) {
            const id = item ? item.id : null;

            const children = this.dataView.getItems()
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

        sortTree: function(tree, field, isAsk, type = null) {
            return {
                item: tree.item,
                children: tree.children
                    .sort((a, b) => {
                        let result;
                        switch(type) {
                            case 'num': result = (parseFloat(a.item[field]) > parseFloat(b.item[field])) ? 1 : -1; break;
                            case 'date': result = (moment(a.item[field]) > moment(b.item[field])) ? 1 : -1; break;
                            default: result = (a.item[field] > b.item[field]) ? 1 : -1; break;
                        }
                        return isAsk ? result : result * -1;
                    })
            }
        },

        findAllChildren: function(parent) {
            const id = this.dataView.getIdxById(parent.id);
            var that = this;
            var data = this.dataView.getItems();
            return data
                .filter(item => item.parent === id)   // find all direct children
                .map((item) => [item].concat(that.findAllChildren(item))) // for every child, find it's children
                .reduce((sum, val) => sum.concat(val), [])  // make one big array with all children
        },
    };

    fgsPlugin = function (options) {
        return new FormGridSlick(this, options);
    }
    $.fn.formGridSlick = fgsPlugin;
    $.fn.formGridSlick.Constructor = FormGridSlick;
})(jQuery);

