var JS = function () {
    this.selectdataready = false;
    this.selectcallbacks = $.Callbacks();
    this.maxZindex = 20;
    this.dcDelay = 300;
    this.clicks = 0;
    this.clicks2 = 0;
    this.openModal_id = 1;
    this.openModalsObject = {};
    this.sdata = {};
    this.cn = {
        "property": "properties/",
        "unit": "units/",
        "lease": "leases/",
        "tenant": "tenants/",
        "inviteTenants": "tenants/inviteTenant",
        "tenanttolease": "tenantstolease/",
        "account": "accounts/",
        "report": "reports/",
        "reportMult": "reports/mult",
        "setting": "settings/",
        "1": "transactions/journalEntry",
        "2": "transactions/bills",
        "4": "transactions/checks",
        "inventory": "inventory/",
        "vendors": "vendors/",
        "employees": "employees/",
        "5": "transactions/receivePayments",
        "reconciliation": "reconciliations/",
        "transactions": "transactions/",
        "D3e": "D3e",
        "7": "transactions/payBills",
        "multBill": "transactions/payBillsMult",
        "bill payment": "transactions/payBills",
        "8": "transactions/deposits",
        "6": "transactions/charges",
        "notes": "notes/",
        "timesheet": "timesheet/",
        "creditCard" :"transactions2/creditCard",
        "bankTrans" :"transactions/bankTrans",
        "9" :"transactions2/creditCard",
        "10": "transactions/applyRefund",
        "utilitiesGrid": "transactions/utilitiesGrid",
        "11": "transactions/memorizedTransactions",
        "invoice": "invoice/",
        "entities": "entities/",
        "report-cust": "reports/custom",
        "encrypt": "encrypt/",
        "in_court": "in_court/",
        "propertyTaxes": "propertyTaxes/",
        "transactionsImport": "transactionsImport/",
        "companySettings": "companySettings/",
        "investor": "investor/",
        "bank_transfer": "bank_transfer/",
        "transfer_bal": "transfer_bal/",
        //"12": "transactions/charges",
        "12": "transactions/nsf",
        "13": "transactions/charges",
        "14": "transfer_bal/",
        "15": "transactions/bankTrans",
        "18": "transactions/invoice",
        "capital": "capital/",
        "disburse": "disburse/",
        "management": "management/",
        "email": "email/",
        "banks": "accounts/chooseBank",
        "maintenance": "maintenance/",
        "init_payment": "tenants/initPayment",
        "tenant_autopay": "tenants/addAutocharge",
        "add_pay_method": "tenants/addPayMethod",
        "merge": "accounts/merge"
        
    };
    var old;// used in indication
    //var printPage = "transactionsprint";// used in print
    html_tag = $('html'),
    body_tag = $('body'),

    nav_id = $('#nav'),
    skip_id = $('#skip'),
    top_id = $('#top'),

    check_a = $('.check-a'),
    date_input = $('input[type="date"], input.date'),
    form_charge = $('.form-charge'),
    form_search = $('.form-search'),
    list_square = $('.list-square'),
    module_print = $('.module-print'),
    nav_a = $('.nav-a'),
    table_c = $('.table-c'),
    table_d = $('.table-d'),
    table_tag = $('table:not(.table-c)'),
    popup_tag = $('[class^="popup"]'),
    select_tag = $('select'),
    table_d = $('.table-d'),

    form_children = $('form > *:not(fieldset), fieldset > *');
};
/** class methods **/
JS.prototype = {
    makeTree: function (obj) {
        $('#psearch').keyup(function () {
            obj.treetable("refresh", $(this).val());
            console.log("keyup 91");
        });
        obj.treetable({
            expandable: true,
            clickableNodeNames: false
        });
    },

    loadSelects: function(type = null, trigger = null){
        var that = this;
        this.selectdataready = false;
        $.post(JS.baseUrl + 'api/getAllSelectsData', {type: type}, function (data) {
            if(type) {
                that.sdata[type] = data;
            } else {
                that.sdata = data;
                that.sitesettings = data['sitesettings'];
                delete data['sitesettings'];
            }
            that.selectdataready = true;
            FESC.clearCache();
            that.selectcallbacks.fire(trigger);
        }, 'JSON').fail(function (data) { console.log(data); });


    },

    showAlert: function (type, message, button = null, action = null) {
        $(".newAlert.alert-" + type + " span"+".alert-body").html(message);
        $(".newAlert.alert-" + type).show();

        if (type === "warning") {
           // $(".newAlert.alert-warning").find("#warningSubmit").append('<li><button style = "background-color:#ffc107"  "type = "button" id="warningSubmit" >ok</button></li>');
           // $(".newAlert.alert-warning").find("#button-container").append('<li><button style = "background-color:#ffc107" type = "button" id="warningCancel" >cancel</button></li>');
           $("#warningSubmit").data('button', button);



        }else {
                  setTimeout(function () {
                     $(".newAlert.alert-" + type).hide();
                  }, message.length * 150);
        }

    },

    showDialogue: function (type, message, callback = null) {
        $('#error-modal .modal-body').text(message);
        var modal = $('#error-modal'), that = this;
        modal.modal({
            show: true
        });
    },

    showWarning: function ( message, callback = null) {
        $('#warning-modal .modal-body').text(message);
        var modal = $('#warning-modal'), that = this;
        modal.modal({
            show: true
        });
    },

    appendHtml: function (url, parent, data = {}) {
        data.count = parent.children().length;
        $.post(JS.baseUrl+url, data, function (result) {
            parent.append(result);
            JS.checkboxes();
        });
        
    },

    printStuff: function (data) {
        console.log("printstuff");
        
        $('#Checkarea').empty();
        $('#Checkarea').append(data);
        $('#Checkarea').addClass('print-section2');
        window.print();
        $('#Checkarea').empty();
    },



    openDraggableModal: function (type, mode, id, parent = null, params = {}, functions = {}) {
        //stops a modal from opening if it's already opened except for reports
        if(type != 'report' && type != 'invoice' && type != 'custom'){
            var newOpenModal = type +mode+ id;
            //checks if modal is already opened and just brings it to the front
            for(var propName in JS.openModalsObject) {
                //console.log(propName)
                if(JS.openModalsObject.hasOwnProperty(propName)) {
                    var propValue = JS.openModalsObject[propName];
                    if(propValue == newOpenModal){
                        
                        var showThisModal = $('body').find('.modal[type="' + type + '"][openModal-id="'+propName+'"]');
                        showThisModal.show("slide", { direction: "right" }, 300);
                        setTimeout(function(){
                            showThisModal.css("z-index", JS.maxZindex++);
                        },400);
                        return;
                    }
                }
            }
        }
        
        //console.log('openmodal');
        //console.log(newOpenModal);
        var url;
        if (type == 'custom') url = params.url; else
            url = this.cn[type] + "getModal";
        var last = moment();
        $.post(JS.baseUrl+url, {
            'type': type,
            'mode': mode,
            'id': id,
            'params': JSON.stringify(params)
        }, function (result) {
            //console.log('from click till load: '+ (moment()-last))
            last = moment();
            if (result == '') {
                JS.showAlert('danger', 'Access denied!');
                return;
            }
            $("body").append(result);
            var modal = $('.modal:last');
            JS.checkboxes(modal);
            JS.toggleRadioButtons();
            JS.checkAll();
            
            JS.datePickerInit(modal);
            if(type == 'report'){
                JS.minMaxClose2(modal.attr('type'), params.title);
            }else{
                JS.minMaxClose2(modal.attr('type'), modal.attr('ref-id'));
            }
            //if all modals have class expanded add it to this one also
            var testExpanded = $('body').find('.modal.expanded');
            if(testExpanded.length != 0){
                modal.addClass('expanded');
            }
            //stores which modals are open
            modal.attr('openModal-id', JS.openModal_id);
            JS.openModalsObject[JS.openModal_id] = type+mode+id;
           // console.log(JS.openModalsObject);
            JS.openModal_id++;
            $('body').find('#minMaxCloseBox').css('display', '');
            //$('body').find('#openModals').css('display', '');
            //formGrid.radioButtons(modal);
            modal.modal({
                backdrop: false,
                keyboard: false,
                show: true
            }).find('.modal-dialog').draggable({
                handle: '.modal-h',
                drag : function(e,ui){         
                    //prevent from moving too much up 
                    if(ui.position.top < -50)    
                        ui.position.top = -50;
                }
            });

            if (params != null) {
                let gridUpdate;
                $.each(params.defaults, function (k, v) {
                    let theader = $(modal).find('section:first').length ? modal.find('section:first') : modal.find('header');
                    
                    if (v.type == 'def')
                        $(modal).find(v.selector).attr('default', v.value);
                    if (v.type == 'val')
                        $(modal).find(v.selector).val( v.value);
                    if (v.type == 'dp'){
                        newdate = new Date(v.value);
                        $(modal).find(v.selector).datepicker('setDate', v.value);
                        console.log($(modal).find(v.selector).closest('p').find('input')[1]);
                        $($(modal).find(v.selector).closest('p').find('input')[1]).val(`${newdate.getFullYear()}/${newdate.getMonth()+1}/${newdate.getDate()}}`);
                    }

                    if (v.type == 'toggle'){
                        $(modal).find(v.selector).find('input[type="radio"]').each(function(){
                            if($(this).val() == v.value){
                                $(this).attr('checked', true)
                            } else {
                                $(this).attr('checked', false) 
                            }
                            
                        });
                    }
                        
                    if (v.type == 'append'){
                        $(theader).append(v.value);
                        if(v.gridUpdate){
                            gridUpdate = true;
                        }
                    }
                        
                    
                });
                if(gridUpdate == true){
                    grid = $(modal).data('formgrid');
                    grid.fillFirstRow();
                    $(grid).removeClass('empty');
                }

        }


        //    modal.find('table').enableCellNavigation();

         //   console.log('from load till shown: '+ (moment()-last))
            modal.on('shown.bs.modal', function () {
                $(document).off('focusin.modal');
                $(this).css("z-index", JS.maxZindex++);
                $(this).find('form').attr('autocomplete','off');
                $(this).find('.editable-select').editableSelect();
                $(this).find('.fastEditableSelect').fastSelect();
                $('.dataTables_scrollBody .table-d').filterTable('#mrh',{trClass:'', parentClass:'.t_input_wrapper'});
                $('.testTable').filterTable('#fsa',{trClass:'', parentClass:'.t_input_wrapper'});
                $(this).find('input.decimal').calculadora({decimals: 2, useCommaAsDecimalMark: false});
                $(this).find('#datepicker').datepicker();
                
                var tablec = $(this).find('.table-c.dt');
                tablec.each(function (t) {

                    //$(this).find('td:first-child').find('shadow').remove().append('<div class="shadow" style="width: 1205px"></div>');
                     //console.log("whatever");
                    //alert("Width of div: " + $("thead").width());
                    var table = $(this).DataTable({
                        "searching": false,
                        "paging": false,
                        "bInfo": false,
                        "ordering": false,
                        "scrollY": "500px",
                        "scrollCollapse": true,
                        "drawCallback": function (settings) {

                            if($(this).is('.a')){
                                        $(this).parents('.dataTables_wrapper').addClass('no-footer has-table-c b mobile-hide text-center').find('td:first-child').each(function(){
                                            $(this).append('<div class="bg3" style="width:'+$(this).parents('table:first').outerWidth()+'px;"></div>')
                                        });
                                    } else if ($(this).is('.b')){
                                        //$(this).find('td:first-child').find('shadow').remove().append('<div class="shadow" style="width: 1205px"></div>');
                                        $(this).parents('.dataTables_wrapper').addClass('has-table-c b mobile-hide');
                                    }
                                    else {
                                        $(this).parents('.dataTables_wrapper').addClass('has-table-c  mobile-hide');
                                    }
                                      //$(this).find('.shadow').each(function(){ $(this).css('width',$(this).parents('tr').outerWidth()); });


                            $(".dataTables_scrollHeadInner").css({
                                "width" : "100%"
                            });

                            $(".dataTables_scrollHeadInner").find('table').css({
                                "width" : "100%"
                            });

                            $(".dataTables_scrollBody").css({
                                "height" : "calc(100vh - 450px); overflow: auto"
                            });




                        }

                    });
        /*            $(this).closest('.modal').on('shown.bs.modal', function () {
                        table.columns.adjust().draw();
                        var sb = $(this).find('.dataTables_scrollBody');
                        sb.css('max-height', $(window).height() - sb.offset().top - 200);
                    });*/
                })
                JS.modalLoadFunctions($(this), type, id);
                $(this).find('.refresh-modal').click(function(){
                    JS.openDraggableModal(type, mode, id, parent, params, functions);
                    modal.remove();
                });
            });

            $('.getDocuments').tooltipster({
                theme: 'tooltipster-shadow',
                side: 'bottom',
                contentAsHTML: true,
                interactive:true,
                animation: 'fade',
                debug: 'false',
                animationDuration: 1000,
                content: 'Loading...',
                // 'instance' is basically the tooltip. More details in the "Object-oriented Tooltipster" section.
                functionBefore: function(instance, helper) {
                    var $origin = $(helper.origin);
                    var modal = $($origin).closest('.modal');
                    var form = $(modal).find('form');
                    var type = $(modal).attr('doc-type');
                    var action = form.attr('action');
                    var thisId = action.split("/").pop();
                    var thisType = $($origin).closest('.modal').attr('doc-type');
                    
                    // we set a variable so the data is only loaded once via Ajax, not every time the tooltip opens
                    if ($origin.data('loaded') !== true) {

                        $.get(JS.baseUrl+ 'documentUpload/tooltipster/'+thisId+'--'+thisType, function(data) {
                            console.log(data);
                            var allDocuments = '';
                            if(data == 'No documents available.'){
                                allDocuments += data;
                            }else{
                                     allDocuments += '<ul>';
                                var data2 = JSON.parse(data);
                                for (var j = 0; j < data2.length; j++) {
                                    allDocuments += '<li><a href="' +JS.baseUrl+'uploads/documents/'+ data2[j].name + '" target="_blank">' + data2[j].name +'</a></li>';
                                }
                                allDocuments += '</ul>';
                                console.log('allDocuments');
                                console.log(allDocuments);
                            }
                            // call the 'content' method to update the content of our tooltip with the returned data.
                            // note: this content update will trigger an update animation (see the updateAnimation option)
                            instance.content(allDocuments);

                            // to remember that the data has been loaded
                            //$origin.data('loaded', true);

                        });
                    }
                }
            });
            //end new tooltipster
            modal.find('.get_send_email_form').tooltipster({
                theme: 'tooltipster-shadow',
                side: 'bottom',
                contentAsHTML: true,
                interactive:true,
                animation: 'fade',
                debug: 'false',
                //trigger: 'custom',
                //triggerOpen: {
                    //mouseenter: true
                //},
                animationDuration: 1000,
                //content: 'Loading...',
                functionBefore: function(instance, helper) {
                    var $origin2 = $(helper.origin);
                    console.log($origin2);
                    var send_email_form = $(`<form id="send_email_form" style=" width: 600px; height: 440px">
                                            <h4>To:</h4>
                                            <input type="text" id="send_email_to" name="send_email[to]" style="font-size: 14px;" />                           
                                            <h4>Subject:</h4>
                                            <input type="text" id="send_email_subject" name="send_email[subject]" style="font-size: 14px;" />
                                            <h4>Message:</h4>
                                            <textarea style="margin-top: 5px; font-size: 14px;" name="send_email[message]" id="" cols="20" rows="10"></textarea>
                                            <span id="send_excel_tooltip" style="min-width: 50px;cursor: pointer;font-size: 14px; margin: 10px auto; background-color: red; text-align: right; width: 200px; height: 40px; margin-left: 40%;">Send Excel</span> 
                                            <span id="send_email_tooltip" style="min-width: 75px; min-height: 40px;cursor: pointer;font-size: 14px; margin: 10px auto; background-color: white; border-radius:5px; box-shadow: 0 3px 6px rgba(0,0,0,.16); padding:8px; text-align: right; width: 200px; height: 40px; margin-left: 75%;">Send Email</span>
                                        </form>`);
                                       
                                        //removed for now until excel works <span id="send_excel_tooltip" style="min-width: 50px;cursor: pointer;font-size: 14px; margin: 10px auto; background-color: red; text-align: right; width: 200px; height: 40px; margin-left: 40%;">Send Excel</span>
                                        console.log(send_email_form);
                                        //send_email_form.find('#attach_document_email').data('modal', modal);
                                        send_email_form.find('#send_email_tooltip').data('modal', modal);
                    //console.log(send_email_form);s
                    $origin2.data('modal', $origin2)
                    instance.content(send_email_form);

                }
            });


            modal.find('.icon-brain').tooltipster({
                theme: 'tooltipster-shadow',
                side: 'bottom',
                trigger: 'click',
                contentAsHTML: true,
                interactive:true,
                animation: 'fade',
                debug: 'false',
                animationDuration: 1000,
                content: 'Loading...',
                functionBefore: function(instance, helper) {
                    var frequencies;
                    $.get(JS.baseUrl+ "leases/getBrainFrequencies", function (result) {
                     frequencies = JSON.parse(result);
                     console.log(frequencies);
                
                    
                    var $origin2 = $(helper.origin);
                    var form = $($origin2).closest('form').find('input');
                    var type = $($origin2).closest('form').attr('type');
                    var formdata = $($origin2).closest('form').serializeArray();
                    
                    //console.log(formdata);
                    //console.log(form);
                    //console.log($origin2);
                    var send_memorizedTransactions_form = `<form id="send_memorizedTransactions" style=" width: 600px; height: 440px">
                                            <input type="hidden" id="transaction_type" name="brain[transaction_type]" value="` + type + `" />
                                            <h4>Name:</h4>
                                            <input type="text" id="name" name="brain[name]" style="font-size: 14px;" />
                                            <h4>frequency:</h4>
                                            <span style="" class="select">
                                            <select class=" editable-select quick-add set-up " id="frequency" name="brain[frequency]"  modal="" type="table" key="">
                                                    <option value="-1" selected ></option>`
                                                for (var j = 0; j < frequencies.length; j++) {
                                                    send_memorizedTransactions_form += `<option value='` + frequencies[j].id + `'`;
                                                    send_memorizedTransactions_form +=`>` + frequencies[j].name + `</option>`;
                                                }
                                                send_memorizedTransactions_form +=`	</select>
                                            </span>                          
                                            <h4>start Date:</h4>
                                            <input data-toggle="datepicker" id="brain[start_date]" name="brain[start_date]" style="font-size: 14px;" />
                                            <h4>End date:</h4>
                                            <input data-toggle="datepicker" id="brain[end_date]" name="brain[end_date]" style="font-size: 14px;" />
                                            <h4>Next trans date:</h4>
                                            <input data-toggle="datepicker" id="brain[next_trans_date]" name="brain[next_trans_date]" style="font-size: 14px;" />
                                            Auto
                                            <label class="switch float-none" style="line-height:18px;" for="auto">
                                                <input type="checkbox" value="1" class="no-js" name="brain[auto]" checked="" id="auto">
                                                <span class="slider round"></span>
                                                <span class="option-text"></span>
                                            </label>
                                            <span id="create_memorized_transaction" style="min-width: 50px;cursor: pointer;font-size: 14px; margin: 10px auto; 
                                            background-color: aqua; text-align: right; width: 200px; height: 40px; margin: 15px 160px;" >Create memorized tranaction</span>`;
                                            for (var j = 0; j < formdata.length; j++) {
                                                send_memorizedTransactions_form +=  `<input type="hidden" name='` + formdata[j].name + `' value='` + formdata[j].value + `'>`;
                                            }                                                                              
                                        send_memorizedTransactions_form += `</form>`;
                    //console.log(send_memorizedTransactions_form);
                    //var send_memorizedTransactions_form2 = $(send_memorizedTransactions_form);
                    //send_memorizedTransactions_form2.find('#create_memorized_transaction').data('mtdata', modal);
                    //$origin2.data('mtdata', $origin2)
                    //console.log(send_memorizedTransactions_form2);
                    instance.content(send_memorizedTransactions_form);
                    JS.datePickerInit($('body').find('#send_memorizedTransactions'));
                        $('body').find('#send_memorizedTransactions').find('[data-toggle="datepicker"]').on('click', function(){
                            $('body').find('div.datepicker-container:not(.datepicker-hide)').css('z-index', '9999999');
                        });
                    })
                }
            });
            modal.on('hidden.bs.modal', function () {
                //console.log('closemodal');
                $(this).remove();
            });
            modal.on("click", function () {
                if ($(this).css("z-index") != JS.maxZindex)
                    $(this).css("z-index", JS.maxZindex++);
            });
            modal.on("change", $('modal form:input'), function () {
                if (!modal.hasClass('changed')) {
                    modal.addClass('changed');
                }

            });
            var date = + new Date();
            modal.addClass(date);
            if(params.tableGrid) modal.find('form').addClass('tableGrid');
            if (parent != null) {
                var table = $(parent).closest('.tabcontent').find('.table-c');
                modal.find('form').addClass('no-submit').find('button[type="submit"]').on('click', function (e) {
                    e.preventDefault();
                    var form = $(this).closest('form');
                    modal.modal('hide');
                    JS.openModalsObjectRemove(modal.attr('type'), modal.attr('openModal-id'));
                    if (mode == 'add'){
                                            console.log(table);
                     Formatter.addDTRow(table, Formatter.parseRow(type, form.get(0), table.DataTable().rows().count()), type);
                    }

                    else
                     Formatter.editDTRow(table, parent, Formatter.parseRow(type, form.get(0), table.DataTable().row(parent).index()));
                });
            }

            //PARSING PARAMS
            if (params != null) $.each(params.dis, function (k, v) {
                modal.find(v).attr('disabled', true);
            });
            $.each(functions, function (k, v) {
                modal.on(v.event, v.function)
            });          
            //addRowToBillsForm()
        }).fail(function(error) { console.log(error) });


        colors = sessionStorage.getItem('colors').split(';');

  
        const root = document.querySelector(':root');
        root.style.setProperty('--pink1', colors[0]);
        root.style.setProperty('--pink2', colors[1]);
        root.style.setProperty('--pink3', colors[2]);
        root.style.setProperty('--pink4', colors[3]);
        root.style.setProperty('--pink5', colors[4]);
        root.style.setProperty('--pink6', colors[5]);

        
    },

    modalLoadFunctions: function(modal, type, id){
        //if(modal.data('formgrid')) modal.data('formgrid').loadRows();
        if(parseInt(type) > 0){
            JS.initTrDetailsPopup(modal, type, id);
            JS.initAuditTrailPopup(modal, type, id);           
        }
        switch(type){
            case '4':
                //checkForm.checkTotal(modal.find('#checkFormBody'));
                //checkForm.getRows(modal.find('#checkFormBody'));
                //formGrid.calculate(modal);
                break;
        }

    },

    initTrDetailsPopup: function(modal, type, id) {
        var li = $('<li><a class="trDetails" href="#" tid="'+id+'" ttype="'+type+'"><i class="icon-link"></i> <span>Details</span></a></li>');
        modal.find('nav:last ul').append(li);
        modal.find('.trDetails').tooltipster({
            content: 'Loading...',
            animation: 'fade',
            delay: 500,
            theme: 'tooltipster-shadow',
            side: 'bottom',
            contentAsHTML: true,
            interactive: true,
            trigger: 'hover',
            functionBefore: function(instance, helper) {
                var $origin = $(helper.origin);
                if ($origin.data('loaded') !== true) {
                    $.get(JS.baseUrl+'transactions/getTrDetailsPopup/'+$origin.attr('ttype')+'/'+$origin.attr('tid'), function(data) {
                        instance.content(data);
                        $origin.data('loaded', true);
                    });
                }
            }
        });
    },

    initAuditTrailPopup: function(modal, type, id) {
        var li = $('<li><a class="auditTrail" href="#" tid="'+id+'" ttype="'+type+'"><i class="icon-trail"></i> <span>Details</span></a></li>');
        modal.find('nav:last ul').append(li);
        modal.find('.auditTrail').tooltipster({
            content: 'Loading...',
            animation: 'fade',
            delay: 500,
            theme: 'tooltipster-shadow',
            side: 'bottom',
            contentAsHTML: true,
            interactive: true,
            trigger: 'hover',
            functionBefore: function(instance, helper) {
                var $origin = $(helper.origin);
                if ($origin.data('loaded') !== true) {
                    $.get(JS.baseUrl+'transactions/getAuditTrailPopup/'+$origin.attr('ttype')+'/'+$origin.attr('tid'), function(data) {
                        instance.content(data);
                        $origin.data('loaded', true);
                    });
                }
            }
        });
    },

    initModals: function () {
        var newCn = this.cn;
        window.addEventListener('popstate', function (event) {
            if (history.state) {
                $('#aside .leftSideBarLink[type="'+history.state.type+'"]').click();
            }
        }, false);
        $('body').on('click', '#aside .leftSideBarLink', function (e) {
            e.preventDefault();
            if($(this).closest('body').find('aside.left-side')[0]){

                var value = $(this).attr('type');
                $('.left-side').attr('data-type', value);
                $(this).closest('body').find('#nav').find('h2').html(value);
                JS.loadLeft($('.left-side'), 'layout/getLeftColumn', value);
                history.pushState({type:value}, "", $(this).attr('href'));
                setTimeout(() => {
                    $('body').find('.left-side').find('.ui-widget-content').first().find('.slick-cell').first().trigger('click');
                }, 4000);
            }else{
                 console.log('no left side.');
                 window.location = $(this).attr('href');
                 /*var that = $(this).closest('nav');
                 var value = $(this).attr('href').substring(1);
                 var url = newCn[value];
                 var newUrl = JS.baseUrl+url;
                 $(this).attr('href', newUrl);
                 $(this).attr('id', 'clickMe');
                 $(this).removeClass('leftSideBarLink');
                 $(that).find('#clickMe')[0].click();*/
            }
        });
        $('body').on('click', '#addPropertyButton', function () {
            JS.openDraggableModal('property', 'add', null);
            //JS.openDraggableModal('custom', 'add', null, null, {url:'leases/getLateChargeModal'});
        });
        $('body').on('click', '.reportsList', function () {
            var reportsHtml = $('body').find('#report_list').html();
            $('body').find('#content .left-side').empty();
            $('body').find('#content .right-side').empty().html(reportsHtml);
            $('body').find('#content .right-side .reportLink').css('width', '40%');
            $('body').find('.right-side .duplicateReport').css('float', '');
            $('body').find('.right-side .reports-row').css('width', '310px');
            //console.log(reportsHtml);
        });
        $('body').on('click', '.addUnitButton', function () {
            JS.openDraggableModal('unit', 'add', null, $(this), {
                dis: ['#property_id'],
                parent_id: $(this).closest('.modal').attr('main-id'),
                newitems: JS.getNewItems($(this).closest('.modal'), '.tid'),
                property_name: $(this).closest('.modal').find("#name").val()
            });
        });
        $('body').on('click', '.lc-edit', function () {
            var modal = $(this).closest('.modal');
            var value = modal.find('input[sel-id="late_charge"]').val();

            JS.openDraggableModal('custom', value>0 ? 'edit' : 'add', value, null, {url:"leases/getLateChargeModal"}, value ? null : [{
                event: 'postsubmit',
                function: function (e, data) {
                    JS.loadList('api/getAllLCList', '', '#late_charge', modal);
                }
            }]);
        });
        $('body').on('click', '#addUnitButton', function () {
            JS.openDraggableModal('unit', 'add', null);
        });
        $('body').on('click', '#addLeaseButton', function () {
            JS.openDraggableModal('lease', 'add', null);
        });
        $('body').on('click', '#addTenantButton', function () {
            JS.openDraggableModal('tenant', 'add', null);
        });
        $('body').on('click', '.addTenantButton', function () {
            var modal = $(this).closest('.modal');
            JS.openDraggableModal('tenant', 'add', null, null, null, [{
                event: 'postsubmit',
                function: function (e, data) {
                    JS.loadList('api/getAllTenantsList', null, '#tenant', modal)
                }
            }]);
        });

        $('body').on('click', '#addMaintenanceButton', function () {
			JS.openDraggableModal('custom', 'add', null, null, {url:$(this).attr('data-url')});
		});
        $('body').on('click', '.reportLink', function (e) {
           e.stopPropagation();
           
           if ($(e.target).parents('.tooltipster-base').length) {
            $(e.target).parents('.tooltipster-base').hide();
           }
            JS.openDraggableModal('report', 'add', $(this).attr('data-id'), null, { data: $(this).attr('defaults'), filters: $(this).attr('filters'), type: $(this).attr('rtype'), title: $(this).attr('title')});
        })
        //multiple reports
        $('body').on('click', '.reportLinkMult', function () {
            //var today = new Date();
            //var month = today.getMonth()+1;
            //var year = today.getFullYear();
            var property = $(this).attr('property_id');
            var startDate = $(this).closest('figcaption').find('#start_date').val();
            var endDate = $(this).closest('figcaption').find('#end_date').val();
            var secondDate;

            //if (month >6) {startDate = year +'-01-01'} else {startDate = (year-1) +'-07-01'};
            //if (month >6) {endDate = year +'-06-30'} else {endtDate = (year-1) +'-12-31'};
            //console.log("start "+ startDate);
            //console.log("end "+ endDate);
            /* JS.openDraggableModal('reportMult', 'add', 7, null, 
            [
              {'id':1,
                'settings':{ data: property+'$$'+endDate, filters: '', type: '7', title: 'RR'}
              },

              {'id':2,
              'settings':{ data: property+'$$'+startDate+'|'+endDate, filters: '', type: '5', title: 'PL'}
              },

              {'id':3,
              'settings':{ data: property+'$$'+endDate+'$$'+startDate, filters: '', type: '40', title: 'BS'}
              }
            ] 

                      
             
            ); */
            JS.openDraggableModal('report', 'add', 7, null, { data: property+'$$'+endDate, filters: '', type: '5', title: 'RR'});
            JS.openDraggableModal('report', 'add', 5, null, { data: property+'$$'+startDate+'|'+endDate, filters: '', type: '5', title: 'PL'});
            JS.openDraggableModal('report', 'add', 40, null, { data: property+'$$'+endDate+'$$'+startDate, filters: '', type: '40', title: 'BS'});
        });
        $('body').on('click', '.reportLinkCust', function () {
            JS.openDraggableModal('report-cust', 'add', $(this).attr('data-id'), null, { data: $(this).attr('defaults'), filters: $(this).attr('filters'), type: $(this).attr('rtype'), title: $(this).attr('title')});
        });
        $('body').on('click', '.addTenantToLeaseButton', function () {
            if($(this).closest('.modal').find("#unit_id").closest('.select').find('input[type=hidden]').val() > 0){
                JS.openDraggableModal('tenanttolease', 'add', null, $(this), { property: $(this).closest('.modal').find("#property_id").closest('.select').find('input[type=hidden]').val(), unit: $(this).closest('.modal').find("#unit_id").closest('.select').find('input[type=hidden]').val(), lease: $(this).closest('.modal').find("#lease_id").val() });
            }else{
                JS.showAlert('danger','No unit selected');
            }
        });
        $('body').on('click', '#addAccountButton', function () {
            JS.openDraggableModal('account', 'add', null);
        });
        $('body').on('click', '#chooseBankButton', function (event) {
            that = event.target;
            if($(that).closest('form').find('input[name="specialAccount[id]"]').val() > 0){
                bank_id = $(that).closest('form').find('input[name="specialAccount[id]"]').val();
            } else {
                bank_id =0;
            }
            acct_id = $(that).closest('form').attr('action').split("/").pop();
            JS.openDraggableModal('banks', 'add', bank_id, null, {acct_id:acct_id});
        });

        $('body').on('click', '#chooseBankButton1', function (event) {
            that = event.target;
            bank_id = $(that).attr('data-bank');
            acct_id = $(that).attr('data-account');
            JS.openDraggableModal('banks', 'add', bank_id, null, {acct_id:acct_id});
        });

        $('body').on('click', '#getInvestorsButton', function () {
            JS.openDraggableModal('investor', 'get', null);
        });
        $('body').on('click', '#addInvestorButton', function () {
            JS.openDraggableModal('investor', 'add', null);
        });
        $('body').on('click', '#bank_transferButton', function () {
            JS.openDraggableModal('bank_transfer', 'add', null);
        });
        $('body').on('click', '#transfer_balButton', function () {
            JS.openDraggableModal('transfer_bal', 'add', null);
            console.log("transfer!");
        });
        $('body').on('click', '#capitalButton', function () {
            JS.openDraggableModal('disburse', 'add', 'capital');
        });
        $('body').on('click', '#disburseButton', function () {
            JS.openDraggableModal('disburse', 'add', 'disburse');
        });
        $('body').on('click', '#managementButton', function () {
            JS.openDraggableModal('management', 'add', 'management');
        });
        $('body').on('click', '#journalEntryButton', function () {
            JS.openDraggableModal('1', 'add', null);
        });
        $('body').on('click', '#addBillButton', function () {
            JS.openDraggableModal('2', 'add', null);
        });
        $('body').on('click', '#addCreditCard', function () {
            JS.openDraggableModal('9', 'record', null);
        });
        $('body').on('click', '#creditCardButton', function () {
            JS.openDraggableModal('9', 'add', null);
        });
        $('body').on('click', '#bankTransButton', function () {
            JS.openDraggableModal('bankTrans', 'add', null);
        });
        // $('body').on('click', '#applyRefundButton', function () {
        //     JS.openDraggableModal('applyRefund', 'add', null);
        // });
        $('body').on('click', '#checkButton', function () {
            var accountId = $(this).attr('data-account-id');
            JS.openDraggableModal('4', 'add', null, null, {'account': accountId});
        });
        $('body').on('click', '#ccButton', function () {
            var accountId = $(this).attr('data-account-id');
            JS.openDraggableModal('9', 'add', null, null, {'account': accountId});
        });
        $('body').on('click', '#checkToPrint', function () {
            JS.openDraggableModal('4', 'checkToPrint', null);
        });
        $('body').on('click', '#depositButton', function () {
            JS.openDraggableModal('8', 'add', null);
        });
        $('body').on('click', '#addInventoryButton', function () {
            JS.openDraggableModal('inventory', 'add', null);
        });
        $('body').on('click', '#receive_paymentButton', function () {
            JS.openDraggableModal('5', 'add', null);
        });
        $('body').on('click', '#payBillButton', function () {
            //if your coming from vendors filters by vendor
            var vendorId = ($(this).attr('data-vendor-id')) ? $(this).attr('data-vendor-id') : null;
                JS.openDraggableModal('7', 'add', vendorId);
        });
        $('body').on('click', '#payBillsButton', function () {
            //if your coming from vendors filters by vendor
            var vendorId = ($(this).attr('data-vendor-id')) ? $(this).attr('data-vendor-id') : null;
                JS.openDraggableModal('multBill', 'add', vendorId);
        });
        $('body').on('click', '#inviteTenantsButton', function () {
                JS.openDraggableModal('inviteTenants', 'add');
        });
        $('body').on('click', '#tenantStatements', function () {
                var tenantId = ($(this).attr('data-tenant-id')) ? $(this).attr('data-tenant-id') : null;
                JS.openDraggableModal('invoice', 'statement', null, null, {id: tenantId, type: 'tenant'});
        });
        $('body').on('click', '#leaseStatements', function () {
                var leaseId = ($(this).attr('data-lease-id')) ? $(this).attr('data-lease-id') : null;
                JS.openDraggableModal('invoice', 'statement', null, null, {id: leaseId, type: 'lease'});
        });
        $('body').on('click', '#printBlankCheck', function(){
            var accountId = $(this).attr('data-account-id');
                $.post(JS.baseUrl+"transactions/printBlankCheck", {
                    'params': JSON.stringify(accountId)
                }, function (result) {
                    // console.log(result);
                    // console.log("result");
                var result2 = JSON.parse(result);

                printChecks.printBlankCheck(result2)
            })
        })
        $('body').on('click', '#printVoidCheck', function(){
            var accountId = $(this).attr('data-account-id');
                $.post(JS.baseUrl+"transactions/printBlankCheck", {
                    'params': JSON.stringify(accountId)
                }, function (result) {
                    // console.log(result);
                    // console.log("result");
                var result2 = JSON.parse(result);

                printChecks.printBlankCheck(result2, true)
            })
        })
        //hides the top nav when a link is clicked
        $('body').on('click', '.tooltipster-content ul li, .tooltipster-content div.reports-col', function () {
            $(this).closest('.tooltipster-base').css('display', 'none');
        });
        $('body').on('click', '.switchModal', function () {
            var id = $(this).closest('.modal').attr('main-id');
            var type = $(this).closest('.modal').attr('type');
            var dir = $(this).attr('dir');
            var ids = $('tr[data-type="' + type + '"]').not('.noshow').map(function() {
                return $( this ).attr('data-id');
            })
            for (var i = 0; i < ids.length; i++) {
                if (ids[i] == id) {
                    $('div[main-id=' + id + ']').modal('hide');
                    var res = 0;
                    if (id == 1 && dir == 'prev') res = ids[ids.length-1];
                    else if (id == ids[ids.length-1] && dir == 'next') res = 1;
                    else res = ids[i+(dir == 'prev' ? -1 : 1)];
                    JS.openDraggableModal(type, 'edit', res);
                    break;
                }
            }
        });
/*         $('body').on('click', '.printModal', function () {
            $('#root').addClass('hide');
            $(this).closest('.modal').addClass('print-section');

            window.print();

            $('#root').removeClass('hide');
            $(this).closest('.modal').removeClass('print-section');
        }); */

/*         $('body').on('click', '#printCheck', function () {
            var a = $(this);
            a.closest('.modal').modal('hide');

            JS.openDraggableModal('PrintCheck', 'print', null, $(this), {check: $(this).closest('.modal').find("#check3").val()},{ $ajax: $.ajax({
                url: "PrintCheck/getModal/print",
                success: setTimeout(function (data){
                    var page = data;
                    var a = document.getElementById("check3");
                    $(a).closest(log'.modal').modal('hide');
                    JS.print(data);
                    //var checkId = page.getElementById("check3").innerHTML;
                    //console.(checkId);
              }, 3000)
          })}) */



            //JS.print();
        //});
        // $('body').on('click', '#timesheetTest', function () {
        //     JS.loadRight($(".right-side"), 'layout/getRightColumn?type=timesheet');
        // });
        $('body').on('click', '#addTime', function () {
            // Date.prototype.toDateInputValue = (function() {
            //         var local = new Date(this);
            //         local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
            //         return local.toJSON().slice(0,10);
            //     });
            // document.getElementById('datePicker').value = new Date().toDateInputValue();
            //$( "#addTimeForm" ).show();
            //$( "#addTimeForm" ).show();
            var employeeId = ($(this).attr('employee-id')) ? $(this).attr('employee-id') : null;
            JS.openDraggableModal('timesheet', 'add', employeeId);
        });
        $('body').on('click', '#timeCancelButton', function () {
            var form = $(this).closest('form');
            form[0].reset();
            $( "#addTimeForm" ).hide();
        });
        $('body').on('click', '#section_8_button', function () {
            $(this).closest('.modal').find( "#section_8_tab" ).toggle();
        });
        $('body').on('click', '#rent_stabilized_button', function () {
            $(this).closest('.modal').find( "#rent_stabilized_tab" ).toggle();
        });
        $('body').on('click', '#in_court_button', function () {
            $(this).closest('.modal').find("#in_court_tab").toggle();
            console.log('incourt');
            //$( "#in_court_tab" ).toggle();
        });
        $('body').on('click', '#commercial_button', function () {
            $(this).closest('.modal').find("#commercial_tab").toggle();
            console.log('commercial');
            //$( "#in_court_tab" ).toggle();
        });
        $('body').on('click', '#addNote', function () {
            $( "#noteForm" ).show();
            //JS.openDraggableModal('notes', 'add', null);
            //JS.openDraggableModal('notes', 'add', $(this).find('#object_id').val());//{type: $(this).find("#type").val()}
        });
        $('body').on('click', '#noteCancelButton', function () {
            var form = $(this).closest('form');
            form[0].reset();
            $( "#noteForm" ).hide();
        });
        $('body').on('click', '#nsf', function (e) {
            e.preventDefault();
            var data = new Object();
            $(this).closest('form').find('input').each(function(){
                name1 = $(this).attr("name");
                value1 = $(this).val();
                data[name1] = value1;
            });

            JS.openDraggableModal('12', 'add', null, null,data);
        });
        $('body').on('click', '#newCharge', function () {
            //$( "#addChargeForm" ).show();
            JS.openDraggableModal('6', 'add', null);
        });
        $('body').on('click', '#newInvoice', function () {
            //$( "#addChargeForm" ).show();
            JS.openDraggableModal('18', 'add', null);
        });
        $('body').on('click', '#newChargeCancelButton', function () {
            var form = $(this).closest('form');
            form[0].reset();
            $( "#addChargeForm" ).hide();
        });
        $('body').on('click', '#addVendorButton', function () {
            JS.openDraggableModal('vendors', 'add', null);
        });
        $('body').on('click', '#addEmployeeButton', function () {
            JS.openDraggableModal('employees', 'add', null);
        });
        $('body').on('click', '#utilitiesGrid', function () {
            JS.openDraggableModal('utilitiesGrid', 'add', null);
        });
        $('body').on('click', '#transactionsImport', function () {
            JS.openDraggableModal('transactionsImport', 'upload', null);
        });
        $('body').on('click', '#propertyTaxesButton', function () {
            JS.openDraggableModal('propertyTaxes', 'get', null);
        });
        $('body').on('click', '#massEmailButton', function () {
            JS.openDraggableModal('email', 'add', null);
        });
        $('body').on('click', '#d3e', function () {
            JS.openDraggableModal('D3e', 'add', null);
        });
        $('body').on('click', '#invoiceButton', function () {
            JS.openDraggableModal('invoice', 'add', null);
        });
        $('body').on('click', '#emailInvoice', function () {
            JS.openDraggableModal('invoice', 'choose', null);
        });
        $('body').on('click', '#companySettings', function () {
            JS.openDraggableModal('companySettings', 'companySettings', null);
        });
        $('body').on('click', '#addUtilityButton', function () {
            JS.openDraggableModal('utilitiesGrid', 'addNew', null);
        });
        // $('body').on('click', '#attach_document_email2', function (e) {
        //     var that = this;
            
        //     e.stopPropagation();
        //     var aa = $(this).data('modal').find('div#reports-table');
        //     var data = aa.data('nestesobj');
        //     console.log(data);
        //     //$('.get_send_email_form').tooltip('show');
        //     console.log(data.dataView.getFilteredItems());
        //     $.post(JS.baseUrl+'reports/pdf', {header: $(this).data('modal').find('#report-header').html(), columns: data.grid.getColumns(),  rows: JSON.stringify(data.dataView.getFilteredItems())}, function(url) {
        //         //window.open(url);
        //         //return url;
        //         $(that).val(url);
        //         //console.log(JSON.stringify(data.dataView.getFilteredItems()));
        //     });
        // });
        $('body').on('click', '#encrypt', function () {
            JS.openDraggableModal('encrypt', 'banks', null);
        });
        $('body').on('click', '#addEntitiesButton', function () {
            JS.openDraggableModal('entities', 'add', null);
        });
        $('body').on('click', '#getEntitiesButton', function () {
            JS.openDraggableModal('entities', 'allEntities', null);
        });
        $('body').on('click', '#getIn_courtButton', function () {
            JS.openDraggableModal('in_court', 'in_court', null);
        });
        $('body').on('click', '#d3transactions', function () {
            JS.openDraggableModal('transactions', 'add', null);
        });
        $('body').on('click', '#create_memorized_transaction', function (e) {
            // e.stopPropagation();
             var that = this;
            //  console.log(that);
            //  console.log('mtdata');
             e.stopPropagation();
             //console.log($(that).closest('form'));
             var form = $(that).closest('form');
             //console.log(form);
             //console.log(form[0]);
             $.post({
                //url: JS.baseUrl+'MemorizedTransactions/addMemorizedTransaction',
                url: JS.baseUrl+'Transactions/addMemorizedTransaction',
                data: new FormData(form[0]),
                success: function (data) {
                    console.log('after ajax');
                    JS.showAlert(data.type, data.message);
                },
                error: function (data) {
                    console.log('error ajax');
                    JS.showAlert(data.type, data.message);
                },
                dataType: 'json',
                processData: false,
                contentType: false,
            });

            //  $.post(JS.baseUrl+'MemorizedTransactions/addMemorizedTransaction', {info: new FormData(form[0])}, function(url) {
            //      console.log('after ajax');

            //  });
 
         });
        $('body').on('click', '#send_email_tooltip', function (e) {
           // e.stopPropagation();
            var that = this;

            e.stopPropagation();
            var aa = $(this).data('modal').find('div#reports-table');
            var data = aa.data('nestesobj');
            console.log(data);
            //$('.get_send_email_form').tooltip('show');
            console.log(data.dataView.getFilteredItems());
            $.post(JS.baseUrl+'reports/pdf', {header: $(this).data('modal').find('#report-header').html(), columns: data.grid.getColumns(),  rows: JSON.stringify(data.dataView.getFilteredItems())}, function(url) {
                //window.open(url);
                //return url;
                $(that).val(url);
                //console.log(JSON.stringify(data.dataView.getFilteredItems()));
                console.log('before ajax');
                var form = $(that).closest('form');
                                    $.post({
                            url: 'sendEmail/sendEmail',
                            data: new FormData(form[0]),
                            success: function (data) {
                                console.log('after ajax');
                                JS.showAlert(data.type, data.message);
                            },
                            error: function (data) {
                                console.log('error ajax');
                                JS.showAlert(data.type, data.message);
                            },
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                        });
            });

        });
        $('body').on('click', '#memorizedTransactionsButton', function () {
            JS.openDraggableModal('11', 'add', null);
        });
        $('body').on('click', '#reconciliationButton', function () {
            JS.openDraggableModal('reconciliation', 'start',$(this).closest('.module-info').find('#accountNum').val(),null, {type:'manual', rectype: $(this).attr('data-rec-type')});
        });
        $('body').on('click', '#reconciliationAutoButton', function () {
            JS.openDraggableModal('reconciliation', 'start',$(this).closest('.module-info').find('#accountNum').val(),null, {type:'auto'});
        });
        /* $('body').on('click', '#createReversal', function (e) {
            e.preventDefault();
                        var form = $(this).closest('form');
                                    bootbox.confirm({
                            message: "Creating a reversal saves the old one. Proceed?",
                            buttons: {
                                confirm: {
                                    label: 'Yes',
                                    className: 'btn-danger'
                                },
                                cancel: {
                                    label: 'No',
                                    className: 'btn'
                                }
                            },
                            callback: function (result) {
                                if (result) {

            $(form).closest('.modal').modal('hide');
            var oldFormInputs = {};
            //for top part of form
            form.find('section:first').find('input').each(function(){
                var value = $(this).attr('value');
                var name = $(this).attr('name');
                if($(this).attr('type') == 'hidden'){
                    oldFormInputs[name] = value;
                }else{
                    oldFormInputs[name] = value;
                }
            });
            //for radio buttons
            var radio = form.find('input[type="radio"]:checked').val();
            //for transactions
            var trs = form.find('tr');
            var oldFormInputsArray = {};
            var inputSpot = 0;
            trs.each(function(){
                var trInputs = $(this).find('input');

                trInputs.each(function(){
                    var value2 = $(this).val();
                    var name2 = $(this).attr('name');
                    console.log(value2);
                    console.log(name2);
                    if($(this).hasClass('editableSelect')){return;
                        //oldFormInputsArray[inputSpot] = $(this).val();
                    }else{
                        oldFormInputsArray[inputSpot] = value2;
                    }
                    inputSpot++;
                });
            });
            console.log(oldFormInputsArray);
            JS.openDraggableModal(form.attr('type'), 'add', null);
            setTimeout(function () {
                //sets radio button
                var radioSelected = $('body').find('input[type="radio"][value="'+radio+'"]');
                    radioSelected.trigger('click');
                //sets top part
                for (var key in oldFormInputs) {
                        var thisInput = $('body').find('form[type="'+ form.attr('type') +'"]').find('section:first').find('input[name="'+ key +'"]');
                        if(key.includes("date")){
                            var date2 = new Date(oldFormInputs[key]);
                            var shownDate =    (date2.getMonth() + 1) + '/' + date2.getDate() + '/' + date2.getFullYear();
                            $(thisInput[0]).val(shownDate);
                            $(thisInput[1]).val(oldFormInputs[key]);

                        }
                        //for radio buttons
                        else if($(thisInput).attr('type') == 'radio'){
                            }
                        else{//for other inputs
                            $(thisInput).val(oldFormInputs[key]);
                        }
                    }
                    //sets transactions
                    var thisTd = $('body').find('form[type="'+ form.attr('type') +'"]').find('table').find('tbody').find('td');
                    for (var i = 0; i < thisTd.length; i++) {

                        if($(thisTd[i]).hasClass('formGridSelectTd')){
                           //$(thisTd[i]).find('input[type=hidden]').val(oldFormInputsArray[i]).trigger('change');
                             var thisLi = $(thisTd[i]).find('li[value="'+oldFormInputsArray[i]+'"]');
                                console.log(thisLi);
                                var value =  $(thisLi).val();
                                var text =  $(thisLi).text();
                                if(value > 0){
                                    $(thisTd[i]).find('input:first').val(text);
                                    $(thisTd[i]).find('input').eq(1).val(value);
                                    console.log(value);
                                    console.log(text);
                                     $(thisTd[i]).find('input').click();
                                }
                        }else{
                            if($(thisTd[i]).find('input').attr('id') == 'debit'){
                                var oldCredit  = oldFormInputsArray[i + 1];
                                $(thisTd[i]).find('input').val(oldCredit).change();
                                $(thisTd[i + 1]).find('input').val(oldFormInputsArray[i]).change();
                                i++;
                            }else{
                                $(thisTd[i]).find('input').val(oldFormInputsArray[i]).change();
                            }
                        }
                    }
            }, 5000);
            //console.log("yes reversal");
        }else{
            console.log("no reversal");
        }
    }
});
            //JS.showAlert("warning", "Creating reversal will save the old one", this);
            //if(form.find('input[name="confirm"]') == true){
           // }
        }); */
        $('body').on('click', '.depositButton', function () {
            var tr = $(this).closest('#content').find('#checkButton');
            var accountId = $(tr).attr('data-account-id');
            JS.openDraggableModal('8', 'add', null, null, {account_id:accountId});
        });
        $('body').on('click', '.delete-row', function (e) {
            e.preventDefault();
            var table = $(this).closest('.dataTable').DataTable();
            var typeId = $(this).closest('.tabcontent').attr("data-id");
            var objId = $(this).closest('tr').attr('id');

            if( objId !== undefined){
                $(this).closest('.tabcontent').append('<input type="hidden" name="delete['+typeId+']['+ objId +']" value="'+ objId +'" > </input>');
             }
            table.row($(this).closest('tr')).remove().draw();

        });
        $('body').on('click', '.delete-confirm', function (e) {
            e.preventDefault();
            $(this).closest('tr').fadeOut(200).remove();
        });
        $('body').on('click', '.addToTable', function () {
            var that = this;
            var table = $(this).closest('.tabcontent').find('.table-c');
            var row = $(that).closest('tr');
            var temp = row.find('[name="temp[ind]"]').val();
            $.post($(this).attr('source'), Formatter.getRowFields(row),
                function (result) {
                    Formatter.addDTRowFromId(table, result);
                    row.find('[name="temp[ind]"]').val(parseInt(temp) + 1);
                }, 'json');
             //$(this).find('td:first-child').append('<div class="shadow" style="width: 1205px"></div>');



        });
        $('body').on('click', '.leasesfilter', function (e) {
            e.preventDefault();
            if($('body').find('.leasesfilterdiv').length == 0) {
                var block = '<div class="leasesfilterdiv mb-1" style="display:none;">' +
                    '<label for="leases0" class="custom-checkbox"><input type="checkbox" value="1" filterid="0" id="leases0" class="hidden" aria-hidden="true"><div class="input"></div>Not active</label>' +
                    '<label for="leases1" class="custom-checkbox"><input type="checkbox" value="1" filterid="1" id="leases1" class="hidden" aria-hidden="true"><div class="input"></div>Past</label>' +
                    '<label for="leases2" class="custom-checkbox"><input type="checkbox" value="1" checked filterid="2" id="leases2" class="hidden" aria-hidden="true"><div class="input"></div>Current</label>' +
                    '<label for="leases3" class="custom-checkbox"><input type="checkbox" value="1" checked filterid="3" id="leases3" class="hidden" aria-hidden="true"><div class="input"></div>Future</label>' +
                    '</div>';
                $(block).insertAfter($(this).closest('form'));
            }
            var block = $('body').find('.leasesfilterdiv');
            block.slideToggle(100);
        });
        $('body').on('click', '.activefilter', function (e) {
            e.preventDefault();
            if($('body').find('.leasesfilterdiv').length == 0) {
                var block = '<div class="leasesfilterdiv mb-1" style="display:none;">' +
                    '<label for="leases0" class="custom-checkbox"><input type="checkbox" value="1" filterid="0" id="leases0" class="hidden" aria-hidden="true"><div class="input"></div>Not active</label>' +
                    '</div>';
                $(block).insertAfter($(this).closest('form'));
            }
            var block = $('body').find('.leasesfilterdiv');
            block.slideToggle(100);
        });

        $('body').on('click', '.instant-search-container', function (e) {
                link = $(e.target).closest('.instant-search-container');
                $("#rightPopup").show(); 
                $("#rightPopup").css("z-index", JS.maxZindex++);          
                JS.loadRight($("#rightPopupWrapper"), 'layout/getRightColumn' + '?type=' + $(link).attr('data-type') + '&id=' + $(link).attr('data-id') + '&pid=' + $(link).attr('data-pid')+ '&lid=' + $(link).attr('data-lid'));
                $('#search-container-wrapper').hide();
        });

        $('body').on('click', '.reportName', function (e) {
            link = $(e.target);
            console.log(link);
            $("#rightPopup").show(); 
            $("#rightPopup").css("z-index", JS.maxZindex++);          
            JS.loadRight($("#rightPopupWrapper"), 'layout/getRightColumn' + '?type=' + $(link).attr('data-type') + '&id=' + $(link).attr('data-id') + '&pid=' + $(link).attr('data-pid')+ '&lid=' + $(link).attr('data-lid'));
            $('#search-container-wrapper').hide();
        });
                   
        $('body').on('click', '.cpopup-trigger', function () {
            console.log($(this).attr('manual'));
            if($(this).attr('manual')) return;
            var popup = $(this).parent().parent().find($(this).data('target'));
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
            console.log("opening popup");


        });
        $('body').on('click', '.cpopup button, .cpopup a', function (e) {
            //if ($(this).hasClass("cpopup-trigger")) {return;}
            e.stopPropagation();
            $(this).closest('.cpopup').removeClass('flex-wrapper').hide(100).removeClass('open');
            console.log("closing popup");
        });
        $('body').on('click', '.editButton', function (e) {
            e.stopPropagation();
            var modal = $(this).attr('modal').split('|');
            JS.openDraggableModal(modal[0], 'edit', $(this).data('id'), null, { url: modal[1] });
        });


        $('body').on('click', '.voidCheckButton', function (e) {
            e.preventDefault();
            form =  $(this).closest('form');
            formdata = $(form).serializeArray();
            formdata.push({name: "validate", value: "yes"});
            console.log(formdata);

            var href = $(this).attr('href'), button = $(this);
            $.post(href, formdata,
                function (data) {
                    if (data.type == 'success') {
                        bootbox.confirm({
                            message: data.messages ? data.messages : "You sure you want to void this check?",
                            buttons: {
                                confirm: {
                                    label: 'Yes',
                                    className: 'btn-danger'
                                },
                                cancel: {
                                    label: 'No',
                                    className: 'btn'
                                }
                            },
                            callback: function (result) {
                                if (result) {
                                   formdata.pop();
                                    $.post(
                                        href, formdata,
                                         function (data) {
                                           var data = JSON.parse(data);
                                            JS.showAlert(data.type, data.message);
                                            if (data.type == 'success') {
                                                if (button.is('[refresh]')) location.reload();
                                                //JS.loadLeft($('.left-column'), 'layout/getLeftColumn', $('.list-bottom li.active a').attr('type'));
                                                button.closest('.modal').modal('hide');
                                                var modal = $(button).closest('.modal')
                                                JS.openModalsObjectRemove(modal.attr('type'), modal.attr('openModal-id'));
                                            }
                                    });
                                }
                            }
                        });
                    }else{
                        JS.showAlert(data.type, data.message);
                    }
                }, 'json');

        });


        $('body').on('click', '.deleteButton', function (e) {
            e.preventDefault();
            var href = $(this).attr('href'), button = $(this);
            $.post(href, {validate: 'yes'},
                function (data) {
                    if (data.type == 'success') {
                        bootbox.confirm({
                            message: data.messages ? data.messages : "You sure you want to delete this item?",
                            buttons: {
                                confirm: {
                                    label: 'Yes',
                                    className: 'btn-danger'
                                },
                                cancel: {
                                    label: 'No',
                                    className: 'btn'
                                }
                            },
                            callback: function (result) {
                                if (result) {
                                    $.get({
                                        url: href,
                                        success: function (data) {
                                            JS.showAlert(data.type, data.message);
                                            if (data.type == 'success') {
                                                if (button.is('[refresh]')) location.reload();
                                                //JS.loadLeft($('.left-column'), 'layout/getLeftColumn', $('.list-bottom li.active a').attr('type'));
                                                button.closest('.modal').modal('hide');
                                                var modal = $(button).closest('.modal')
                                                JS.openModalsObjectRemove(modal.attr('type'), modal.attr('openModal-id'));
                                                if(JS.lastSlickCell && $(".right-side").length > 0) JS.loadRight($(".right-side"), 'layout/getRightColumn' + '?type=' + JS.lastSlickCell.dtype + '&id=' + JS.lastSlickCell.did + '&pid=' + JS.lastSlickCell.parent+ '&lid=' + JS.lastSlickCell.lid);
                                            }
                                        },
                                        dataType: 'json'
                                    });
                                }
                            }
                        });
                    }else{
                        JS.showAlert(data.type, data.message);
                    }
                }, 'json');

        });

        $('body').on('change', '#permissions span.custom-control.general input:not([type="hidden"])', function () {
            var section = $.trim($(this).parent().text());
            var checkbox = $(this);
            $.each( $('tbody th[colspan="6"]'), function() {
                if (section == $(this).text()) {
                    $(this).closest('tbody').toggle(checkbox.is(':checked'));
                    if(!checkbox.is(':checked')) $(this).closest('tbody').find($( ":checkbox")).prop('checked', false);

                }
            });
        });

        $(document).on('shown.bs.tab', 'a[data-toggle="pill"]', function (e) {
            alert("tab");
            var tab = $(this).closest('.nav').parent().find($(this).attr('href'));
            tab.find('.table-b').each(function () {
                $(this).DataTable().columns.adjust().draw();
            });
        });
    },

    initTables: function () {
        var tablea = $('.table-a');
        tablea.each(function (t) {
            $(this).DataTable({
                "order": [[0, "desc"]],
                "searching": false,
                "paging": false,
                "bInfo": false,
                "ordering": true,
                "scrollY": $(this).closest('.column-body').innerHeight() - 54 + "px",
                "scrollCollapse": true,
                "drawCallback": function (settings) {
                    $(".dataTables_scrollHeadInner").css({
                        "width": "100%"
                    });
                    $(".dataTables_scrollHeadInner").find('table').css({
                        "width": "100%"
                    });
                    JS.addEmptyRows($(this));
                }
            });
        });
    },

    initModalTables: function (modal) {
        var tableb = $(modal).find('.table-c');
        tableb.each(function (t) {
            console.log("init");
            var table = $(this).DataTable({
                "searching": false,
                "paging": false,
                "bInfo": false,
                "ordering": false,
                "scrollY": "500px",
                "scrollCollapse": true,
                "drawCallback": function (settings) {
                    /*$(".dataTables_scrollHeadInner").css({
                        "width" : "100%"
                    });

                    $(".dataTables_scrollHeadInner").find('table').css({
                        "width" : "100%"
                    });*/
                }
            });
            $(this).closest('.modal').on('shown.bs.modal', function () {
                table.columns.adjust().draw();
                var sb = $(this).find('.dataTables_scrollBody');
                //sb.css('max-height', $(window).height() - sb.offset().top - 200);
            });
        })
    },

    addEmptyRows: function (table) {
        var iColumns = table.find('th').length;
        var tWrapper = table.closest('.dataTables_wrapper');
        var count = parseInt((tWrapper.closest('.column-body').outerHeight() - tWrapper.outerHeight()) / (tWrapper.outerHeight() / (table.find('tr').length)));
        for (var i = 0; i < count; i++)
            table.append("<tr class='even'><td>&nbsp;</td>" + Array(iColumns).join("<td>&nbsp;</td>") + "</tr>");

    },

    initMobile: function () {
        $('[data-toggle="slide-collapse"]').on('click', function () {
            $navMenuCont = $($(this).data('target'));
            $navMenuCont.toggle("slide", {
                direction: "left"
            }, 500);
            $(".menu-overlay").fadeIn(500);

        });
        $(".menu-overlay").click(function (event) {
            $(".navbar-toggle").trigger("click");
            $(".menu-overlay").fadeOut(500);
        });
    },

    listToTree: function(item) {
        const id = item ? parseInt(item.id.substr(3)) : null;

        const children = JS.ldata
            .filter((item) => item.parent === id)
            .map((item) => JS.listToTree(item))

        return {
            item,
            children
        }
    },

    treeToList: function(tree) {
        const item = tree.item;
        const children = tree.children;

        const childrenList = children
            .map(child => JS.treeToList(child))
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
            .map(subtree => JS.sortTree(subtree, field, isAsk))
            .sort((a, b) => {
                let result;
                if(a.item[field] && a.item[field][0] == '$'){
                    result = (parseFloat(a.item[field].replace(/[$,]+/g,"")) > parseFloat(b.item[field].replace(/[$,]+/g,""))) ? 1 : -1;
                }else
                    result = (a.item[field] > b.item[field]) ? 1 : -1;

                return isAsk ? result : result * -1;
            })
        }
    },

    findAllChildren: function(parent) {
        const id = parseInt(parent.id.substr(3));

        return JS.ldata
          .filter(item => item.parent === id)   // find all direct children
          .map((item) => [item].concat(JS.findAllChildren(item))) // for every child, find it's children
          .reduce((sum, val) => sum.concat(val), [])  // make one big array with all children
    },
    myFilter: function(item) {
        const searchString = JS.treeTableSearchString.toLowerCase() || '';
        var total = $('.leasesfilterdiv').children().length;
        let parent = JS.ldata[item.parent];
        // check if parents are collapsed
        while (parent) {
            if (parent._collapsed) {
                return false;
            }

            parent = JS.ldata[parent.parent];
        }
        const children = JS.findAllChildren(item);
        if(JS.leftFilter.length < total || total == 0) {
            /*parent = JS.ldata[item.parent];
            while (parent) {
                if(JS.leftFilter[0] != '0' && parent.active == false) return false;
                parent = JS.ldata[parent.parent];
            }
            if ([item].concat(children)
                    .map(i =>
                        ((JS.leftFilter && i.dtype == 'lease' && JS.leftFilter.includes(i.lstatus)))
                    ).reduce((sum, val) => sum || val, false) == false) return false;*/
            if(JS.leftFilter  && JS.leftFilter[0] != '0' && item.active == false) return false;
            if(JS.leftFilter && item.dtype == 'lease' && !JS.leftFilter.includes(item.lstatus)) return false;
        }
        parent = JS.ldata[item.parent];
        while (parent) {
            if (parent["col0"].toLowerCase().indexOf(searchString) > -1) {
                return true
            }

            parent = JS.ldata[parent.parent];
        }


        return [item].concat(children)  // get an array of this item and all of it's children
            .map(i =>                     // find if this item or any of it's children satisfy the
            (!searchString || (i["col0"].toLowerCase().indexOf(searchString) > -1))
            ).reduce((sum, val) => sum || val, false);

    },
    loadTreeTable: function (type, searchString = ''){
        $.get(JS.baseUrl+'layout/getLeftData/'+type, function (data) {
            var dataView;
            var grid;
            JS.treeTableSearchString = searchString;
            JS.ldata = data.data;
            if(data.cols == null) {
                $("aside.left-side .tree-table").fadeTo(100, 1);
                return;
            }

/*             $('#psearch').keyup(_.debounce(function (e) {
                Slick.GlobalEditorLock.cancelCurrentEdit();

                // clear on Esc
                if (e.which == 27) {
                  this.value = "";
                }

                JS.treeTableSearchString = this.value;
                dataView.refresh();
            } , 200)); */

            $('#psearch').on('input', (function (e) {
                Slick.GlobalEditorLock.cancelCurrentEdit();

                // clear on Esc
                if (e.which == 27) {
                  this.value = "";
                }

                JS.treeTableSearchString = this.value;
                dataView.refresh();
            }));


            var NameFormatter = function (row, cell, value, columnDef, dataContext) {
                if (value == null || value == undefined || dataContext === undefined) { return ""; }
                value = value.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
                var spacer = `<span id= ${dataContext.did} style='display:inline-block;height:1px;width:` + (15 * dataContext["indent"]) + "px'></span>";
                var idx = JS.ldata.indexOf(dataContext);
                value = dataContext.icon+value;
                if (JS.ldata[idx + 1] && JS.ldata[idx] && JS.ldata[idx + 1].indent > JS.ldata[idx].indent) {
                    if (dataContext._collapsed) {
                        return spacer + " <span class='toggle expand'></span>" + value;
                    } else {
                        return spacer + " <span class='toggle collapse'></span>" + value;
                    }
                } else {
                    return spacer + " <span class='toggle'></span>" + value;
                }
            };
            var columns = [];
            for(var i in data.cols){
               var column = {id: "col"+i, name: data.cols[i]['title'], field: "col"+i, width: data.cols[i]['width'], sortable: true, cssClass:data.cols[i]['class'] || ''};
               if(i == 0) column.formatter = NameFormatter;
                   columns.push(column);
            }

            // initialize the model test
            dataView = new Slick.Data.DataView({inlineFilters: true});
            JS.dataView = dataView;
            JS.leftFilter = ['2','3'];
            // console.log(JS.ldata);
            dataView.beginUpdate();
            dataView.setItems(JS.ldata);
            dataView.setFilter(JS.myFilter);
            dataView.endUpdate();
            // initialize the grid
            var options = {forceFitColumns: true, rowHeight: 32};
            grid = new Slick.Grid("aside.left-side .tree-table", dataView, columns, options);
            var columnField = "col0";

            $('body').on('change', '.leasesfilterdiv input, .activefilterdiv input', function (e) {
                e.preventDefault();
                JS.leftFilter = $('body').find('.leasesfilterdiv input:checked').map(function(){return $(this).attr('filterid')}).get();
                dataView.refresh();
                grid.invalidate();
            });
            var gridSorter = function(columnField, isAsc, grid, gridData) {
                var sign = isAsc ? 1 : -1;
                var field = columnField
                gridData.sort(function (dataRow1, dataRow2) {
                    var value1 = dataRow1[field], value2 = dataRow2[field];
                    var result = (value1 == value2) ?  0 :
                        ((value1 > value2 ? 1 : -1)) * sign;
                    return result;
                });
                grid.invalidate();
                plugin = new Slick.AutoTooltips({ enableForHeaderCells: true });
                grid.registerPlugin(plugin);
                grid.render();
            };
            //gridSorter(columnField, true, grid, dataView);
            grid.setSortColumn(columnField, true);

            grid.onSort.subscribe(function (e, args) {
                let tree = JS.listToTree(null);

                tree = JS.sortTree(tree, args.sortCol.field, args.sortAsc);
                const newData = JS.treeToList(tree);

                dataView.setItems(newData);
            });
            dataView.onRowsChanged.subscribe(function (e, args) {
                grid.invalidateRows(args.rows);
                plugin = new Slick.AutoTooltips({ enableForHeaderCells: true });
                grid.registerPlugin(plugin);
                grid.render();
            });
            grid.onCellChange.subscribe(function (e, args) {
                dataView.updateItem(args.item.id, args.item);
            });
            dataView.onRowCountChanged.subscribe(function (e, args) {
                grid.updateRowCount();
                plugin = new Slick.AutoTooltips({ enableForHeaderCells: true });
                grid.registerPlugin(plugin);
                grid.render();
            });

            grid.onActiveCellChanged.subscribe(function(e, args) {
                const item = dataView.getItem(args.row);
                $(".left-side").attr('data-activeId', item.did);
               if (!JS.activecell || item.id !== JS.activecell.id ) {  // do not reload if user clicks on the same row
                    JS.activecell = item;
                    // JS.newLease(item);
                    route = 'layout/getRightColumn';
                    JS.loadRight($(".right-side"), route + '?type=' + item.dtype + '&id=' + item.did + '&pid=' + item.parent+ '&lid=' + item.lid);
                    JS.lastSlickCell = item;

                    if(item.dtype == 'account'){//used for date mortgages
                        setTimeout(() => JS.datePickerInit($('body').find('.right-side').find('.tabFunction').find('#setup'))
                                        ($('body').find('.right-side').find('.tabFunction').find('#setup').find('.editable-select').editableSelect())//,
                        ,5000);
                    }else{

                    }
                }
            });

            grid.onDblClick.subscribe(function(e, args) {
                const item = dataView.getItem(args.row);
                JS.activecell = null;
                JS.openDraggableModal(item.dtype, 'edit', item.did);
            });


            grid.onClick.subscribe(function (e, args) {
                var item = dataView.getItem(args.row);
                if (item) {
                    if (!item._collapsed) {
                        item._collapsed = true;
                    } else {
                        item._collapsed = false;
                    }

                    window.setTimeout(() => {dataView.updateItem(item.id, item);JS.activecell = null;}, 200);   // delaying update to avoid messing with dblclick
                }
            });

            $(window).resize(() => {
                grid.resizeCanvas();
            })

            $("aside.left-side .tree-table").fadeTo(100, 1);
            let slickgrid = {grid: grid, dataView : dataView, columns:columns, options:options};
            $("aside.left-side .tree-table").data('slickgrid', slickgrid);

            //$("aside.left-side .tree-table").data('dataView', dataView);
        },'JSON');
        setTimeout(() => console.log('when do we get here')
                ,3000);
    },

    loadLeftTest: function (body, url, type, toClick = null) {
        if(type != 'maintenance'){
                $(body).closest('main').removeClass('cols-maintenance');  
            }else {
                $(body).closest('main').addClass('cols-maintenance');  
                JS.loadRight($(".right-side"), 'layout/getRightColumn?type=maintenance');
            }
        $("aside.left-side").fadeTo(100, 1, function () {
            body.parent("loadleft").toggleClass('col-lg-5 col-xl-4', type == 'tenant' || type == 'lease');
            body.parent().next().toggleClass('col-lg-8 col-xl-9', type != 'tenant' && type != 'lease');
            $.get(JS.baseUrl+url, {type: type}, function (data) {
                body.html(data);
                JS.loadTreeTable(type);
                var info = body.find(".page-info");
                if (info.data("title") != '') $(".navbar-brand h2").text(info.data("title"));
                JS.arrowKeys();
                if(toClick != null)  body.find(toClick).first().trigger('click');

                $('.tooltip-menu').tooltipster({
                    theme: 'tooltipster-shadow',
                    side: 'bottom',
                    contentAsHTML: true,
                    interactive:true,
                    animation: 'fade',
                    debug: 'false',
                    trigger: 'custom',
                    triggerOpen: {
                        click: true
                    },
                    triggerClose: {
                        click: true,
                        scroll: true
                    },
                    animationDuration: 500
                });
                $('.tooltip-menu-logout').tooltipster({
                    theme: 'tooltipster-shadow',
                    side: 'bottom',
                    contentAsHTML: true,
                    interactive:true,
                    animation: 'fade',
                    debug: 'false',
                    animationDuration: 500
                });


                //start timesheet function by yossi
                                var timeSheatResults;
                                var ST;
                                var status;
                                var time1;
                                var startTime;
                                var project;
                                var projectName;
                    $('.timer').tooltipster({
                        theme: 'tooltipster-shadow',
                        side: 'bottom',
                        contentAsHTML: true,
                        interactive:true,
                        animation: 'fade',
                        debug: 'false',
                        animationDuration: 1000,

                    functionReady: function(instance, helper) {
                        
                        var $origin = $(helper.origin);
                                            // we set a variable so the data is only loaded once via Ajax, not every time the tooltip opens

                            if ($origin.data('loaded') !== true) {

                                    console.log("loading data...");

                                    $.get('timesheet/startEndTime', function(results) {
                                        // call the 'content' method to update the content of our tooltip with the returned data.
                                        // note: this content update will trigger an update animation (see the updateAnimation option)
                                        JSON.stringify(results);
                                        timeSheatResults = JSON.parse(results);

                                        //instance.content(results);

                                        // to remember that the data has been loaded
                                        $origin.data('loaded', true);
                                       
                                        status = timeSheatResults.timer;
                                        $('#timerStatus').val(status);
                                        time1 = new Date(timeSheatResults.date+" "+ timeSheatResults.startTime).getTime();
                                            startTime = timeSheatResults.startTime;
                                            console.log(startTime + " loaded from db");
                                            console.log(status + " loaded from db");
                                            projectName = timeSheatResults.project;
                                        timer(time1,startTime,timeSheatResults,projectName);
                                    });

                            } else {
                                    console.log("remembering data...");
                                    console.log(status + " remembered");
                                    var date = new Date();
                                    var now = date.getHours() + ":" + date.getMinutes()+ ":" +date.getSeconds();
                                    var ymd = date.getFullYear()+"-" + ('0' + (date.getMonth() + 1)).slice(-2) +"-" + date.getDate();
                                    startTime = status =  $('#startTime').val();;
                                        time1 = new Date(ymd+" "+ startTime).getTime();
                                        timer(time1,startTime);
                                    }
                            function timer(time1,startTime,timeSheatResults,projectName)  {

                                    //var startDate = timeSheatResults.date;
                                    var selectproject = $('#selectproject').html();
                                        status =  $('#timerStatus').val();
                                        $('#startEnd').text(status);
                                    if (status==='Start') {
                                        $('#startEnd').text('Start');
                                        $('#startEnd').css('background-color','#00be00');
                                        $('#timerInfo').css('display','none');
                                        $('#selectproject').css('display','block');
                                    } else if (status==='End'){
                                        var date = new Date();
                                        var now = date.getHours() + ":" + ('0' + date.getMinutes()).slice(-2)/*+ ":" +date.getSeconds()*/;
                                        var ymd = date.getFullYear()+"-" + ('0' + (date.getMonth() + 1)).slice(-2) +"-" + date.getDate();
                                        var time2 = new Date(ymd+" "+ now).getTime();
                                        var msec = time2 - time1;
                                        var mins = Math.floor(msec / 60000);
                                        var hrs = Math.floor(mins / 60);
                                        mins = mins % 60;
                                        var timer = ('0'+ hrs).slice(-2) + ":" +("0"+ mins).slice(-2);
                                        $('#startTime').val(startTime);
                                        $('#timerInfo').css('display','block');
                                        $('#timern').text(timer);
                                        $('#projectName').text(projectName);
                                        $('#selectproject').css('display','none');
                                        $('#startEnd').text('End');
                                        $('#startEnd').css('background-color','#be0a00');
                                        ST = startTime;
                                    }
                            }
                    }
                });
            });
        });

    },

    loadLeft: function (body, url, type, toClick = null) {
        if(true){JS.loadLeftTest(body, url, type, toClick); return;}
        //$('body').off('click', '.tree-table tr');
        $("aside.left-side:not('.noajax')").fadeTo(100, 1, function () {
            body.parent("loadleft").toggleClass('col-lg-5 col-xl-4', type == 'tenant' || type == 'lease');
            body.parent().next().toggleClass('col-lg-8 col-xl-9', type != 'tenant' && type != 'lease');
            $.get(JS.baseUrl+url, {type: type}, function (data) {
                body.html(data);
                var ttable = $(body).find('.tree-table');

                ttable = $(body).find('.tree-table');
                    ttable.fadeTo(100, 1, function(){setTimeout(function(){JS.makeTree(ttable);ttable.tablesorter({delayInit: true});},0)});
                //$('.tree-table').tablesorter();
                //ttable.find("tr:first").trigger("click");
                var info = body.find(".page-info");
                if (info.data("title") != '') $(".navbar-brand h2").text(info.data("title"));
                JS.arrowKeys();
                //console.log(body.find(toClick).length);
                if(toClick != null)  body.find(toClick).first().trigger('click');

                $('.timer').tooltipster({
                    theme: 'tooltipster-shadow',
                    side: 'bottom',
                    contentAsHTML: true,
                    interactive:true,
                    animation: 'fade',
                    debug: 'false',
                    animationDuration: 1000
                });


                 //start timesheet function by yossi
                 var timeSheatResults;
                 var ST;
                 var status;
                 var time1;
                 var startTime;
                 var project;
                 var projectName;
     $('.timer').tooltipster({
         theme: 'tooltipster-shadow',
         side: 'bottom',
         contentAsHTML: true,
         interactive:true,
         animation: 'fade',
         debug: 'false',
         animationDuration: 1000,
        
     functionReady: function(instance, helper) { 
                            
            var $origin = $(helper.origin);       
                             // we set a variable so the data is only loaded once via Ajax, not every time the tooltip opens
               
             if ($origin.data('loaded') !== true) {
                 
                 
                 
                     $.get('timesheet/startEndTime', function(results) {
                         // call the 'content' method to update the content of our tooltip with the returned data.
                         // note: this content update will trigger an update animation (see the updateAnimation option)
                         JSON.stringify(results);
                         timeSheatResults = JSON.parse(results);
                         
                         //instance.content(results);
                         
                         // to remember that the data has been loaded
                         $origin.data('loaded', true);
                         
                         status = timeSheatResults.timer; 
                         time1 = new Date(timeSheatResults.date+" "+ timeSheatResults.startTime).getTime();
                             startTime = timeSheatResults.startTime;
                             projectName = timeSheatResults.project;
                         timer(time1,startTime,timeSheatResults,projectName);
                     });

             } else {         
                     var date = new Date();
                     var now = date.getHours() + ":" + date.getMinutes()+ ":" +date.getSeconds();
                     var ymd = date.getFullYear()+"-" + ('0' + (date.getMonth() + 1)).slice(-2) +"-" + date.getDate();
                     //startTime = ST;  
                         time1 = new Date(ymd+" "+ startTime).getTime();     
                         timer(time1,startTime);
                     }
             function timer(time1,startTime,timeSheatResults,projectName)  {
                 
                     //var startDate = timeSheatResults.date;
                     var selectproject = $('#selectproject').html();
                         $('#startEnd').text(status);
                     if (status==='Start') {
                         $('#startEnd').text('Start');
                         $('#startEnd').css('background-color','#00be00');
                         $('#timerInfo').css('display','none');
                         $('#selectproject').css('display','block');
                     } else if (status==='End'){
                         var date = new Date();
                         var now = date.getHours() + ":" + ('0' + date.getMinutes()).slice(-2)/*+ ":" +date.getSeconds()*/;
                         var ymd = date.getFullYear()+"-" + ('0' + (date.getMonth() + 1)).slice(-2) +"-" + date.getDate();
                         var time2 = new Date(ymd+" "+ now).getTime();
                         var msec = time2 - time1;
                         var mins = Math.floor(msec / 60000);
                         var hrs = Math.floor(mins / 60);
                         mins = mins % 60;
                         var timer = ('0'+ hrs).slice(-2) + ":" +("0"+ mins).slice(-2);
                         $('#timerInfo').css('display','block');
                         $('#timern').text(timer);
                         $('#projectName').text(projectName);
                         $('#selectproject').css('display','none');
                         $('#startEnd').text('End');
                         $('#startEnd').css('background-color','#be0a00');
                         ST = startTime;
                     }                       
              }                 
      }              
});

            



            });
        });
    },

    loadRight: function (body, url, id = null, pid = null) {

        //body.fadeTo(100, 0, function () {
        //JS.lastSlickCell = null;
        if(JS.rightPost && JS.rightPost.readyState !== 4){
            JS.rightPost.abort();
        }
        JS.rightPost = $.post(JS.baseUrl + url, {}, function(data) {
            body.html(data);
            if (document.getElementById("defaultOpen")) {
                document.getElementById("defaultOpen").className += " active";
            }
            //JS.initTables();
            JS.scroll();
        })
        // });
    },

    getNewItems: function (body, item) {
        var result = [];
        $(body).find(item).each(function () {
            result.push({ id: $(this).val(), name: $(this).attr('unit') });
        });
        return result;
    },

    textAreaAdjust: function (o) {
        var def = $(o).height();
        $(o).height(2);
        $(o).height($(o).prop('scrollHeight') );
        if (def != o.style.height) {
            $(o).closest('.modal').find('.dataTables_scrollBody').css('max-height', "-=" + ($(o).height() - def));
        }
    },
    //modified on 12/12 so value can be an object for receive payments
    loadList: function (url, value, target, body, unitId = null) {
        var target = $(body).find(target).first();
        if(typeof value === 'object'){var value = "'&" +$.param( value );}
        if(target.hasClass('editable-select')){
            $.get(url + "?value=" + value +"", function(data){
                target.editableSelect('resetSelect',data);
                if(unitId){
                    $(target).closest('.select').find('li[value='+unitId+']').remove();
                }
            }) ;
        }else{
            $(body).find(target).load(JS.baseUrl+url + '?value=' + value);
        }
            $(target).find('.editable-select').editableSelect();
    },

    loadList3: function (url, value, target, body) {
        var target = $(body).find(target).first();
        if(typeof value === 'object'){var value = "'&" +$.param( value );}
        //if(target.hasClass('editable-select'))
            $.get(url + "?value='" + value +"'", function(data){
                target.editableSelect('resetSelect',data);
            }) ;
        //else
            //$(body).find(target).load(JS.baseUrl+url + '?value=' + value);
            //$(body).find('.editable-select').editableSelect();
    },

    loadChecks: function (url, value = {}, target, body) {
        var target = $(body).find(target).first();

            $.post(JS.baseUrl+url, {
                'params': JSON.stringify(value)
            }, function (result) {
                console.log(result);
                //return result;
                $(body).find(target).empty();
                $(body).find(target).html(result);
        })
    },

    // loadList2: function (value, accountId, unitId, multiple = null) {
    //         JS.unitsApi(value, unitId, multiple);
    //         JS.accountsApi(value, accountId, multiple);
    // },

    // unitsApi: function(value, td, multiple = null){
	// 		$(td).empty();
	// 		var unitsDropdown = "";
	// 			unitsDropdown +="<option value='0'>None</option>";
	// 		for (var j = 0; j < units.length; j++) {
	// 			if(units[j].property_id == value){
	// 				unitsDropdown += `<option value='` + units[j].id + `'>` + units[j].name + `</option>`;
	// 			}
	// 		}
	// 		unitsDropdown += "</select>";
	// 		//$(td).append(unitsDropdown);
	// 		td.editableSelect('resetSelect',unitsDropdown);
	// 		//$('body').find('.editable-select').editableSelect();
	// 			//unitsDropdown +=`	</select>`;
	// 	},

    //     accountsApi: function(value, td, multiple = null){
    //         var oldAccount = td.closest('td').find('input[type=hidden]').val();
    //         var transId =  td.closest('tr').attr('id')
    //         console.log(oldAccount);
    //         $(td).empty();
    //         var accountsDropdown = `<span class="select">
    //             <select class="w135 editable-select quick-add set-up "  id="accountId2" name="transactions[`+transId+`][account_id]"  modal="" type="table" key=""`;
    //              if(multiple){ accountsDropdown += `onselect="editMultipleAccounts($(this));"`;}
    //             accountsDropdown +=` >`;
    //         accountsDropdown +="<option value='0'>None</option>";
    //     var propertyAccountsArray = [];
    //         for (var i = 0; i < propertyAccounts.length; i++) {
    //             if(propertyAccounts[i].property_id == value){
    //                 propertyAccountsArray.push(propertyAccounts[i].account_id);
    //                 //console.log(propertyAccounts[i]);
    //                 //console.log('second if works!!');
    //             }
    //         }
    //     for (var j = 0; j < accounts.length; j++) {
    //         if(accounts[j].all_props == 1 || propertyAccountsArray.includes(accounts[j].id)){
    //             accountsDropdown += `<option value='` + accounts[j].id + `'`;
    //             if(accounts[j].id == oldAccount){accountsDropdown += ` selected`;}
    //             accountsDropdown += `>` + accounts[j].name + `</option>`;
    //         }
    //     }

    //     accountsDropdown += "</select></span>";
    //     $(td).append(accountsDropdown);
    //     //td.editableSelect('resetSelect',accountsDropdown);
    //     $('body').find('.editable-select').editableSelect();
    //         //unitsDropdown +=`	</select>`;
    // },

    loadBills: function (url, date, vendor, property, target, body) {

        var newUrl = "";

        if(date != null){
            newUrl =  newUrl + "date=" + date;
        }

        if(vendor != null){
            if(date  != null){
                newUrl += '&'
            }
            //var objectValue = "'&" +$.param( vendor );
            objectValue = "vendor=" + vendor;
            newUrl =  newUrl + objectValue;

        }

        if(property != null){
            if(date  != null || vendor  != null){
                newUrl += '&'
            }
            newUrl =  newUrl + "property=" + property
        }

        $(body).find(target).load(JS.baseUrl + url + "?" + newUrl);
    },

    initPropertiesPage: function () {

        $('body').on('click', '.list-bottom a, .list-top a', function () {
            JS.loadLeft($('.left-side'), 'layout/getLeftColumn', $(this).attr('type'));
        });
        $('body').on('click', 'tr[data-mode], a[data-mode]', function () {
            JS.clicks++;
            if (JS.clicks === 1 && ['TR', 'TL'].includes($(this).prop('tagName'))) {
                JS.clickTimer = setTimeout(function () {
                    JS.clicks = 0;
                }, JS.dcDelay);
            } else {
                clearTimeout(JS.clickTimer);
                var parent = null;
                if ($(this).attr('data-parent')) parent = $(this);
                JS.openDraggableModal($(this).attr('data-type'), $(this).attr('data-mode'), $(this).attr('data-id'), parent, {
                    newitems: JS.getNewItems($(this).closest('.modal'), '.tid'),
                    parent_id: $(this).closest('.modal').attr('main-id'),
                    serialized: $(this).find('.serialized').val(),
                    dis: ['#property_id'],
                    url: $(this).attr('url'),
                    property: $(this).closest('.modal').find("#property_id option:selected").val()
                });

                JS.clicks = 0;
            }
        });

        $('body').on('focusout', '.formatCurrency', function (e) {
            var money = number_format($(this).val());
            if( money === 'NaN'){
                $(this).val('Enter a valid number');
            } else {
                $(this).val(money);
            }

        });
        // gets the bank name when you type in the routing number
        $('body').on('focusout', '.getBankName', function (e) {
            //console.log($(this).val());
            var input = $(this);
            $.get(JS.baseUrl+ 'accounts/getBankName/'+$(this).val(), function(data) {
                //console.log(data);
                if(data){
                    input.closest('ul').find('#bank_name').val(data);
                }
            });
        });


        //confirm message when you close a modal and takes it out of the open modals box
        $('body').on('click', '.modal button[type="button"]', function (e) {
            var modal = $(this).closest('.modal'), button = $(this);
            if($(modal).attr('id') != 'utilitiesModal' && $(modal).attr('id') != 'Disburse'){            
                if ($(modal).hasClass('changed')) {
                        if (confirm('Do you want to close without saving?')) {

                            button.closest('.modal').modal('hide');
                            JS.openModalsObjectRemove(modal.attr('type'), modal.attr('openModal-id'));
                        } else {

                        }

                    } else {
                        button.closest('.modal').modal('hide');
                        JS.openModalsObjectRemove(modal.attr('type'), modal.attr('openModal-id'));
                    }
            }else{
                button.closest('.modal').modal('hide');
                JS.openModalsObjectRemove(modal.attr('type'), modal.attr('openModal-id'));
            }


        });

        $('body').on('click', '#globalSearchCloseBtn, .tenantToLeaseClose', function () {
            var modal = $(this).closest('.modal');
            JS.openModalsObjectRemove(modal.attr('type'), modal.attr('openModal-id'));
        });
        $('body').on('click', '#instantSearchCloseBtn', function () {
            $('#search-container-wrapper').fadeOut();
        });
        $('body').on('click', '#rightPopupCloseBtn', function () {
            $('#rightPopup').fadeOut();
            $('#rightPopupWrapper').empty();
        });
        $('body').on('click', '.getCourtNotes', function () {
            var that = this;
            var id = $(this).closest('tr').attr('id');
            var courtForm = `<form style="margin-bottom: 20px;">
            <p>
                <input placeholder = "Comment title" type="text" id="title" name="title">
            </p>
            <input type="hidden" id="object_id" name="object_id" value="`+id+`"/>
            <input type="hidden" name="profile_id" value="`+userId+`"/>
            <textarea placeholder ="Write your comment here" name="note" style="height:50px; border: 1px solid rgba(128, 128, 128, 0.4); border-radius: 5px; background-color: white;    padding-top: 15px; font-size: 13px;"></textarea>
            <a  href ="#" id="submitNote" style="min-width: 50px;font-size: 12px;text-align: right;margin-left:220px;margin-top: 20px;height: 20px;background-color: #e884c7;color: white;text-decoration: none;padding: 5px;border-radius: 7px;">Submit Note</a>
            </form>`;
            var allNotes ="";
                        $.get(JS.baseUrl+ 'notes/getNotes/'+id+'/in_court', function(result) {
                            var notes = JSON.parse(result);
                            console.log(notes);
                            if(notes){
                                for (var i = 0; i < notes.length; i++) {
                                    allNotes +=  `<div style="    margin-bottom: 5px; background-color: white; padding: 5px;  border-radius: 10px; ">
                                                        <p><span style="color: #7f8081; font-weight:bold"> `+ notes[i].name +`</span><span style=" margin-left: 20px; color: #bfbebd; font-size:8px;"> `+ notes[i].note_date +`</span></p>
                                                        <p  style="color: #7f8081; font-weight:bold"> `+ notes[i].title +`</p>
                                                        <p>`+ notes[i].note +`</p>
                                                </div>`;
                                }
                            }else{
                                allNotes += "No Notes.";
                            }
                            $(that).closest('.modal').find('#courtNotes').empty();
                            $(that).closest('.modal').find('#noteForm').empty();
                            $(that).closest('.modal').find('#courtNotes').append(allNotes);
                            $(that).closest('.modal').find('#noteForm').append(courtForm);
                            $(that).closest('.modal').find('.table-c').animate({
                                width: "75%",
                                margin: "0px"
                              }, 200 );
                            $(that).closest('.modal').find('.table-c').css("margin", "0px");
                            //$(that).closest('.modal').find('#in_court_body').css("width", "75%");
                            $(that).closest('.modal').find('#courtNotesDiv').show("slide", { direction: "left" }, 200);

                        });
        });
        //for in court note submit
        // $('body').on('click', '#submitNote', function (e){
        //     e.stopPropagation();
        //     if($(this).closest('form').find())
        //         console.log('before if');
        //         var id = $(this).closest('form').find('#object_id').val();
        //         console.log(id);
        //             var form = $(this).closest('form');
        //             var modal = $(this).closest('.modal');
        //             $.post({
        //                 url: 'notes/addNote/in_court',
        //                 data: new FormData(form[0]),
        //                 success: function (data) {
        //                     modal.find('#in_court_body').find('#'+id).trigger('click');
        //                 },
        //                 error: function (data) {
        //                     console.log('second');
        //                     console.log(data);
        //                 },
        //                 dataType: 'json',
        //                 processData: false,
        //                 contentType: false,
        //             })
        // });
        //utilities notes
        $('body').on('click', '.getUtilitiesNotes', function () {
            var that = this;
            var id = $(this).closest('tr').attr('id');
            var courtForm = `<form style="margin-bottom: 20px;">
            <p>
                <input placeholder = "Comment title" type="text" id="title" name="title">
            </p>
            <input type="hidden" id="object_id" name="object_id" value="`+id+`"/>
            <input type="hidden" name="profile_id" value="`+userId+`"/>
            <textarea placeholder ="Write your comment here" name="note" style="height:50px; border: 1px solid rgba(128, 128, 128, 0.4); border-radius: 5px; background-color: white;    padding-top: 15px; font-size: 13px;"></textarea>
            <a  href ="#" id="submitNote" style="min-width: 50px;font-size: 12px;text-align: right;margin-left:220px;margin-top: 20px;height: 20px;background-color: #e884c7;color: white;text-decoration: none;padding: 5px;border-radius: 7px;">Submit Note</a>
            </form>`;
            var allNotes ="";
                        $.get(JS.baseUrl+ 'notes/getNotes/'+id+'/utilities', function(result) {
                            var notes = JSON.parse(result);
                            console.log(notes);
                            if(notes){
                                for (var i = 0; i < notes.length; i++) {
                                    allNotes +=  `<div style="    margin-bottom: 5px; background-color: white; padding: 5px;  border-radius: 10px; ">
                                                        <p><span style="color: #7f8081; font-weight:bold"> `+ notes[i].name +`</span><span style=" margin-left: 20px; color: #bfbebd; font-size:8px;"> `+ notes[i].note_date +`</span></p>
                                                        <p  style="color: #7f8081; font-weight:bold"> `+ notes[i].title +`</p>
                                                        <p>`+ notes[i].note +`</p>
                                                </div>`;
                                }
                            }else{
                                allNotes += "No Notes.";
                            }
                            $(that).closest('.modal').find('#utilityNotes').empty();
                            $(that).closest('.modal').find('#noteForm').empty();
                            $(that).closest('.modal').find('#utilityNotes').append(allNotes);
                            $(that).closest('.modal').find('#noteForm').append(courtForm);
                            $(that).closest('.modal').find('.table-c').animate({
                                width: "75%",
                                margin: "0px"
                              }, 200 );
                            $(that).closest('.modal').find('.table-c').css("margin", "0px");
                            //$(that).closest('.modal').find('#in_court_body').css("width", "75%");
                            $(that).closest('.modal').find('#utilityNotesDiv').show("slide", { direction: "left" }, 200);

                        });
        });
        //for in court note submit
        $('body').on('click', '#submitNote', function (e){
            var type = $(this).closest('.modal').attr('id');
            if(type == 'utilitiesModal'){type = 'utilities'}
            e.stopPropagation();
            if($(this).closest('form').find())
                console.log('before if');
                var id = $(this).closest('form').find('#object_id').val();
                console.log(id);
                    var form = $(this).closest('form');
                    var modal = $(this).closest('.modal');
                    $.post({
                        url: 'notes/addNote/' + type,
                        data: new FormData(form[0]),
                        success: function (data) {
                            if(type == 'in_court'){
                                modal.find('#in_court_body').find('#'+id).trigger('click');
                            }
                            if(type == 'utilities'){
                                modal.find('#utilities_body').find('#'+id).trigger('click');
                            }
                        },
                        error: function (data) {
                            console.log('second');
                            console.log(data);
                        },
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                    })
        });
        //end utility notes


        $('body').on('click', '.modal button[type="message"]', function(e){
            e.preventDefault();
            button = e.target;

            modal = $(button).closest('.modal');
            ticket_id = $(modal).attr('data-id');
            $.post(`maintenance/addMessage/${ticket_id}`, {text: $($(modal).find('#mmessage')[0]).val(), internal: $(this).hasClass('internal')*1}, function(){
                $($(modal).find('#mmessage')[0]).val('');
                loadMessages(ticket_id);
            });
        });

        $('body').on('click', '.modal button[type="submit"], .notesForm button[type="submit"], .chargeForm button[type="submit"], #addTimeForm button[type="submit"], #unitsUtilitiesForm button[type="submit"], .submitAccount, .pageForm button[type="submit"]', function (e) {
            e.preventDefault();
            var button = $(this);
            $(button).animate({opacity: 0.5,}, 200, function() {$(button).animate({ opacity: 1}, 1000, function() { })});
            if(button.is(':disabled')) return;
            button.prop('disabled', true);
            var form = $(this).closest('form');
            if(button.hasClass('saveAndPrint'))
                {form.find('#saveAndPrint').val(1);
            }
            if(button.hasClass('saveAndPay'))
                {form.find('#saveAndPay').val(1);
            }

            if(button.attr('id') == 'deleteMemorizedTransactions'){
                form.attr('action', 'transactions/deleteMemorizedTransactions');
            }
            
            if(button.attr('id') == 'postSelected'){
                form.attr('action', 'transactions/manualTransactions');
            }
            if (form.hasClass('tableGrid')) {
                button.closest('.modal').trigger("postsubmit", form);
                type = button.closest('.modal').attr('type');
                id = button.closest('.modal').attr('openmodal-id');
                $('body').find('#minMaxCloseBox').find('ul').find('li[id="' + type + '--' +id+'"]').remove();
                delete JS.openModalsObject[id];
                button.closest('.modal').modal('hide');
                return;
            }
            
            if(button.attr('id') == 'deleteUtilities'){
                form.attr('action', 'transactions/deleteUtilities');
            }
            if (form.hasClass('no-submit')) return;
            //var confirmed = true;
            var confirmed;
             //confirmed = JS.validation(form.attr('type'), form);

              confirmed = Validation.validation(form.attr('type'), form,form.attr('specialType'));

              //if(confirmed.leave){return;}
            //if(confirmed.bool){
            var formdata = new FormData(form[0]);
            if(button.hasClass('submitAttachements') && $($(form).find('.attachments-list')[0]).data('files')){
                var files = $($(form).find('.attachments-list')[0]).data('files');
                $(files).each( function(key,file){
                    formdata.append('files['+key+']', files[key]['file']);
                  }

                );
            }
            if(form.find('.formGridSlickTable').length) {
                formdata = form.find('.formGridSlickTable').first().data('fsg').getFormData();
            }
                $.post({
                    url: form.attr('action'),
                    data: formdata,
                    success: function (data) {
                        //data.password = "pass";
                        if(data.auth == 'fail' || data.auth == 'get'){JS.submitPassword(data.auth, button); return;}
                        if(data.nutil){JS.addUtilTogrid(form,data.nutil);}
                        if(button.closest('.modal').attr('type') == 'capital' || button.closest('.modal').attr('type') == 'Disburse'){
                            JS.printStuff(data);
                            //button.closest('.modal').find('.has-table-c').empty().html(data).css({'height': 'calc(100vh - 100px)'}).attr("id","divToPrint");
                            //button.closest('.modal-content').css({'width': '90%','margin': 'auto','position': 'relative'} );
                            //button.closest('.modal').find('header').append('<button type="button" id="printStuff">Print</button>');
                            
                            //button.closest('.modal').find('.remove').remove();                           
                            return;
                        }

                        if(button.closest('.modal').attr('type') == 'Invite' ){
                            slickbody = button.closest('.modal').find('.formGridSlickTable')[0];
                            slick = $(slickbody).data('slickgrid');
                            $.each(data,function(key,res){
                                row = slick.dataView.getRowById(key);
                                selector = $(slick.grid.getCellNode(row,1)).closest('.slick-row');
                                if(res.status =='success'){
                                    
                                    $(selector).css('border','3px solid #71d087');
                                   /*  $(selector).stop()
                                        .css("background-color","#71d087")
                                        .hide(500, function() {
                                            slick.dataView.deleteItem(key);
                                            //that.grid.invalidate();
                                    }); */
                                } else {
                                    $(selector).css('border','3px solid red');
                                }
                            });
                            //button.closest('.modal').find('.has-table-c').empty().html(data).css({'height': 'calc(100vh - 100px)'}).attr("id","divToPrint");
                            //button.closest('.modal-content').css({'width': '90%','margin': 'auto','position': 'relative'} );
                            //button.closest('.modal').find('header').append('<button type="button" id="printStuff">Print</button>');
                            
                            //button.closest('.modal').find('.remove').remove();                           
                            return;
                        }

                        if (data.confirm_a){
                            params = {'confirm_a': data.confirm_a, 'confirm_b': data.confirm_b, 'message':data.message};
                            JS.openDraggableModal('merge', 'add',  null, null, params, null);
                        }
                        
                        if (data.suggested_transactions){
                            inputOptions = [];
                            $.each(data.suggested_transactions,function(key,st){
                               //console.log(value.description);
                               
                               transdata2 = {};
                               transdata2['transdata'] =st.transdata;
                               transdata2['type'] =key;
                               console.log(transdata2);
                               inputOptions.push({text: st.description, value: JSON.stringify(transdata2)});
                            });

                            bootbox.prompt({
								title: "Following are suggested transactions based on the lease info:",
								value: ['1'],
								inputType: 'checkbox',
								inputOptions: inputOptions,
								callback: function (result) {
									//todo send emails based on choice
									console.log({result});

                                  $.post( JS.baseUrl + "/leases/insert_suggested_trans/", { result: result}, function( result ) {
										if (result) {alert('sent');};
								    }); 
							    }
							});

							//$(item).closest('.modal').removeClass('changed');
                        }
                        if(button.closest('.modal').attr('type') == 'Management' ){
                            console.log(data);
                            printChecks.checkPrint(data, $(button).closest('.modal'));                          
                            return;
                        }
                        if(form.attr('type') == 'paybills'){
                            // var dangerFound = data.some(function (el) {
                            //     return el.type === 'danger';
                            //   });
                            //   var typeFound = dangerFound ? 'danger' : 'success';
                            // $(data).each(function(k, v){
                            //     console.log('k'+ k);
                            //     console.log('v'+ v);
                            // })
                            JS.showAlert(data[0].type, data[0].message);
                                if(data.checks.length != 0){
                                    setTimeout(function(){
                                        if(data.checks.length == 1){
                                            printChecks.getCheckNumber(data.checks[0].next_check_num, data.checks, $(button).closest('.modal'));
                                        }else{
                                            printChecks.billsPrint(data.checks, $(button).closest('.modal'), $(button).closest('.modal').find('#check_print'));
                                        }
                                        button.closest('.modal').modal('hide');
                                        JS.openModalsObjectRemove(button.closest('.modal').attr('type'), button.closest('.modal').attr('openModal-id'));
                                    },3000);
                                }else{
                                    button.closest('.modal').modal('hide');
                                    JS.openModalsObjectRemove(button.closest('.modal').attr('type'), button.closest('.modal').attr('openModal-id'));
                                }
                            return;
                        }

                        if(form.attr('type') == 'maintenance'){

                            slickbody = $(document).find('.maintenance-page')[0];
                            slick = $(slickbody).data('slickgrid');
                            ticket_id = data.id;
                                                            
                                
                            post1 = $.post(JS.baseUrl+slick.options.dataUrl, {}, async function (result) {
                                data1 = JSON.parse(result);

                                slick.dataView.beginUpdate();
                                slick.dataView.setItems(data1.data);
                                await slick.dataView.endUpdate();
                                row = slick.dataView.getItemById(ticket_id);
                                let selector;
                                if(row.property_id){
                                    selector = `input[type=checkbox][value=${row.property_id}]`;
                                } 

                                if($(document).find('.cols-maintenance') && $(document).find(selector).length <1){
                                    
                                    addfilter = `<li><label for="cl${row.property_id}"><input type="checkbox" id="cl${row.property_id}" value="${row.property_id}" dtype="property" checked><span>${row.property}</span></label></li>`;
                                    $('div.checkbox-group[field="property_id"]').find('.checklist-a').append(addfilter);
                                    addfilter1 = $(document).find(selector)[0];
                                    label = $(addfilter1).closest('label')[0];
                                    $(label).addClass('active');
                                    $(addfilter1).after('<div class="input"></div>').addClass('hidden').attr('aria-hidden', true).on('click', function () {
                                        $(this).parent('label').toggleClass('active');
                                    });
                                    slick.generateFilters();
                                    
                                }
                                await slick.grid.invalidate();
                                i = slick.dataView.getRowById(ticket_id);
                                if(data.message =='Ticket successfully added.'){
                                    slick.grid.scrollRowToTop(i);
                                }
 
                                selector = $(slick.grid.getCellNode(i,1)).closest('.slick-row');
                                let pos = $(selector[0]).offset();
                                let width = $(selector[0]).width();
                                button.closest('.modal-content').css({'width': '800px'});
                                button.closest('.modal-dialog').animate({maxWidth: width},700);
                                button.closest('.modal-content').css({'position': 'absolute', 'overflow' :'hidden', 'border-radius': '0px' }).animate({ 
                                    height: '65px',
                                    width: width,
                                    top: pos.top-30,
                                    left: pos.left-320,

                                }, 
                                    700 , function() { 
                                        button.closest('.modal-content').animate({
                                            opacity: .1
                                        }, 400, function() {
                                            button.closest('.modal').modal('hide');
                                            JS.openModalsObjectRemove(button.closest('.modal').attr('type'), button.closest('.modal').attr('openModal-id'));
                                        });
                                        
                                    }
                                );

                                $(selector).animate({
                                    opacity: 0.2
                                    }, 700, function() {
                                    $(selector).animate({
                                        opacity: 1
                                        }, 1000, function() {
                                        })
                                    });
                                });
                            return;
                        }


                        if((form.attr('type') == '4' && data.checks && data.checks.length != 0) || (form.attr('type') == '10' && data.checks && data.checks.length != 0)   || form.attr('type') == 'paybill' && data.checks && data.checks.length != 0){                          
                                if(data.checks.length != 0){
                                        printChecks.getCheckNumber(data.checks[0].next_check_num, data.checks, $(button).closest('.modal'));
                                        button.closest('.modal').modal('hide');
                                        JS.openModalsObjectRemove(button.closest('.modal').attr('type'), button.closest('.modal').attr('openModal-id'));
                                    
                                }else{
                                    button.closest('.modal').modal('hide');
                                    JS.openModalsObjectRemove(button.closest('.modal').attr('type'), button.closest('.modal').attr('openModal-id'));
                                    JS.showAlert(data.type, data.message);
                                }

                                if(!data.recId){
                                    return;
                                }
                            
                        }
                        if ($(button).hasClass("slgrid")){
                            griddata = $(form).find('.formGridSlickTable').data('fsg');
                            $.each(data, function (i,item) {                                
                                gridItem = grid.dataView.getItemById(item.msgInfo);
                                delete gridItem['response'];
                                utlId = item.msgInfo;

                                if (item.type == 'danger') {
                                    gridItem['response'] = "error";
                                    gridItem['error'] = item.message;
                                    button.prop('disabled', false);
                                    
                                }

                                if (item.type == 'warning') {
                                    form.find('tr' + utlId).closest('tr').css("border", "2px solid #f3cf65");
                                    var errorTr = form.find('tr'+utlId);
                                    var tdCount = errorTr.children('td').length;
                                    form.find(errorTr).after("<tr class='errors' style='display: table; width: 100%; table-layout: fixed; border-bottom: 1px solid #e0dddd;'><td colspan='"+ tdCount +"' style='color:red;'>"+item.message+"</td><tr>");
                                    //console.log(form[0]);
                                    button.prop('disabled', false);
                                    console.log(item.message);
                                }
                                if (item.type == 'success') {   
                                    delete gridItem['error'];
                                    gridItem['response'] = "success";
                                    gridItem['old_last_paid_date'] = gridItem['date'];
                                    form.find('#closingPassword').remove();
                                    button.prop('disabled', false);
                                }
                                grid.dataView.getItemMetadata = function(utlId)
                                {
                                    var item1 = grid.dataView.getItem(utlId);
                                    if(item1.response =='error') {
                                        
                                        return { cssClasses: 'errorRow' };
                                        
                                    }
                                    else if (item1.response =='success'){
                                         return { cssClasses: 'successRow' };
                                         
                                    } 
                                    
                                };
                                return;
                            });
                            griddata.grid.invalidate();
                            plugin = new Slick.AutoTooltips({ enableForHeaderCells: true });
                            griddata.grid.registerPlugin(plugin);
                            griddata.grid.render();
                            return;
                        }
                        if ($(button).hasClass("grid")){

                            form.find(".errors").remove();
                            
                           
                            $.each(data, function (i,item) {

                                console.log(item.message);
                                utlId = "#"+ item.msgInfo;
                                if (item.type == 'danger') {
                                    console.log(utlId);
                                    var errorTr = form.find('tr#'+item.msgInfo);
                                    //console.log(errorTr);
                                    var tdCount = errorTr.children('td').length;
                                    form.find(errorTr).css("border", "2px solid red");
                                    form.find(errorTr).after("<tr class='errors' style='display: table; width: 100%; table-layout: fixed; border-bottom: 1px solid #e0dddd;'><td colspan='"+ tdCount +"' style='color:red;'>"+item.message+"</td><tr>");
                                    button.prop('disabled', false);
                                    form.find('.fastEditableSelect').fastSelect();
                                    return;
                            }

                            if (item.type == 'warning') {
                                form.find('tr' + utlId).closest('tr').css("border", "2px solid #f3cf65");
                                var errorTr = form.find('tr'+utlId);
                                var tdCount = errorTr.children('td').length;
                                form.find(errorTr).after("<tr class='errors' style='display: table; width: 100%; table-layout: fixed; border-bottom: 1px solid #e0dddd;'><td colspan='"+ tdCount +"' style='color:red;'>"+item.message+"</td><tr>");
                                //console.log(form[0]);
                                button.prop('disabled', false);
                                console.log(item.message);
                                return;
                            }
                            form.find('tr' + utlId).find('.check-a').find('input').trigger( "click");
                            form.find('tr' + utlId).find('.check-a').html('<i class="icon-check" aria-hidden="true" ></i>');
                            form.find('tr' + utlId).find('input').removeAttr('name');
                            form.find('tr' + utlId).find('.link').find('a').remove();
                            form.find('tr' + utlId).css("border" ,"2px solid #1bde1b");
                            form.find('#closingPassword').remove();
                            button.prop('disabled', false);
                          });

                          return;
                        } else {
                            if (data.type == 'danger') {
                                JS.showAlert(data.type, data.message);
                                button.prop('disabled', false);
                                form.find('.is-invalid').removeClass('is-invalid');
                                form.find('.invalid-feedback').remove();
                                data.errors = JSON.parse(data.errors);
                                $.each(data.errors, function (k, e) {
                                    form.find('[name="' + k + '"]').addClass('is-invalid')
                                        .after('<div class="invalid-feedback">' + e + '</div>');
                                });
                                return;
                            }

                            if (data.type == 'warning') {
                                JS.showAlert(data.type, data.message,button);
                                console.log(form[0]);
                                button.prop('disabled', false);
                                return;
                            }

                            if(button.attr('after') != 'duplicate' && !button.hasClass('saveAndPrint')) { 

                                JS.showAlert(data.type, data.message);
                            }

                            if (button.hasClass("autobills")) {
                                route = 'layout/getRightColumn';
                                JS.loadRight($(".right-side"), route + '?type=' + form.attr('formType') + '&id=' + form.find('#object_id').val() /*+ '&pid=' + form.find('#object_id').val()*/);
                               setTimeout(function () {
                                   $("body").find('#autoTab').trigger('click');
                                   }, 2000);
                               return;
                           }
                            button.closest('.modal').trigger("postsubmit", data);
                            if(data.recId){
                                if($('#rec-add').length !== 0) {
                                    rowid = data.transId;
                                    that = $($('#rec-add').find('.formGridSlickTable')[0]).data('fsg');
                                    
                                    let row = that.dataView.getIdxById(rowid);
                                    selector = $(that.grid.getCellNode(row,1)).closest('.slick-row');
                                    $(selector).stop()
                                    .css("background-color","#4ec1fd63")
                                    .hide(500, function() {
                                        that.dataView.deleteItem(rowid);
                                    });
                                }
                                
                                if($('#banktrans').length !== 0) {
                                    let slgrid = $('#banktrans').data('grid');
                            
                                    let rowid = data.transId;
                                    let row = slgrid.dataView.getItem(rowid);
                                    console.log(row);
                                    let obj = slgrid.data.find((o, i) => {
                                        if (o.transaction_id === rowid) {
                                            slgrid.data[i].trans_match = data.recId;
                                            slgrid.grid.invalidate();
                                            return true;
                                             // stop searching
                                        }
                                    });
                                }

                                button.closest('.modal').modal('hide');
                                JS.openModalsObjectRemove(button.closest('.modal').attr('type'), button.closest('.modal').attr('openModal-id'));
                                return;
                            }
                            if (button.hasClass("autoRecSubmit")) {
                                var type = $(button).attr('data-type');
                                var formData1 = new FormData(form[0]);
                                button.closest('.modal').find("#dsearch").val('').trigger("input");
                                button.closest('.modal').find("#crsearch").val('').trigger("input");
                                formData1.append('type',type);
                                refreshRec(formData1, null); 
                                button.prop('disabled', false);
                                return;
                            }
                            JS.openModalsObjectRemove(button.closest('.modal').attr('type'), button.closest('.modal').attr('openModal-id'));
                        }





                        if (button.attr('id') == 'noteSubmitButton'){
                           
                            noteform =  button.closest('.notesForm');
                            title = $(noteform).find('#title');
                            note = $(noteform).find('#note');
                            button.closest('.notesForm').hide();
                            newNote =`<li>
										<header>
											<h3>`+$(title).val()+`</h3>
											<ul>
												<li>You</li>
												<li>${data.date}</li>
											</ul>
										</header>
										<p>`+$(note).val()+`</p>
										<ul class="list-square">
											<li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
											<li><a href="./"><i class="icon-notes"></i> <span>Notes</span></a></li>
											<li><a href="./" class="print"><i class="icon-print"></i> <span>Print</span></a></li>
										</ul>
									</li>`;
                            $('#notesprint').prepend(newNote);
                            console.log($('#notesprint'));
                            $(title).val('');
                            $(note).val('');
                            button.prop('disabled', false);
                             /* route = 'layout/getRightColumn';
                             JS.loadRight($(".right-side"), route + '?type=' + form.attr('formType') + '&id=' + form.find('#object_id').val() /*+ '&pid=' + form.find('#object_id').val()*/
                             /*);
                            setTimeout(function () {
                                $("body").find('#notesTab').trigger('click');
                                }, 2000);*/
                            return; 
                        }

                        if (button.attr('id') == 'chargeSubmitButton'){form[0].reset(); button.closest('#addChargeForm').hide();}
                        if(button.is('[refresh]')) location.reload();

                        //update left tree table
                        var properties = ["property", "unit", "tenant", "lease", "account", "vendor", "employee", "4", "newCharge", "1"];
                            if(properties.indexOf(form.attr('type')) > -1){
                                //get which property/unit/tenant/lease(if new added right side won't open)
                                //var idNum = form.attr('action').split('/');
                                //var idNum = idNum[idNum.length - 1];
                                dataType = $('.left-side').attr('data-type');
                                if(dataType == 'property') dataType = $($($('.list-bottom').find('li.active')[0]).find('a')).attr('type');
                                if(dataType=='employee'){dataType='timesheet'};
                                if(dataType=='vendor'){dataType='vendors'};
                                activeId = $('.left-side').attr('data-activeid');
                                
                                slickgrid = $('.tree-table').data('slickgrid');
                                $.get(JS.baseUrl+'layout/getLeftData/'+dataType, async function (result) {
                                    data1 = JSON.parse(result);

                                    $(JS.ldata).each(function() {
                                        if(this._collapsed == false){
                                            data1.data.find(x => x.did === this.did)._collapsed = false;
                                        }
                                    });
                                    JS.ldata = data1.data;
                                    slickgrid.dataView.beginUpdate();
                                    slickgrid.dataView.setItems(data1.data);
                                    slickgrid.dataView.endUpdate();
                                    //row = slickgrid.dataView.getItemById(idNum);
                                    slickgrid.grid.invalidate();
                            });
                            
                            

                        }

                        var selectUpdates = ["property", "unit", "account", "vendor"];
                        selectType = form.attr('type'); 
                            if(selectUpdates.indexOf(form.attr('type')) > -1){
                                
                                if(selectType=='vendor'){selectType='vendors'};
                                JS.loadSelects(selectType);
                            }
                            if(selectType=='lease' || selectType=='tenant'){JS.loadSelects('tenant');};
                            var selectProfileUpdates = ["vendor", "tenant", "employee", 'investor', 'lease'];
                            if(selectProfileUpdates.indexOf(form.attr('type')) > -1){ 
                                JS.loadSelects('profile');
                            }

                            if($('#banktrans').length !== 0) {
                                rowid = data.transId;
                                that = $('#banktrans').data('grid');
                                console.log(that);
                                
                                let row = that.dataView.getIdxById(rowid);
                                selector = $(that.grid.getCellNode(row,1)).closest('.slick-row');
                                $(selector).stop()
                                .css("background-color","#4ec1fd63")
                                .hide(500, function() {
                                    that.dataView.deleteItem(rowid);
                                    return;
                                });
                            }
                        if(JS.lastSlickCell && $(".right-side").length > 0) JS.loadRight($(".right-side"), 'layout/getRightColumn' + '?type=' + JS.lastSlickCell.dtype + '&id=' + JS.lastSlickCell.did + '&pid=' + JS.lastSlickCell.parent+ '&lid=' + JS.lastSlickCell.lid);

                        if (button.attr('after') == 'mnew'){
                            if(form.attr('action').includes("edit")){
                                console.log('edit!');
                                button.closest('.modal').modal('hide');
                                JS.openDraggableModal(form.attr('type'), 'add', null);
                            }else{
                                button.closest('.modal').modal('hide');
                                JS.openDraggableModal(form.attr('type'), 'add', null);
                            }
                        }else if(button.attr('after') == 'duplicate'){
                            
                            duplmsg = "Simplicity saved the original transaction. You can change whatever you need on this form and save it to create a copy";
                            if(form.attr('action').includes("edit")){
                                
                                mtype = $(form).closest('.modal').attr("type");
                                console.log(mtype);
                                $(form).attr('action',"transactions/add"+mtype);
                                $(form).find('input[name ="header[id]"], input[name ="headerTransaction[id]"], div.paidInFull, div.paidInPartial, div.hasRecId').remove();
                                $(form).find('.formGridTable > input[type = "hidden"]').remove(); 
                                $(form).find('section.a').prepend('<div class="duplicate">Duplicate</div>');
                            }

                            //remove check number
                            if(form.attr('action').includes("check")) $(form).find('input[name ="header[transaction_ref]"]').val(null); 
                             
                            if ($(button).attr('id') == "createReversal"){
                                $(form).find('tr.filledRow').each(function () {
                                    var debit = $(this).find("#debit");
                                    var credit = $(this).find("#credit");
                                    var debitval = $(debit).val();
                                    var creditval = $(credit).val();
                                    $(debit).val(creditval);
                                    $(credit).val(debitval);
                                    duplmsg = "Simplicity saved the original transaction and created a copy with reversed values. You can change whatever you need on this form and save it to create the Reversal";
                                });
                            }
                            JS.showAlert(data.type, duplmsg);
                            button.prop('disabled', false);
                            
                        }else{
                            button.closest('.modal').modal('hide');
                        }

                    },
                    error: function (data) {
                        button.prop('disabled', false);
                        console.log(data);
                    },
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                })
            // }else{

            //     JS.showAlert('danger', confirmed.msg )

            // }

        });

        $('body').on('click', '.modal #email', function (e) {
                       var form = $(this).closest('form'), button = $(this);
                       //if (form.hasClass('no-submit')) return;


                       e.preventDefault();

                           $.post({
                               url: form.attr('email'),
                               data: new FormData(form[0]),
                               success: function (data) {
                                   JS.showAlert(data.type, data.message);
                                   if (data.type == 'danger') {
                                       form.find('.is-invalid').removeClass('is-invalid');
                                       form.find('.invalid-feedback').remove();
                                       data.errors = JSON.parse(data.errors);
                                       $.each(data.errors, function (k, e) {
                                           form.find('[name="' + k + '"]').addClass('is-invalid')
                                               .after('<div class="invalid-feedback">' + e + '</div>');
                                       });
                                       return;
                                   }





                                   button.closest('.modal').trigger("postsubmit", data);
                                   if(button.is('[refresh]')) location.reload();
                                   JS.loadLeft($('.left-side'), 'layout/getLeftColumn', $('.list-bottom li.active a').attr('type'));
                                   if (button.attr('after') == 'mnew')
                                       form[0].reset();
                                   else
                                       button.closest('.modal').modal('hide');
                               },
                               error: function (data) {
                                   console.log(data);
                               },
                               dataType: 'json',
                               processData: false,
                               contentType: false,
                           })


                   });
        $('body').on('click', '.viewEntity', function () {
            var entity_id = $(this).attr('entity-id');
            JS.openDraggableModal('entities', 'edit', entity_id); 
        });
        //for reconcilliation form and for pay bills and edit bill
        $('body').on('dblclick', '.clickable2 tr[data-type]', function (event) {
                    JS.openDraggableModal($(this).attr('data-type'), 'edit', $(this).attr('data-id')); 
            });

        $('body').on('click', 'a.clickable', function (event) {
                JS.openDraggableModal($(this).attr('data-type'), 'edit', $(this).attr('data-id'), null, {lease: $(this).attr('data-lease'),profile: $(this).attr('data-profile')}); 
        });

        $('body').on('dblclick', '#received_payment_row', function (event) {
                JS.openDraggableModal($(this).attr('data-type'), 'edit', $(this).attr('data-id'), null, {lease: $(this).attr('data-lease'),profile: $(this).attr('data-profile')});
        });

        $('body').on('click', '.setting-fields a.add-option', function () {
            var columns = parseInt($(this).siblings().filter('.field-input').last().attr('column'))+1,
                input = '<div class="field-input col" column="'+columns+'"><input type="text" class="form-control" name="fields['+columns+'][name]">' +
                    '          <a href="#" class="delete-field" column="'+columns+'"><i class="fas fa-times-circle"></i></a>' +
                    '</div>';
            $(this).parent().append(input);
            $(this).parent().parent().siblings().not('.setting-fields').find('.delete-option').parent().each(function(){
                input = '<div class="field-input col" column="'+columns+'"><input type="text" class="form-control" name="values['+$(this).attr('row-id')+']['+columns+'][value]"></div>';
                $(this).append(input);
            });
        });
        $('body').on('click', '.setting-fields a.delete-field', function () {
            $(this).closest('.box-list').find('div[column="'+$(this).attr('column')+'"]').remove();
        });

    },

    readURL: function (input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $($(input).closest('.modal').find($(input).attr('targetimg'))).attr('src', e.target.result);
                $('.file-name').html(input.files[0].name);
            }

            reader.readAsDataURL(input.files[0]);
        }
    },

    indication: function (event) {
        $('body').on('click', 'tr', function () {
            //if ($(this).is('.tree-table tr:first')) { return }
            if ($(this).closest("table").hasClass("tree-table")) {
                if (typeof old != 'undefined' && old) {
                    old.toggleClass('on');
                    old = $(this);
                } else {
                    //old.toggleClass('on');
                    old = $(this);
                }
                $(this).toggleClass('on');
            }
        });
    },
    //moving up and down the treetable with arrowkeys
    arrowKeys: function () {//conflicts with scrollbar


            $('table:not(.noarrow) tr').keyup(function(evt){

                var pid;
                var nextRow = $(this);


                    //making sure it focuses only on rows that aren't collapsed
                    do {
                      //if keydown is pressed
                      if(evt.keyCode == 40){
                          nextRow = nextRow.next();
                         }  else if (evt.keyCode == 38){
                            nextRow = nextRow.prev();
                         }
                      pid = nextRow.attr('data-id');
                    } while (nextRow.css("display") == "none");


                nextRow.focus();
                JS.loadRight($(".right-side"), 'layout/getRightColumn?type=' + nextRow.attr('data-type') + '&&id=' + pid + '&pid=' + pid);

            })

    },

    print: function (id = null) {
        //var printPage = "transactionsprint";
        var first = 0;
        var printContents;
        // $('body').on('click', '#printId', function () {
        //     if (first === 0) {
        //         var printPage2 = document.getElementById("transactionsprint");
        //         printContents = document.getElementById(printPage2).innerHTML;
        //     }
        //     else { printContents = document.getElementById(printPage).innerHTML; }
            //$(printContents).css({"color": "yellow", "font-size": "200%"});
            //$('body').on('click', '#printCheck2', function () {
                //JS.openDraggableModal('check', 'print', null);
                var checksId = id;
            printContents = document.getElementById("check3").innerHTML;

            console.log(printContents);
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            //id.closest('.modal').modal('hide');
            //first++;
       // });
    },

    scroll: function () {
        $(document).ready(function () {
            //set table hieght to max
            // $('.table-wrapper').each(function () {
            //     console.log($(this).offset().top)
            //     $(this).css('max-height', $(window).height() - $(this).offset().top - 30);
            // });

        });
/*         $(window).on('resize', function () {

            $('.table-wrapper').each(function () {
               // console.log($(this).offset().top)
                $(this).css('max-height', $(window).height() - $(this).offset().top - 30);
                console.log("resize12");
            });
        }); */
    },

    accountTypeForm: function(value, body, target) {
        if (value == "Credit Card") {
            $(body).find(target).load('themes/default/views/forms/account/creditCardTypeAccount.php')
        } else if (value == "Bank") {
            $(body).find(target).load('themes/default/views/forms/account/bankTypeAccount.php')
        } else if (value == "Mortgages") {
            $(body).find(target).load('themes/default/views/forms/account/Mortgage.php')
        } else {
            $(body).find(target).html('')
        }
    },

    forms: function (form = null) {
        form_search.find('label:not(.hidden) + :input:not(select,button)').each(function () {
            $(this).attr('placeholder', $(this).parent().children('label').text()).parent().children('label').addClass('hidden').attr('aria-hidden', true);
        });

        form_children.each(function (k, v) { $(v).css('z-index', (form_children.length - k)); });

        $(':checkbox, :radio').each(function () {
            if ($(this).is('[checked]')) {
                $(this).prop('checked', true).parent('label').addClass('active');
            } else {
                $(this).prop('checked', false).removeAttr('checked');
            }
        });
        check_a.add(table_d.find('.check')).find('label').each(function () {
            $(this).addClass($(this).children(':checkbox, :radio').attr('type'));
        }).children(':checkbox, :radio').after('<div class="input"></div>').addClass('hidden').attr('aria-hidden', true) 
        select_tag.each(function () {
            $(this).wrap('<span class="select"></span>');
            if ($(this).is('[class]')) {
                $(this).parent().addClass($(this).attr('class'));
            }
        });
    },

    checkboxes: function(modal){
        $(modal).find('input[type="checkbox"], input[type="radio"]').off()
        $(modal).find('input[type="checkbox"], input[type="radio"]').click(function(e){
			if($(this).parent().hasClass('radio')) {
					$(this).parents('p, ul:first').find('label').removeClass('active');
                    }
                    e.stopPropagation();
                    $(this).parent('label').toggleClass('active');
         });
    },

    datePickerInit: function(tr = null){
        function addZero (n){
           return n < 10 ? '0'+ n : '' + n;
       }
       var datePicker = tr ? $(tr).find('[data-toggle="datepicker"]') : ('[data-toggle="datepicker"]');
       
       $(datePicker).each(function(){
           var date =  $(this).val().replace(/-/g, '\/');
           var name = $(this).attr("name");
          
           
           if(!isNaN(new Date(date))){
               var date2 = new Date(date);
               var hiddenDate =    date2.getFullYear()  + '/' + addZero(date2.getMonth() + 1) + '/' + addZero(date2.getDate());
               var shownDate =    (date2.getMonth() + 1) + '/' + date2.getDate() + '/' + date2.getFullYear();
               var hiddenInput = `<input type="hidden" name="`+ name +`" value="` + hiddenDate + `"/>`;
           }else{
               var date2 = new Date();
               var hiddenDate =    date2.getFullYear()  + '/' + addZero(date2.getMonth() + 1) + '/' + addZero(date2.getDate());
               var shownDate =    new Date();
               var hiddenInput = `<input type="hidden" name="`+ name +`" value="` + hiddenDate + `"/>`;
           }
           if($(this).closest('.modal').hasClass('edit') && isNaN(new Date(date))){
               $(this).addClass('leaveEmpty');
           }

           $(this).datepicker();
           if($(this).hasClass('leaveEmpty') && isNaN(new Date(date))){
               var hiddenInput = `<input type="hidden" name="`+ name +`" value=""/>`;
               $(this).after(hiddenInput);
               $(this).val( '');
           }else{
               $(this).datepicker('setDate', shownDate);
               $(this).after(hiddenInput);
           }
           $( this).on( "change", function() {
               var newValue = $(this).val();
               var newDate = new Date(newValue);
               var hiddenDate;
               if(!isNaN(newDate)){
                    hiddenDate = newDate.getFullYear()  + '/' + addZero(newDate.getMonth() + 1) + '/' + addZero(newDate.getDate());
               }else{
                    hiddenDate = "";
               }
               $(this).next().val(hiddenDate);
                   
             });
       })
   },

    

    applyRefund: function(profile = null, property, unit, lease){
        JS.openDraggableModal('10', 'add', null, null, {profile:profile, property:property, unit:unit, lease:lease});
        console.log(property);
        console.log(unit);
        console.log(lease);
    },

    receive_payment: function(profile, property, unit, lease){
        JS.openDraggableModal('5', 'add', null, null, {profile:profile, property:property, unit:unit, lease:lease});
    },
    invoice: function(profile){
        JS.openDraggableModal('invoice', 'get', null, null,profile);
    },
    newCharge: function(profile, lease){
        JS.openDraggableModal('6', 'add', null, null,{profile:profile, lease:lease});
    },
    newLease: function(unit){
        JS.openDraggableModal('lease', 'add', null, null, {unit: unit});
    },
    //used for credit cards and bills forms radio buttons
    toggleRadioButtons: function(){
        $('body').on('focusout', '.toggleRadio', function (e) {
            if($(this).val() < 0){
                console.log('first if');
                if(!$(this).closest('.modal').find('.check-a').find('#credit').is('checked')){
                    console.log('second if');
                    $(this).closest('.modal').find('.check-a').find('#credit').trigger('click');
                    var oldAmount = $(this).val();
                    var newAmount = oldAmount.replace("-", "");
                    $(this).val(newAmount);
                }
            }
        });
    },

    checkAll: function(){
        // check all and uncheck all checkboxes
        $('body').on('change', '.selectAllCheckboxes', function () { 	
                var selectAll = $(this).closest('label');
            var rows = $(this).closest('.modal').find('tr:visible .allAccounts');
            var checked = $(this).prop("checked");
            

                rows.each(function(){
                    console.log(checked);
                    if($(this).prop("checked") != checked){
                        $(this).prop("checked",checked);
                        $(this).closest('label').toggleClass('active');
                        $(this).change();
                    }
                    
                })
        });
    },
    //expands, hides, and closes all modals
    minMaxCloseFunctions: function(){
        $('body').on('click', '.min', function(){
            $(this).closest('.modal').hide();
        });
        $('body').on('click', '.maximizeModal', function(){            
            var type_id = $(this).attr('id').split('--');
            console.log(type_id);
            console.log(type_id[0]);
            console.log(type_id[1]);
            var showThisModal = $('body').find('.modal[type="' + type_id[0] + '"][openModal-id="'+type_id[1]+'"]');
            if($(showThisModal).is(":hidden")){
                showThisModal.show("slide", { direction: "right" }, 300);
                setTimeout(function(){
                    showThisModal.css("z-index", JS.maxZindex++);
                },400)
            } else {
                showThisModal.hide("slide", { direction: "right" }, 300);
            }
            
        });
        $('body').on('click', '.max', function(){
                //$(this).closest('.modal').toggleClass('expanded');
                $('body').find('.modal').toggleClass('expanded');
         });
        $('body').on('click', '.close2', function(){
            var typeId = $(this).closest('.modal').attr('type');
            var openId = $(this).closest('.modal').attr('openModal-id');
            $(this).closest('.modal').fadeOut().remove();
            //console.log($(this).closest('.modal'));
            JS.openModalsObjectRemove(typeId, openId);
        });
    },
    //adds the new open modal to the open modal box
    minMaxClose2: function(type, id){
        var liTemplate = `<li id="`+type+`--`+JS.openModal_id+`" class="maximizeModal">`+type;
        liTemplate += id? '-' +id: "";
        liTemplate += `</li>`;
        $('body').find('#minMaxCloseBox').find('ul').append(liTemplate);
        //$('body').find('#openModals').find('ul').append(liTemplate);
        //$('body').find('#openModals').width('+=110');
    },
    //removes open modals from open modal box
    openModalsObjectRemove: function(type, id){
        $('body').find('#minMaxCloseBox').find('ul').find('li[id="' + type + '--' +id+'"]').remove();
        //$('body').find('#openModals').find('ul').find('li[id="' + type + '--' +id+'"]').remove();
        //$('body').find('#openModals').width($('body').find('#openModals').width() - 110);
        //$('body').find('#openModals').width('-=110');
        delete JS.openModalsObject[id];
        console.log(JS.openModalsObject);
        if(jQuery.isEmptyObject(JS.openModalsObject)){
            $('body').find('#minMaxCloseBox').css('display', 'none');
           // $('body').find('#openModals').css('display', 'none');
        }
    },
    //uploads document from modals when user chooses document 
    uploadDocument: function(){
        var newCn = this.cn;
        $('body').on('click', '.uploadDocument', function(){
            console.log('upload clicked');
            var modal = $(this).closest('.modal');
            var input = $('body').find('#attach_document_form').find('#attach_document');
            var form = $(modal).find('form');
            var action = form.attr('action');
            if(modal.attr('main-id') == -1){
               $( form ).append( '<input type="file" id="attach_document_unsaved" name="attach_document" style="visibility: hidden; width: 1px; height: 1px; z-index: 1;" multiple="">' );
               input = form.find('#attach_document_unsaved')[0];
            } else {              
                $(input).attr('doc-type',$(modal).attr('doc-type'));
                $(input).attr('data-id',action.split("/").pop());
            }           
            input.click();
           /*  var form = $(modal).find('form');
            var type = $(modal).attr('doc-type');
            var action = form.attr('action');
            var res = action.split("/").pop();
            $(input).change(function() {
                console.log('submitting input');
                var headerForm = $(input).closest('form');
                    headerForm = new FormData(headerForm[0]);

                $.post({
                url: JS.baseUrl+'documentUpload/attach_document/'+ res + '--'+ type,
                data: headerForm,
                success: function (data) {
                    console.log(data);
                    var data = JSON.parse(data);
                    JS.showAlert(data.type, data.message);
                },
                        error: function (data) {
                            console.log('failed');
                            console.log(data);
                        },
                        //dataType: 'string',
                        processData: false,
                        contentType: false,

                });
                //$(input).val('');
            }); */
        });
    },


    //uploads rec document
    uploadRecDocument: function(){
        $('body').on('click', '#uploadRecDocument', function(e){
            console.log('got to rec');
            var that = this;
            e.stopPropagation();
            var input = $('body').find('#attach_document_form').find('#attach_document');
            input.click();
            var value;
            var id = $(this).closest('td').attr('rec-id');
            $(input).change(function() {
                console.log('submitting input');
                var headerForm = $(input).closest('form');
                    headerForm = new FormData(headerForm[0]);
                    value = $(input).val();

                $.post({
                url: JS.baseUrl+'documentUpload/attach_rec_document/'+ id,
                data: headerForm,
                success: function (data) {
                    console.log(data);
                    var data = JSON.parse(data);
                    JS.showAlert(data.type, data.message);
                    if(data.type == 'success'){
                        var newValue = value.substring(value.lastIndexOf('\\') + 1);
                        var nvalue = newValue.replace(/\s/g, "_");
                        $(that).closest('td').empty().append('<a href="'+JS.baseUrl+ 'uploads/documents/'+ nvalue+'" target="_blank">View</a>');
                    }
                },
                        error: function (data) {
                            console.log('failed');
                            console.log(data);
                        },
                        //dataType: 'string',
                        processData: false,
                        contentType: false,

                });
                $(input).empty();
                
            });
        });
    },
    //deletes a rec
    deleteRec: function(){
        $('body').on('click', '#deleteRec', function(e){
            e.stopPropagation();
            var that = this; 
            var next_tr = $(this).closest('tr').next('tr'); 
            var id = $(this).closest('td').attr('rec-id');
            var type = $(this).closest('td').attr('data-type');
            if($(that).hasClass('lnkDeleteRec')){
                id = $(this).attr('rec-id');
                type = $(this).attr('data-type'); 
            }
            $.post({
                url: JS.baseUrl+`reconciliations/delete/${id}/${type}`,
                success: function (data) {
                    var data = JSON.parse(data);
                    JS.showAlert(data.type, data.message);
                    if(data.type == 'success'){
                        if($(that).hasClass('lnkDeleteRec')){
                            let slgrid = $(that).closest('#banktrans').data('grid');
                            
                            let rowid = $(that).attr('data-id');
                            let row = slgrid.dataView.getItem(rowid);
                            console.log(row);
                            let obj = slgrid.data.find((o, i) => {
                                if (o.transaction_id === rowid) {
                                    slgrid.data[i].trans_match = null;
                                    slgrid.grid.invalidate();
                                    return true; // stop searching
                                }
                            });
                            

                        } else{
                           $(that).closest('tr').remove();
                           next_tr.find('.deleteTd').append('<a href="#" style="color: red;">Delete</a>');
                           next_tr.find('.deleteTd').attr('id', 'deleteRec');  
                        }
                       
                    }
                },
                        error: function (data) {
                            console.log('failed');
                            console.log(data);
                        },
                        processData: false,
                        contentType: false,

                });
        }) 
    },
    //reopens a closed rec
    reopenRec: function(){
        $('body').on('click', '#reopenRec', function(e){
            e.stopPropagation();
            var that = this; 
            //var next_tr = $(this).closest('tr').next('tr'); 
            var id = $(this).closest('td').attr('rec-id');
            $.post({
                url: JS.baseUrl+'reconciliations/reopen/'+ id,
                success: function (data) {
                    console.log(data);
                    var data = JSON.parse(data);
                    JS.showAlert(data.type, data.message);
                    if(JS.lastSlickCell && $(".right-side").length > 0) JS.loadRight($(".right-side"), 'layout/getRightColumn' + '?type=' + JS.lastSlickCell.dtype + '&id=' + JS.lastSlickCell.did + '&pid=' + JS.lastSlickCell.parent+ '&lid=' + JS.lastSlickCell.lid);
                    // if(data.type == 'success'){
                    //     $(that).closest('tr').remove();
                    //     next_tr.find('.deleteTd').append('<a href="#" style="color: red;">Delete</a>');
                    //     next_tr.find('.deleteTd').attr('id', 'deleteRec');
                    // }
                },
                        error: function (data) {
                            console.log('failed');
                            console.log(data);
                        },
                        processData: false,
                        contentType: false,

                });
        }) 
    },
    //password to override validation
    submitPassword: function(password, button){
        if(password == 'fail'){JS.showAlert('danger', 'Incorrect password!');}
        bootbox.prompt({
                                title: "Closing date password",
                                //title: "Password",
                                inputType: "password",
                                buttons: {
                                    confirm: {
                                        label: 'Submit',
                                        className: 'btn-danger'
                                    },
                                    cancel: {
                                        label: 'Cancel',
                                        className: 'btn'
                                    }
                                },
                                callback: function (result) {
                                    if (result) {
                                        $(button).closest('form').append(`<input type="hidden" id="closingPassword" name="header[password]" value="`+ result +`"/>`)
                                        console.log(result);
                                        button.prop('disabled', false);
                                        $(button).trigger('click');                                      
                                    }
                                    console.log('did not hit result');
                                }
                            });
    },
    //adds new utility to utility grid
    addUtilTogrid: function(form, id){
        var that = form;
        console.log('that');
        var firstTr = $('body').find('.modal #utilities_body tr:first').clone();
        console.log($(that).find('#profile_id').val());
        console.log($(that).find('input[name*="property_id"]').val());
        console.log($(that).find('input[name*="default_expense_acct"]').val());
        console.log($(that).find('input[name*="unit_id"]').val());
        var newRow = `<tr id="`+ id +`" role="row" class="allTransactions editing getUtilitiesNotes" style="display: table; width: 100%; table-layout: fixed; border-bottom: 1px solid #e0dddd;">
        <input type="hidden" name="" value="`+ id +`" id="id">
            <td style="width: 4% !important;" class="link">
                 <a href="#" class="selectTransactionClass" onclick="formsJs.expand($(this).closest('td'))"><span class="hidden">More</span></a>
             </td>
            <td style="width: 4%;" class="check-a">
                 <label for="`+ formsJs.checkboxSpot +`" class="checkbox">
                     <input type="checkbox" id="`+ formsJs.checkboxSpot +`" name="`+ formsJs.checkboxSpot +`" class="hidden allAccounts" aria-hidden="true"
                     onchange="formsJs.Checkbox($(this),`+ id +`);">
                     <div class="input"></div>
                 </label>
             </td>`;
             newRow +=`<td id="profile_id" style="width: 5%;">` + $(that).find('#payee').val() +`<input type="hidden"  name="" value="` + $(that).find('input[name*="payee"]').val() +`"></td>`;
             newRow +=`<td id="property_id" style="width: 5%;">` + $(that).find('#property_id').val() +`<input type="hidden"  name="" value="` + $(that).find('input[name*="property_id"]').val() +`"></td>`;
             newRow +=`<td id="unit_id" style="width: 5%;">` + $(that).find('#unit_id').val() +`<input type="hidden"  name="" value="` + $(that).find('input[name*="unit_id"]').val() +`"></td>`;
             newRow +=`<td id="description" style="width: 5%;"><input type="text" class="instantUpdate" value="` + $(that).find('#description').val() +`"></td>`;
             newRow +=`<td id="account" style="width: 5%;">` + $(that).find('#account').val() +`<input type="hidden"  name="" value="` + $(that).find('#account').val() +`"></td>`;
             newRow +=`<td id="utility_type" style="width: 5%;">` + $(that).find('#utility_type').val() +`<input type="hidden"  name="" value="` + $(that).find('input[name*="utility_type"]').val() +`"></td>`;
             newRow +=`<td id="old_last_paid_date" style="width: 5%;"><input type="hidden"  name="" value=""></td>`;
             newRow += `<td id="direct_payment"  class="check-a" style="width: 5%;">`;
             newRow += `<label for="direct_payment_` + formsJs.checkboxSpot +`" class="checkbox`;
             if(that.direct_payment == 1)newRow +=  ' active';
             newRow += `"><input type="hidden" name="" value="0" /><input type="checkbox"  value="1" `;
             if(that.direct_payment == 1)newRow +=' checked';
             newRow +=  ` id="direct_payment_` + formsJs.checkboxSpot +`" name=""  class="hidden" aria-hidden="true"><div class="input"></div></label></td>`;
             newRow +=  `<td style="text-align:center; width: 5%;" id="account_id" class="formGridAccountTd">
                     <span class="select">
                         <select stype="account" default="` + $(that).find('input[name*="default_expense_acct"]').val() +`" class=" fastEditableSelect quick-add set-up "  id="account_id" name=""  modal="" type="table" key="">
                            <option value="-1" selected ></option>	</select>
                     </span>
                </td>`
            newRow += `<td id="billable"  class="check-a" style="width: 5%;">`;
             newRow += `<label for="billable_` + formsJs.checkboxSpot +`" class="checkbox`;
             if($(that).find('#billable').val() == 1)newRow +=  ' active';
             newRow += `"><input type="hidden" name="" value="0" /><input type="checkbox"  value="1" `;
             if($(that).find('#billable').val() == 1)newRow +=' checked';
             newRow +=  ` id="billable_` + formsJs.checkboxSpot +`" name=""  class="hidden" aria-hidden="true"><div class="input"></div></label></td>`;
        newRow += `
            <td id="last_paid_date" style="width: 5%;">
                <input data-toggle="datepicker" id="last_paid_date" class="selectTransactionClass" type="text"  name="" value="` + new Date() + `">
            </td>
            <td id="amount" style="width: 5%;">
            <input type="text" class="selectTransactionClass"  name="" value="" placeholder="Enter Amount">			
            </td>
            <td id="util_usage" style="width: 5%;">
                <input type="text" class="selectTransactionClass"  name="" value="" placeholder="Enter Usage">									
            </td>
            <td id="estimate" class="check-a" style="width: 5%;">
                <label for="estimate` + formsJs.checkboxSpot +`" class="checkbox">
                <input type="hidden" name="" value="0" /><input type="checkbox"  value="1"  id="estimate` + formsJs.checkboxSpot +`" name=""  class="hidden" aria-hidden="true">
                <div class="input"></div></label></td>
                <td id="memo" style="width: 5%;">
                <input type="text"  class="instantUpdate" value="` + $(that).find('#memo').val() +`">									
            </td></tr>`;
        $('body').find('.modal #utilities_body').append(newRow);
        $('body').find('.modal #utilities_body tr:last').find('.fastEditableSelect').fastSelect();
        $('body').find('.modal #utilities_body tr:last').find('.editable-select').editableSelect();
        JS.datePickerInit($('body').find('.modal #utilities_body tr:last'));
    }

};
var JS = new JS();
$(document).ready(function () {
    JS.initPropertiesPage();
    JS.initModals();
    JS.initMobile();
    JS.indication();
    JS.arrowKeys();
   // JS.print();
    //JS.forms();
    JS.loadSelects();
    JS.minMaxCloseFunctions();
    JS.uploadDocument();
    JS.uploadRecDocument();
    JS.deleteRec();
    JS.reopenRec();
    //setTimeout(function(){JS.openDraggableModal('reconciliation', 'start', 1346);},500);
    //setTimeout(function(){JS.openDraggableModal(8, 'edit', 571934, null, {"profile":"994"});},1000);
    function isqlparse(table) {
        var result = {};
        table.find('tr:not(:first)').each(function(){
            result[$(this).attr('ckey')] = $(this).attr('cid');
        });
        return result;
    }

    $('body').on('click', '.sqlParse', function(){
        var table = $(this).closest('.modal').find('table.parsedRows');
        var line = $(this).closest('.modal').find('textarea#query').val();
        var regex = /{s}(.*?){\/s}/i;
        var regex1 = /(.*?) as (.*)/i;
        var found = line.match(regex)[1];

        found = found.split(', ');
        if(!$(this).data('parsed')) {
            $(this).data('parsed', isqlparse(table));
        }
        console.log(found);
        var parsed = JSON.parse(JSON.stringify($(this).data('parsed')));
        table.empty();
        var tr = $('<tr><th>Table</th>'+
            '<th>Column</th>'+
            '<th>Name</th>'+
            '<th>Group Key</th>'+
            '<th>Type</th>'+
            '<th>Source</th>'+
            '</tr>')
        table.append(tr);
        for(var i in found) {
            var line1 = found[i];
            var found1 = line1.match(regex1);
            var fname = found1[1].split('.');
            var key =fname[0].trim()+'.'+fname[1].trim();
            var tr = $('<tr ckey="'+key+'"><td>'+
                (parsed[key] ? '<input type="hidden" name="field['+i+'][id]" value="'+parsed[key]+'">' : '')+
                '<input readonly name="field['+i+'][table_name]" value="'+fname[0].trim()+'"></td>'+
                '<td><input readonly name="field['+i+'][column_name]" value="'+fname[1].trim()+'"></td>'+
                '<td><input name="field['+i+'][name]" value="'+found1[2].trim().replace(/'/g, '')+'"></td>'+
                '<td><input name="field['+i+'][key_column]" value=""></td>'+
                '<td><input name="field['+i+'][type]" value="text"></td>'+
                '<td><input name="field['+i+'][source]" value=""></td>'+
                '</tr>')
            table.append(tr);
            if(parsed[key]) parsed[key] = 'found';
        }
        for(var i in parsed) {
            if(parsed[i] != 'found') table.append('<input type="hidden" name="delete[]" value="'+parsed[i]+'">');
        }

    })

    $('body').on('click', '.duplicateReport', function(){
        $.post('reports/duplicateReport/'+$(this).data('id'), {}, function(data){
            if(data.redirect != null) window.location = data.redirect;
        }, 'JSON')
    })

    $('body').on('keydown', 'input[type="date"]', function (e) {
        var date = new Date($(this).val());
        if (e.keyCode == 107 || e.keyCode == 109) {
            date.setDate(date.getDate() + 108 - e.keyCode);
            $(this).val(date.toISOString().substring(0, 10));
            return false;
        } 
    });
    $('#error-modal').modal({
        backdrop: true,
        show: false
    });
    $('#warning-modal').modal({
        backdrop: true,
        show: false
    });
    $('body').on('change', "input.upload", function () {
        JS.readURL(this);
    });
    $('body').on('click', "a[href*='#']", function (e) {
        e.preventDefault();
    });
    $('body').on('click', "label", function (e) {
        var target = $(this).attr('for'),
            body = $(this).parent();
        if (target != '') {
              e.preventDefault();
             body.find('#' + target).trigger('click').trigger('focus');
             
        }
    });

    $('body').on('click', '#choosePropertiesDiv #choosePropertiesFooter', function () {

        $(this).closest('#choosePropertiesDiv').fadeOut(200).show();

    });

    $('body').on('click', '#chooseProperties', function (e) {
        e.preventDefault();

        $(this).next('#choosePropertiesDiv').toggle();

    });
    $('body').on('click', '.chooseAccountsDiv #chooseAccountsFooter', function () {

        $(this).closest('.chooseAccountsDiv').fadeOut(200).show();

    });

    $('body').on('click', '.chooseAccounts', function (e) {
        e.preventDefault();

        $(this).next('.chooseAccountsDiv').toggle();

    });

    $('body').on('click', '#exportocsvR', function (e) {
        e.preventDefault();
        exportTableToCSV('filename',e);


    });

    $('body').on('click', '#printIdR', function (e) {
         printPart();


    });

    
});


//new tab function without bootstrap

function tabswitch(evt, tabName, body2) {
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    var modal = body2.closest(".tabFunction");
    tabcontent = modal.find(".tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = modal.find(".tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    //document.getElementById(tabName).style.display = "block";
    modal.find('.tabcontent').removeClass('active');
    modal.find("#" + tabName).css("display", "block").addClass('active');
    var slick = modal.find("#" + tabName).find('.slick-pane.slick-pane-header').parent();
    if(slick.length) slick.data('grid').grid.resizeCanvas();
    evt.currentTarget.parentNode.className += " active";
    printPage = tabName + "print";//for print function

};


// showing and hiding editable select
$(document).on('click', '.select', function(target) {
    var select = target.target;
    width = $(select).outerWidth();
    if($(this).find('.fes').length > 0) return

    if ((width - target.offsetX) <20 ){
        var ul = $(select).closest('span').find("ul")[0];
        if ($(ul).is(":visible")) { $(ul).hide(); } else { $(ul).show();}
    }
 
    
});


//printing tables on the page
function printPart() {
    var id = event.target.id;
    var toBePrint = "";
    var accountName = "";
    var footer ="";

 if (id == "printId"){
        accountName = document.getElementById("title2").innerHTML;
        var reportTable = document.createElement("TABLE");
        var  thToBePrint = document.getElementsByClassName("transaction-t")[0].innerHTML;
        var tRows = document.getElementById("transactionst").innerHTML;
        $(reportTable).append(thToBePrint);
        $(reportTable).append(tRows);
        $("#print_information").append(reportTable);
    }else if (id =="printIdB") {
        toBePrint = document.getElementById("accountList").innerHTML;
        accountName = document.getElementById("accName").innerHTML = "Account List";
    } else if (id == "printIdC"){
        toBePrint = document.getElementById("rightList").innerHTML;
        accountName = document.getElementById("accName").innerHTML = "Property List";
    } else if (id == "printIdD"){
        toBePrint = document.getElementById("rightList").innerHTML;
        accountName = document.getElementById("accName").innerHTML = "Units List";
    }  else if (id == "printIdE"){
        toBePrint = document.getElementById("rightList").innerHTML;
        accountName = document.getElementById("accName").innerHTML = "Tenant List";
    } else if (id == "printIdF"){
        toBePrint = document.getElementById("rightList").innerHTML;
        accountName = document.getElementById("accName").innerHTML = "Leases List";
    } else if (id == "printIdR"){
        accountName = document.getElementById("report-header").innerHTML;

        var reportTable = document.createElement("TABLE");
        var tRows = document.getElementById("table0_wrapper").innerHTML;
        $(reportTable).append(tRows);
        $("#print_information").append(reportTable);
        //document.getElementById("pageHeader").innerHTML = "Reports";
        //document.getElementById("accName").innerHTML = accountName;

    } 
    
     $("#page").addClass("print-section");
     document.getElementById("accName").innerHTML = accountName ;
     var d = new Date();
     document.getElementById('print_header_date').innerHTML =d.getMonth()+"/"+ d.getDate()+"/"+d.getFullYear();
     $("#print_information").append(toBePrint);
     window.print();
     $("#page").removeClass("print-section");
     $("#print_information").empty();
    
};
/*$('body').on('click', '.printModal', function () {
    $('#root').addClass('hide');
    $(this).closest('.modal').addClass('print-section');

    window.print();
    
    $('#root').removeClass('hide');
    $(this).closest('.modal').removeClass('print-section');
});*/


function exportTableToCSV(filename,e) {
    var id = event.target.id;
    var rows = ""; 
    var accountName ="";
    filename = "";
    var button = e.target;

   if (id == "exportocsv"){
    rows =    $("#transactions").find(" tr");
    filename = document.getElementById("title2").innerHTML +" Transactions.csv";

    /*var res = accountName.split(" ");
    var filename;

    for (i = 0; i < 2; i++) { 
        filename += res[i]+" ";
    }
    filename   += ".csv";*/
     
   } else if (id == "exportocsvC") {
    rows =    $(".tree-table").find(" tr");
    filename = "propertyList.csv";
   } else if (id == "exportocsvR") {

     rows = $(button).closest(".modal-content").find("#table0_wrapper tbody tr").not( "thead tr" ).clone();

     $(rows).find("span").replaceWith( " " );
     $(rows).find("table").remove();

     var headerColspan = rows[0].cells.length;
     var header = $(button).closest(".modal-content").find("#report-header h2, #report-header h4");
     var headerRows
     $(header).each(function(){headerRows += '<tr><td>'+this.text+'</td></tr>'});



     filename = document.getElementById("title2").innerHTML +" Report.csv";
   }
    var csv = [];
     
    for (var i = 0; i < rows.length; i++) 
    {
        var row = []; 
        var cols = rows[i].querySelectorAll("td, th");
       
        
        for (var j = 0; j < cols.length; j++) {
            var rowText = '"' +cols[j].innerText + '"';
            row.push(rowText);
      
        }
        
        csv.push(row.join());   
 
    }

    //console.log('downloadcsv');
    // Download CSV file
    downloadCSV(csv.join("\n"), filename);
    
}







function downloadCSV(csv, filename) {
    var csvFile;
    var downloadLink;

    // CSV file
    csvFile = new Blob([csv], {type: "text/csv"});

    // Download link
    downloadLink = document.createElement("a");

    // File name
    downloadLink.download = filename;

    // Create a link to the file
    downloadLink.href = window.URL.createObjectURL(csvFile);

    // Hide download link
    downloadLink.style.display = "none";

    // Add the link to DOM
    document.body.appendChild(downloadLink);

    // Click download link
    downloadLink.click();
}

//closing alert boxes

$(".alert-close").click(function() {
    console.log("close");
    $(this).closest(".newAlert").hide();
}

);

$( document ).ajaxSuccess(function( event, xhr, settings ) {
    if (xhr.responseText == 'redirect') {
        window.location = JS.baseUrl+'auth/login';
    }
});

//datepicker
// $('[data-toggle="datepicker"]').datepicker();



$('body').on('click', 'label input', function(e){

            if(!e) return;
            e.stopPropagation();

 });



function number_format (number, decimals = 2, dec_point = '.', thousands_sep = ',') {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}


$('body').on('click', '#startEnd', function(){
    
    var status = document.getElementById("timerStatus").value;
    var date = new Date();
    var ymd = date.getFullYear()+"-" + ('0' + (date.getMonth() + 1)).slice(-2) +"-" + date.getDate();
    var time = date.getHours() + ":" + ('0' + date.getMinutes()).slice(-2)/*+ ":" +date.getSeconds()*/;

            if (status === "Start") {
                    ST = time;
                    startTime = ST;
                    console.log(time);
                    console.log(ST);
                    project = $('#selected option:selected').text();
                    var profile = $('#timerProfile').val();
                    $('#selectproject').css('display','none');
                    $('#timerInfo').css('display','block');
                    $('#timern').text('00:00');
                    $('#projectName').text(project);
                    $(this).css('background-color','#be0a00');
                    $(this).text('End');
                        $.ajax({
                                url     : 'timesheet/startTime',
                                method    : 'POST',
                                    data :{
                                    'start_date':ymd,
                                    'start_time':time,
                                    'project':project,
                                    'profile': profile
                                    },
                            
                                error : function(e)
                                {
                                alert("data not sent")
                                }
                        })
                        document.getElementById("timerStatus").value = 'End';
                        document.getElementById("startTime").value = ST;
                }
                else if (status === 'End') { 
                    var profile = $('#timerProfile').val(); 
                        $.ajax({
                                url     : 'timesheet/endTime',
                                    method    : 'POST',
                                    data :{
                                'end_time':time,
                                'profile':profile
                                    },
                                
                                error : function(e)
                                {
                                alert("data not sent")
                                }
                        })
                        $('#timerInfo').css('display','none');
                        $('#selectproject').css('display','block');
            
                        $('#startEnd').css('background-color','#00be00');
                        $(this).text('Start');
                        document.getElementById("timerStatus").value = 'Start';
                        
                } 
    })


    $('body').on('click', '#printStuff', function(e){
        e.preventDefault();
        console.log("printstuff");
        var modal = $(this).closest('.modal');
        var divToPrint =  $(modal).find('#divToPrint').html();
        $('#Checkarea').empty();
        $('#Checkarea').append(divToPrint);
        $('#Checkarea').addClass('print-section2');
        window.print();
        
    });

    $('body').on('click', '#refreshRec', function(e){
        var modal = $(this).closest('.modal');
        e.stopPropagation();
        var type = $(this).attr('data-type');
        var form = $(modal).find('form')[0];
        var formData1 = new FormData(form);
        formData1.append('type',type);
        refreshRec(formData1, 'Successfully Refreshed!');        
    });


    function refreshRec(formData1, message){
        $.ajax({
            type: "POST",
            url: JS.baseUrl+'reconciliations/refresh',
            data: formData1,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                updateSlick(credit, response.rightColumn, 'trans_match');
                updateSlick(debit, response.leftColumn, 'rec_id');

                function updateSlick(column, resdata, field){
                    column.data = resdata;
                    for(var i in column.data) {
                        if(column.data[i][field]) column.data[i]['check'] = true;
                        column.generateField(column.data[i]);
                    }
                    column.dataView.setItems(resdata);
                    column.grid.invalidate();
                    column.updateTotals();
                }
                if (message != null){
                    alert(message);
                }
                    
            }
        });
    };



        $('body').on('change', '#attach_document', function(e){
            console.log('upload changed');
            input = e.target;

            var type = $(input).attr('doc-type');
            var res = $(input).attr('data-id');
            if (input.files.length == 0){
                console.log("no files selected");
            }

                var headerForm = $(this).closest('form');
                    headerForm = new FormData(headerForm[0]);

                $.post({
                url: JS.baseUrl+'documentUpload/attach_document/'+ res + '--'+ type,
                data: headerForm,
                success: function (data) {
                    console.log(data);
                    var data = JSON.parse(data);
                    JS.showAlert(data.type, data.message);
                },
                        error: function (data) {
                            console.log('failed');
                            console.log(data);
                        },
                        //dataType: 'string',
                        processData: false,
                        contentType: false,

                });
                $(input).val('');
        });
        

        $('body').on('click', '#processPayment', function(e){
            bank_account = $(e.target).attr('data-id');
            profile_id = $(e.target).closest('form').attr('action').split("/").pop();
            account_mask = $(e.target).closest('tr').attr('data-accno');
            account_type = $(e.target).closest('tr').attr('data-acctype');
            JS.openDraggableModal('init_payment', 'add', null, null, {bank_account:bank_account, profile_id:profile_id, account_mask: account_mask, account_type : account_type });

        });

        $('body').on('click', '#addAutoPay', function(e){
            modal = $(e.target).closest('.modal');
            bank_account = $(e.target).attr('data-id');
            profile_id = $(e.target).closest('form').attr('action').split("/").pop();
            account_mask = $(e.target).closest('tr').attr('data-accno');
            account_type = $(e.target).closest('tr').attr('data-acctype');
            JS.openDraggableModal('tenant_autopay', 'add', null, null, {bank_account:bank_account, profile_id:profile_id, account_mask: account_mask, account_type : account_type });

        });
        
        $('body').on('click', '#deleteAutoPay', function(e){
            button = e.target;
            schedule_id = $(button).attr('data-schedule');
         

            data1 = {schedule_id : schedule_id, lease_id:1, profile_id: 1, uid:1};
            $.post(JS.baseUrl+'tenantapi/enableRecurring', JSON.stringify(data1), function (data) {
                    
                var data = JSON.parse(data);
                if(data.status == 1){
                    JS.showAlert('success', 'Autopay schedule succesfully deleted');
                    $(button).text('Add Autopay');
                    $(button).attr('id', 'addAutoPay');

                } else {
                    JS.showAlert('danger', data.message);
                }

                JS.openModalsObjectRemove(modal.attr('type'), modal.attr('openModal-id'));
            });

        });

        $('body').on('click', '#addPayMethod', function(e){
            type = $(e.target).attr('data-type');
            profile_id = $(e.target).closest('form').attr('action').split("/").pop();
            JS.openDraggableModal('add_pay_method', 'add', null, null, {type:type, profile_id:profile_id });

        });

        $('body').on('click', '#initPaymentSubmitButton', function(e){
            modal = $(e.target).closest('.modal');
            profile_id = $($(e.target).closest('form').find('#profile_id')[0]).val();
            bank_account = $($(e.target).closest('form').find('#bank_id')[0]).val();
            lease_id =$($($(e.target).closest('form').find('#leasesSpan')[0]).find('input[type="hidden"]')[0]).val();
            amount = $($(e.target).closest('form').find('#amount')[0]).val();
            data =  {bank_account : bank_account, amount: amount, lease_id : lease_id, uid: profile_id};
            $.post(JS.baseUrl+'tenantapi/sendPayment', JSON.stringify(data), function (data) {
                    
                    var data = JSON.parse(data);
                    if(data.GatewayStatus == 'Approved'){
                        JS.showAlert('success', 'Payment succesfully charged and added to tenant ledger');

                        JS.loadRight($(".right-side"), 'layout/getRightColumn?type=tenant&&id=' + profile_id + '&lid=' + lease_id);

                    } else {
                        JS.showAlert('danger', data.GatewayErrorMessage);
                    }

                    modal.modal('hide');
                    JS.openModalsObjectRemove(modal.attr('type'), modal.attr('openModal-id'));
                });
        });

        $('body').on('click', '#initAutoChargeSubmitButton', function(e){
            modal = $(e.target).closest('.modal');
            button1 = e.target;
            profile_id = $($(e.target).closest('form').find('#profile_id')[0]).val();
            bank_account = $($(e.target).closest('form').find('#bank_id')[0]).val();
            lease_id =$($($(e.target).closest('form').find('#leasesSpan')[0]).find('input[type="hidden"]')[0]).val();
            amount = $($(e.target).closest('form').find('#amount')[0]).val();
            start_date = $($(e.target).closest('form').find('input[name="start_date"]')[1]).val();
            end_date = $($(e.target).closest('form').find('input[name="end_date"]')[1]).val();
            data =  {bank_account : bank_account, amount: amount, lease_id : lease_id, uid: profile_id, start_date: start_date, end_date :  end_date, enable:true};
            $.post(JS.baseUrl+'tenantapi/enableRecurring', JSON.stringify(data), function (data) {
                    
                    var data = JSON.parse(data);
                    if(data.status == 1){
                        JS.showAlert('success', 'Autocharge successfully set up. the first charge date is :'+data.start_date);
                        console.log(button);
                        $(button).text('Delete Autopay');
                        $(button).attr('id', 'deleteAutoPay');
                        $(button).attr('data-schedule', data.ScheduleId);
                        
                        modal.modal('hide');
                        JS.openModalsObjectRemove(modal.attr('type'), modal.attr('openModal-id'));

                    } else {
                        JS.showAlert('danger', data.message);
                    }
                    
                });
        });

        $('body').on('click', '#new_pay_method', function(e){
            let form = $(e.target).closest('#add_pay_method')[0];
            let modal = $(e.target).closest('.modal');
            let target = $(form).attr('ptype');
            let ptype = '';

            var submitBtn = document.getElementById('new_pay_method');
            submitBtn.disabled = true;
            getTokens(
                function() { 
                    var result = {};
                    $.each($(form).serializeArray(), function() {
                        result[this.name] = this.value;
                    });
                    
                    if (target == 'Bank'){
                        result['token'] = result["xACH"];
                        result['account'] = result["xACH"].substr(0, result["xACH"].indexOf(';'));
                        result['account'] = result['account'].slice(result['account'].length - 4);
                        ptype = 'addBankAccount';
                    } else {
                        result['token'] = result["xCardNum"];
                        result['account'] = result["xCardNum"].substr(0, result["xCardNum"].indexOf(';'));
                        result['account'] = result['account'].slice(result['account'].length - 4);
                        ptype = 'addCCAccount';
                    }
                    $.post(JS.baseUrl+'tenantapi/'+ptype, JSON.stringify(result), function (data) {
                    
                        var data = JSON.parse(data);
                        if(data.status == 1){
                            JS.showAlert('success', 'Payment method added!');
                            tenantModal = $(document).find('.modal[main-id="'+result["profile_id"]+'"]')[0];
                            newrow = `<tr role="row" style="display: hidden;" data-accno = "${data.id}"${target}">
                            <!--<td width="7%" class="text-center">Primary</td>-->
                            <td width="20%" class="text-center">${result['nickname']}</td>
                            <td width="20%" class="text-center">******${result['account']}</td>
                            <td width="20%" class="text-center">${type}</td>
                            <td width="20%" class="text-center"></td>
                            <td width="10%" class="text-center"><a href="#" data-id ='${data.id}' id="processPayment">Process payment</a></td>
                            <td width="10%" class="text-center"><a href="#" data-id ='${data.id}' id="deletePayMethod">Delete</a></td>
                            </tr>`;
                            tbody = $($(tenantModal).find('#paymethodstable')[0]).find('tbody')[0];
                            $(newrow).prependTo(tbody).show('slow');
    
                            //JS.loadRight($(".right-side"), 'layout/getRightColumn?type=tenant&&id=' + profile_id + '&lid=' + lease_id);
    
                        } else {
                            JS.showAlert('danger', data.message);
                        }
    
                        modal.modal('hide');
                        JS.openModalsObjectRemove(modal.attr('type'), modal.attr('openModal-id'));
                    })
                    .fail(function(data) {
                        var data = JSON.parse(data.responseText);
                        console.log(data.message);
                        JS.showAlert('danger', data.message);
                        submitBtn.disabled = false;
                    });
                }
            );
        });

        $('body').on('click', '#deletePayMethod', function(e){
            let row = $(e.target).closest('tr');
            data =  {bank_account : $(e.target).attr('data-id')};

            $.post(JS.baseUrl+'tenantapi/removeAccount', JSON.stringify(data), function (data) {
                    $(row).hide('slow', function(){ $(row).remove(); });
            });
        });

        $('body').on('click', '#warningSubmit', function(e){
            button = $(this).data('button');
            if(button) button.closest("form").append("<input type='hidden' name='confirm' value = 'true'>");
                        $(".newAlert.alert-" + "warning").hide();
						if(button) button.click();
        });

        $('body').on('click', '#warningCancel', function(e){
            $(".newAlert.alert-" + "warning").hide();
        });

        $('body').on('click', '.addtransas', function(e){
            
            e.stopPropagation();
            let popup2 = $("<ul></ul>");
            let transData = JSON.parse($(this).attr('data-transinfo'));
            $(popup2).data('transinfo', transData);
            if(transData.amount > 0){
                popup2.append(`
                        <li class = 'recAddAs' data-id = '15' data-type = 'debit'><i class ="icon-bank"></i> Bank Transaction</li>
                        <li class = 'recAddAs' data-id = '5' ><i class ="icon-documents"></i> Customer Payment</li>
                        <li class = 'recAddAs' data-id = '8' ><i class ="icon-documents"></i> Deposit</li>
                        <li class = 'recAddAs' data-id = '1' data-type = 'debit' ><i class='far fa-file-alt'></i> Journal</li>
                    `);
            } else {
                popup2.append(`
                    <li class = 'recAddAs' data-id = '15' data-type = 'credit' ><i class ="icon-bank"></i> Bank Transaction</li>
                    <li class = 'recAddAs' data-id = '7' ><i class ="icon-documents"></i> Bill payment</li>
                    <li class = 'recAddAs' data-id = '1' data-type = 'credit' ><i class='far fa-file-alt'></i> Journal</li>
                `);
                transData.amount = 0 - transData.amount;
            }

                    
            showMenuContainer(popup2, this, 200);
            
        });

        $('body').on('click', '.recAddAs', function(){
            let type = $(this).attr('data-id');
            prefill_data = $(this).closest('ul').data('transinfo'); console.log(prefill_data);
            newdate = new Date(prefill_data.date);
            defaults = { 
                'account':{ 'selector': 'input[name="headerTransaction[account_id]"]', 'value': prefill_data.bank_account, type:'def' },
                'property': { 'selector': 'input[name="headerTransaction[property_id]"]', 'value': prefill_data.property_id, type:'def'},
                'credit': { 'selector': 'input[name="headerTransaction[credit]"]', 'value': prefill_data.amount, type:'val'},
                'debit': { 'selector': 'input[name="headerTransaction[property_id]"]', 'value': prefill_data.amount, type:'val'},
                'date': { 'selector': 'input[name="header[transaction_date]"]', 'value': `${newdate.getMonth()+1}/${newdate.getDate()+1}/${newdate.getFullYear()}`, type:'dp'},
                'memo': { 'selector': 'input[name="header[memo]"]', 'value': prefill_data.name, type:'val'},
                'ref': { 'selector': 'input[name="header[transaction_ref]"]', 'value': prefill_data.transaction_id, type:'val'},
                'debitHeader': { 'selector': 'input[name="headerTransaction[property_id]"]', 'value': prefill_data.property, type:'val'},
                'hiddenamt': { 'value': `<input type ='hidden' name='bankTransAmt' value='${prefill_data.amount}'>`, type:'append'},
                'hiddenBanktrans': {  'value': `<input type ='hidden' name='bankTransId' id='bankTransId' value='${prefill_data.transaction_id}'>`, type:'append'},
                'hiddenacct': {  'value': `<input type ='hidden' name='bankTransAcct' id='bankTransAcct' value='${prefill_data.bank_account}'>`, type:'append'}
            }
            if(type == 5){
                defaults.credit.selector = '#received_amount';
                defaults.account.selector = 'input[name="account_id"]';
            }
            if(type == 8){
                defaults.account.selector = 'input[name="account_id"]';
            }
            if(type == 7){
                defaults.credit.selector = '#editBillAmount';
                defaults.account.selector = 'input[name="bank"]';
                defaults.property.selector = 'input[name="property"]';
            }
            if(type == 1){
                defaults.property ={'value': `<input type ='hidden' name='bankTransProp' id='bankTransProp' value='${prefill_data.property}'>`, type:'append', 'gridUpdate': true};
                defaults.desc ={'value': `<input type ='hidden' name='bankTransDesc' id='bankTransDesc' value='${prefill_data.name}'>`, type:'append'};
                if($(this).attr('data-type') == 'debit'){
                    defaults.debit ={'value': `<input type ='hidden' name='headerDebit' id='headerDebit' value='${prefill_data.amount}'>`, type:'append'};
                } else {
                    defaults.credit ={'value': `<input type ='hidden' name='headerCredit' id='headerCredit' value='${prefill_data.amount}'>`, type:'append'};
                }
            }

            if(type == 15 && $(this).attr('data-type') == 'credit'){
                defaults.type = { 'selector': 'ul.check-a', 'value': 'credit', type:'toggle'}
            }
            JS.openDraggableModal(type, "add", null, null,{
                defaults:defaults
            },null,null);
            $('#menucontainer').hide();
            $('#menucontainer').empty();
            /* var modal = $('.modal:last');
            console.log(modal);
            console.log($(modal).find('form'));
            $(modal).find('form').append(`<input type ='hidden' name='bankTrans' value='${prefill_data.transaction_id}'>`);
                $(modal).find('form').append(`<input type ='hidden' name='bankTransAmt' value='${prefill_data.amount}'>`);
                $(modal).find('form').append(`<input type ='hidden' name='bankTransAcct' value='${prefill_data.account_id}'>`); */

            

        });
        
        $('body').on('click', '.recDetails', function(e){
            that = e.target;
            
            data =  {rec_id : $(that).attr('rec-id'), type : $(that).attr('data-type'), };
            type = $(that).attr('data-type');
            url = 'reconciliations/getMatched';
            if(type != 'banktrans'){
                url =""
            }

            $.post(JS.baseUrl+url, data, function (data) {
                
                    var popup2 = $('<ul></ul>');
                    var trans = JSON.parse(data);
                    if(type == 'banktrans' && trans.length == 1){
                        JS.openDraggableModal(trans[0].transaction_type, "edit", trans[0].trans_id);
                    } else {
                        $(trans).each(function(){
                            popup2.append(`
                               <li class = "transLink" data-type="${this.transaction_type}" data-id="${this.trans_id}"> <i class ="icon-documents"></i> ${this.transaction_date}<span> $${this.debit - this.credit}</span> <span> ${this.memo}</span><span> ${this.transaction_type}</span></li>          
                            `);
                            
                        });
                     
                        showMenuContainer(popup2, that, 400);
                    }
            });
        });

        
        


        function showMenuContainer(menucontent, el, width){
            let menu = $('#menucontainer');
            $('#menucontainer').empty();
            $('#menucontainer').append(menucontent);
            $('#menucontainer').css({
                'left': $(el).offset().left - width,
                'top': $(el).offset().top - $('#menucontainer').height()+25,
                'width':width
            });
            $('#menucontainer').show();
        }

        $(document).click(function(event) { 
            var $target = $(event.target);
            if(!$target.closest('#menucontainer').length && $('#menucontainer').is(":visible")) {
              $('#menucontainer').hide();
              $('#menucontainer').empty();
            }        
        });

        
        $('body').on('click', '.transLink', function(e){
            e.stopPropagation;
            that = this;
            JS.openDraggableModal($(that).attr('data-type'), "edit", $(that).attr('data-id'));

            $('#menucontainer').hide();
            $('#menucontainer').empty();
        });

        $('body').on('focusout', '#depositBody #amount', function () {
            var modal = $(this).closest('.modal');
            updateBottomTotals(modal);
         });

         function updateBottomTotals(modal){
              
            var amounts = $(modal).find('#depositBody #amount');
            var totalbottomAmount = 0;
            amounts.each(function(){
              var amount = $(this);
              totalbottomAmount += Number(amount.val().replace(',', ''));
            })
            $(modal).find('#deposit-bottom-total').html(totalbottomAmount);
               var topTotal = Number($(modal).find('#total_of_deposit_checks').html().replace(',', ''));
               var bottomTotal = Number($(modal).find('#deposit-bottom-total').html().replace(',', ''));
               var allTotal = topTotal + bottomTotal;
               $(modal).find('#hidden-deposit-total-amount').val(allTotal);
               $(modal).find('#deposit-total').text(allTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
          }
        
  $('body').on('change', '#editBillName, #editBillproperty_id', async function(e){
      //alert('name changed');
      that = e.target;
      //vendor = $($(this).closest('editBillVendorAddress').find('input[type="hidden"]')[0]).val();
      vendor = $($(that).closest('#editBillVendorAddress').find('input[type="hidden"]')[0]).val();
      property = $($($(that).closest('.modal').find('#editBillproperty_id')[0]).find('input[type="hidden"]')[0]).val();
      property = property !== null ? "&property=" + property :'';
      console.log($(that).closest('.modal').find('#editBillproperty_id')[0]);
      console.log(vendor);
      await $($(that).closest('.modal').find('#editBillBody')[0]).load(JS.baseUrl + 'api/getBillTransactions' + "?vendor=" + vendor + property);
      payBill.pbcalcTotal($($(that).closest('form')));
  });

  $('body').on('click', '#deleteTicket', function(e){
    that = $(e.target).closest('li')[0];
    console.log(that);
    ticket = $(that).attr('data-id');
    $.post(JS.baseUrl+'maintenance/deleteTicket/'+ticket, async function (data) {
              var data = JSON.parse(data);
              
              JS.openModalsObjectRemove($(that).closest('.modal').attr('type'), $(that).closest('.modal').attr('openModal-id'));
              that.closest('.modal').remove();

            slickbody = $(document).find('.maintenance-page')[0];
            slick = $(slickbody).data('slickgrid');
            ticket_id = data.id;

            await slick.grid.invalidate();
            i = slick.dataView.getRowById(ticket_id);
            selector = $(slick.grid.getCellNode(i,1)).closest('.slick-row');
            $(selector).stop()
                .css("background-color","#e884c7")
                .hide(500, function() {
                    slick.dataView.deleteItem(data.id);
                    //that.grid.invalidate();
            });

    });
  });

  $('body').on('click', '#ignoreTrans, #deleteIgnore', function(e){
    that = e.target;
    url = $(that).attr('data-url');
    id = $(that).attr('data-id');
    $.post({
        url: JS.baseUrl+`reconciliations/${url}/${id}`,
        success: function (data) {
            var data = JSON.parse(data);
            //JS.showAlert(data.type, data.message);
            if(data.type == 'success'){
                    let slgrid = $(that).closest('#banktrans').data('grid');
                    
                    let rowid = $(that).attr('data-id');
                    let obj = slgrid.data.find((o, i) => {
                        if (o.transaction_id === rowid) {
                            if(url == 'unIgnoreTrans'){slgrid.data[i].trans_match = null;} else {slgrid.data[i].trans_match = 1;}
                            
                            slgrid.grid.invalidate();
                            return true; // stop searching
                        }
                    });
               
            }
        },
                error: function (data) {
                    console.log('failed');
                    console.log(data);
                },
                processData: false,
                contentType: false,

        });
  });


  $('body').on('click', '#ignoreBefore', function(e){
    that = e.target;
    id = $(that).attr('data-id');
    //console.log($($(that).closest('.right-side').find()[]).val());
    // add datefield 
    date = $($(that).closest('.right-side').find('#ignorebeforedate')[0]).val();
    var date2 = new Date(date);
    console.log(date2);
    var date = (date2.getFullYear()+'-'+(date2.getMonth() + 1)) + '-' + date2.getDate();
    $.post({
        url: JS.baseUrl+`transactions1/ignoreBefore/${id}/${date}`,
        success: function (data) {
            var data = JSON.parse(data);
            JS.showAlert(data.type, data.message);
            if(data.type == 'success'){
                    let slgrid1 = $($(that).closest('.right-side').find('#banktrans')[0]).data('grid');
                    //console.log(slgrid);
                    //console.log(slgrid.data);
                    slgrid1.dataView.beginUpdate();
                    slgrid1.dataView.setItems(data.transdata.data);
                    //dataView.setFilter(JS.myFilter);
                    slgrid1.dataView.endUpdate();
                    //slgrid1.data = data.transdata.data;                          
                    slgrid1.grid.invalidate(); 
                    
                    //also update ignore link html
            }
        },
                error: function (data) {
                    console.log('failed');
                    console.log(data);
                },
                processData: false,
                contentType: false,

        });
  });


  


  
          
        
        
    





