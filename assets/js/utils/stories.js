$(document).ready(function () {
    $(document).on('click', '.story', function () {
        $('.overlay').fadeIn();
        $('.story').removeClass('active');

        $(this).addClass('active');

        if ($('.modal').length <= 0) {
            $.ajax({
                type: 'POST',
                url: 'ajax_route.php',
                data: {
                    url: '/core/action/barcode/edit_product_barcode_modal.php',
                    route: 'appendStoriesModal',
                },
                dataType: 'json',
                success: (data) => {
                    $('.container').append(data.res);

                    innerStoriesImage();
                }
            });
        }


        innerStoriesImage();

    });

    $(document).on('click', function (event) {
        var content = $('.stories-modal');
        var target = $(event.target);

        // Проверяем, является ли цель клика элементом контента или его дочерним элементом
        if (!target.closest(content).length && !target.closest('.active').length && content.is(':visible')) {
            remove_modal();
            $('.overlay').hide();
        }
    });

    // Остановка всплытия события клика для контента и его дочерних элементов
    $(document).find('.modal-content').on('click', function (event) {
        event.stopPropagation();
    });



    $(document).on('mouseup', '.next', function () {
        var totalSlide = $('.story').length;
        var currSlide = $('.active').index() + 1;

        if (currSlide >= totalSlide) {
            remove_modal();
            $('.overlay').hide();
            return;
        }

        $('.active').next().trigger('click');
    });


    $(document).on('mouseup', '.prev', function () {
        $('.active').prev().trigger('click');
    });


    $(document).on('mousedown', ['.prev', '.next'], function () {
        if($(document).find('#myId').length) {
            $(document).find('#myId').get(0).pause()
        }
    });

    $(document).on('mouseup', '.prev', '.next', function () {
        if($(document).find('#myId').length) {
            $(document).find('#myId').get(0).play()
        }
    });



    function innerStoriesImage() {
        var img = $('.story.active').data('image');

        if ($('.story.active').has('.stories-image')) {
            if ($('.story.active').data('image')) {
                $(document).find('.modal-content').find('.image').html(`
          <img class="animate__animated animate__fadeIn" src="${img}">
        `);
            }
        }

        if ($('.story.active').has('.stories-video')) {
            if ($('.story.active').data('video')) {
                var video = $('.story.active').data('video');

                $(document).find('.modal-content').find('.image').html(`
          <video id="myId" control class="animate__animated animate__fadeIn so" src="${video}" autoplay>
        `);
            }
        }

        $(document).find('.modal-content').attr('style', `background-image: url(${img});`)
    }


});