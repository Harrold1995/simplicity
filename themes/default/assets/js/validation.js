var Validation = function() {
	//$ = jQuery.noConflict();
};
/** class methods **/
Validation.prototype = {

validation: function(form, data,specialType = null){
    switch (form) {
        case 'journalEntry' :
            return Validation.journalEntryValidation(data);
        case 'property' :
            return Validation.propertyValidation(data);
        case 'unit' :
            return Validation.unitValidation(data);
        case 'lease' :
            return Validation.leaseValidation(data);
        case 'tenant' :
            return Validation.tenantValidation(data);
        case 'bill' :
            return Validation.billValidation(data);
        case 'vendor' :
            return Validation.vendorValidation(data);
        case 'inventory' :
            return Validation.inventoryValidation(data);
        case 'account' :
            return Validation.accountValidation(data, specialType);
        case 'reconciliations' :
            return Validation.reconciliationsValidation(data);
        case 'receivePayments' :
            return Validation.receivePaymentsValidation(data);
        case 'check' :
            return Validation.checkValidation(data);
        case 'settings' :
            return Validation.settingsValidation(data);
        case 'AddNoteform' :
            return Validation.AddNoteValidation(data);
        case 'timesheet' :
            return Validation.timesheetValidation(data);
    }

},
journalEntryValidation: function(data){
    var confirmed = {bool:true, msg:""},
     message = "";
    date = data.find('#transaction_date'),
    ref = data.find('#transaction_ref');
    // property = data.find('#property_id'),
    // unit = data.find('#unit_id');
            //check total
            // var JETotals =  calcTotal(data.find('#journalEntryBody'));
            // if(JETotals[0] !== JETotals[1]){
            //     message += "Totals don't match.<br> ";
            //     confirmed = {bool:false, msg:message};
            // }
            //check if date set
             if( date[0].value == ""){
                 message += "No date.<br>";
                 confirmed = {bool:false, msg:message};
            }
            //check if reference set
             if( ref[0].value == ""){
                 message += "No reference.";
                 confirmed = {bool:false, msg:message};
            }
            // else if( unit[0].value != "" && property[0].value == ""){
            //     return confirmed = {bool:false, msg:"No property."};
            // }

        return confirmed;
},
propertyValidation: function(data){
    var confirmed = {bool:true, msg:""};
//     message = "";
//     var formData = [data.find('#name'),data.find('#address'),
//     data.find('#city'), data.find('#state'), data.find('#zip')]; 
//     for (var i = 0; i < formData.length; i++) {
//         if( formData[i][0].value == ""){
//             console.log(formData[i][0]['id']);
//             message += "No " + formData[i][0]['id'] + ".<br>";
//             confirmed = {bool:false, msg:message};
//        }
//   }
    return confirmed;
},
unitValidation: function(data){
    var confirmed = {bool:true, msg:""};
    message = "";
    var property = data.find('#property_id'),
        unit_name = data.find('#name');
    if(property[0].value ==""){
        message += "No property selected.<br>";
        confirmed = {bool:false, msg:message};
   }
   if(unit_name[0].value == ""){
       message += "No unit name.<br>";
       confirmed = {bool:false, msg:message};
  }
    return confirmed;
},
leaseValidation: function(data){
    var confirmed = {bool:true, msg:""};
    message = "";
    var start = data.find('#start'),
        end = data.find('#end'),
        moveIn = data.find('#move_in'),
        moveOut = data.find('#move_out');        
        if( new Date(start[0].value) >= new Date(end[0].value)){
            message += "Incorrect dates.";
            confirmed = {bool:false, msg:message};
       }
       if( new Date(moveIn[0].value) > new Date(moveOut[0].value)){// should probably be >= like by start
        message += "Incorrect dates.";
        confirmed = {bool:false, msg:message};
        }
    //    //check if property set
    //    if( property[0].value == ""){
    //     message += "No property selected.<br>";
    //     confirmed = {bool:false, msg:message};
    //     }
    //     //check if unit set
    //         if( unit[0].value == ""){
    //             message += "No unit selected.";
    //             confirmed = {bool:false, msg:message};
    //     }
  
    return confirmed;
},
tenantValidation: function(data){
    var confirmed = {bool:true, msg:""};
    message = "";
    var first_name = data.find('#first_name'),
        last_name = data.find('#last_name');
    if(first_name[0].value == ""){
        message += "No first name.<br>";
        confirmed = {bool:false, msg:message};
   }
   if(last_name[0].value == ""){
       message += "No last name.<br>";
       confirmed = {bool:false, msg:message};
  }
    return confirmed;
},
billValidation: function(){
    var confirmed = {bool:true, msg:""};
    return confirmed;
},
vendorValidation: function(data){
    var confirmed = {bool:true, msg:""};
    message = "";
    var first_name = data.find('#first_name'),
        last_name = data.find('#last_name');
    if(first_name[0].value == ""){
        message += "No first name.<br>";
        confirmed = {bool:false, msg:message};
   }
   if(last_name[0].value == ""){
       message += "No last name.<br>";
       confirmed = {bool:false, msg:message};
  }
    return confirmed;
},
inventoryValidation: function(data){
    var confirmed = {bool:true, msg:""};
    message = "";
    var formData = [data.find('#name'),data.find('#type')]; 
  for (var i = 0; i < formData.length; i++) {
      if( formData[i][0].value == ""){
          console.log(formData[i][0]['id']);
          message += "No item " + formData[i][0]['id'] + ".<br>";
          confirmed = {bool:false, msg:message};
     }
}
//     var name = data.find('#name'),
//         type = data.find('#type');
//     if(name[0].value == ""){
//         message += "No item name.<br>";
//         confirmed = {bool:false, msg:message};
//    }
//    if(type[0].value == ""){
//        message += "No item type.<br>";
//        confirmed = {bool:false, msg:message};
//   }
    return confirmed;
},
accountValidation: function(data, specialType){
    var confirmed = {bool:true, msg:""};
    message = "";
        var name = data.find('#name'),
            type = data.find('#account_types_id'),
            account_number = data.find('#accno');
        if(name[0].value == ""){
            message += "No account name.<br>";
            confirmed = {bool:false, msg:message};
        }
        if(type[0].value == ""){
            message += "No account type.<br>";
            confirmed = {bool:false, msg:message};
        }
        if(account_number[0].value == ""){
            message += "No account number.<br>";
            confirmed = {bool:false, msg:message};
        }
    // Mortgages

        if(specialType == "Bank"){
            var routing = data.find('#routing'),
                bankName = data.find('#vendor');
            if( routing[0] && bankName[0]){
                if( routing[0].value.length !== 9){
                    message += "Invalid routing number!<br>";
                    confirmed = {bool:false, msg:message};
                }
                if( bankName[0].value.length == ""){
                    message += "No Bank selected!<br>";
                    confirmed = {bool:false, msg:message};
                }
            }else{
                message += "No bank or routing number!";
                confirmed = {bool:false, msg:message};
            }
        }
        if(specialType == "Credit Card"){
            var  cc_num = data.find('#cc_num'),
                 expiration = data.find('#expiration');
                /* if( cc_num[0] && expiration[0]){
                    if( cc_num[0].value.length !== 16){
                        message += "Invalid credit card number!<br>";
                        confirmed = {bool:false, msg:message};
                    }
                    if( expiration[0].value.length < 3 || expiration[0].value.length > 4){
                        message += "Invalid expiration number!";
                        confirmed = {bool:false, msg:message};
                        }
                }else{
                    message += "No credit card or expiration number.";
                    confirmed = {bool:false, msg:message};
                } */
            }
        
    return confirmed;
},
reconciliationsValidation: function(){
    var confirmed = {bool:true, msg:""};
    return confirmed;
},
receivePaymentsValidation: function(){
    var confirmed = {bool:true, msg:""};
    return confirmed;
},
checkValidation: function(data){
    var confirmed = {bool:true, msg:""},
    message = "";
    // var formData = [data.find('#checkDate'),data.find('#check_num'),
    // data.find('#credit'), data.find('#property_id')]; 
    // for (var i = 0; i < formData.length; i++) {
    //           if( formData[i][0].value == ""){
    //               console.log(formData[i][0]['id']);
    //               message += "No " + formData[i][0]['id'] + ".<br>";
    //               confirmed = {bool:false, msg:message};
    //          }
    //     }
    var property = data.find('#property_id'),
        amount = data.find('#credit'),
        date = data.find('#checkDate');
        //check_num = data.find('#check_num');
                if(property[0].value == ""){
                    message += "No property chosen.<br> ";
                    confirmed = {bool:false, msg:message};
                }
                if( date[0].value == ""){
                     message += "No date.<br>";
                     confirmed = {bool:false, msg:message};
                }
                //  if( amount[0].value < 1){
                //      message += "No amount.<br>";
                //      confirmed = {bool:false, msg:message};
                // }
            //     if( check_num[0].value == ""){
            //         message += "No check number.<br>";
            //         confirmed = {bool:false, msg:message};
            //    }
              confirmed = Validation.dateChecker(date[0].value, confirmed);
    return confirmed;
    },
    settingsValidation: function(){
        var confirmed = {bool:true, msg:""};
        return confirmed;
    },
    AddNoteValidation: function(){
        var confirmed = {bool:true, msg:""};
        return confirmed;
    },
    timesheetValidation: function(){
        var confirmed = {bool:true, msg:""};
        return confirmed;
    },
    dateChecker: function(userTransactionDate ,oldConfirm){
        if(userTransactionDate != ""){
                var todayDate = new Date();
                //var oldDate = date[0].value;
                    if(( new Date(todayDate) - new Date(userTransactionDate)) / (1000 * 3600 * 24 * 365) > 1){
                        if (confirm("Date is more than a year old.")) {
                            //txt = "You pressed OK!";
                            //console.log(txt);
                            confirmed += {bool:true, msg:""};
                            return confirmed;
                        } else {
                            confirmed = {leave:true};
                            //txt = "You pressed cancel!";
                            //console.log(txt);
                            return confirmed;
                        }
                    }
                    var inAYearDate = new Date(new Date().setFullYear(new Date().getFullYear() + 1));
                    if( new Date(userTransactionDate) > inAYearDate){
                        if (confirm("Date is in more than a year. Are you sure you want to continue?")) {
                            confirmed += {bool:true, msg:""};
                            return confirmed;
                        } else {
                            confirmed = {leave:true};
                            return confirmed;
                        }
                    }
            }else{return oldConfirm;}
    }

};

var Validation = new Validation();