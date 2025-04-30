"use strict";

// Class definition
var KTFormRepeaterAdvanced = function () {
    var sectionItemRepeater = function () {
        $('#attributes_repeater').repeater({
            initEmpty: false,
            defaultValues: {
                'text-input': 'foo'
            },
            show: function () {
                $(this).slideDown();
                KTImageInput.createInstances();

                $(this).find('.attribute-select').each(function() {
                    var $select = $(this);
                    $select.select2({
                        placeholder: "اختر القيمة",
                        allowClear: true,
                        width: '100%',
                    });
                });

                const index = Date.now(); // إنشاء index فريد
                const newItem = $(this).slideDown();
                newItem.find('[name]').each(function () {
                    const name = $(this).attr('name').replace('__index__', index);
                    $(this).attr('name', name);
                });
            },
            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
    }
    
    var mediaRepeater = function () {
        $('#media_repeater').repeater({
            initEmpty: false,

            defaultValues: {
                'text-input': 'foo'
            },

            show: function () {
                $(this).slideDown();
                KTImageInput.createInstances();
                // Re-init select2
                // $(this).find('[data-kt-repeater="select2"]').select2();

                // Re-init flatpickr
                // $(this).find('[data-kt-repeater="datepicker"]').flatpickr();

                // Re-init tagify
                // new Tagify(this.querySelector('[data-kt-repeater="tagify"]'));
            },

            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            },

            ready: function(){
                // Init select
                // $('[data-kt-repeater="select2"]').select2();

                // Init flatpickr
                // $('[data-kt-repeater="datepicker"]').flatpickr();

                // Init Tagify
                // new Tagify(document.querySelector('[data-kt-repeater="tagify"]'));
            }
        });        
    }
    return {
        init: function () {
            mediaRepeater();
            sectionItemRepeater();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTFormRepeaterAdvanced.init();
});
