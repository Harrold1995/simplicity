RecSlick = function (body, options) {
    this.options = options;
    this.body = $(body);
    this.parent = this.body.parent();
    this.modal = this.body.closest('.modal');
    this.numberobj = this.modal.find(this.options.number);
    this.totalobj = this.modal.find(this.options.total);
    this.searchstring = '';
    this.checkall = false;
    this.init();
};
RecSlick.prototype = {
    init: function () {
        var that = this;
        this.initEvents();
        $.post(this.options.dataUrl, {}, function (result){
            that.data = result.data;
            that.columns = result.columns;
            that.initSlick();
        }, 'JSON');
    },

    initEvents: function() {
        var that = this;
        this.modal.on('input', this.options.search, function() {
            that.searchstring = $(this).val().toLowerCase();
            if(that.dataView) that.dataView.refresh();
        });
        this.body.on('click', '#checkall', function() {
            that.checkall = !that.checkall;
            $(this).find('i').toggle();
            if(!that.dataView) return;
            var data = that.dataView.getItems();
            for(var i in data) {
                data[i].check = that.checkall;
            }
            that.parent.find('input').val(that.checkall ? '1' : '0');
            that.dataView.setItems(data);
            that.grid.invalidate();
            that.updateTotals();
        })
    },

    initSlick: function () {
        var that = this;
        var dataView;
        var grid;
        var options = {forceFitColumns: true, rowHeight: 32};
        for(var i in this.data) {
            if(this.data[i]['rec_id']) this.data[i]['check'] = true;
            this.generateField(this.data[i]);
        }
        dataView = new Slick.Data.DataView({inlineFilters: true});
        dataView.setFilterArgs(this);
        dataView.beginUpdate();
        dataView.setFilter(this.slickFilter);
        dataView.setItems(this.data);
        dataView.endUpdate();
        this.dataView = dataView;
        grid = new Slick.Grid(this.body, dataView, this.parseColumns(this.columns), options);
        this.grid = grid;
        grid.onCellChange.subscribe(function (e, args) {
            dataView.updateItem(args.item.id, args.item);
        });
        dataView.onRowCountChanged.subscribe(function (e, args) {
            grid.updateRowCount();
            grid.render();
        });
        dataView.onRowsChanged.subscribe(function (e, args) {
            grid.invalidateRows(args.rows);
            grid.render();
        });
        grid.onClick.subscribe(function (e, args) {
            var item = dataView.getItem(args.row);
            item.check = !item.check;
            that.parent.find('input.t'+item.id).val(item.check ? '1' : '0');
            window.setTimeout(() => dataView.updateItem(item.id, item), 200);
            that.updateTotals();
        });
        grid.onDblClick.subscribe(function (e, args) {
            var item = dataView.getItem(args.row);
            JS.openDraggableModal(item.type_id, 'edit', item.th_id, null);
        });
        grid.onSort.subscribe(function (e, args) {
            let tree = that.listToTree(null);
            //console.log(args);
            tree = that.sortTree(tree, args.sortCol.field, args.sortAsc, args.sortCol.datatype || null);
            const newData = that.treeToList(tree);
            that.calculate = true;
            dataView.setItems(newData);
        });
        this.updateTotals();
    },

    slickFilter: function(item, that) {
        return !that.searchstring || Object.values(item).join('||').toLowerCase().indexOf(that.searchstring) > -1;
    },

    generateField: function(item) {
        this.body.parent().append(`<input type="hidden" class="t${item.id}" name="${item.type == 'auto'?'banktrans':'transactions'}[${item.th_id}]" value="${(item.check ? '1' : '0')}">`);
    },

    parseColumns: function(columns) {
        var newcolumns = [];
        var that = this;
        this.formatters = {'CheckFormatter' : function(row, cell, value, columnDef, dataContext) {
            if(value == true) return'<i id="rec-icon-check" class="icon-check" style="visibility: visible;"></i>';
            else return'';
        }};
        for(var i in columns){
            if(typeof columns[i].formatter !== 'function') columns[i].strformatter = columns[i].formatter || "";
            if(typeof columns[i].formatter === 'string') {
                columns[i].formatter = this.formatters[columns[i].formatter];
            }            
        }
        return columns;
    },

    updateTotals: function() {
        var data = this.dataView.getItems();
        this.total = 0;
        this.number = 0;
        for(var i in data) {
            if(data[i].check) {
                this.total += parseFloat(data[i].amount);
                this.number++;
            }
        }
        this.numberobj.text(this.number);
        this.totalobj.text(number_format(this.total));
        this.differenceCallback ? this.differenceCallback() : '';
    },

    setCallback: function(callback) {
        this.differenceCallback = callback;
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


