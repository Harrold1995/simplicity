var CheckboxBlock = function(obj, options) {
    this.obj = $(obj).last();
    this.slickbody = this.obj.find('.cbgroup');
    this.options = options;
    this.init();
};
/** class methods **/
CheckboxBlock.prototype = {
	init : function() {
	    if(window.cbcount)
	        window.cbcount ++;
	    else
	        window.cbcount = 1;
        this.cbcounter = 0;
	    this.id = window.cbcount;
        this.slickbodyid = '#cbgroup'+this.id;
	    this.render();
	    this.obj.data('cbBlock', this);
	},

    initEvents: function() {
	    var that = this;
	    this.obj.on('change', '.selectAllCheckboxes', function() {
            var checked = $(this).is(':checked');
            var data = that.dataView.getFilteredItems();
            var alldata = that.dataView.getItems();
            for (var i in alldata) {
                alldata[i].check = false;
            }
            for (var i in data) {
                data[i].check = checked;
            }
            that.dataView.setItems(alldata);
            that.grid.invalidate();
            //if(checked) that.cbcounter = 0;
            //else 
            that.cbcounter = data.length;
            //console.log(that.getSelected());
            if(that.changeCallback) that.changeCallback(that.getSelected());
        });
        this.obj.on('input', 'input[type="text"]', function() {
            that.searchstring = $(this).val();
            that.dataView.refresh();
        });
    },

    getSelected: function() {
	    var result = {filter: this.options.filter};
	    if(this.cbcounter == 0) {
	        result.all = true;
        } else {
	        result.all = false;
	        result.data = [];
            var data = this.dataView.getItems();
            for (var i in data) {
                if(data[i].check)
                    result.data.push(data[i].id);
            }
        }
        //console.log(result);
        return result;
    },

    addChangeCallback: function(func) {
	    this.changeCallback = func;
    },

    render: function() {
	    var template = this.getTemplate();
	    this.obj.html(template);
	    this.initEvents();
	    var that = this;
        setTimeout(function(){that.renderSlick(that.getData())},500);

    },

    getData: function() {
	    //return[{id: 1, check:1,name:"test1"}, {id: 2, name:"test2"}, {id: 3, name:"test3"}]
	    var data = JS.sdata[this.options.source];
	    for(var i in data) {
	        data[i].check = true;
        }
        return data;
    },

    renderSlick : function(data) {
        var that = this;
        this.selectall = true;
        slickOptions = {rowHeight: 32, hideHeader:true, forceFitColumns: true};
        var dataView = new Slick.Data.DataView({ inlineFilters: true });
        dataView.beginUpdate();
        dataView.setItems(data);
        dataView.setFilter(this.slickFilter);
        dataView.setFilterArgs(this);
        dataView.endUpdate();
        var CheckFormatter = function (row, cell, value, columnDef, dataContext) {
            //console.log(dataContext);
            return '<label  class="custom-checkbox" >'+
                '<input type="checkbox"  value="'+dataContext.id+'" class="commonCheckbox" '+(dataContext.check === true ? 'checked' : '')+'>'+
                '<div class="input"></div>'+
            '</label>';
        };
        var columns = [];
        columns.push({id: 'check', field: 'check', name: 'check',width: 10, formatter: CheckFormatter});
        columns.push({id: 'name', field: 'name', name: 'name', width: 200});

        var grid = new Slick.Grid(this.slickbodyid, dataView, columns, slickOptions);

        grid.onClick.subscribe(function (e, args) {
            var item = dataView.getItem(args.row);
            if(item.check)
                that.cbcounter++;
            else
                that.cbcounter--;
            item.check = !item.check;
            dataView.updateItem(item.id, item);
            if(that.cbcounter == 0)
                that.obj.find('.selectAllCheckboxes').prop('checked', true);
            else
                that.obj.find('.selectAllCheckboxes').removeAttr('checked');
            if(that.changeCallback)
                that.changeCallback(that.getSelected());
        });
        dataView.onRowCountChanged.subscribe(function (e, args) {
            grid.updateRowCount();
            grid.render();
        });
        dataView.onRowsChanged.subscribe(function (e, args) {
            grid.invalidateRows(args.rows);
            grid.render();
        });
        this.dataView = dataView;
        this.grid = grid;
        //$(this.slickbodyid).find(".slick-header-columns").css("height","0px");
        $(window).on('resize', function(){grid.resizeCanvas();})

    },

    slickFilter: function(item, that) {
        return !that.searchstring || item.name.toLowerCase().indexOf(that.searchstring) > -1;
    },

    getTemplate: function() {
        return'<h3 class="header-th">'+
                    '<label for="pselect" class="custom-checkbox">'+
                    '<input type="checkbox" id="pselect" class="selectAllCheckboxes" checked>'+
                    '<div class="input"></div>'+
                    this.options.title+
                    '</label>'+
                    '<span><input type="text" class="pfilter paybillfilter"></span>'+
                '</h3>'+
                '<div class="cbgroup" id="cbgroup'+this.id+'"></div>';
    }
};
