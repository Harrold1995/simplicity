/**
 * jQuery Editable Select
 * Indri Muska <indrimuska@gmail.com>
 *
 * Source on GitHub @ https://github.com/indrimuska/jquery-editable-select
 */

+(function ($) {
	// jQuery Editable Select
	EditableSelect = function (select, options) {
		var that     = this;
        this.options = options;
        this.data = [];
        this.lastClicked = '';
		this.$select = $(select);
		this.$input  = $('<input type="text" autocomplete="off">');
		this.$hinput  = $('<input type="hidden" type="for-select" sel-id="'+this.$select[0].id+'" text=""/>');
        if(this.$select.attr('key')) {
        	this.$hinput.attr('key',this.$select.attr('key'));
            //this.$select.removeAttr('key');
        }
		this.$list   = $('<ul class="es-list">');
		this.utility = new EditableSelectUtility(this);
		this.start = $.now();
		this.defVal = '0';
		this.buffer = document.createDocumentFragment();
		if (['focus', 'manual'].indexOf(this.options.trigger) < 0) this.options.trigger = 'focus';
		if (['default', 'fade', 'slide'].indexOf(this.options.effects) < 0) this.options.effects = 'default';
		if (isNaN(this.options.duration) && ['fast', 'slow'].indexOf(this.options.duration) < 0) this.options.duration = 'fast';
		
		// create text input
		this.defVal = this.$select.find('option:selected').val();		
        this.$select.replaceWith(this.$input);

		// initalization
        this.$hinput.val(this.defVal);
        this.$input.attr('sel-value', this.defVal);
        if(this.options.cache && ESC.exists(this.options.cache)) {
            this.saveCache = false;
            console.log('loaded from cache');
            this.utility.initFromCache(this.options.cache);
        } else{
            this.$list.appendTo(this.options.appendTo || this.$input.parent());
            if(this.options.cache) this.saveCache = true;
            this.utility.initialize();
        }
        this.utility.initializeList();
        this.utility.initializeInput();
		this.utility.trigger('created');
		this.$hinput.appendTo(this.options.appendTo || this.$input.parent());
		this.$hinput.attr('name',this.$input.attr('name'));
		if(!options.keepName) this.$input.removeAttr('name');
	}
	EditableSelect.DEFAULTS = { filter: true, effects: 'slide', duration: 'fast', trigger: 'focus' };
	EditableSelect.prototype.filter = function (first = false) {
        var that = this;
		var found = [], forceShow = false;
		var search  = this.$input.val().toLowerCase().trim(), text  = this.$input.val().trim();
		if(search == '' || (first && this.options.ignore_first == true && (this.$hinput.val() == '0' || this.$hinput.val() == '-1'))) {this.$list.find('li').addClass('es-visible').show();return true;}
		this.$list.find('li').removeClass('es-visible').hide();
        this.$list.find('li.es-add').html('<i class="fas fa-plus"></i> Quick Add'+((search!='')?' \''+text+'\'':''));
        this.$list.find('li.es-setup').html('<i class="fas fa-cog"></i> Set Up'+((search!='')?' \''+text+'\'':''));
        //console.log(search);
		if(search && this.$list.find('li:not(.no-filter)').filter(function (i, li) { return $(li).text().toLowerCase() === search}).length<=0){
            this.$list.find('li.no-filter').show().addClass('es-visible');
            forceShow = true;
		}
		//console.log("L" + this.data.length);
		if (this.options.filter) {
			found = this.data.filter(word => word.text.includes(search));
            //hiddens = this.$list.find('li:not(.no-filter)').filter(function (i, li) { return $(li).text().toLowerCase().indexOf(search) >= 0; }).show().addClass('es-visible');
			var temp;
            for (var i = 0; i < found.length; i++) {
				temp = that.$list.get(0).querySelector('li#'+found[i].id);
				if(temp==null) continue;
				//console.log(temp);
                temp.classList.add("es-visible");
                temp.style.display = 'block';
				//$('#'+this.id).show().addClass('es-visible');
				//var pid = $(this).data('parent-id');
				//while(pid > "0"){
                 //   pid = that.$list.find('li:not(.no-filter)[data-id="'+pid+'"]').show().addClass('es-visible').data('parent-id');
				//}
			};
			if (found.length == 0 && !forceShow) this.hide();
		}
	};
    EditableSelect.prototype.afterClone = function () {
    	alert("here")
	},
	EditableSelect.prototype.show = function () {
    	this.$list.css({
			top:   this.$input.position().top + this.$input.outerHeight() - 1,
			left:  this.$input.position().left,
			//width: this.$input.outerWidth()
			width: 250
		});
		//this.filter();
		if (!this.$list.is(':visible') && this.$list.find('li.es-visible').length > 0) {
			var fns = { default: 'show', fade: 'fadeIn', slide: 'slideDown' };
			var fn  = fns[this.options.effects];
			this.utility.trigger('show');
			this.$input.addClass('open');
			this.$list[fn](this.options.duration, $.proxy(this.utility.trigger, this.utility, 'shown'));
		}
	};
	EditableSelect.prototype.hide = function () {
        this.lastClicked = '';
		var fns = { default: 'hide', fade: 'fadeOut', slide: 'slideUp' };
		var fn  = fns[this.options.effects];
		this.utility.trigger('hide');
		this.$input.removeClass('open');
		this.$list[fn](this.options.duration, $.proxy(this.utility.trigger, this.utility, 'hidden'));
	};
	EditableSelect.prototype.select = function ($li) {
		if (!this.$list.has($li) || !$li.is('li.es-visible:not([disabled])')) return;
		var that = this;
		if($li.hasClass('es-add')){
			this.lastClicked = 'qa';
			var url='api/quickAdd';
			$.post(url, {
				'key' : this.$select.attr('key'),
				'value' : this.$input.val(),
				'type' : this.$select.attr('type'),
			}, function(result) {
				that.add(result.text, '-', [{name:"value", value:result.value}], null);
				that.select(that.$list.find('li:last'));
				that.$hinput.val(result.value);
			}, 'json');
			return;
		}
		if($li.hasClass('es-setup')){
            this.lastClicked = 'su';
			var modal = this.$select.attr('modal').split('|');
			JS.openDraggableModal(modal[0], 'add', null, null, {es_key: this.$select.attr('key'), es_value:this.$input.val(), url:modal[1]},
			[{event: 'postsubmit', function: $.proxy(that.addNSelect, that)}]);
			return;
		}
        this.lastClicked = '';
		var oldval = this.$input.val();
		this.$input.val($li.text());
		if (this.options.filter) this.hide();
		this.filter();
		this.utility.trigger('select', $li);
		this.$hinput.val($li.attr('value')).attr('text', $li.text());
        this.$input.attr('sel-value', $li.attr('value'));
        this.$input.trigger('change');
	};
    EditableSelect.prototype.selectDefault = function (value) {
    	if(value == null) return;
    	var $li = this.$list.find('li[value="'+value+'"]');
    	if ($li.length == 0) return;
        this.$input.val($li.text());
        //this.utility.trigger('select', $li);
        this.$hinput.val(value).attr('text', $li.text());
        this.$input.attr('sel-value', value);
        this.filter();
    };
	EditableSelect.prototype.add = function (text, index, attrs, data, isdata = true) {
		var li     = document.createElement("li");
		var option = document.createElement("option");
		var last    = this.buffer.childElementCount;
		li.innerHTML = text;
		option.text = text;
		if (isNaN(index)) index = last;
		else index = Math.min(index, last);
		if (index == 0) {
		  //this.$list.prepend(li);
		  //this.$select.prepend(option);
		} else {
		  //this.$list.find('li').eq(index - 1).after(li);
		  //this.$select.find('option').eq(index - 1).after(option);
		}
        //console.log(li);
		li = this.utility.setAttributes(li, attrs, data);
        if(isdata){
            li.id = 'option'+last;
            var obj={};
            obj.text = text.toLowerCase();
			obj.id = 'option'+last;
			this.data.push(obj);
			if(li.hasAttribute('selected') || this.$input[0].value == '') li.classList += ' es-visible'; else li.style.display = 'none';
        }
        //this.utility.setAttributes(option, attrs, data);
		return li;

	};
	EditableSelect.prototype.addNSelect = function (event, data) {
		this.add(data.es_text, '-', [{name:"value", value:data.id}], null);
		this.select(this.$list.find('li:last'));
	};
	EditableSelect.prototype.remove = function (index) {
		var last = this.$list.find('li').length;

		if (isNaN(index)) index = last;
		else index = Math.min(Math.max(0, index), last - 1);
		this.$list.find('li').eq(index).remove();
		this.$select.find('option').eq(index).remove();
		this.filter();
	};
	EditableSelect.prototype.clear = function () {
		this.$list.find('li').remove();
		this.$select.find('option').remove();
		this.filter();
	};
    EditableSelect.prototype.resetSelect = function (data) {
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
	EditableSelect.prototype.destroy = function () {
		this.$list.off('mousemove mousedown mouseup');
		this.$input.off('focus blur input keydown');
		this.$input.replaceWith(this.$select);
		this.$list.remove();
		this.$select.removeData('editable-select');
	};
	EditableSelect.prototype.onBlur = function () {
        if(this.$input.val().trim() == '') {
        	this.$hinput.val('');
            this.$input.attr('sel-value','');
        }
        if(this.$input.val().trim() != '' && this.data.filter(word => word.text == this.$input.val().toLowerCase().trim()).length <= 0 && this.lastClicked=='') this.utility.showError('Please select valid value'); else this.hide();
	};
	// Utility
	EditableSelectUtility = function (es) {
		this.es = es;
	}
	EditableSelectUtility.prototype.initialize = function () {
		var that = this;
		this.es.data = [];
        that.setAttributes(that.es.$input[0], that.es.$select[0].attributes, that.es.$select.data());
        that.es.$select.remove();
		that.es.$input.addClass('es-input').data('editable-select', that.es);
		var start=2;
        this.es.buffer = document.createDocumentFragment();

        if(that.es.$select.hasClass('quick-add')) that.es.buffer.appendChild(that.es.add('<i class="fas fa-plus"></i>Quick Add', start++, [{name:"class", value:"es-add no-filter"}], null, false));
        if(that.es.$select.hasClass('set-up')) that.es.buffer.appendChild(that.es.add('<i class="fas fa-cog"></i>Set Up', start++, [{name:"class", value:"es-setup no-filter last"}], null, false));
		var opts = that.es.$select[0].options;
		var selectedValue = false;
        for(var i=0; i<opts.length;i++){
        	that.es.buffer.appendChild(that.es.add(opts[i].text, 1, opts[i].attributes, opts[i].dataset));
            if (opts[i].hasAttribute('selected')) {
				that.es.$input.val(opts[i].text);
			}
		}
		/*that.es.$select.find('option').each(function (i, option) {
			that.es.buffer.appendChild(that.es.add(option.text, start+i, option.attributes, option.dataset));
			if (option.getAttribute('selected')) that.es.$input.val(option.text);
		});*/
		this.es.$list.prepend(this.es.buffer);
		if(that.es.$input.val()=='') this.es.$list.find('li:not(.no-filter)').addClass('es-visible');
		this.es.$list.find('li.no-filter').hide();
		if(this.es.saveCache)this.saveCache(this.es.options.cache);
        this.es.selectDefault(this.es.options.selected);
        if(that.es.options.ignore_first == true) that.es.filter(true);
		//that.es.filter();
        //console.log($.now()-this.es.start);

    };
	EditableSelectUtility.prototype.initializeList = function () {
		var that = this;
		that.es.$list
			.on('mousemove', 'li:not([disabled])', function () {
				that.es.$list.find('.selected').removeClass('selected');
				$(this).addClass('selected');
			})
			.on('mousedown', 'li', function (e) {
				//that.es.$input.off('blur');
				if ($(this).is('[disabled]')) e.preventDefault();
				else that.es.select($(this));
			})
			.on('mouseup', function () {
                that.es.$list.find('li.selected').removeClass('selected');
				//that.es.$input.on('blur', $.proxy(that.es.onBlur, that.es));
			});
	};
	EditableSelectUtility.prototype.initializeInput = function () {
		var that = this;
        switch (this.es.options.trigger) {
			default:
			case 'keydown':
                that.es.$input
					.on('keydown', function(e){if(e.keyCode < 37 || e.keyCode > 40) {$.proxy(that.es.show, that.es);}})
					.on('blur', $.proxy(that.es.onBlur, that.es))
					.on('click', function(){this.select();});
				break;
			case 'manual':
				break;
		}
		that.es.$input.on('input keydown', function (e) {
			switch (e.keyCode) {
				case 38: // Up
					var visibles = that.es.$list.find('li.es-visible:not([disabled])');
					var selectedIndex = visibles.index(visibles.filter('li.selected'));
					that.highlight(selectedIndex - 1, true);
					e.preventDefault();
					break;
				case 40: // Down
					var visibles = that.es.$list.find('li.es-visible:not([disabled])');
					var selectedIndex = visibles.index(visibles.filter('li.selected'));
					that.highlight(selectedIndex + 1, true);
					e.preventDefault();
					break;
				case 13: // Enter
					if (that.es.$list.is(':visible')) {
						that.es.select(that.es.$list.find('li.selected'));
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
		});
	};

	EditableSelectUtility.prototype.showError = function (text) {
		$('#error-modal .modal-body').text(text);
		that.es.$input.val('');
		var modal = $('#error-modal'), that = this;
		modal.modal({
			show : true
		});
		modal.on('hidden.bs.modal', function() {
			that.es.$input.focus();
		});
	};
	EditableSelectUtility.prototype.highlight = function (index, arrow = false) {
		var that = this;
		if(!arrow)that.es.show();
		setTimeout(function () {
			var visibles         = that.es.$list.find('li.es-visible');
			var oldSelected      = that.es.$list.find('li.selected').removeClass('selected');
			var oldSelectedIndex = visibles.index(oldSelected);

			if (visibles.length > 0) {
				var selectedIndex = (visibles.length + index) % visibles.length;
				var selected      = visibles.eq(selectedIndex);
				var top           = selected.position().top;

				selected.addClass('selected');
				if (selectedIndex < oldSelectedIndex && top < 0)
					that.es.$list.scrollTop(that.es.$list.scrollTop() + top);
				if (selectedIndex > oldSelectedIndex && top + selected.outerHeight() > that.es.$list.outerHeight())
					that.es.$list.scrollTop(that.es.$list.scrollTop() + selected.outerHeight() + 2 * (top - that.es.$list.outerHeight()));
			}
		});
	};
	EditableSelectUtility.prototype.setAttributes = function (element, attrs, data) {
		$.each(attrs || {}, function (i, attr) { element.setAttribute(attr.name, attr.value); });
		element.dataset = data;
		return element;
	};
	EditableSelectUtility.prototype.trigger = function (event) {
		var params = Array.prototype.slice.call(arguments, 1);
		var args   = [event + '.editable-select'];
		args.push(params);
		this.es.$select.trigger.apply(this.es.$select, args);
		this.es.$input.trigger.apply(this.es.$input, args);
	};
    EditableSelectUtility.prototype.initFromCache = function (cache) {
        var that = this;
    	this.es.data = ESC.getCacheData(cache);
        this.es.$list = ESC.getCacheUl(cache).clone();
        this.es.$list.appendTo(this.es.options.appendTo || this.es.$input.parent());
        that.setAttributes(that.es.$input[0], that.es.$select[0].attributes, that.es.$select.data());
        that.es.$select.remove();
        that.es.$input.addClass('es-input').data('editable-select', that.es);

        if(that.es.$input.val()=='') this.es.$list.find('li:not(.no-filter)').addClass('es-visible');
        this.es.$list.find('li.no-filter').hide();
        this.es.selectDefault(this.es.options.selected);
        if(this.es.options.ignore_first == true) that.es.filter(true);
    };
    EditableSelectUtility.prototype.saveCache = function (cache) {
    	var clone = this.es.$list.clone();
			clone.find('li:not(.no-filter)').addClass('es-visible');
        ESC.setCacheData(cache, this.es.data);
        ESC.setCacheUl(cache, clone);
    };

	// Plugin
	Plugin = function (option) {
		var args = Array.prototype.slice.call(arguments, 1);
		this.each(function () {
			var $this   = $(this);
			var data    = $this.data('editable-select');
			var options = $.extend({}, EditableSelect.DEFAULTS, $this.data(), typeof option == 'object' && option);
			if (!data) data = new EditableSelect(this, options);
			if (typeof option == 'string') data[option].apply(data, args);
		});
	}
	$.fn.editableSelect             = Plugin;
	$.fn.editableSelect.Constructor = EditableSelect;

})(jQuery);

EditableSelectCache = function () {
	this.data = {};
	this.ul = {};
};
EditableSelectCache.prototype = {
	exists: function(name){
		return name in this.data && name in this.ul;
	},

	getCacheData: function(name) {
		if(this.exists(name)) return this.data[name]; else return [];
	},

    getCacheUl: function(name) {
        if(this.exists(name)) return this.ul[name]; else return '';
    },

	setCacheData: function(name, data) {
		this.data[name] = data;
	},

    setCacheUl: function(name, data) {
        this.ul[name] = data;
    }
};
var ESC;
$(document).ready(function(){
    ESC = new EditableSelectCache();
})