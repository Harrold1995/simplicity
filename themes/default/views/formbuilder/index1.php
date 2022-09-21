<div id="main_content" class="inner">
    <form class="build-form clearfix"></form>
    <div class="render-form"></div>
</div>
<div class="render-btn-wrap">
    <button id="renderForm" class="btn btn-default">Preview Template</button>
</div>
<div class="recordset-btn-wrap">
    <select class="recordset">
        <option value="none" <?=(!$recordset ? 'selected' : '')?>>None Selected</option>
        <option value="property" <?=($recordset == 'property' ? 'selected' : '')?>>Property</option>
        <option value="lease" <?=($recordset == 'lease' ? 'selected' : '')?>>Lease</option>
    </select>
</div>
<script type="text/javascript">
    var list = [];
    $.post('<?php echo base_url().'formbuilder/getFields'; ?>', {recordset: $('select.recordset').val()}, function(data){
        list = data;
    }, 'json');
    $(document).ready(function(){
        $('.recordset').change(function(){
            $.post('<?php echo base_url().'formbuilder/getFields'; ?>', {recordset: $(this).val()}, function(data){
                list = data;
            }, 'json');
        });
        $('body').on('click', '.save-form', function(){
            $.post('<?php echo base_url().'formbuilder/saveRecordset/'.$id; ?>', {recordset: $('select.recordset').val()});
        });
        const isSite = (window.location.href.indexOf('draggable.github.io') !== -1);
        let container = document.querySelector('.build-form');
        let renderContainer = document.querySelector('.render-form');
        let formeoOpts = {
            container: container,
            actionUrl: '<?=$saveUrl?>',
            i18n: {
                preloaded: {
                    'en-US': {'row.makeInputGroup': ' Repeatable Region'}
                }
            },
            controls: {
                sortable: false,
                groupOrder: [
                    'common',
                    'html',
                ],
                elementOrder: {
                    common: [
                        'button',
                        'checkbox',
                        'date-input',
                        'inline-input',
                        'inline-checkbox',
                        'hidden',
                        'upload',
                        'number',
                        'radio',
                        'select',
                        'text-input',
                        'textarea',
                    ]
                }
            },
            events: {
                // onUpdate: console.log,
                // onSave: console.log
            },
            svgSprite: '<?=base_url();?>themes/default/assets/formeo/img/formeo-sprite.svg',
            // debug: true,
            sessionStorage: false,
            editPanelOrder: ['attrs', 'options']
        };


        const formeo = new window.Formeo(formeoOpts<?=($formData != '') ? ', '.$formData : '';?>);
        let toggleEdit = document.getElementById('renderForm');
        let editing = true;

        toggleEdit.onclick = evt => {
            document.body.classList.toggle('form-rendered', editing);
            if (editing) {
                formeo.render(renderContainer);
                evt.target.innerHTML = 'Edit Template';
            } else {
                evt.target.innerHTML = 'Preview Template';
            }

            return editing = !editing;
        };

        let formeoLocale = window.sessionStorage.getItem('formeo-locale');
        if (formeoLocale) {
            localeSelect.value = formeoLocale;
        }



        function makeUL(array, field) {
            // Create the list element:
            var list = document.createElement('ul');
            list.className = 'inline-fields';
            for (var i = 0; i < array.length; i++) {
                // Create the list item:
                var item = document.createElement('li');

                // Set its contents:
                item.appendChild(document.createTextNode(array[i]));

                // Add it to the list:
                list.appendChild(item);
            }
            list.addEventListener('mousedown',function(e){
                if(e.target && e.target.tagName == 'LI'){
                    field.setAttribute('value',e.target.innerHTML);
                    list.remove();
                    formeo.h.triggerEvent(field.parentElement.parentElement.parentElement, 'input');
                }
            });
            field.addEventListener('blur',function(e){
                list.remove();
            });
            field.addEventListener('input',function(e){
                this.setAttribute('value',this.value);
            });
            return list;
        }
        document.addEventListener('click',function(e){
            if(e.target && e.target.className == 'inline-input'){
                let _this = e.target;
                e.stopPropagation();
                let ul = makeUL(list, _this);
                ul.style.position = 'absolute';
                ul.style.left = '10px';
                ul.style.top = _this.style.top;
                _this.parentElement.appendChild(ul);
            }
        })
    });
</script>
