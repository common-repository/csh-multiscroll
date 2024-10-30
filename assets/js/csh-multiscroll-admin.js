jQuery(document).ready(function ($) {
    /*----------------------------------Setting Js------------------------------------*/
    /*-------------------------------------------------------------------------------------*/
    $('.cshms-color-field').wpColorPicker();

    $("#cshms-select-page").select2({
        placeholder: "Select a page",
        allowClear: true
    });

    var init_count = parseInt( $('#cshms_section_count').val() );
    function cshms_update_positon() {
        $('.cshms-meta').each(function(index,element){
            var $this = $(this);
            var current_index = index + 1;
            var add_class = 'cshms-meta-' + current_index;
            for (var i = 1; i <= init_count; i++) {
                var check_class = 'cshms-meta-' + i;
                if($this.hasClass(check_class)){
                    $this.removeClass(check_class);
                    $this.addClass(add_class);
                    $this.find('.cshms-section-name').html('Section ' + current_index);

                    $this.find('.left-content-image .hide-image-url').attr({id:'cshms_left_image_' + current_index, name: 'cshms_left_image_' + current_index});
                    $this.find('.left-content-text textarea').attr({id:'cshms_left_text_' + current_index, name: 'cshms_left_text_' + current_index});

                    $this.find('.right-content-image .hide-image-url').attr({id:'cshms_right_image_' + current_index, name: 'cshms_right_image_' + current_index});
                    $this.find('.right-content-text textarea').attr({id:'cshms_right_text_' + current_index, name: 'cshms_right_text_' + current_index});
                }
            }
        });
    }
    // Select image slide
    $(document).on('click', '.select-image',  function (e) {
        e.preventDefault();
        var $this = $(this);
        var image = wp.media({
            title: 'Upload image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
            .on('select', function (e) {
                // This will return the selected image from the Media Uploader, the result is an object
                var uploaded_image = image.state().get('selection').first();
                // We convert uploaded_image to a JSON object to make accessing it easier
                // Output to the console uploaded_image
                var image_url = uploaded_image.toJSON().url;
                // Let's assign the url value to the input field
                $this.parent().find('.hide-image-url').val(image_url);
                $this.parent().find('.cshms-show-image').empty();
                $this.parent().find('.cshms-show-image').append('<img src = "' + image_url + '">');
                $this.hide();
                $this.parent().find('.remove-image').show();
            });
    });

    $(document).on('click', '.remove-image', function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.parent().find('.hide-image-url').val('');
        $this.parent().find('.cshms-show-image').empty();
        $this.hide();
        $this.parent().find('.select-image').show();
    });

    $(document).on('click', '.cshms-add-new', function (e) {
        e.preventDefault();
        var slide_count = parseInt($('#cshms_section_count').val()) + 1;
        var new_section = cshms_new_section.new_html;
        new_section = new_section.replaceAll('{{slide-count}}', slide_count);
        $('#cshms-meta-wrap').append(new_section);
        $('#cshms_section_count').val(slide_count);
        init_count = init_count + 1;
        $('.cshms-color-field').wpColorPicker();
    });

    String.prototype.replaceAll = function (search, replacement) {
        var target = this;
        return target.replace(new RegExp(search, 'g'), replacement);
    };

    $(document).on('click', '.cshms-heading', function (e) {
        e.preventDefault();
        var $this = $(this);
        var dashicon = $(this).find('.cshms-bar-title .dashicons');
        if (dashicon.hasClass('dashicons-arrow-up')) {
            dashicon.removeClass('dashicons-arrow-up');
            dashicon.addClass('dashicons-arrow-down');
            $this.parent().find('.cshms_hide_content').val('hide-content');
        } else {
            dashicon.removeClass('dashicons-arrow-down');
            dashicon.addClass('dashicons-arrow-up');
            $this.parent().find('.cshms_hide_content').val('');
        }
        $(this).parent().find('.cshms-toggle-action').slideToggle();
    });

    $(document).on('click', '.cshms-close', function (e) {
        e.preventDefault();
        var $this = $(this);
        var cshms_confirm = confirm("Do you want to delete this section?");
        if (cshms_confirm == true) {
            $this.parents('.cshms-meta').remove();
            var current_count = parseInt( $('#cshms_section_count').val() ) - 1;
            $('#cshms_section_count').val(current_count);
            cshms_update_positon();
        }
    });

    /**
     * Sort songs
     */
    var metaSections = $("#cshms-meta-wrap");
    metaSections.sortable({
        handle: ".cshms-heading",
        items: ".cshms-meta",
        placeholder: "sortable-placeholder",
        over: function (event, ui) {
            $(".ui-sortable-helper").css({
                "height": "40px","overflow": "hidden"
            });
            $('.cshms-toggle-action').addClass('cshms-dragging');
        },
        start: function (event, ui) {
            metaSections.addClass('hidden-control');
        },
        update: function (event, ui) {
            cshms_update_positon();
        },
        stop: function () {
            metaSections.removeClass('hidden-control');
            $('.cshms-toggle-action').removeClass('cshms-dragging');
        }
    });

});