var Formatter = function() {
	//$ = jQuery.noConflict();
};
/** class methods **/
Formatter.prototype = {
	addDTRowFromId : function(table, data){
		var dt = table.DataTable();
		var row = dt.row.add(data.data).draw().node();
		if($(row).closest('table').attr('id') == '#key_codestable'){
			$(row).addClass("editTabTRs2");
		}else{$(row).addClass("editTabTRs");}
		$(row).find('td').each(function(i, cell){			
			$(cell).addClass(data.styles[i]);
		});
	},
	addDTRow : function(table, data, type){
		var dt = table.DataTable();
		var row = dt.row.add(data).draw();
		
		row.nodes().to$().attr('data-mode', 'edit').attr('data-parent', 'true').attr('data-type', type);
        $(row.node()).find('td').each(function(i, cell){
            $(cell).addClass('text-center');
        });
	},
	editDTRow : function(table, row, data){
		//console.log(table);
		var dt = table.DataTable();
		dt.row(row).data(data).draw();
	},
	parseRow : function(type, form, count){
		var result=[], data = Formatter.formToJson(form);
		var fields=Formatter.formToFields(form, count, type);
			
		if(type == "unit"){
			if(data.id==null)fields+="<input type='hidden' class='tid' name='units["+count+"][tid]' value='t"+count+"' unit='"+data.name+"'/>";
            result[0]='<i class=\'icon-door\'></i>';
			result[1]=data.name;
			result[2]=data.floor;
			result[3]=data.unit_type_id_text;
			result[4]=data.sq_ft;
			result[5]=data.memo+fields;
			result[6]=data.market_rent;
			result[7]=data.status_text;
			result[8]='<a href="#" class="delete-row mr-auto"><i class="icon-x"></i></a>';
		}else
		if(type == "tenanttolease"){
			console.log(data);
			result[0]='<i class="icon-user" aria-hidden="true"></i>'
			result[1]=number_format(data.amount.replace(',', ''));
			result[2]=data.profile_id_text;
			result[3]=number_format(data.deposit);
            result[4]=number_format(data.last_month);
			result[5]=data.unit_id_text;
			result[6]=data.move_in;
			result[7]=data.move_out;
			result[8]=data.memo+fields;
			result[9]='<a class="link-icon"><i class="icon-x" aria-hidden="true"></i> </a>'
		}
		return result;
	},
	formToJson : function(form) {
		var obj = {};
		var elements = form.querySelectorAll( "input, select, textarea" );
		for( var i = 0; i < elements.length; ++i ) {
			var element = elements[i];
			var name = element.name;
			var value = element.value;
			if( name ) {
				obj[ name ] = value;
				if(element.type=='select-one')
				obj[ name+"_text" ] = element.options[element.selectedIndex].text;
				else if($(element).is('[text]')) {
					console.log($(element));
                    obj[name + "_text"] = $(element).prev().prev('input').val();
                }else
				if($(element).attr('text'))
				obj[ name+"_text" ] = $(element).attr('text');
			}
		}
		return obj;
	},
	formToFields : function(form, count, type) {
		var obj = "";
		var elements = form.querySelectorAll( "input, select, textarea, checkbox" );	
		var name
		for( var i = 0; i < elements.length; ++i ) {
			var element = elements[i];
			var name = element.name;
			var value = element.value;
			if( name ) {
				if(element.type=='checkbox')
				obj+="<input type='hidden' name='"+type+"s["+count+"]["+name+"]' value='"+(element.checked ? 1 : 0 )+"'/>";
				else
				obj+="<input type='hidden' name='"+type+"s["+count+"]["+name+"]' value='"+value+"'/>";							
			}			
		}
		obj+="<input type='hidden' class='serialized' value='"+$(form).serialize()+"'/>";		
		return obj;
	},
	getRowFields : function(row) {	
		var data = row.find('select, textarea, input, li label input').serialize();
		row.find('select, textarea, input').val('').removeAttr('checked').removeAttr('selected').closest('label').removeClass("active");
		row.find('li label input').val('1');
		return data;
	},
    DTDetailsFormat : function ( type, d ) {
    	var result='';
    	switch(type) {
			case 'transaction':
				result += '<table class="row-details">';
				$.each(d, function(i, v){
                    result += '<tr><td>'+v.type+'</td><td>'+v.amount+'</td><td>'+v.name+'</td></tr>';
				});
			break;
		}
		result += '</table>';
    	return result;
	}
};

var Formatter = new Formatter();
