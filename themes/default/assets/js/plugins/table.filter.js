+(function ($) {
	
	TableFilter = function (table, search, options) {
		var that = this;
		this.search = table.closest(options.parentClass).find(search);
		this.table = table;
		this.options = options;
		//this.data = this.initData();
		console.log(this.search);			
		this.search.on('input', function(e){
			that.filter($(this).val());
		});		
	};
	TableFilter.prototype.filter = function (search) {
		this.table.find('tr'+this.options.trClass).hide();
		this.table.find('tr').filter(function (i, tr) { return $(tr).text().toLowerCase().indexOf(search) >=0}).show();		
	};
	TableFilter.prototype.initData = function () {
		var that = this;
		var result = [];
		var ind = 0;
		this.table.find('tr'+this.options.trClass).each(function(){
			var line = '';
			$(this).find('td').each(function(){line=line+this.text()+'|';});
			result[ind++] = line;
		}); 
		return result;
	};	
	
	// Plugin
	Plugin = function (search, option) {
		var args = Array.prototype.slice.call(arguments, 1);
		return this.each(function () {
			var $this   = $(this);
			var data    = $this.data('filterTable');			
			var options = typeof option == 'object' && option;			
			if (!data) data = new TableFilter($this, search, options);			
		});
	}
	$.fn.filterTable             = Plugin;
	$.fn.filterTable.Constructor = TableFilter;
	
})(jQuery);