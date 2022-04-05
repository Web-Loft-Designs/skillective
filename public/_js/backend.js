jQuery(document).ready(function($) {
    $(document).delegate('input.searchnow-in-list[type="text"]', 'keyup', function(e) {
        if (e.keyCode === 13) {
            e.preventDefault()
            initSearchInList();
            return false;
        }
    });
    $(document).delegate('select.searchnow-in-list', 'change', function(e) {
        e.preventDefault()
        initSearchInList();
        return false;
    });

    $(document).delegate('input.searchnow-in-list-toggle', 'change', function(e) {
        e.preventDefault()
        initSearchInList();
        return false;
    });

    $('body').on('click', '.flash-message a.close', function(e){
        e.preventDefault();
        $(this).parent().remove();
        return false;
    });

    function initSearchInList(){
        var url = $('#current-list-url').attr('href');
        $(".searchnow-in-list").each(function() {
            var re = new RegExp('[\&]?'+ $(this).attr('name') + '=[^\&]*\&?',"g");
            url = url.replace(re, '');
        });
        url.replace('&&', '&');
        if (url.indexOf('?') == -1) {
            url += '?';
        }
        $(".searchnow-in-list").each(function() {
            if ($(this).attr('type')!=undefined && $(this).attr('type')=='radio' && $(this).attr("checked") == 'checked'){
                var checkedRadio = $(this).parents('div').find('input:radio:checked');
                url += '&' + checkedRadio.attr('name') + '=' + checkedRadio.val();
            }else if ($(this).attr('type')==undefined || $(this).attr('type')!='radio'){
                if ($(this).val() !== '') {
                    url += '&' + $(this).attr('name') + '=' + $(this).val();
                }
            }
        });

        window.location = url;
    }

    $('.item-editform-delete').click(function(){
        $('.form-delete-current-page-item button').trigger('click');
    });

    $('.item-editform-approve').click(function(){
        $('.form-approve-current-page-item button').trigger('click');
    });

    $('.item-editform-deny').click(function(){
        $('.form-deny-current-page-item button').trigger('click');
    });

    // dropdown filter for dates
    // DROPDOWN: date filter from-to
    function CreateDropdownFilter(el){
        var self = this;
        this.parent = el;
        this.clearBtn = el.querySelector('[role="dropdown-clear"]');
        this.applyBtn = el.querySelector('[role="dropdown-apply"]');
        this.todayBtn = el.querySelector('[role="dropdown-today"]');
        this.mainInput = el.parentElement.querySelector('input');
        this.fromInput = el.querySelector('[name="'+el.parentElement.querySelector('input').getAttribute('name')+'_from"]');
        this.toInput =	el.querySelector('[name="'+el.parentElement.querySelector('input').getAttribute('name')+'_to"]');
        this.dateSelector = el.querySelector('.date-selector');

        this.clearBtn.addEventListener('click', function(e){
            stopEventDef(e);
            self.clrFieldDate(self);
        }, false);

        this.todayBtn.addEventListener('click', function(e){
            stopEventDef(e);
            self.setTodayDate();
        }, false);


        this.applyBtn.addEventListener('click', function(e){
            // stopEventDef(e);
            // $(".datemask").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
            self.applyDate();
            self.mainInput.removeAttribute('disabled');

            // var today = dateFormatter(new Date());
            // var from = !!self.fromInput.value ? self.fromInput.value : '01/01/1980';
            // var to = !!self.toInput.value ? self.toInput.value : today;

            // TODO: trigger keyup on input - default.js Line 3525
            // if (self.fromInput.hasClass('searchnow-in-list-async')){
            //     var _el = $(this);
            //     initSearchInListAsync(_el)
            // }else if (self.fromInput.hasClass('searchnow-in-list')){
            //     initSearchInList()
            // }

            if ($(self.mainInput).parent().hasClass('searchnow-in-list-async-dropdown')){
                initSearchInListAsync($(self.mainInput));
            }else if ($(self.mainInput).parent().hasClass('searchnow-in-list-dropdown')){
                initSearchInList();
            }
        }, false);



        // this.mainInput.addEventListener('focus', function(e){
        //     stopEventDef(e);
        //     self.parent.classList.add('active');
        // }, false);

        this.dateSelector.addEventListener('click', function(e){
            stopEventDef(e);
            var target = e.target || e.srcElement;
            switchSelectedField(this, target);
            target = target.getAttribute('data-days');
            // $(".datemask").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
            self.setPresetDays(target);
        }, false);
    };


    CreateDropdownFilter.prototype.clrFieldDate = function(){
        this.setCustomDate();
        this.fromInput.value = '';
        this.toInput.value = '';
    };

    CreateDropdownFilter.prototype.setTodayDate = function(){
        // $(".datemask").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
        var curDate = dateFormatter( new Date() );
        this.setCustomDate();
        this.fromInput.value = curDate;
        this.toInput.value = curDate;
    };

    CreateDropdownFilter.prototype.setCustomDate = function(){
        this.dateSelector.querySelector('.selected').classList.remove('selected');
        this.dateSelector.querySelector('li:last-child').classList.add('selected');
        this.fromInput.removeAttribute('disabled');
        this.toInput.removeAttribute('disabled');
    };

    CreateDropdownFilter.prototype.applyDate = function(){
        var from = formatChecker(this.fromInput.value) ?
            this.fromInput.value : '01/01/1980',
            to = formatChecker(this.toInput.value) ?
                this.toInput.value : dateFormatter( new Date() ),
            selected = from == to ? from : from + ' - ' + to;


        this.fromInput.value = from;
        this.toInput.value = to;

        if ( from && to ){
            this.mainInput.value = selected;
            this.parent.classList.remove("active");
        }
        this.parent.classList.remove("active");
    };

    CreateDropdownFilter.prototype.setPresetDays = function(days){
        if (!days){
            this.fromInput.removeAttribute('disabled');
            this.toInput.removeAttribute('disabled');
            this.clrFieldDate();
        }
        else{
            var days = parseInt(days) * 24 * 3600 * 1000,
                now = new Date(),
                from = new Date( now.getTime() - days );
            this.fromInput.value = dateFormatter(from);
            this.toInput.value = dateFormatter(now);
            this.fromInput.setAttribute('disabled', true);
            this.toInput.setAttribute('disabled', true);
        };
    };

    function switchSelectedField (ul, li){
        var prev = ul.querySelector('.selected');
        if ( prev == li || li.tagName == 'UL') return false;
        prev.classList.remove('selected')
        li.classList.add('selected');
    };

    function dateFormatter(date){
        var month = (date.getMonth() + 1).toString(),
            day = date.getDate().toString(),
            year = date.getFullYear();
        month = month.length > 1 ? month : '0' + month;
        day = day.length > 1 ? day : '0' + day;

        date = month + '/' + day + '/' + year;
        return date;
    };

    function stopEventDef(event){
        var e = event || window.event;
        e.preventDefault();
    };

    function formatChecker(str){
        return /^\d{2}\/\d{2}\/\d{4}$/.test(str);
    };

    // hide dropdown on click outside
    (function(){
        $(document).click(function(e) {
            var target = $(e.target);
            var parent = $(target.parent()[0]);

            var clickOnInput = target.hasClass('has-dd-calendar');
            var dropdownIsOpen = $('.dropdown-container').hasClass('active');

            if(clickOnInput){
                $('input.has-dd-calendar').prop('disabled', false);
                target.prop('disabled', true);
                $('.dropdown-container').removeClass('active');
                $('.dropdown-container', parent).addClass('active');

                $('.dropdown-container', parent).click(function(e){
                    e.stopPropagation();
                });
            } else if(dropdownIsOpen){
                $('.dropdown-container').removeClass('active');
                $('input.has-dd-calendar').prop('disabled', false);
                $('.dropdown-container', parent).unbind('click');
            }
        });
    })();

    var dropdown = [];
    $('.dropdown-container').each(function(i, el){
        dropdown[i] = new CreateDropdownFilter(el);
    });

    $('.wrapper-file-input input').change(function(e) {
        if(e.target.files.length) {
            $(this).closest('.wrapper-file-input').find('.name').html(e.target.files[0].name);
        }
    })
});

function reindexSectionFieldsNames(fieldsContainer){
    // var fieldsContainerIndex = fieldsContainer.parents('.section-row').attr('id').replace('section-row-', '');
    var _counter = 0;
    fieldsContainer.find('li').each(function(){
        jQuery(this).attr('id', fieldsContainer.attr('id') + '-sortable-'+_counter);
        jQuery(this).find('input').each(function(){
            _name = jQuery(this).attr('name');
            jQuery(this).attr('name', _name.replace(/\[([^\[])*\]$/, '['+_counter+']'));
        });
        _counter++;
    });
}

function serializeSortableOrder(fieldsContainer){
    var orderNew = fieldsContainer.nestedSortable('serialize', {startDepthCount: 0});
    fieldsContainer.parents('.section-row').find('input.section-fields-inp').val(orderNew);
}