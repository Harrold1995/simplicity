
+(function ($) {
	FastEditableSelect = function (select, options) {
		this.id = FESC.getId();
		this.initstarted = false;
		this.initialized = false;
		this.datareadyCallback = null;
		this.firstindex = 0;
        this.sindex = 0;
		this.options = options;
		this.data = [];
        this.$select = $(select);
		this.$input  = $('<input type="text" autocomplete="off">');
		this.$hinput  = $('<input type="hidden" type="for-select" sel-id="'+this.$select[0].id+'" text=""/>');
		this.$list  = $('<div class="es-list">');
		this.utility = new FastEditableSelectUtility(this);
		this.defVal = '0';
		var that = this;
		if(this.$select.is('[stype]'))
			this.options.type = this.$select.attr('stype');
		if(this.$select.attr('filter_key'))
		  this.options.filter_key = this.$select.attr('filter_key');
		  if(this.$select.attr('filter_value'))
		  this.options.filter_value = this.$select.attr('filter_value');
		if(this.$select.is('[default]'))
			this.options.default = this.$select.attr('default');			
		if (['focus', 'manual'].indexOf(this.options.trigger) < 0) this.options.trigger = 'focus';
		if (['default', 'fade', 'slide'].indexOf(this.options.effects) < 0) this.options.effects = 'default';
		if (isNaN(this.options.duration) && ['fast', 'slow'].indexOf(this.options.duration) < 0) this.options.duration = 'fast';
		this.defVal = options.default;
		this.lastValue = this.defVal;
        this.$select.replaceWith(this.$input);
		this.$hinput.val(this.defVal);
        this.$input.attr('sel-value', this.defVal).addClass('fes');
        this.$list.appendTo(this.$input.parent());
        this.$hinput.attr('name',this.$select.attr('name'));
        if(!options.keepName) this.$select.removeAttr('name');
		this.utility.initializeList();
		options.fastinit = true;
        if(options.fastinit !== true) this.utility.initializeSlick();
        this.utility.initializeInput();
        this.utility.trigger('created');
        this.$hinput.appendTo(this.options.appendTo || this.$input.parent());

        //console.log('select generated in '+moment.duration(moment().diff(this.start)))

	}
	FastEditableSelect.DEFAULTS = { filter: true, effects: 'slide', duration: 'fast', trigger: 'focus' };
	FastEditableSelect.prototype.filter = function (first = false) {
		if(!this.dataView) return;
		this.dataView.refresh();
	};
    FastEditableSelect.prototype.show = function () {
    	//console.log(this.initialized);
    	if(!this.initialized) {
    		this.showoninit = true;
    		return;
        }
        if(FESC.visible && FESC.visible.id != this.id) FESC.visible.hide();
        FESC.visible = this;
    	this.$list.css({
			top:   this.$input.position().top + this.$input.outerHeight() - 1,
			left:  this.$input.position().left,
			//width: this.$input.outerWidth()
			width: 250,
            height: this.dataView.getFilteredItems().length*32 + 22
		});
        this.grid.resizeCanvas();
        if (!this.$list.is(':visible') && this.dataView.getFilteredItems().length > 0) {
        	var fns = { default: 'show', fade: 'fadeIn', slide: 'slideDown' };
			var fn  = fns[this.options.effects];
            this.$input.parent().on('click', function(e){e.stopPropagation();});
            this.$list.on('click', function(e){e.stopPropagation();});
			this.utility.trigger('show');
			this.$input.addClass('open');
			this.$list[fn](0, $.proxy(this.utility.trigger, this.utility, 'shown'));
		}
	};
	FastEditableSelect.prototype.hide = function () {
		this.lastClicked = '';
		var fns = { default: 'hide', fade: 'fadeOut', slide: 'slideUp' };
		var fn  = fns[this.options.effects];
		this.utility.trigger('hide');
		this.$input.removeClass('open');
        //this.$input.off('click');
        //this.$list.off('click');
		this.$list[fn](this.options.duration, $.proxy(this.utility.trigger, this.utility, 'hidden'));
	};
	FastEditableSelect.prototype.select = function (o) {
		//console.log(that.options);
		var that = this;
        if(!o) return;
        if(o.id == 'add'){
			var url='api/quickAddFast';
			$.post(url, {
				'value' : this.$input.val(),
				'type' : this.options.type,
			}, function(result) {
				var item = that.addItem(result.text, result.value);
				that.select(item);
				JS.loadSelects(that.options.type, that.id);
			}, 'json');
			return;
		}
		if(o.id == 'setup'){
            var modal = this.$select.attr('modal').split('|');
			JS.openDraggableModal(modal[0], 'add', null, null, {es_key: this.$select.attr('key'), es_value:this.$input.val(), url:modal[1]},
			[{event: 'postsubmit', function: $.proxy(that.addNSelect, that)}]);
			return;
		}
        this.lastClicked = '';
		var oldval = this.$input.val();
		this.lastValue = o.id;
		this.$input.val(o.name.trim());
		if (this.options.filter) this.hide();
		this.filter();
		var rownum2 = $(this.$input).closest("tr").attr("id");
		
		
		//this.utility.trigger('select', o);

		//compound select for profiles (adds hidden input for lease_id if it is a tenant)
		if (($(this.$input).closest("td").attr("stype")=="profile" || $(this.$input).closest("span").attr("stype")=="profile" ) && o.id.indexOf('-') > -1){
			var res2 = o.id.split("-");
			var leaseName = $(this.$input).attr("hidden-name");
			var leaseInput = '<input type="hidden" id="hidden_lease_id" value="';
				leaseInput += res2[1];
				if (rownum2 >=0) {
					leaseInput += '" name="transactions[' + rownum2 + '][lease_id]"></input>';
				} else {
					leaseInput += '" name="'+leaseName+'[lease_id]"></input>';
					leaseInput += '<input type="hidden" id="hidden_prop_id" value="'+o.prop_id+'" name="'+leaseName+'[prop_id]"></input>';
					leaseInput += '<input type="hidden" id="hidden_unit_id" value="'+o.unit_id+'" name="'+leaseName+'[unit_id]"></input>';
				}
			$(this.$input).closest("span").find("#hidden_lease_id,#hidden_prop_id, #hidden_unit_id").remove();
			$(this.$input).closest("span").append(leaseInput);
			this.$input.attr('sel-value', res2[0]);
			this.$hinput.val(res2[0]).attr('text', o.name);
			console.log(o);
			this.$hinput.attr('data-property', o.propname);
			//o.id = res2[0];
		} else {
			if($(this.$input).closest("span").find("#hidden_lease_id").length){
                $(this.$input).closest("span").find("#hidden_lease_id").val(null);
			} else {
				$(this.$input).closest("span").append(leaseInput);
			}
			this.$input.attr('sel-value', o.id);
			this.$hinput.val(o.id).attr('text', o.name);
		}
		
		if (($(this.$input).closest("td").attr("stype")=="profile" || $(this.$input).closest("span").attr("stype")=="profile" )){
			console.log("oid "+o.id);
		}


        
        this.$input.trigger('change');
	};
    FastEditableSelect.prototype.selectDefault = function (value) {
    	if(value == null || value == '') return;
    	var o = _.find(this.data, function(o) { return o.id == value; });
    	if (o) {
            this.$input.val(o.name);
            this.$hinput.val(value).attr('text', o.name);
			this.$input.attr('sel-value', value);

			//compound select for profiles (adds hidden input for lease_id if it is a tenant)
			var rownum2 = $(this.$input).closest("tr").attr("id");
					if (($(this.$input).closest("td").attr("stype")=="profile" || $(this.$input).closest("span").attr("stype")=="profile" ) && o.id.indexOf('-') > -1){
						var res2 = o.id.split("-");
						var leaseName = $(this.$input).attr("hidden-name");
						console.log(o);
						console.log("profile"+res2[0]);
						var leaseInput = '<input type="hidden" id="hidden_lease_id" value="';
							leaseInput += res2[1];
							if (rownum2 >=0) {
								leaseInput += '" name="transactions[' + rownum2 + '][lease_id]"></input>';
							} else {
								leaseInput += '" name="'+leaseName+'[lease_id]" text="'+o.tenantname+'"></input>';
								leaseInput += '<input type="hidden" id="hidden_prop_id" value="'+o.prop_id+'" name="'+leaseName+'[prop_id]" text="'+o.propname+'"></input> ';
								leaseInput += '<input type="hidden" id="hidden_unit_id" value="'+o.unit_id+'" name="'+leaseName+'[unit_id]" text="'+o.unitname+'"></input>';
							}
						$(this.$input).closest("span").find("#hidden_lease_id,#hidden_prop_id, #hidden_unit_id").remove();
						$(this.$input).closest("span").append(leaseInput);
						this.$input.attr('sel-value', res2[0]);
						this.$hinput.val(res2[0]).attr('text', o.name);

						console.log(o.prop_id);
						//o.id = res2[0];
					} else {
						if($(this.$input).closest("span").find("#hidden_lease_id").length){
							$(this.$input).closest("span").find("#hidden_lease_id").val(null);
						} else {
							$(this.$input).closest("span").append(leaseInput);
						}
						this.$input.attr('sel-value', o.id);
						this.$hinput.val(o.id).attr('text', o.name);
					}
					
					if (($(this.$input).closest("td").attr("stype")=="profile" || $(this.$input).closest("span").attr("stype")=="profile" )){
						console.log("oid "+o.id);
					}// end compound


            this.filter();
		}
    };
    FastEditableSelect.prototype.revertValue = function () {
        var o = this.dataView.getIdxById(this.lastValue);
        this.select(this.dataView.getItemByIdx(o));
    };
	FastEditableSelect.prototype.addItem = function (text, id) {
		var data = this.dataView.getItems();
		data.push({id: id, name: text});
		this.dataView.setItems(data);
		this.dataView.refresh();
		return this.dataView.getItemById(id);

	};
	FastEditableSelect.prototype.addNSelect = function (event, data) {
        var item = this.addItem(data.es_text, data.id);
        this.select(item);
        JS.loadSelects(this.options.type, this.id);
	};
	FastEditableSelect.prototype.remove = function (index) {
		var last = this.$list.find('li').length;

		if (isNaN(index)) index = last;
		else index = Math.min(Math.max(0, index), last - 1);
		this.$list.find('li').eq(index).remove();
		this.$select.find('option').eq(index).remove();
		this.filter();
	};
	FastEditableSelect.prototype.clear = function () {
		this.$list.find('li').remove();
		this.$select.find('option').remove();
		this.filter();
	};
    FastEditableSelect.prototype.resetSelect = function (data) {
        var that = this;
        var start=2;
        this.data = [];
        that.clear();
		this.buffer = document.createDocumentFragment();
        if(that.$select.hasClass('quick-add')) that.buffer.appendChild(that.add('<i class="fas fa-plus"></i>Quick Add', start++, [{name:"class", value:"es-add no-filter"}], null, false));
        if(that.$select.hasClass('set-up')) that.buffer.appendChild(that.add('<i class="fas fa-cog"></i>Set Up', start++, [{name:"class", value:"es-setup no-filter last"}], null, false));

        $('<select></select>').append(data).find('option').each(function (i, option) {
            that.buffer.appendChild(that.add(option.text, start, option.attributes, option.dataset));
            if (option.hasAttribute('selected')) that.$input.val(option.text);
        });
        var start=2;

        this.$input.val('');
        this.$hinput.val('0');
        this.$list.prepend(this.buffer);
        that.filter();
        //that.select(this.$list.find('li[value="0"]'));
    };
	FastEditableSelect.prototype.destroy = function () {
		this.$list.off('mousemove mousedown mouseup');
		this.$input.off('focus blur input keydown');
		this.$input.replaceWith(this.$select);
		this.$list.remove();
		this.$select.removeData('editable-select');
        JS.selectcallbacks.remove(this.utility.reloadData);
	};
	FastEditableSelect.prototype.onBlur = function () {
		var that = this;
		if(this.$input.val().trim() == '') {
        	this.$hinput.val('');
            this.$input.attr('sel-value','');
        }
        if(this.$input.val().trim() != '' && _.filter(this.data, function(o){return o.name.toLowerCase() == that.$input.val().toLowerCase().trim()}).length <= 0)
        	this.revertValue();
        else
        	this.hide();
	};

	// Utility
	FastEditableSelectUtility = function (es) {
		this.es = es;
	};
    FastEditableSelectUtility.prototype.initData = function () {
    	var that = this;
    	if(this.es.options.data) return this.es.options.data;
    	var key = this.es.options.type;
    	if(this.es.options.filter_value && this.es.options.filter_value!='-1' && this.es.options.filter_key && ['unit', 'account', 'profile', 'tenant'].includes(this.es.options.type))
    		key += '.'+this.es.options.filter_key+'.'+this.es.options.filter_value;
    	else
            this.es.options.filter_key;
    	if(FESC.exists(key))
    		return FESC.getCacheData(key);
    	else{
            var data = JS.sdata[this.es.options.type];
            if(this.es.options.filter_key && this.es.options.filter_value)
				switch(this.es.options.type){
					case 'account':
						data = _.filter(data, function(o){
							return that.es.options.filter_key == 'property_id' && (o.all_props == 1 || o.property_id && o.property_id.split("|").includes(that.es.options.filter_value)) || o[that.es.options.filter_key] == that.es.options.filter_value;
						});
						break;
					case 'unit':
						data = _.filter(data, function(o){
							return o[that.es.options.filter_key] == that.es.options.filter_value;
						});
						break;
                    case 'profile':
                    	if(that.es.options.filter_key == 'prop_id' || that.es.options.filter_key == 'unit_id' || that.es.options.filter_key == 'profile_type_id') {
							data = _.filter(data, function (o) {
								return that.es.options.filter_value == o[that.es.options.filter_key];
							});
						} else
                        if(that.es.options.filter_value == JS.sitesettings.accounts_receivable || that.es.options.filter_value == JS.sitesettings.accounts_payable) {
                            data = _.filter(data, function (o) {
                                return that.es.options.filter_value == JS.sitesettings.accounts_receivable ? o[that.es.options.filter_key] == '3' : o[that.es.options.filter_key] == '1';
                            });
                        }
                        break;
					case 'tenant':
						if(that.es.options.filter_key == 'prop_id' || that.es.options.filter_key == 'unit_id' || that.es.options.filter_key == 'profile_type_id') {
							data = _.filter(data, function (o) {
								return that.es.options.filter_value == o[that.es.options.filter_key];
							});
						}
						break;
					default: key = this.es.options.type;
				}
            FESC.setCacheData(key, data);
            return data;
		}
	};
    FastEditableSelectUtility.prototype.reloadData = function (id) {
    	if(this.es.id == id) return;
        this.es.data = this.initData();
    	if(this.es.dataView) {
    		this.es.dataView.setItems(this.es.data);
            this.es.dataView.refresh();
        }else{
            this.es.initialized = false;
            this.es.initstarted = false;
            this.initializeList();
		}
    };
    FastEditableSelectUtility.prototype.initializeSlick = function () {
        if(this.es.initstarted || this.es.initialized) return;
        this.es.initstarted = true;
        var es = this.es;
    	es.slickOptions = {rowHeight: 32,headerHeight: 0, forceFitColumns: true, enableCellNavigation: true, cellHighlightCssClass: "changed"};
        //es.data = this.initData();
        es.dataView = new Slick.Data.DataView({ inlineFilters: true });
        es.dataView.getItemMetadata = function(index)
        {
            var item = es.dataView.getItem(index);
            if(item.id == 'add') {
                return { cssClasses: 'fes-add' };
            }
            else if(item.id == 'setup') {
                return { cssClasses: 'fes-setup' };
            } else {
                return { cssClasses: 'common' };
			}
        };
        es.dataView.setFilterArgs(es);
        es.dataView.beginUpdate();
        es.dataView.setItems(this.initSpecialRows(es.data));
        es.dataView.setFilter(this.slickFilter);
        es.dataView.endUpdate();
        var NameFormatter = function (row, cell, value, columnDef, dataContext) {
            if (value == null || value == undefined || dataContext === undefined) { return ""; }
            var spacer = "<span style='display:inline-block;height:1px;width:" + (15 * dataContext["indent"]) + "px'></span>";
            return spacer + value;
        };
        var columns = [{data:'name', field: 'name', title: 'Name',width: 200, formatter: NameFormatter}];
        if(es.data[0] && es.data[0].details) columns.push({data:'details', field: 'details', title: 'Details',width: 55});
        es.grid = new Slick.Grid(es.$list, es.dataView, columns, es.options);
        es.$list.find(".slick-header-columns").css("height","0px");
        es.grid.resizeCanvas();
        es.grid.onCellChange.subscribe(function (e, args) {
            es.dataView.updateItem(args.item.id, args.item);
        });
        es.grid.onClick.subscribe(function (e, args) {
        	var item = es.dataView.getItem(args.row);
        	es.select(item);
            es.dataView.updateItem(item.id, item);
        });
        es.dataView.onRowCountChanged.subscribe(function (e, args) {
			es.grid.updateRowCount();
			plugin = new Slick.AutoTooltips();
            es.grid.registerPlugin(plugin);
            es.grid.render();
        });
        es.dataView.onRowsChanged.subscribe(function (e, args) {
			es.grid.invalidateRows(args.rows);
			plugin = new Slick.AutoTooltips();
            es.grid.registerPlugin(plugin);
            es.grid.render();
        });
        //es.filter();
        es.initstarted = false;
        es.initialized = true;
        //console.log(es.showoninit);
        if(es.showoninit) es.show();
        this.es.$input.focus();
        //console.log('end init');
        return true;
    };
    FastEditableSelectUtility.prototype.initSpecialRows = function (data) {
        if(this.es.$select.hasClass('es-setup')) {
        	newRow = {name: '<i class="fas fa-cog"></i> Set Up', id: 'setup'};
            data.splice(0, 0, newRow);
            this.es.firstindex++;
            this.es.sindex++;
        }
        if(this.es.$select.hasClass('es-add')) {
            var newRow = {name: '<i class="fas fa-plus"></i> Quick Add', id: 'add'};
            data.splice(0, 0, newRow);
            this.es.sindex++;
            this.es.firstindex++;
		}
		if(this.es.$select.hasClass('es-default')) {
            var newRow = {name: 'Default',  id:'-1'};
            data.splice(0, 0, newRow);
            this.es.sindex++;
            this.es.firstindex++;
        }
        return data;
    };
	FastEditableSelectUtility.prototype.initializeList = function () {
        if(!this.callbackset)
        	JS.selectcallbacks.add($.proxy(this.reloadData, this));
        this.callbackset = true;
        if(!JS.selectdataready) return;
		this.es.data = this.initData();
        this.setAttributes(this.es.$input[0], this.es.$select[0].attributes, this.es.$select.data());
        this.es.$select.remove();
        this.es.$input.addClass('es-input').data('editable-select', this.es);
        this.es.selectDefault(this.es.options.default);
	};
	FastEditableSelectUtility.prototype.initializeInput = function () {
		var that = this;
        $(window).click(function() {
            if(that.es.$input.hasClass('open')) {
            	that.es.onBlur();
            	//console.log('gg');
            }
        });
        switch (this.es.options.trigger) {
			default:
			case 'keydown':
                that.es.$input
					.on('keydown', function(e){if(e.keyCode < 37 || e.keyCode > 40) {$.proxy(that.es.show, that.es); }})
				break;
			case 'manual':
				break;
			case 'focus':
				that.es.$input
					.on('focus', function(e){that.es.show();})
			break;
		}
		that.es.$input.on('input keydown', function (e) {
			switch (e.keyCode) {
				case 37: case 39: break;
				case 38: // Up
                    if (that.es.$input.hasClass('open')) that.highlight(-1);
					e.preventDefault();
					break;
				case 40: // Down
                    if (that.es.$input.hasClass('open')) that.highlight(1);
					e.preventDefault();
					break;
				case 13: // Enter
					if (that.es.$input.hasClass('open')) {
                        that.es.grid.onClick.notify({row:that.es.sindex});
						e.preventDefault();
					}
					break;
				case 9:  // Tab
				case 27: // Esc
					that.es.hide();
					break;
				default:
					that.es.filter();
					that.highlight(0);
					break;
			}
		}).on('click', function(e){
			//if(!e.originalEvent) return;
			if(that.es.options.formgrid && !$(this).closest('tr').hasClass('filledRow')) {
                if(!that.es.$input.hasClass('open'))$(window).trigger('click');
				$(this).closest('tr').trigger('click');
			}
            if(!that.es.initialized && !that.es.initstarted) that.initializeSlick();
            //var parentOffset = $(this).parent().offset();
            //var relX = $(this).width() - e.pageX + parentOffset.left;
            //if(relX < 0) $.proxy(that.es.show, that.es);
            $(this).select();
            e.stopPropagation();
            if(!that.es.$input.hasClass('open'))$(window).trigger('click');
		});
        that.es.$input.parent().off('click').click(function(e){
        	e.stopPropagation();
            var e = jQuery.Event("keydown");
            e.which = 16; // # Some key code value
            that.es.$input.trigger('click');
			that.es.filter();
            that.highlight(0);
            if(!that.es.$input.hasClass('open'))$(window).trigger('click');
		});
	};
    FastEditableSelectUtility.prototype.slickFilter = function (item, es) {
    	const searchString = es.$input.val().toLowerCase();
        if ((item.id == 'add' || item.id == 'setup') && !es.data.map(i =>                     // find if this item or any of it's children satisfy the
        (!searchString || i["name"].toLowerCase() == searchString)
    ).reduce((sum, val) => sum || val, false)) return true;

            const children = FESC.findAllChildren(item, es.data);

        //var parent = es.data[item.parent];
        /*while (parent) {
            if (parent["name"].toLowerCase().indexOf(searchString) > -1) {
                return true
            }
            parent = es.data[parent.parent];
        }*/

        return [item].concat(children)  // get an array of this item and all of it's children
            .map(i =>                     // find if this item or any of it's children satisfy the
                (!searchString || (i["name"].toLowerCase().indexOf(searchString) > -1))
            ).reduce((sum, val) => sum || val, false);

    };
	FastEditableSelectUtility.prototype.showError = function (text) {
		$('#error-modal .modal-body').text(text);
		var modal = $('#error-modal'), that = this;
		modal.modal({
			show : true
		});
		modal.on('hidden.bs.modal', function() {
			that.es.$input.focus();
		});
	};
	FastEditableSelectUtility.prototype.highlight = function (index = 0) {
        var that = this;
        that.es.show();
        var visibles = that.es.$list.find('.slick-row.common');
        var realindex = that.es.sindex - that.es.firstindex;
        setTimeout(function () {
            if (visibles.length > 0) {
                var selected = that.es.$list.find('.slick-row.highlighted');
                if(selected.length == 0) selected = visibles.eq(realindex);
            	switch(index) {
					case 0:
                        selected.removeClass('highlighted');
						that.es.sindex = that.es.firstindex; selected = visibles.eq(0); break;
                    case 1:
                    	if(that.es.sindex < that.es.dataView.getFilteredItems().length - 1) {
                            selected = selected.removeClass('highlighted').next();
                            that.es.sindex++;
                        }
                        break;
                    case -1:
                    	if(realindex > 0) {
                    		selected = selected.removeClass('highlighted').prev();
                            that.es.sindex--;
                    	}
                    	break;
				}
				selected.addClass('highlighted');
            	//console.log(selected);
                that.es.grid.scrollRowIntoView(that.es.sindex);
            }
        });
	};
	FastEditableSelectUtility.prototype.setAttributes = function (element, attrs, data) {
		$.each(attrs || {}, function (i, attr) { element.setAttribute(attr.name, attr.value); });
		element.dataset = data;
		return element;
	};
	FastEditableSelectUtility.prototype.trigger = function (event) {
		var that = this;
		var params = Array.prototype.slice.call(arguments, 1);
		var args   = [event + '.editable-select'];
		args.push(params);
		this.es.$select.trigger.apply(this.es.$select, args);
		this.es.$input.trigger.apply(this.es.$input, args);
	};

	// Plugin
	Plugin = function (option) {
		var args = Array.prototype.slice.call(arguments, 1);
		this.each(function () {
			var $this   = $(this);
			var data    = $this.data('editable-select');
			var options = $.extend({}, FastEditableSelect.DEFAULTS, $this.data(), typeof option == 'object' && option);
			if (!data) data = new FastEditableSelect(this, options);
			if (typeof option == 'string') data[option].apply(data, args);
		});
	}
	$.fn.fastSelect             = Plugin;
	$.fn.fastSelect.Constructor = FastEditableSelect;

})(jQuery);

FastEditableSelectCache = function () {
	this.data = {};
	this.id = 1;
	this.visible = null;
};
FastEditableSelectCache.prototype = {
	exists: function(name){
		return name in this.data;
	},

	getId: function() {
		return this.id++;
	},

	clearCache: function() {
        this.data = {};
	},

	getCacheData: function(name) {
		if(this.exists(name)) return JSON.parse(JSON.stringify(this.data[name])); else return [];
	},

	setCacheData: function(name, data) {
		this.data[name] = JSON.parse(JSON.stringify(data));
	},
    findAllChildren: function(parent, data) {
        const id = parseInt(parent.slickid);

        return data
            .filter(item => item.parent === id)   // find all direct children
            .map((item) => [item].concat(FESC.findAllChildren(item, data))) // for every child, find it's children
            .reduce((sum, val) => sum.concat(val), [])  // make one big array with all children
    },

};
var FESC;
$(document).ready(function(){
    FESC = new FastEditableSelectCache();
})
