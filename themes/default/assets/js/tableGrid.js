+(function ($) {
    TableGrid = function (body, options) {
        this.options = options;
        this.body = $(body);
        this.table = body.find('tbody');
        //this.footer = body.find('.tableGridTable').append($('tfoot'));
        this.data = options.data;
        this.template = options.template;
        this.linesInd = 0;
        this.init();
        this.initEvents();
    };
    TableGrid.prototype = {
        init: function () {
            var row = "";
            this.body.data('tablegrid', this);
            if (this.data == null || this.data == 0 || this.data.length == 0) this.table.addClass('empty');
            if(this.options.mode == 'inputrow') {
                this.generateAddRow();
            }
            for (var i in this.data) {
                row = $(this.template(i, this.data[i]));
                row = this.formatRow(row);
                this.appendRow(row);
                if(this.options.mode == 'inputrow') this.addIdInput(row);
                this.linesInd++;
            }
        },

        initEvents: function () {
            var that = this;
            this.table.on('click', '.delete', function () {
                that.deleteRow($(this).closest('tr'));
            });
            this.table.on('click', '.noclick', function (e) {
                e.preventDefault();
                //return false;
            });
            this.table.on('scroll',function () {
                that.table.parent().find('thead, tfoot').css({'margin-left': -$(this).scrollLeft()});
            });
            this.table.on('click', 'tr', function () {
                JS.clicks++;
                if (JS.clicks === 1) {
                    JS.clickTimer = setTimeout(function () {
                        JS.clicks = 0;
                    }, JS.dcDelay);
                } else {
                    clearTimeout(JS.clickTimer);
                    JS.clicks = 0;
                    that.initEdit($(this));
                }
            });
            this.table.parent().find('.add').click(function() {
                switch(that.options.mode) {
                    case 'modal':
                        that.options.newFunction(this, that);
                        break;
                    case 'inputrow':
                        that.addRowFromEditRow($(this).closest('tr'));
                        break;
                }

            });
        },

        generateAddRow: function() {
            var row = $(this.template(0, {}));
            var name = '';
            var that = this;
            row.find('input, select').each(function(){
                name = $(this).attr('name');
                $(this).attr('name', name.replace(that.options.name, 'temp'));
            });
            row.find('td:last').html('<a href="#" class="add"><i class="fas fa-plus-circle"></i></a>');
            this.initEditInputRow(row);
            this.appendRow(row);
        },

        appendRow: function (row) {
            this.table.append(row);
        },

        deleteRow: function (row) {
            var id = row.attr('rid');
            var typeId = row.closest('.tabcontent').attr("data-id");
            if (id !== undefined) {
                this.table.append('<input type="hidden" name="delete[' + typeId + '][' + id + ']" value="' + id + '" > </input>');
                this.table.find('.idfield[value="'+id+'"]').remove();
            }
            row.remove();
        },

        addIdInput: function (row) {
            var row_id = row.attr('index');
            var id = row.attr('rid');
            var input  = '<input class="idfield" type="hidden" name="'+this.options.name+'[' + row_id + '][id]" value="' + id + '"/>';
            this.table.append(input);
        },

        initEdit: function (row) {
            var mode = row.attr('edit-mode');
            switch (mode) {
                case 'modal':
                    this.initEditModal(row);
                    break;
                case 'inputrow':
                    this.initEditInputRow(row);
                    break;
            }
        },

        addRowFromEditRow: function (row) {
            var that = this;
            var form = row;
            var errors = '';
            $(form).find('input').each(function(){
                if($(this).attr('data-validation') == 'required'){
                    if(!$(this).val()){
                       errors += "You need to choose a "+$(this).attr('data-title')+'<br>';
                    };
                }
                if($(this).attr('data-validation') == 'nonzero'){
                    if($(this).val() == 0){
                        errors += "The "+$(this).attr('data-title')+" can not be 0"+'<br>';
                     };
                }
                    
            });
            if(errors == ''){
                var data = that.options.parseData(that.formToJson(form[0], true));
                var newrow = $(that.template(that.linesInd++, data));
                newrow = this.formatRow(newrow);
                this.appendRow(newrow);
            } else{
                JS.showAlert('danger', errors);
            }

        },

        addRowModal: function (data) {
            var that = this;
            var form = $(data);
            var data = that.options.parseData(that.formToJson(form[0]));
            console.log(data);
            var newrow = $(that.template(that.linesInd, data));
            newrow.find('td:first').append("<input type='hidden' class='serialized' value='"+form.serialize()+"'>").append(that.formToFields(form[0],that.linesInd++,that.options.name));
            this.appendRow(newrow);
        },

        initEditModal: function (row) {
            var that = this;
            JS.openDraggableModal(row.attr('rtype'), 'edit', row.attr('rid'), null,
                {
                    newitems: JS.getNewItems(row.closest('.modal'), '.tid'),
                    serialized: row.find('.serialized').val(),
                    main_id: row.closest('.modal').attr('main-id'),
                    tableGrid: true
                }, [{
                    event: 'postsubmit',
                    function: function (e, data) {
                        var form = $(data);
                        var data = that.options.parseData(that.formToJson(form[0]));
                        var newrow = $(that.template(row.attr('index'), data));
                        newrow.find('td:first').append("<input type='hidden' class='serialized' value='"+form.serialize()+"'>").append(that.formToFields(form[0],row.attr('index'),that.options.name));
                        row.replaceWith(newrow);
                        that.addIdInput(newrow);
                    }
                }]
            );
        },

        initEditInputRow: function (row) {
            if(row.hasClass('editing')) return;
            row.addClass('editing');
            var item;
            var that = this;
            $(row).find('input, select').each(function(){
                var tag = $(this).prop("tagName").toLowerCase();
                var parent = $(this).parent();
                switch(tag) {
                    case 'input':
                        if($(this).hasClass('multiple-select')) {
                            $(this).selectize({
                                persist: false,
                                plugins: ["remove_button"],
                                maxItems: null,
                                valueField: 'id',
                                labelField: 'name',
                                searchField: ['name'],
                                options: tsources[$(this).attr('tsource')],
                                render: {
                                    item: function(item, escape) {
                                        return '<div>' +
                                            (item.name ? '<span class="name">' + escape(item.name) + '</span>' : '') +
                                            (item.email ? '<span class="email">' + escape(item.email) + '</span>' : '') +
                                            '</div>';
                                    },
                                    option: function(item, escape) {
                                        var label = item.name || item.email;
                                        var caption = item.name ? item.email : null;
                                        return '<div>' +
                                            '<span class="label">' + escape(label) + '</span>'  +
                                            (caption ? '<span class="caption">' + escape(caption) + '</span>' : '') +
                                            '</div>';
                                    }
                                }
                            });
                            break;
                        }
                        if($(this).parent('.custom-checkbox').length) {
                            $(this).prop('disabled', false);
                            break;
                        }
                        if($(this).attr('type') == 'checkbox') {
                            row.replaceWith(that.formatRow(row));
                            break;
                        }
                        if($(this).attr('data-toggle') == 'datepickerh') {
                            $(this).attr('data-toggle','datepicker') ;
                        }
                        item = $(this).attr('type', 'text');
                        if(item.val() == 'undefined') item.val('');
                        parent.html(item);
                        break;
                    case 'select':
                        var item = $(this);
                        parent.html('<span class="select">'+item.get(0).outerHTML+'</span>');
                        parent.find('select').fastSelect({type: 'custom', data: that.options.tsources[item.attr('tsource')]});
                        parent.find('input:first').show();
                        break;
                }
            });
            if(row.attr('rid')){
                JS.datePickerInit(row);
            }    
        },

        formToJson: function (form, extract = false) {
            var obj = {};
            var elements = form.querySelectorAll("input, select, textarea");
            for (var i = 0; i < elements.length; ++i) {
                var element = elements[i];
                var name = element.name;
                if(extract && name)
                    name = name.match(/\[([^\]]*)\][^\[]*$/)[1];
                var value = element.value;
                if (name) {
                    obj[name] = value;
                    if($(element).hasClass('multiple-select') && element.value){
                        var opt_array = element.value.split(',');
                        opt_arr = tsources[$(element).attr('tsource')];
                        combined_name = '';
                        opt_array.forEach((opt, i) => combined_name = `${combined_name}${opt_arr.find(x => x.id === opt).name}, `);
                        obj[name + "_text"] = combined_name;
                    } else if (element.type == 'select-one')
                        obj[name + "_text"] = element.options[element.selectedIndex].text;
                    else if ($(element).is('[text]')) {
                        obj[name + "_text"] = $(element).prev().prev('input').val();
                    } else if ($(element).attr('text'))
                        obj[name + "_text"] = $(element).attr('text');
                }
            }
            return obj;
        },
        formToFields: function (form, count, type) {
            var obj = "";
            var elements = form.querySelectorAll("input, select, textarea, checkbox");
            var name
            for (var i = 0; i < elements.length; ++i) {
                var element = elements[i];
                var name = element.name;
                var value = element.value;
                if (name) {
                    if (element.type == 'checkbox')
                        obj += "<input type='hidden' name='" + type + "[" + count + "][" + name + "]' value='" + (element.checked ? 1 : 0) + "'/>";
                    else
                        obj += "<input type='hidden' name='" + type + "[" + count + "][" + name + "]' value='" + value + "'/>";
                }
            }
            obj += "<input type='hidden' class='serialized' value='" + $(form).serialize() + "'/>";
            return obj;
        },

        formatRow: function(row) {
            $(row).find('input[type="checkbox"]').each(function(){
                var checkbox = '<label for="'+$(this).attr('id')+'" class="custom-checkbox '+(row.hasClass('editing') ? '' : 'noclick')+'">'+
                    '<input type="hidden" name="'+$(this).attr('name')+'" value="0">'+
                    '<input type="checkbox" value="1" '+($(this).is(':checked') ? 'checked' : '')+' id="'+$(this).attr('id')+'" name="'+$(this).attr('name')+'" class="hidden" aria-hidden="true">'+
                    '<div class="input"></div></label>';
                $(this).replaceWith(checkbox);
            });
            $(row).find('select').each(function(){
                if(!row.hasClass('editing')){
                    $(this).hide();
                    $(this).parent().append('<input type="hidden" name="'+$(this).attr('name')+'" value="'+$(this).attr('default')+'">');
                } else
                    $(this).show();

            });
            return row;
        }
    };


    tgPlugin = function (options) {
        return new TableGrid(this, options);
    }
    $.fn.tableGrid = tgPlugin;
    $.fn.tableGrid.Constructor = TableGrid;

    var ownerSpot = 1;

    $('body').on('click', '#addOwner', function(e){
        e.preventDefault();
        console.log('new owner');
        var that = $(this);
        var percentage = that.closest('tr').find('#percentage').val();
        var owner_id = that.closest('tr').find('#profile_id').closest('.select').find('input[type=hidden]').val();
        // var percenowner_id = that.closest('tr').find('#profile_id').val();
        // console.log(percentage);
        // console.log(owner_id);
        // console.log(percenowner_id);
        //var form = [percentage, percenowner];
        $.ajax({
            url: JS.baseUrl+'tableapi/getProfileRow/owners/' + percentage + '/'+ owner_id + '/'+ ownerSpot,
            type: "POST",
            //data: JSON.stringify(form),
            success: function (data) {
                console.log('success');
                //console.log(data);
                //console.log(data.data);
                that.closest('tbody').append(data.data);
            },
            error: function (data) {
                console.log('fail');
                console.log(data);
            },
            dataType: 'json',
            processData: false,
            contentType: false,
        })
        ownerSpot++;
    });

    $('body').on('click', '#addtenanttolease1', function(e){
        e.preventDefault();
        console.log('new tenant1');
        var grid = $(this).closest('#peoplesstable').data('tablegrid');
        var that = $(this);
        var button = e.target;
        var tenant_id = $(button).closest('tr').find('#profile_id').closest('.select').find('input[type=hidden]').val();
        var tenant_name = $(button).closest('tr').find('#profile_id').closest('.select').find('input[type=hidden]').attr('text');
        var start = $(button).closest('.modal').find('#start').val();
        var end = $(button).closest('.modal').find('#end').val();
        var amount = $(button).closest('.modal').find('#amount').val();
        var security = $(button).closest('.modal').find('#deposit').val();
        var lateCharge = $(button).closest('tr').find('#late_charge').closest('.select').find('input[type=hidden]').val();
        var lmr = $(button).closest('.modal').find('#last_month').val();
        var unit = $(button).closest('.modal').find('#unit_id').val();
        var unitId = $(button).closest('.modal').find('#unit_id').closest('.select').find('input[type=hidden]').val();
        var data1 = {'amount':amount
                    ,'deposit': security
                    ,'id': null
                    ,'last_month': lmr
                    ,'memo': ''
                    ,'move_in': start
                    ,'move_out': end
                    ,'name': tenant_name
                    ,'unit': unit
                };
                var data2 = {
                'lease_id':null
                ,'profile_id': tenant_id
                ,'unit_id': unitId
                ,'amount': amount
                ,'deposit': security
                ,'last_month': lmr
                ,'late_charge': lateCharge
                ,'move_in': start == ""?"":start
                ,'move_out': end == ""?"":end
                ,'pets': 0
                ,'pet_deposit': 0
                ,'restrict_payments': 0
                ,'active': 1
            };
        newdata="";
        let inputs = "";
        $.each( data2, function( key, value ) {
            newdata +=`${key}=${value}`;
            if (key != 'unit'){newdata +=`&`};
            inputs +=`<input type="hidden" name="tenanttoleases[${grid.linesInd}][${key}]" value="${value}">`;
         });
        var newrow = $(grid.template(grid.linesInd, data1));
        newrow.find('td:first').append("<input type='hidden' class='serialized' value='"+newdata+"'>").append(inputs);
        grid.appendRow(newrow);
        grid.addIdInput(newrow);
        grid.linesInd++;


    });


    //to delete an owner works the same as deleteRow function on line 84 but ownerswas not created through tablegrid
    $('body').on('click', '.delete2', function(e){
        var id = $(this).closest('tr').attr('id');
        var tr = $(this).closest('tr');
        var typeId = $(this).closest('.tabcontent').attr("data-id");
            $(this).closest('tbody').append('<input type="hidden" name="delete[' + typeId + '][' + id + ']" value="' + id + '" />');
            $(this).closest('table').find('.idfield[value="'+id+'"]').remove();
        tr.remove();
    });

})(jQuery);