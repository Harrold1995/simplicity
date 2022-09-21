var SlickMaintenance = function (body, options = {}) {
    this.body = $(body);
    this.grid = null;
    this.filters = [];
    this.left = $('.left-side');
    this.dataView = null;
    this.options = options;
    this.hidden = options.hidden || [];
    this.parent = this.body.closest('.tabcontent');
    this.visible = true;
    this.types = ['', 'Plumbing', 'Electric', 'Appliances', 'Heat/Air', 'Locks & Key', 'Pest', 'Other'];
    this.statuses = ['', 'New', 'In Progress', 'Completed', 'Deffered', 'Closed'];
    this.due_labels = ['','Today', 'Yesterday', 'Tommorrow', 'Earlier This Week', 'Later This Week', 'Earlier This month' , 'Later This month', 'Older', 'Newer', 'No Due Date'];
    this.status_colors = ['', '#fbb03b', '#7fafd0', '#73b680', '#a56bda', '#a5a5a6'];
	this.priorities = ['Low', 'Normal', 'High'];
    this.init();
};
/** class methods **/
SlickMaintenance.prototype = {
    init: function () {
        JS.right_pid = 0;
        JS.onlyopen = false;
        this.body.data('grid', this);
        this.initFormatters();
        this.generateFilters();
        this.loadSettings();
        this.initEvents();
        //this.dataView.fastSort('due_date', true)
    },

	generateFilters: function() {
		this.filters = this.left.find('.checkbox-group').map(function () { return {field: $(this).attr('field'), unchecked: $(this).find('input[type="checkbox"]:not(:checked)').length, data: $(this).find('input[type="checkbox"]:checked').map(function () { return $(this).val() }).get() }}).get();
		if(this.dataView) this.dataView.refresh();
	},

	initEvents: function() {
		var that = this;
		this.body.on("remove", function () {
			that.destroy();
		})
		$('#mgrouping').on('change', function() {
			var grouping = parseInt($(this).val());
			switch(grouping) {
				case 0: that.clearGrouping(); break;
				case 1: that.groupByProperty(); break;
				case 2: that.groupByStatus(); break;
				case 3: that.groupByType(); break;
				case 4: that.groupByAssigned(); break;
                case 5: that.groupByPriority(); break;
                case 6: that.groupByDueDate(); break;                
			}
		});
		this.left.on('change', '.checklist-a input[type="checkbox"]', function () {
			that.generateFilters();
        });
        this.left.on('click', '.customView', function () {
            dtype = $(this).attr('data-filterfield');
            dataId = $(this).attr('data-filterid');
            dataIds = dataId.split(",");
            $(this).closest('.accordion-a').find(`input`).each( function() {
                if ( dataIds.includes($(this).val()) || $(this).attr('dtype') != dtype){
                    $(this).prop('checked', true).parent('label').addClass('active');
                } else {
                    $(this).prop('checked', false).removeAttr('checked').parent('label').removeClass('active');
                }
            });
            that.generateFilters();
		});
	},



	clearGrouping: function() {
		this.dataView.groupBy(null);
	},

	groupByProperty: function() {
    	this.dataView.groupBy(
			"property",
			function (g) {
				return "Property:  <b>" + (g.value ? g.value : 'No Property') + "</b>  <span style='color:green'>(" + g.count + " tickets)</span>";
			},
			function (a, b) {
                avalue = a.value ? a.value: 'zzzz';
                bvalue = b.value ? b.value: 'zzzz';
				return avalue > bvalue ? 1 : -1 ;
			}
		);
	},

	groupByStatus: function() {
        var that = this;
		this.dataView.groupBy(
			"status",
			function (g) {
				return "Status:  <b>" + that.statuses[g.value] + "</b>  <span style='color:green'>(" + g.count + " tickets)</span>";
			},
			function (a, b) {
				return a.count - b.count;
			}
		);
	},

	groupByType: function() {
    	var that = this;
		this.dataView.groupBy(
			"type",
			function (g) {
				return "Type:  <b>" + that.types[g.value] + "</b>  <span style='color:green'>(" + g.count + " tickets)</span>";
			},
			function (a, b) {
				return a.count - b.count;
			}
		);
	},

	groupByAssigned: function() {
		var that = this;
		this.dataView.groupBy(
			"assigned_name",
			function (g) {
				return "Assigned to:  <b>" + (g.value ? g.value : 'Not Assigned') + "</b>  <span style='color:green'>(" + g.count + " tickets)</span>";
			},
			function (a, b) {
				return a.count - b.count;
			}
		);
	},

	groupByPriority: function() {
		var that = this;
		this.dataView.groupBy(
			"priority",
			function (g) {
				return "Type:  <b>" + that.priorities[g.value] + "</b>  <span style='color:green'>(" + g.count + " tickets)</span>";
			},
			function (a, b) {
				return a.count - b.count;
			}
		);
    },
    
	groupByDueDate: function() {
        var that = this;
    	this.dataView.groupBy(
			"due_date_calc",
			function (g) {
				return "Due on:  <b>" + (that.due_labels[g.value]) + "</b>  <span style='color:green'>(" + g.count + " tickets)</span>";
			},
			function (a, b) {
				return a.value - b.value;
			}
		);
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
        var options = {forceFitColumns: true, rowHeight: 60, explicitInitialization: true, showHeaderRow: false,headerRowHeight: 0};
        dataView = new Slick.Data.DataView({ inlineFilters: true });
        dataView.beginUpdate();
        dataView.setItems(this.data);
        dataView.endUpdate();
        this.dataView = dataView;
        grid = new Slick.Grid(this.body, dataView, this.parseColumns(this.columns), options);
        this.grid = grid;

        dataView.onRowCountChanged.subscribe(function (e, args) {
			grid.updateRowCount();
			grid.render();
		});
		dataView.onRowsChanged.subscribe(function (e, args) {
			grid.invalidateRows(args.rows);
			grid.render();
		});
		grid.onClick.subscribe(function(e, args) {
            if(e.originalEvent.detail > 1){
                return;}
			var item = this.getDataItem(args.row);
			if (item && item instanceof Slick.Group && $(e.target).find(".slick-group-toggle")) {
				if (item.collapsed) {
					this.getData().expandGroup(item.value);
				}
				else {
					this.getData().collapseGroup(item.value);
				}

				e.stopImmediatePropagation();
				e.preventDefault();
			} else {
                var item = dataView.getItem(args.row);
                cellclicked = grid.getCellFromEvent(e);
                selector = $(grid.getCellNode(cellclicked.row,1)).closest('.slick-row');
                $(selector).animate({opacity: 0.5}, 400, function() {$(selector).animate({opacity: 1}, 1000)});
                
                JS.openDraggableModal('maintenance', 'edit', item.id, null, {url:'maintenance/getModal'});
            }
		});
/* 		grid.onDblClick.subscribe(function (e, args) {
			var item = dataView.getItem(args.row);
			JS.openDraggableModal('custom', 'edit', item.id, null, {url:'maintenance/getModal'});
		}); */

        grid.init();

		if(!this.options.nofilter){
			dataView.setFilterArgs(this);
			dataView.beginUpdate();
			dataView.setFilter(this.myFilter);
			dataView.endUpdate();
        }
        this.groupByDueDate();
		this.resizeCanvas();
		//this.appendFilterButton();
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
            if(typeof columns[i].formatter !== 'function') columns[i].strformatter = columns[i].formatter || "";
            if(typeof columns[i].formatter === 'string') {
                columns[i].formatter = this.formatters[columns[i].formatter];
            }
            newcolumns.push(columns[i]);
        }
        return newcolumns;
    },

    initFormatters: function() {
        var that = this;
        this.formatters = {
            TypeFormatter: function (row, cell, value, columnDef, dataContext) {
                if (value == null || value == undefined || dataContext === undefined) { return ""; }
                var icons = ['', 'drop.svg', 'electricity.svg', 'appliances.svg', 'heatair.svg', 'keys.svg', 'pest.svg', 'home.svg'];
                        return `<img class="micon" src="${JS.baseUrl}themes/default/assets/images/icons/${icons[value]}">`;
            },
            TitleFormatter: function (row, cell, value, columnDef, dataContext) {
                if (value == null || value == undefined || dataContext === undefined) { return columnDef && columnDef.horizontal ? "$0.00" : ""; }
                value = 'Ticket #' + dataContext['id'] + ' ' + value;
                return value;
            },
            UsersFormatter: function (row, cell, value, columnDef, dataContext) {
            	return '<div class="mproperty-wrapper"><span>Assigned to: </span><span class="munit">' + (dataContext['assigned_name'] ? dataContext['assigned_name'] : '-') + '</span></div>';
            },
			PropertyFormatter: function (row, cell, value, columnDef, dataContext) {
				return '<div class="mproperty-wrapper"><span>' + value + '</span><span class="munit">' + dataContext['unit'] + '</span></div>';
			},
            StatusFormatter: function (row, cell, value, columnDef, dataContext) {
                
                return '<div class="mstatus-wrapper"><small class ="statusspan"style="background-color:' + (that.status_colors[value])+'">'+ (that.statuses[value])+'</small>' + (dataContext['priority'] == 2 ? '<span class="light">/Urgent</span>' : '') + '<br> <span class="s14">Created By: ' + (dataContext['created_name'] ? dataContext['created_name'] : '-') + '</span></div>';
            },
            LinkFormatter: function (row, cell, value, columnDef, dataContext) {

                return '...';
            },
            DateFormatter: function (row, cell, value, columnDef, dataContext) {
                return '<span class="scheme-box overlay-lime">Due ' + (value ? value : '-') + '</span>';
            }
        };
    },

    myFilter: function(item, obj) {
        var r = true;
        obj.filters.forEach(function(filter){
        	if(filter.field == 'tags') {
				if(item[filter.field]) {
					tags = item[filter.field].split(',');
					var r1 = false;
					tags.forEach(function(tag){
						if(filter.data.includes(tag)) r1 = true;
					});
					if(!r1) r = r1;
				} else {
        			if(filter.unchecked > 0)  r = false;
        		}
			} else {
				if(!filter.data.includes(item[filter.field])) r = false;
			}
		});
        return r;
    },


    loadSettings() {
        var that = this;
        var filters = that.options.data;
        this.post1 = $.post(JS.baseUrl+that.options.dataUrl, {}, function (result) {
            that.data = result.data;
            that.columns = result.columns;
			that.initGrid();
			//that.initColumnsPopup(that.parent.parent().find('#editColumns'),that.options.tableName);

		}, "JSON");
    },

	toggleColumn: function(val, def = null){

		//this.saveSettings();
		if(def == null)this.grid.setColumns(this.parseColumns(this.columns));

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

    sortTree: function(tree, field, isAsk) {
        return {
            item: tree.item,
            children: tree.children
                .sort((a, b) => {
                    let result;
                    result = (a.item[field] > b.item[field]) ? 1 : -1;

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
