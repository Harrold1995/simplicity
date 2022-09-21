var Documents = function(obj, options) {
    this.obj = $(obj);
    this.body = $(obj).find(options.bodyId);
    this.header = $(obj).find(options.headerId);
    this.crumbs = this.header.find('.d-breadcrumbs');
    this.filtersLabel = this.header.find(options.filtersLabelId);
    this.filtersPopup = this.header.find(options.filtersPopupId);
    this.filtersOverlay = this.header.find(options.filtersOverlayId);
    this.options = options;
    this.base = options.base;
    this.folders = $(obj).find(options.bodyId + " " + "d-folder-wrapper");
    this.path = options.path;
    this.values = options.values;
    this.init();
};
/** class methods **/
Documents.prototype = {
	init : function(){
	    var that = this;
	    $.get(this.base + this.options.fileTemplateUrl, function(result){that.fileTemplate = result;});
        $.fn.dataTable.ext.errMode = function ( settings, helpPage, message ) {
            console.log(message);
        };
	    this.initEvents();
	    this.refreshData();
	},

	refreshData : function(firstLoad = false) {
	    var that = this;
	    this.hash = this.genUrl();
        $.post( this.base+"documents/getData",{path:this.path, values:this.values}, function( data ) {
            that.renderFoldersView(data.folders, data.columns);
            that.renderFiltersList(data.filters);
            that.renderBreadcrumbs(data.crumbs);
            that.renderNav(that.options.docsurl+that.hash);
            //console.log(that.options.docsurl+that.hash);
            if(!firstLoad && (true || !that.options.pathArray.includes(that.values))) history.pushState({path:that.path, values:that.values}, "Documents", that.options.docsurl+that.hash);
        }, 'JSON').fail(function(data){console.log(data);});
	},

	renderFoldersView : function(data, columns=[]) {
	    var that = this;
		this.body.html(data);
		var table = $('#documents-table');
		if($(table).length){
            var columns = JSON.parse(columns);
            this.mode = "table";
            this.table = table.DataTable( {
                "order": [[1, "desc"]],
                "searching": true,
                "paging": false,
                "bInfo": false,
                "autoWidth": true,
                "scrollX": true,
                "scrollCollapse": true,

                "ajax": {
                    "url": this.base + this.options.tableAjaxUrl,
                    "type": "POST",
                    "data": {
                        "path": this.path,
                        "values": this.values
                    }
                },
                "createdRow": function (row, data, dataIndex) {
                    $(row).attr('document-id', data.id);
                    $(row).attr('selects', data.selects);
                },
                "columns": columns,
                "columnDefs": [
                    {
                        "render": function ( data, type, row ) {
                            return '<label><input type="checkbox"  name="documents" value="'+data+'"> <span>Choose</span></label>';
                        },
                        "sClass":"check",
                        "width": 20,
                        "searchable": false,
                        "orderable": false,
                        "targets": 0
                    }, {
                    "render": function (data, type, row) {
                        return '<a target="_blank" href="' + that.base + 'uploads/documents/' + data + '" >' + data + '</a>';
                    },
                    "targets": 1
                },{
                    "render": function (data, type, row) {
                        return '<ul class="list-square">'+
                                    '<li><a href="#"><i class="icon-envelope-outline2"></i> <span>Envelope</span></a></li>'+
                                    '<li><a href="#" class="d-docedit"><i class="icon-notes"></i> <span>Document</span></a></li>'+
                                    '<li><a href="#" class="print"><i class="icon-print"></i> <span>Print</span></a></li>'+
                                    '<li><a href="#" class="d-docdelete"><i class="icon-trash"></i> <span>Delete</span></a></li>'+
                                '</ul>';
                    },
                    "searchable": false,
                    "orderable": false,
                    "targets": -1
                }]
            } );
        } else {
            this.mode = "folders";
        }

	},

    renderFiltersList : function(data) {
        if(data.label == 'disabled'){
            this.filtersLabel.html("No Filters");
            this.filtersLabel.prop("disabled",true);
            this.filtersPopup.hide().html("");
        }
        else{
            this.filtersLabel.prop("disabled",false);
            this.filtersLabel.html(data.label);
            this.filtersPopup.hide().html(data.list);
        }
        if(this.mode == 'table'){
            this.body.find('#d-columns-popup').hide().html(data.list);
        }
    },

    renderBreadcrumbs : function(data) {
        this.crumbs.html(data);
    },

    renderNav : function(hash) {
        $('.d-nav li').removeClass('active');
        $('.d-nav a[href="'+hash+'"]').closest('li').addClass('active');
        if(this.path[0] == '0') $(this.options.favoritesButton).parent().show();
        else $(this.options.favoritesButton).parent().hide();
    },

	initEvents : function(){
        $.expr[":"].contains = $.expr.createPseudo(function(arg) {
            return function( elem ) {
                return $(elem).text().toLowerCase().indexOf(arg.toLowerCase()) >= 0;
            };
        });
        $.fn.extend({
            toggleText: function(a, b){
                return this.text(this.text() == b ? a : b);
            }
        });
	    var that = this;
        this.filtersLabel.click(function(e){
            that.filtersPopup.toggle();
            that.filtersOverlay.toggle();
        });

        this.body.on("mousedown", "#d-columns-select", function(e){
            e.preventDefault();
            $(this).blur();
            $(window).focus();
            that.body.find('#d-columns-popup').toggle();
            that.body.find('#d-columns-overlay').toggle();
        });

        this.obj.on('click', '.d-filters-overlay', function(){
            that.body.find('#d-columns-popup').hide();
            $(this).hide();
        });
        this.obj.on("change", ".document-filter-option", function(e){
            if(that.mode == 'folders'){
                $('[filter-id = '+$(this).val()+']').toggle(200);
                var data = $(this).closest('ul').first().find('input').serializeArray();
                data ={"path": that.path, "filter": $(this).val()};
                $.post( that.base+"documents/toggleFolderDisplay", data, function( data ) {
                }, 'JSON');
            }else if(that.mode == 'table') {
                if(that.table == undefined) return;
                var column = that.table.column("c"+$(this).val()+":name");
                column.visible( ! column.visible());
            }
        });
        this.header.on("click", ".d-filters-back", function(e){
                that.crumbs.find("a").eq(-2).trigger("click");
        });
        this.header.on("click", this.options.favoritesButton, function(e) {
            $.post(that.base + that.options.favoritesModalUrl, {}, function (result) {
                $("body").append(result);
                var modal = $(that.options.favoritesModal);
                modal.modal({
                    show: true
                });
                modal.on('hidden.bs.modal', function () {
                    $(this).remove();
                });
                modal.find('')
            });
        });
        this.obj.on("click", ".folder-link", function(e){
            that.path = $(this).attr('path');
            if($(this).attr('path')!="" && $(this).is('[filter-id]')) that.path += that.options.pathDelimiter;
            if($(this).is('[filter-id]')) that.path += $(this).attr('filter-id');
            that.values = $(this).attr('values');
            that.refreshData();
        });
        $('body').on("click", this.options.favoritesSubmitButton, function(e){
                var name = $(this).closest('.modal').find('#foldername').val();
                $.post( that.base+that.options.favoritesSubmitUrl, {"name" : name, "path" : that.genUrl()}, function( data ) {
                    JS.showAlert(data.type, data.message);
                }, 'JSON').fail(function(data){console.log(data);});
        }).on("click", this.options.pathsSubmitButton, function(e){
            var name = $(this).closest('.modal').find('#foldername').val();
            $.post( that.base+that.options.pathsSubmitUrl, {"name" : name}, function( data ) {
                JS.showAlert(data.type, data.message);
            }, 'JSON').fail(function(data){console.log(data);});
        });
        this.header.find('#psearch').keyup(function(){
            if(that.table != null) that.table.search($(this).val()).draw(); else that.filterFolders($(this).val());
        });
        window.addEventListener('popstate', function (event) {
            if (history.state) {
                that.path = history.state.path;
                that.values = history.state.values;
                that.refreshData(true);
            }
        }, false);
        $('#d-files').on("change", function(e){
            e.preventDefault();
            var files = (e.dataTransfer || e.target).files;
            if(that.mode != "files") {
                that.mode = "files";
                that.body.load(that.base + that.options.uploadTemplateUrl, function(ev){that.initUploadFiles(files);});
            }else{
                that.initUploadFiles(files);
            }

        });
        $('p.input-file').on('dragover',function(e) {e.preventDefault();e.stopPropagation();})
        .on('dragenter',function(e) {e.preventDefault();e.stopPropagation();})
        .on("drop", function(e){
            e.preventDefault();
            e.stopPropagation();
            var files = (e.originalEvent.dataTransfer || e.originalEvent.target).files;
            if(that.mode != "files") {
                that.mode = "files";
                that.body.load(that.base + that.options.uploadTemplateUrl, function(ev){that.initUploadFiles(files);});
            }else{
                that.initUploadFiles(files);
            }

        });
        this.obj.on("change", 'input[name = "files"]', function(e) {
            $(this).parent().toggleClass("active");
            that.multipleFileEditor(that.body.find('input[name = "files"]:checked'));
        });
        this.obj.on("change", 'input[name = "documents"]', function(e) {
            $(this).parent().toggleClass("active");
            that.multipleDocumentEditor(that.body.find('input[name = "documents"]:checked'));
        });
        this.obj.on("change", 'input#d-allfiles', function(e) {
            $(this).parent().toggleClass("active");
            $(this).parent().parent().find('span').toggleText("Select All", "Deselect All");
            if($(this).is(":checked"))
                that.body.find('input[name = "files"]').prop("checked", true).parent().addClass("active");
            else
                that.body.find('input[name = "files"]').prop("checked", false).parent().removeClass("active");
            that.multipleFileEditor(that.body.find('input[name = "files"]:checked'));
        }).on("change", 'input#d-alldocs', function(e) {
            $(this).parent().toggleClass("active");
            $(this).parent().parent().find('span').toggleText("Select All", "Deselect All");
            if($(this).is(":checked"))
                that.body.find('input[name = "documents"]').prop("checked", true).parent().addClass("active");
            else
                that.body.find('input[name = "documents"]').prop("checked", false).parent().removeClass("active");
            that.multipleFileEditor(that.body.find('input[name = "documents"]:checked'));
        });
        this.obj.on("click", '#d-multifile-wrapper .edit-button', function(e) {
            that.editMultipleFiles(that.body.find('input[name = "files"]:checked'));
        });
        this.obj.on("click", '#d-multidoc-wrapper .edit-button', function(e) {
            that.editMultipleDocs(that.body.find('input[name = "documents"]:checked'));
        });
        this.obj.on("click", '#d-multifile-wrapper .delete-button', function(e) {
            bootbox.confirm({
                message: "You sure you want to delete selected files?",
                buttons: {
                    confirm: {label: 'Yes',className: 'btn-danger'},
                    cancel: {label: 'No', className: 'btn'}
                },
                callback: function (result) {
                    if (result) {
                        var array = that.body.find('input[name = "files"]:checked').map(function(){
                            return this.value;
                        }).get();
                        $.post({url: that.base + that.options.filesDeleteUrl, data: {data:array},
                            success: function (data) {
                                JS.showAlert(data.type, data.message);
                                if (data.type == 'success') {
                                    that.body.find('input[name = "files"]:checked').closest('tr').next().hide(100).remove();
                                    that.body.find('input[name = "files"]:checked').closest('tr').hide(100).remove();
                                    that.multipleFileEditor(that.body.find('input[name = "files"]:checked'));
                                }
                            }, dataType: 'json'});
                    }
                }
            });
        });
        this.obj.on("click", '.d-filedelete', function(e) {
            var id = $(this).closest('tr').attr('document-id');
            bootbox.confirm({
                message: "You sure you want to delete this file?",
                buttons: {
                    confirm: {label: 'Yes',className: 'btn-danger'},
                    cancel: {label: 'No', className: 'btn'}
                },
                callback: function (result) {
                    if (result) {
                        var array = [id];
                        $.post({url: that.base + that.options.filesDeleteUrl, data: {data:array},
                            success: function (data) {
                                JS.showAlert(data.type, data.message);
                                if (data.type == 'success') {
                                    that.body.find('tr[document-id="'+id+'"]').hide(100).remove();
                                }
                            }, dataType: 'json'});
                    }
                }
            });
        });
        this.obj.on("click", '#d-multidoc-wrapper .delete-button', function(e) {
            bootbox.confirm({
                message: "You sure you want to delete selected documents?",
                buttons: {
                    confirm: {label: 'Yes',className: 'btn-danger'},
                    cancel: {label: 'No', className: 'btn'}
                },
                callback: function (result) {
                    if (result) {
                        var array = that.body.find('input[name = "documents"]:checked').map(function(){
                            return this.value;
                        }).get();
                        $.post({url: that.base + that.options.docsDeleteUrl, data: {data:array},
                            success: function (data) {
                                JS.showAlert(data.type, data.message);
                                if (data.type == 'success') {
                                    that.body.find('input[name = "documents"]:checked').closest('tr').hide(100).remove();
                                    that.multipleDocumentEditor(that.body.find('input[name = "documents"]:checked'));
                                }
                            }, dataType: 'json'});
                    }
                }
            });
        });
        this.obj.on("click", '.d-docdelete', function(e) {
            var id = $(this).closest('tr').attr('document-id');
            bootbox.confirm({
                message: "You sure you want to delete this document?",
                buttons: {
                    confirm: {label: 'Yes',className: 'btn-danger'},
                    cancel: {label: 'No', className: 'btn'}
                },
                callback: function (result) {
                    if (result) {
                        var array = [id];
                        $.post({url: that.base + that.options.docsDeleteUrl, data: {data:array},
                            success: function (data) {
                                JS.showAlert(data.type, data.message);
                                if (data.type == 'success') {
                                    that.body.find('tr[document-id="'+id+'"]').hide(100).remove();
                                }
                            }, dataType: 'json'});
                    }
                }
            });
        }).on("click", '.d-docedit', function(e) {
            var id = $(this).closest('tr').attr('document-id');
            var div = $("<div class='d-selects row'></div>");
            var tr = $(this).closest('tr');
            var row = that.table.row( tr );
            if(!tr.hasClass('shown')) row.child(div).show();
            tr.addClass('shown');
            var selects = tr.attr('selects').split('|');
            var ind = 0;
            for(i in selects)
                if(ind == 0)
                    that.attachFileSelect(ind++, tr.next(), id, 0, 0, false, selects[i]);
                else
                    that.attachFileSelect(ind++, tr.next(), id, selects[0], selects[i-1], false, selects[i]);

        });
    },

    genUrl: function(){
        var path = [];
        if(this.path!="") path = this.path.split(this.options.pathDelimiter);
        var values = [];
        if(this.values!="") values = this.values.split(this.options.pathDelimiter);
        var hash = "";
        if(this.options.pathArray.includes(this.values))
            hash += values[0]+this.options.pathDelimiter;
        else
            for(var i=0;i<path.length;i++){
                hash += path[i]+this.options.pathDelimiter;
                if(values[i-2] != undefined) hash += values[i-2]+this.options.pathDelimiter;
            }
        return hash;
    },

    filterFolders: function(search){
        $('.d-folder p:contains("'+search+'")').parent().parent().show(100);
        $('.d-folder p:not(:contains("'+search+'"))').parent().parent().filter(":not([filter-id=-1])").hide(100);
    },

    initUploadFiles: function(files){
        var that = this,
            filesLen = files.length;
        var body = this.body.find('.table-d');
        for (var i = 0 ; i < filesLen ; i++)
        {
            if(files[i] != '')
            {

                var newDiv = $(this.fileTemplate).clone();
                newDiv.find('.d-filename').text(files[i].name);
                newDiv.find('.d-filesize').text(parseInt(files[i].size/1000)+" KB");
                body.append(newDiv);
            }
            var formData = new FormData();
            formData.append('file', files[i]);
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.upload.div = newDiv;
            (function(elId) {
                xmlhttp.upload.addEventListener('progress', function (e) {
                    that.updateFileProgress(e, this.div)
                }, false);
            })(i);
            xmlhttp.addEventListener("loadend", function(){
                /*completedCount++;
                if (completedCount == length) {
                    // here you should hide your gif animation
                }*/
            }, false);
            xmlhttp.open("POST", this.base+this.options.filesUploadUrl);
            xmlhttp.send(formData);
            xmlhttp.onload = function() {
                console.log(this.response);
                if (this.status == 200) {
                    var resp = JSON.parse(this.response);
                    this.upload.div.attr('document-id', resp.id);
                    this.upload.div.find('input[name="files"]').val(resp.id);
                    that.attachFileSelect(0, this.upload.div, resp.id, 0);
                }else{
                    alert("Error has occured");
                }
            }
        }
    },

    updateFileProgress: function(e, div){
        if(e.lengthComputable){
            var max = e.total;
            var current = e.loaded;
            var percentage = parseInt((current * 100)/max);
            if(percentage>=10) div.find('.percentage').show();
            div.find('.percentage').text(percentage + "%");
            div.find('.container').css("width", percentage + "%");
        }
    },

    attachFileSelect: function(ind, div, docid, type, value = 0, multifile = false, preselected = 0, trigger = false){
	    var body = div.find('.d-selects #select'+ind);
	    var that = this;
	    var type = type;
        div.find(".file-select").filter(function() {
            return  parseInt($(this).attr("index")) > ind;
        }).remove().parent().hide();
        if(body.length == 0) body = $('<div id="select'+ind+'" class="col" style="max-width:300px;"></div>').appendTo(div.find('.d-selects'));

        $.post( this.base+this.options.fileSelectUrl, {"index" : ind, "document-id" : docid, "type" : type, "value" : value, "preselected" : preselected}, function( data ) {
            if(data.status=="select"){

                if(multifile) {
                    that.fstash.length = ind;
                    that.fstash[ind] = $(data.select);
                }
                body.show().html(data.select);
                body.find('.file-select').editableSelect({ignore_first: true});
                body.find('.file-select').change(function(e){
                    if($(this).attr('sel-value') == '0') return false;
                    if($(this).attr('index') == "0"){
                        type = $(this).attr('sel-value');
                        if(multifile)that.fstash[0].attr('type', type);
                    }
                    if(multifile)that.fstash[ind].attr('sel-value', $(this).attr('sel-value'));
                    that.attachFileSelect(ind + 1, div, docid, type, $(this).attr('sel-value'), multifile);
                });
                if(trigger) body.find('.file-select').trigger("change");
            }else {
                body.remove();
                if(!multifile) JS.showAlert(data.status, data.message);
            }
        }, 'JSON').fail(function(data){console.log(data);});
    },

    attachFileSelectFromStash: function(ind, div, docid, type, value = 0, html="", preselected = 0, trigger = false){
        var body = div.find('.d-selects #select'+ind);
        var that = this;
                if(body.length == 0) body = $('<div id="select'+ind+'" class="col" style="max-width:300px;"></div>').appendTo(div.find('.d-selects'));

                body.show().html("").append(html);
                body.find('.file-select').editableSelect({ignore_first: true});
                body.find('.file-select').change(function(e){
                    if($(this).attr('sel-value') == '0') return false;
                    if($(this).attr('index') == "0"){
                        type = $(this).attr('sel-value');
                        that.fstash[0].attr('type', type);
                    }
                    that.fstash[ind].attr('sel-value', $(this).attr('sel-value'));
                    that.attachFileSelect(ind + 1, div, docid, type, $(this).attr('sel-value'));
                });
                if(trigger) body.find('.file-select').trigger("change");
    },
    multipleFileEditor: function(options){
        var multifile = this.body.find('#d-multifile-wrapper');
	    if(options.length > 0){
            var count = multifile.find('.d-multifile-count');
            var selects = multifile.find('.d-selects');
            count.text(options.length);
            if(!multifile.is(":visible")){
                this.fstash = [];
                selects.html("");
                selects.show();
                multifile.show();
                this.attachFileSelect(0, multifile, 0, 0, 0, true);
            }
        }else
            multifile.hide();

    },

    multipleDocumentEditor: function(options){
        var multifile = this.body.find('#d-multidoc-wrapper');
        if(options.length > 0){
            var count = multifile.find('.d-multidoc-count');
            var selects = multifile.find('.d-selects');
            count.text(options.length);
            if(!multifile.is(":visible")){
                multifile.show();
                this.fstash = [];
                selects.html("");
                selects.show();
                multifile.show();
                this.attachFileSelect(0, multifile, 0, 0, 0, true);
            }
        }else
            multifile.hide();

    },
    editMultipleFiles: function(options){
	    var that = this;
        var selects = this.body.find('#d-multifile-wrapper .file-select');
        var targets = this.body.find('input[name = "files"]:checked').closest('tr').next();
        targets.find('.d-selects').html("");
        var lastval = 0;
        var trigger = false;
        if(this.fstash.length == 0) return true;
        var type = this.fstash[0].attr('type');
        for(var sid in this.fstash){
            var select = this.fstash[sid];
            if(sid == this.fstash.length-1) trigger = true;
            if(select.is('[sel-value]')) {
                select.find('option:selected').prop("selected", false).removeAttr("selected");
                select.find('option[value="' + select.attr('sel-value') + '"]').prop("selected", true).attr("selected", true);
            }
            targets.each(function(){
                that.attachFileSelectFromStash(parseInt(sid), $(this), $(this).attr('document-id'), type, lastval, select, select.attr('sel-value'), trigger);
            });
            lastval = select.attr('sel-value');
        }
        /*selects.each(function(ind){
            var select = $(this);console.log(select);
            lastval = select.attr('sel-value');
            if(selects.length == ind+1) trigger = true;
            targets.each(function(){
                that.attachFileSelect(parseInt(select.attr('index')), $(this), $(this).attr('document-id'), select.attr('type'), lastval, false, select.attr('sel-value'), trigger);
            });
        });*/
	},
    editMultipleDocs: function(options){
        var that = this;
        var selects = this.body.find('#d-multidoc-wrapper .file-select');
        var div = $("<div class='d-selects row'></div>");
        var tr = this.body.find('input[name = "documents"]:checked').closest('tr');
        tr.each(function(){
            var row = that.table.row( $(this) );
            if(!$(this).hasClass('shown')) row.child(div.clone()).show();
        });
        var targets = this.body.find('input[name = "documents"]:checked').closest('tr').next();
        targets.find('.d-selects').html("");
        var lastval = 0;
        var trigger = false;
        if(this.fstash.length == 0) return true;
        var type = this.fstash[0].attr('type');
        for(var sid in this.fstash){
            var select = this.fstash[sid];
            if(sid == this.fstash.length-1) trigger = true;
            if(select.is('[sel-value]')) {
                select.find('option:selected').prop("selected", false).removeAttr("selected");
                select.find('option[value="' + select.attr('sel-value') + '"]').prop("selected", true).attr("selected", true);
            }
            targets.each(function(){
                that.attachFileSelectFromStash(parseInt(sid), $(this), $(this).prev().attr('document-id'), type, lastval, select, select.attr('sel-value'), trigger);
            });
            lastval = select.attr('sel-value');
        }
        /*selects.each(function(ind){
            var select = $(this);console.log(select);
            lastval = select.attr('sel-value');
            if(selects.length == ind+1) trigger = true;
            targets.each(function(){
                that.attachFileSelect(parseInt(select.attr('index')), $(this), $(this).attr('document-id'), select.attr('type'), lastval, false, select.attr('sel-value'), trigger);
            });
        });*/
    }
}