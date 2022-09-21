+(function ($) {
    FormGrid = function (body, options) {
        this.options = options;
        this.body = $(body);
        this.table = body.find('.formGridTable tbody');
        this.theader = body.find('section:first').length ? body.find('section:first') : body.find('header');
        this.footer = body.find('.formGridTable tfoot').length > 0 ? body.find('.formGridTable tfoot') : body.find('footer');
        this.header = body.find('.formGridTable thead');
        this.template = options.template;
        this.minRows = options.minRows;
        this.data = options.data;
        this.linesInd = 0;
        this.loadlimit = 15;
        this.contextMenu;
        this.totals = {};
        this.pasteMode = false;
        this.selectLines = {
            "account" : ' modal="account" type="table" key="accounts.name" ',
            "property" : ' modal="property" type="table" key="properties.name" id="property_id" ',
            "unit" : ' modal="unit" type="table" key="units.name" ',
            "class" : ' ',
            "name" : ' ',
        };
        this.typeRelations = {
            property: {selects: ['[stype="account"]', '[stype="unit"]'], field: 'property_id'},
            //account: {selects: ['[stype="profile"]'], field: 'profile_type_id'}
        }
        this.init();
        this.initEvents();
    };
    FormGrid.prototype = {
        init: function () {
            var row = "";
            var that = this;
            this.body.data('formgrid', this);
            if(this.data == null || this.data == 0 || this.data.length == 0) this.table.addClass('empty');
            for (var i in this.data) {
                this.addIdInput(i, this.data[i].id);
                row = $(this.template(i, this.data[i]));
                row = this.formatRow(row, i);
                row.addClass('filledRow');
                this.appendRow(row);
                this.linesInd++ ;
            }
            for(var i = this.linesInd; i < this.minRows; i++) {
                row = $(this.template(i, {}));
                row = this.formatRow(row, i);
                this.appendRow(row);
                this.linesInd++;
            }
            //var w = this.header.find('th').length;
            //this.footer.find('td').css({width: (100/w)+'%'});
            //this.header.find('th').css({width: (100/w)+'%'});
            this.contextMenu = this.appendContextMenu();
        },

        initEvents(){
            var that = this;
            this.contextMenu.on('click', 'a.context-menu__link', function(){
                that.contextFunction($(this).data('action'));
            });
            this.table.on('click', 'tr', function(){
                if($(this).prev() && $(this).prev().hasClass('filledRow') && !$(this).hasClass('filledRow')) that.fillNextRow($(this));
            });
            this.table.on('contextmenu', 'tr', function(e){
                that.lastTr = $(this);
                e.preventDefault();
                that.initContextMenu(e.clientX, e.clientY);
            });
            this.table.on('click', 'tr:last-child', function(){
                row = that.template(that.linesInd, {});
                row = that.formatRow(row, that.linesInd++);
                that.appendRow(row);
            });
            this.table.on('click', function(e){
                if($(this).hasClass('empty')) {
                    that.fillFirstRow();
                    $(this).removeClass('empty');
                    e.stopPropagation();
                }
            });
            this.table.on('change', 'input', function(){
                var row = $(this).closest('tr');
                if($(this).val() != '') row.addClass('filledRow');
            });
            this.table.on('scroll',function () {
                that.table.parent().find('thead, tfoot').css({'margin-left': -$(this).scrollLeft()});
            });

            this.table.on('keydown', 'td', function () {
                var columnNum = $(this).index();

                var keyPressed = event.which || event.keyCode;
                if($(this).find('input.open').length) return true;
                switch (keyPressed) {
                    case 37:
                        $(this).prev('td').find("input:first-child").focus();
                        break;
                    case 38:
                        $(this).closest('tr').prev('tr').find("td:nth-child(" + (columnNum + 1) + ")").find("input:first-child").focus();
                        break;
                    case 39:
                        $(this).next('td').find("input:first-child").focus();
                        break;
                    case 40:
                        var thiss = this;
                        $(thiss).closest('tr').next("tr").find("td:nth-child(" + (columnNum + 1) + ")").find("input:first-child").focus();
                        setTimeout(function(){$(thiss).closest('tr').next("tr").trigger('click')},200);
                        break;
                }
            });
        },

        fillFirstRow: function(){
            var that = this;
            var row = this.table.find('tr:first');
            row.addClass('filledRow');
            var property_id = this.theader.find('[sel-id="property_id"]').val() || null;
            row.find('td').each(function(){
                if($(this).attr('source'))
                    if($(this).hasClass('formGridSelectTd')) {
                        if($(this).attr('stype') == 'profile')
                            return;
                            //that.formatTd($(this), null, row.attr('id'), that.theader.find($(this).attr('source')).val() || null, 'profile_type_id');
                        else
                            that.formatTd($(this), property_id, row.attr('id'), that.theader.find($(this).attr('source')).val() || null);
                    }else{
                        var input = $(this).find('input');
                        input.val(that.theader.find($(this).attr('source')).val());
                    }
                else
                    if($(this).hasClass('formGridSelectTd'))
                        that.formatTd($(this), property_id, row.attr('id'), null);

            });
            this.triggerTotals(row);
            //row.find('td').first().find(":input:not([type=hidden])").click();

        },

        fillNextRow: function(row) {
            row.addClass('filledRow');
            var that = this;
            var oldrow = row.prev();
            var property_id = oldrow.find('input[sel-id="property_id"]').val() || null;
            console.log(property_id);
            row.find('td').each(function(i){
                if($(this).hasClass('formGridSelectTd')) {
                    if($(this).attr('stype') == 'account') return;
                    if($(this).attr('stype') == 'profile') return;
                    var value = oldrow.find('td:eq(' + i + ') input.editableSelect').attr('sel-value') || null;

                    if($(this).attr('stype') == 'profile')
                        that.formatTd($(this), null, row.attr('id'), value, 'profile_type_id');
                    else
                        that.formatTd($(this), property_id, row.attr('id'), value);

                }else{
                    var total = $(this).attr('total') || null;
                    var value = oldrow.find('td:eq(' + i + ') input').val();
                    if(total && that.totals[total]){
                        if (that.totals['debit'] && that.totals['credit']){
                            value = 0;
                            var diff = that.totals['debit'].total - that.totals['credit'].total;
                            if(total == 'debit' && diff < 0 || total == 'credit' && diff > 0) value = number_format(Math.abs(diff));
                        }else
                        if(that.totals[total].match) value = number_format((Number(that.theader.find(that.totals[total].match).val().replace(',', '')) || 0) - that.totals[total].total);

                    }
                    var input = $(this).find('input');
                    input.val(value);
                }
            });
            this.triggerTotals(row);
        },

        addIdInput: function(row_id, id) {
            var input  = '<input type="hidden" name="transactions[' + row_id + '][id]" value="' + id + '"/>';
            this.table.parent().append(input);
        },

        formatRow: function(row, id) {
            var that = this;
            var row = $(row);
            var property_id = row.attr('property_id') || null;
            var account_id = row.find('td[stype="account"]').attr('value') || null;
            row.find('.formGridSelectTd').each(function(){
                if($(this).attr('stype') == 'profile')
                    that.formatTd($(this), null, id, $(this).attr('value'), 'profile_type_id');
                else
                    that.formatTd($(this), property_id, id, $(this).attr('value'));
            });
            return row;
        },

        formatTd: function(body, filter_value, row_id, value, filter_key = 'property_id') {
            var that = this;
            var type = body.attr('stype');
            var type1 = type;
            var td = '<span class="select"><select class="editableSelect '+body.attr('sclasses')+'" '+that.selectLines[type]+' name="transactions[' + row_id + ']['+type+'_id]">';
            td += '</select></span>';
            body.empty().html(td);
            body.find('.editableSelect').fastSelect({type: type1, default: value, filter_key: filter_key, filter_value: filter_value, fastinit: true, formgrid: true});
            if(that.typeRelations[type]){
                body.find('input.editableSelect').change(function(e){
                    if (e.originalEvent !== undefined) return;
                    for(var i in that.typeRelations[type].selects) {
                        var td = $(this).closest('tr').find(that.typeRelations[type].selects[i]);
                        var value = td.find('.editableSelect').attr('sel-value') || null;
                        that.formatTd(td, $(this).attr('sel-value'), row_id, value, that.typeRelations[type].field);
                    }_
                });
            }
            body.find('input.decimal').calculadora({decimals: 2, useCommaAsDecimalMark: false});
        },

        filterOptions: function(data, property_id, type) {
            if(!property_id || property_id == '-1')
                if(ESC.exists(type)) return []; else return data;
            if(ESC.exists(type+'.'+property_id)) return [];
            switch(type){
                case 'account':
                    return _.filter(data, function(o){
                        return o.all_props == 1 || o.property_id && o.property_id.split("|").includes(property_id);
                    });
                case 'unit':
                    return _.filter(data, function(o){
                        return o.property_id == property_id;
                    });
                default:
                    return data;
            }
        },

        appendRow: function (row) {
            this.table.append(row)
        },

        initContextMenu: function(x, y){
            var menu = this.contextMenu[0];
            menu.classList.add( "context-menu--active" ); // make context menu appear
            menu.style.top = y +"px"  //position top
            menu.style.left = x +"px"  //position left

            document.addEventListener("click", hideContextMenu);  //listen for user clicking away
            document.addEventListener("keypress", hideContextMenu); //listen for user pressing key

            function hideContextMenu(){
                menu.classList.remove( "context-menu--active" );
                document.removeEventListener("click", hideContextMenu);
                document.removeEventListener("keypress", hideContextMenu);
            }

        },

        appendContextMenu: function(){
            var ind = $('.context-menu').length;
            $('body').append('<nav id="context-menu'+ind+'" class="context-menu no-print">'+
                '<ul class="context-menu__items">'+
                '<li class="context-menu__item">'+
                '    <a href="#" class="context-menu__link" data-action="insert"> Insert Line</a>'+
                '</li>'+
                '<li class="context-menu__item">'+
                '    <a href="#" class="context-menu__link" data-action="delete"> Delete Line</a>'+
                '</li>'+
                '<li class="context-menu__item">'+
                '    <a href="#" class="context-menu__link" data-action="copy"> Copy Line</a>'+
                '</li>'+
                '</ul>'+
                '</nav>');
            return $('#context-menu'+ind).first();
        },

        contextFunction: function(action){
            switch(action){
                case 'delete': this.cDeleteLine(this.lastTr); break;
                case 'insert': this.cInsertLine(this.lastTr); break;
                case 'copy': this.cCopyPasteLine(this.lastTr); break;
            }
        },

        cDeleteLine: function(tr) {
            console.log('delete line');
            if(tr.is('[tid]')){
                var input = $('<input type="hidden" name="delete[' + tr.attr('id') + ']" value="' + tr.attr('tid') + '">');
                this.table.parent().append(input);
            }
            tr.find('.total').each(function(){
                $(this).data('val', $(this).val().replace(',','')).val(0);
            });
           
            this.triggerTotals(tr);
            
            var modal = $(tr).closest('.modal');
            tr.remove();
            if($(modal).hasClass('deposit-modal')){
                updateBottomTotals(modal);
            }
            
        },

        cInsertLine: function(tr) {
            console.log('insert line');
            var row = $(this.template(this.linesInd, {}));
            row = this.formatRow(row, this.linesInd++);
            row.addClass('filledRow');
            row.insertBefore(tr);
            this.triggerTotals(row);
        },

        cCopyPasteLine: function(tr) {
            console.log('copy line');
            this.pasteMode = !this.pasteMode;
            if(this.pasteMode) {
                this.bufferLine = tr.clone();
                this.contextMenu.find('a.context-menu__link:eq(2)').text(' Paste Line')
            } else {
                var row = $(this.template(this.linesInd, {}));
                //row = this.formatRow(row, this.linesInd++);
                row = this.copyLineContents(this.bufferLine, row);
                row.addClass('filledRow');
                row.insertBefore(tr);
                this.triggerTotals(row);
                this.contextMenu.find('a.context-menu__link:eq(2)').text(' Copy Line');
                var modal = $(row).closest('.modal');
                if($(modal).hasClass('deposit-modal')){
                    updateBottomTotals(modal);
                }
            }
        },

        copyLineContents: function(oldtr, newtr) {
            var that = this;
            var property_id = oldtr.find('input[sel-id="property_id"]').val() || null;
            newtr.find('td').each(function(i){
                if($(this).hasClass('formGridSelectTd')) {
                    var value = oldtr.find('td:eq(' + i + ') input.editableSelect').attr('sel-value') || null;
                    that.formatTd($(this), property_id, newtr.attr('id'), value);
                }else{
                    var input = $(this).find('input');
                    input.val(oldtr.find('td:eq(' + i + ') input').val());
                }
            });
            return newtr;
        },

        addTotal: function(type, match, input, totalbody) {
            var that = this;
            var body = this.footer.find(totalbody);
            var total = {total: 0, match: match, input: input, body:totalbody};
            this.table.find(input).each(function(i){
                total.total+=Number($(this).val());
                $(this).data('val',Number($(this).val()));
                $(this).val(number_format($(this).val()));
                body.text(total.total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'))
            });
            this.totals[type] = total;
            this.table.on('focusin', input, function(){
                $(this).val($(this).data('val'));
            }).on('focusout', input, function(){
                $(this).val(number_format($(this).val()));
            }).on('change', input, function(){
                var value = Number($(this).val().replace(',', ''));
                if(isNaN(value)) {
                    $(this).animate({backgroundColor:'#ffc0c6'}, 400).delay(400).animate({backgroundColor:'none'}, 400, function(){$(this).css({'background-color':'inherit'})});
                    $(this).val(number_format($(this).data('val')));
                    return;
                }
                var itrigger = false;
                if(that.totals['credit'] && that.totals['debit']) {
                    var input, avalue;
                    if(type == 'credit') {
                        input = $(this).closest('tr').find(that.totals['debit'].input);
                        avalue = Number(input.val().replace(',', ''));
                    } else if(type == 'debit') {
                        input = $(this).closest('tr').find(that.totals['credit'].input);
                        avalue = Number(input.val().replace(',', ''));
                    }
                    $(this).val(value);
                    if(avalue != 0 && value != 0) {
                        input.val(number_format(0));
                        itrigger = true;
                    }
                }
                that.totals[type].total += value - (Number($(this).data('val')) || 0);
                $(this).data('val', value);
                body.text(that.totals[type].total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                if(itrigger) input.trigger('change');
            })
        },

        triggerTotals: function(row) {
            row.find('.total').trigger('change');
        }

    };


    fgPlugin = function (options) {
        return new FormGrid(this, options);
    }
    $.fn.formGrid = fgPlugin;
    $.fn.formGrid.Constructor = FormGrid;

})(jQuery);