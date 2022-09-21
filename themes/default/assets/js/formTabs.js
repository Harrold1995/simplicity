var formTabs = function() {
	//$ = jQuery.noConflict();
};
/** class methods **/
formTabs.prototype = {

    editTabTRs: function(){
            //     $('body').on('dblclick', '.editTabTRs', function (event) {
            //     var row = $(this);
            //         console.log("tab d clicked");
            //         console.log($(row).closest('tr'));
            //         var tds = $(row).find('td');
            //         var newRow = "";
            //         tds.each(function(){
            //                  td = $(this);
            //             var value = $(td).text();
            //             var name = $(td).find('input').attr('name');
            //             console.log(value);
            //             console.log(name);
            //             newRow += `<td><input name="`+ name +`" value="`+ value +`"/></td>`;
            //         })
            //         $(row).closest('tr').empty();
            //         $(row).closest('tr').html(newRow);
            //    });



                        //    $('body').on('dblclick', '.editTabTRs', function (event) {
                        //     var row = $(this);
                        //     var values = [];
                        //     var names = [];
                        //         var tds = $(row).find('td');
                        //         var firstRow = $(row).closest('tbody').find('tr:first-child');
                        //         console.log(firstRow);
                        //         var newRow = "<tr>";
                        //         tds.each(function(){
                        //                  td = $(this);
                        //              values.push($(td).text());
                        //              names.push($(td).find('input').attr('name'));
                        //             //newRow += `<td><input name="`+ name +`" value="`+ value +`"/></td>`;
                        //         })
                        //          firstRow;
                        //         var clonedRowTds = firstRow.find('td');
                        //         for(var i =0; i < clonedRowTds.length; i++){
                        //             var newTd = $(clonedRowTds[i]).clone();
                        //             newRow += `<td>`+newTd+`</td>`;
                        //             //clonedRowTds.find('input').val(values[i]);
                        //             console.log(newRow);
                        //         }
                        //         newRow += "</tr>";
                        //         $(row).closest('tr').empty();
                        //         $(row).closest('tr').html(newRow);
                        //         //$(row).closest('tr').find('.editable-select').editableSelect();
                        //    });
                        
                        $('body').on('dblclick', '.editTabTRs', function (event) {
                            var datePicker = false;
                            var row = $(this);
                            if(row.hasClass('inCourt')){
                                var profilesOnLease = row.closest('.modal').find('#tenants_on_lease').val();
                            }
                                console.log("tab d clicked");
                                console.log($(row).closest('tr'));
                                var firstRow = $(row).closest('tbody').find('tr:first-child');

                                var tds = $(row).find('td');
                                var newRow = "";
                                var rowId = $(row).find('input:first').val();
                                var rowName = $(row).find('input:first').attr('name');
                                newRow += `<input type="hidden" name="`+ rowName +`" value="`+ rowId +`"/>`;
                                for(var j =0; j < tds.length -1; j++){
                                    var test = $(firstRow).find('td');
                                    //for editable selects
                                    if($(test[j]).find('input:first').hasClass('editable-select')){
                                        //console.log($(tds[j]).text());
                                            td = tds[j];
                                            var esvalue = $(td).find('input').val();
                                            var esname = $(td).find('input').attr('name');
                                            var id = $(test[j]).find('input').attr("id");
                                             console.log("id" + id);
                                             console.log("name" + esname);
                                             console.log("value" + esvalue);
                                             if(id == 'tenants_on_lease_name'){
                                                var aa = formTabs.tenants_on_lease_nameTd(id, esvalue, esname, profilesOnLease);
                                             }else{
                                                var aa = formTabs.whichTd(id, esvalue, esname);
                                             }
                                             newRow += aa;
                                    }else{
                                        //for checkbox
                                        if($(test[j]).find('ul').hasClass('check-a')){
                                            td = tds[j];
                                            var ulname = $(td).find('input').attr('name');
                                            var ulfor = $(td).find('label').attr("for");
                                            console.log(ulname);
                                            var ul = "";
                                            ul += '<td><ul class="check-a a"><li><label for="'+ ulfor +'" class="checkbox';
                                                        ul += ($(td).find('label').hasClass('active')) ? " active" : ""; 
                                                        ul += '"><input type="hidden" id="'+ ulfor +'" name="'+ ulname +'" value="0" /><input type="checkbox" value="1"';
                                                        ul += ($(td).find('label').hasClass('active')) ? " checked" : ""; 
                                                        ul +=' id="'+ ulfor +'" name="'+ ulname +'" class="hidden" aria-hidden="true"><div class="input"></div></label></li></ul>';
                                                       ul += '</td>';
                                                       newRow += ul;
                                        }else{
                                            //for dates
                                            if($(test[j]).hasClass('date-picker')){
                                                td = tds[j];
                                                var datevalue = $(td).find('input').val();
                                                var datename = $(td).find('input').attr('name');$( "em" ).attr( "title" );
                                                var datenfor = $(test[j]).find('label').attr("for");
                                                newRow += `<td>
                                                            <label for="`+ datenfor +`"></label>
                                                            <input data-toggle="datepicker" id="`+ datenfor +`" name="`+ datename +`" value="`+ datevalue +`">
                                                          </td>`;
                                                          datePicker = true;
                                            }else{
                                                //for all other inputs
                                                td = tds[j];
                                                var type = $(test[j]).find('input').attr('type');                                               
                                                var value = $(td).text();
                                                var name = $(td).find('input').attr('name');
                                                console.log(value);
                                                console.log(name);
                                                newRow += `<td><input type="`+ type +`" name="`+ name +`" value="`+ value +`"/></td>`;
                                            }
                                        }
                                    }
                                    
                                }
                                newRow += `<td class ="text-center"><a href="" class="text-center link-icon delete-row"><i class="icon-x"></i></a></td></tr>`;
                                $(row).closest('tr').empty();
                                $(row).closest('tr').html(newRow);
                                $(row).closest('tr').find('.editable-select').editableSelect();
                                JS.checkboxes();
                                $(row).removeClass( "editTabTRs");
                                if(datePicker){JS.datePickerInit($(row).closest('tr'));}
                           });
                           //used for property key codes
                           $('body').on('dblclick', '.editTabTRs2', function (event) {
                                var row = $(this);
                                //console.log($(row).closest('tr'));
                                var rowId = $(row).find('#id').val();
                                var key_codeTd = $(row).find('#key_code').closest('td');
                                var areaTd = $(row).find('#area').closest('td');
                                var key_code_input = '';
                                if(rowId){
                                    $(row).find('#id').attr('name', 'key_codes[' + rowId +'][id]');
                                     key_code_input = `<input type="text" name="key_codes[` + rowId +`][key_code]" value="`+ key_codeTd.find('#key_code').val() +`"/>`;
                                     area_input = `<input type="text" name="key_codes[` + rowId +`][area]" value="`+ areaTd.find('#area').val() +`"/>`;
                                }else{
                                     key_code_input = $(row).find('#key_code').attr("type","text");
                                     area_input = $(row).find('#area').attr("type","text");
                                }
                                key_codeTd.empty().html(key_code_input);
                                areaTd.empty().html(area_input);
                                $(row).removeClass( "editTabTRs2");
                           });
                            //used for property owners
                            $('body').on('dblclick', '.editOwner', function (event) {
                                var row = $(this);
                                var rowId = $(row).attr('id');
                                var profile_id_td = $(row).find('#profile_id').closest('td');
                                var percentage_td = $(row).find('#percentage').closest('td');
                                var profile_id_input = '';
                                var percentage_input = '';
                                if(rowId){
                                    //$(row).find('#id').attr('name', 'owners[' + rowId +'][id]');
                                     percentage_input = `<input type="text" name="owners[` + rowId +`][percentage]" value="`+ percentage_td.find('#percentage').val() +`"/>`;
                                }else{
                                     percentage_input = $(row).find('#percentage').attr("type","text");
                                }
                                              profile_id_input =`<span class="select">
                                                <label for="profile_id" class="hidden">Label</label>
                                                <select stype="profile" class="fastEditableSelect" key="profiles.first_name" modal="tenant" default="`+ profile_id_td.find('#profile_id').val() +`" id="profile_id" name="`+ profile_id_td.find('#profile_id').attr('name') +`">
                                                </select>
                                                </span>`;
                                profile_id_td.empty().html(profile_id_input);
                                percentage_td.removeClass( "dt-percentage").empty().html(percentage_input);
                                $(row).removeClass( "editOwner");
                                $(row).find('.fastEditableSelect').fastSelect();
                                $(row).addClass('changingOwner');
                        });
                        //edit owner pulls up owner info
                        $('body').on('change', '.changingOwner', function (event) {
                            var row = $(this);
                            var profile_id =$(row).find('#profile_id').closest('.select').find('input[type=hidden]').val();
                            row.find('.dt-email').empty();
                            row.find('.dt-phone').empty();
                            row.find('.ownerAddress').empty();
                            console.log('changingOwner');
                                    $.post('tableapi/getProfileInfo/'+profile_id, function(data){
                                        //console.log(data);
                                        row.find('.dt-email').html(data.email);
                                        row.find('.dt-phone').html(data.phone);
                                        row.find('.ownerAddress').html(data.address_line_1 + ' ' + data.address_line_2)
                                    }, 'JSON')
                        })
        },
        
        whichTd: function (tdId, value, trId){

            switch (tdId) {
                case 'frequency' :
                    var frequenciesNewRow = '';
                    frequenciesNewRow += `<td style=" text-align:center">
                                        <span style="width:96px;" class="select">
                                            <select class=" editable-select quick-add set-up " id="frequency" name="`+trId+`"  modal="" type="table" key=""
                                                    >
                                                    <option value="-1" selected ></option>`
                                                for (var j = 0; j < frequencies.length; j++) {
                                                    frequenciesNewRow += `<option value='` + frequencies[j].id + `'`;
                                                        if(value == frequencies[j].id){ frequenciesNewRow += 'selected'};
                                                        frequenciesNewRow +=`>` + frequencies[j].name + `</option>`;
                                                }
                                                frequenciesNewRow +=`	</select>
                                            </span>
                                    </td>`;
                    return frequenciesNewRow;
                case 'account_id':
                        var accountNewRow = '';
                        accountNewRow +=  `<td style=" text-align:center" id="" class="">
                        <span style="width:96px;" class="select">
                            <select class=" editable-select quick-add set-up "  id="`+trId+`" name="`+trId+`"  modal="" type="table" key="">
                                <option value="-1" selected ></option>`
                            for (var a = 0; a < subexpenseAccounts.length; a++) {
                                accountNewRow += `<option value='` + subexpenseAccounts[a].id + `'`;
                                if(value == subexpenseAccounts[a].id){ accountNewRow += 'selected'};
                                accountNewRow += `>` + subexpenseAccounts[a].name + `</option>`;
                            }
                            accountNewRow +=`	</select>
                            </span>
                    </td>`;
                    return accountNewRow;
                case 'unit_id':
                        var unitNewRow = '';
                        unitNewRow +=  `<td style="text-align:center">
                        <span style="width:96px;" class="select">
                            <select class="w135 editable-select quick-add set-up "  id="unit_id" name="`+trId+`"  modal="" type="table" key="">
                                <option value="-1" selected ></option>`
                            for (var a = 0; a < units.length; a++) {
                                unitNewRow += `<option value='` + units[a].id + `'`;
                                if(value == units[a].id){ unitNewRow += 'selected'};
                                unitNewRow += `>` + units[a].name + `</option>`;
                            }
                            unitNewRow +=`	</select>
                            </span>
                    </td>`;
                    return unitNewRow;
                case 'item_id':
                    var classNewRow = '<td>' + value +'</td>';
                    var profileNewRow = '';
                    profileNewRow +=  `<td style="text-align:center">
                                        <span style="width:96px;" class="select">
                                            <select class=" editable-select quick-add set-up "  id="item_id" name="`+trId+`"  modal="" type="table" key="">
                                                <option value="-1" selected ></option>`
                                            for (var a = 0; a < items.length; a++) {
                                                profileNewRow += `<option value='` + items[a].id + `'`;
                                                if(value == items[a].id){ profileNewRow += 'selected'};
                                                profileNewRow += `>` + items[a].item_name + `</option>`;
                                            }
                                            profileNewRow +=`	</select>
                                            </span>
                                    </td>`;
                    return profileNewRow;
                case 'vendor':
                case 'payee':
                case 'broker':
                    var profileNewRow = '';
                    profileNewRow +=  `<td style="text-align:center">
                                        <span style="width:96px;" class="select">
                                            <select class=" editable-select quick-add set-up "  id="`+trId+`" name="`+trId+`"  modal="" type="table" key="">
                                                <option value="-1" selected ></option>`
                                            for (var a = 0; a < vendors.length; a++) {
                                                profileNewRow += `<option value='` + vendors[a].id + `'`;
                                                if(value == vendors[a].id){ profileNewRow += 'selected'};
                                                profileNewRow += `>` + vendors[a].vendor + `</option>`;
                                            }
                                            profileNewRow +=`	</select>
                                            </span>
                                    </td>`;
                    return profileNewRow;
                    case 'utility_type':
                    var utility_typesNewRow = '';
                    utility_typesNewRow +=  `<td style="text-align:center">
                                        <span style="width:96px;" class="select">
                                            <select class=" editable-select quick-add set-up "  id="`+trId+`" name="`+trId+`"  modal="" type="table" key="">
                                                <option value="-1" selected ></option>`
                                            for (var a = 0; a < utilityTypes.length; a++) {
                                                utility_typesNewRow += `<option value='` + utilityTypes[a].id + `'`;
                                                if(value == utilityTypes[a].id){ utility_typesNewRow += 'selected'};
                                                utility_typesNewRow += `>` + utilityTypes[a].name + `</option>`;
                                            }
                                            utility_typesNewRow +=`	</select>
                                            </span>
                                    </td>`;
                    return utility_typesNewRow;
     
                case 'default_expense_acct':
                    var default_expense_acctNewRow = '';
                    default_expense_acctNewRow +=  `<td style="text-align:center">
                                        <span style="width:96px;" class="select">
                                            <select class=" editable-select quick-add set-up "  id="`+trId+`" name="`+trId+`"  modal="" type="table" key="">
                                                <option value="-1" selected ></option>`
                                            for (var a = 0; a < subaccounts.length; a++) {
                                                default_expense_acctNewRow += `<option value='` + subaccounts[a].id + `'`;
                                                if(value == subaccounts[a].id){ default_expense_acctNewRow += 'selected'};
                                                default_expense_acctNewRow += `>` + subaccounts[a].name + `</option>`;
                                            }
                                            default_expense_acctNewRow +=`	</select>
                                            </span>
                                    </td>`;
                    return default_expense_acctNewRow;


                    case 'profile_id':
                    var nameNewRow = '';
                    nameNewRow +=  `<td style="text-align:center">
                                        <span style="width:96px;" class="select">
                                            <select class=" editable-select quick-add set-up "  id="`+trId+`" name="`+trId+`"  modal="" type="table" key="">
                                                <option value="-1" selected ></option>`
                                            for (var a = 0; a < tenants_on_lease.length; a++) {
                                                nameNewRow += `<option value='` + tenants_on_lease[a].id + `'`;
                                                if(value == tenants_on_lease[a].id){ nameNewRow += 'selected'};
                                                nameNewRow += `>` + tenants_on_lease[a].name + `</option>`;
                                            }
                                            nameNewRow +=`	</select>
                                            </span>
                                    </td>`;
                    return nameNewRow;

                    case 'item_type_id':
                    var item_type_idNewRow = '';
                    item_type_idNewRow +=  `<td style="text-align:center">
                                        <span style="width:96px;" class="select">
                                            <select class=" editable-select quick-add set-up "  id="`+trId+`" name="`+trId+`"  modal="" type="table" key="">
                                                <option value="-1" selected ></option>`
                                            for (var a = 0; a < auto_charges_items.length; a++) {
                                                item_type_idNewRow += `<option value='` + auto_charges_items[a].id + `'`;
                                                if(value == auto_charges_items[a].id){ item_type_idNewRow += 'selected'};
                                                item_type_idNewRow += `>` + auto_charges_items[a].name + `</option>`;
                                            }
                                            item_type_idNewRow +=`	</select>
                                            </span>
                                    </td>`;
                    return item_type_idNewRow;

                    case 'paid_by':
                    var paid_byNewRow = '';
                    paid_byNewRow +=  `<td style="text-align:center">
                                        <span style="width:96px;" class="select">
                                            <select class=" editable-select quick-add set-up "  id="`+trId+`" name="`+trId+`"  modal="" type="table" key="">
                                                <option value="-1" selected ></option>`
                                            for (var a = 0; a < paid_by_types.length; a++) {
                                                paid_byNewRow += `<option value='` + paid_by_types[a].id + `'`;
                                                if(value == paid_by_types[a].id){ paid_byNewRow += 'selected'};
                                                paid_byNewRow += `>` + paid_by_types[a].name + `</option>`;
                                            }
                                            paid_byNewRow +=`	</select>
                                            </span>
                                    </td>`;
                    return paid_byNewRow;
                    
                    case 'payment_acct':
                    var payment_acctNewRow = '';
                    payment_acctNewRow +=  `<td style="text-align:center">
                                        <span style="width:96px;" class="select">
                                            <select class=" editable-select quick-add set-up "  id="`+trId+`" name="`+trId+`"  modal="" type="table" key="">
                                                <option value="-1" selected ></option>`
                                            for (var a = 0; a < bankAccounts.length; a++) {
                                                payment_acctNewRow += `<option value='` + bankAccounts[a].id + `'`;
                                                if(value == bankAccounts[a].id){ payment_acctNewRow += 'selected'};
                                                payment_acctNewRow += `>` + bankAccounts[a].name + `</option>`;
                                            }
                                            payment_acctNewRow +=`	</select>
                                            </span>
                                    </td>`;
                    return payment_acctNewRow;

            }
            
        },
        tenants_on_lease_nameTd: function(tdId, value, trId, profilesOnLease){
            var profilesOnLease = JSON.parse(profilesOnLease);
            var profileNewRow = '';
            profileNewRow +=  `<td style="text-align:center">
                                <span class="select">
                                    <select class=" editable-select quick-add set-up "  id="`+trId+`" name="`+trId+`"  modal="" type="table" key="">
                                        <option value="-1" selected ></option>`
                                    for (var a = 0; a < profilesOnLease.length; a++) {
                                        profileNewRow += `<option value='` + profilesOnLease[a].id + `'`;
                                        if(value == profilesOnLease[a].id){ profileNewRow += 'selected'};
                                        profileNewRow += `>` + profilesOnLease[a].name + `</option>`;
                                    }
                                    profileNewRow +=`	</select>
                                    </span>
                            </td>`;
            return profileNewRow;
        }
        		


};

var formTabs = new formTabs();
$(document).ready(function () {
    formTabs.editTabTRs();

});