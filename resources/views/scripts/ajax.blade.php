<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let loaded = 0;
    $(document).ready(function () {
        let timeoutSearch;
        $("input[name='products']").on('keyup', function () {
            let _self = $(this);
            clearInterval(timeoutSearch);

            timeoutSearch = setTimeout(() => {
                let $title = _self.val();
                $.ajax({
                    type: 'get',
                    dataType: 'html',
                    url: '{{route('searchProducts')}}',
                    cache: false,
                    data: {'products': $title, isAjax: true},
                    success:
                        function (response) {
                            $('#productData').html(response).show();
                        }
                });
            }, 800)
        });
        var pageNumber = 1;
        //load_more(pageNumber);
        /*$(window).scroll(function() {
            if($(window).scrollTop() + $(window).height() >= $(document).height()) {
                pageNumber++;
                load_more(pageNumber);
            }
        });*/
        /*function load_more(pageNumber) {
            // console.log(($('.box__catalog-view li.active').val()));
            if (pageNumber > 1)
                $.ajax({
                    dataType: 'html',
                    async: true,
                    type: 'GET',
                    url: "?page=" + pageNumber+"&attributeStyle="+($('.box__catalog-view li.active>button').val()),
                    beforeSend: function () {
                        $('.ajax-loading').show();
                    }
                })
                    .done(function (data) {
                        if (data.length == 0) {
                            $('.ajax-loading').hide();
                            $('.showMore').hide();
                            return;
                        }
                        $('.ajax-loading').hide();
                        data = $(data).find('div#productData');
                        $('#productFind').append(data);

                    }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    // console.log('No response from server');
                });
        }*/

        $("body").on("click", ".add-to-cart", function () {
            let but = $(this).attr('value'),
                _self = $(this);

            let container = $('<span>', {class: 'loading'});
            _self.hide();
            for (let i = 0; i < 5; i++)
                container.append($('<span>', {
                    class: 'dot'
                }))


            $(this).parent().append(container);
            $(this).parent().parent().parent().find('.ifcart').text('Товар есть в корзине');
            $(this).parent().parent().parent().find('.add-to-cart').addClass('ifcart').text('Докупить');
            // $(this).parent().parent().parent().find('.add-to-cart').addClass('ifcart');
            $.ajax({
                url: '{{ url('add-to-cart/') }}/'+but,
                method: "post",
                data: {
                    quantity: $(".quantity"+but).val()
                },
                success: function (response) {
                    _self.show().parent().find('.loading').remove();
                    $('.box__card').removeClass('d-none').addClass('d-block')
                    $.get('/basket/load', function (html) {
                        $('body').find('.box__popup-basket .wrapper-popup-center').html(html);

                        $('.box__card-quality').text($('body').find('.box__popup-basket .wrapper-popup-center').find('.box__basket-item').length);
                        let total = 0;
                        $('.box__popup-basket').find('.wrapper-popup-center').find('.box__basket-item').each(function () {
                            let num = parseFloat($(this).find('.box__price').text().replace(/[^\d.-]/g, ''));
                            // console.log(num);
                            total += num;
                        })
                        // console.log(`total: ${total}`);

                        $('[data-popup="basket"] .wrapper-popup-bottom .box__price, #total-price').text(Number(total).toFixed(0) + ' ₽')
                    });
                }
            });
        });

        /*$('body').on('click', ".update-cart", function (e) {
            e.preventDefault();
            var id_pr = $(this).attr("data-id");
            $.ajax({
                url: '{{ url('update-cart/') }}/'+id_pr,
                method: "patch",
                data: {
                    _token: '{{ csrf_token() }}',
                    quantity: $(".quantityUpdate"+id_pr).val()
                },
                success: function (response) {
                    setTimeout(function () {
                        $.get('', (html) => {
                            $('.wrapper__baskets').html($(html).find('.wrapper__baskets').html());
                            $('.wrapper__bascket-bottom').html($(html).find('.wrapper__bascket-bottom').html());
                        })
                    }, 1000)
                }
            });
        });*/

        $("body").on("click", ".remove-from-cart", function (e) {
            e.preventDefault();
            var ele = $(this);
            $.ajax({
                url: '{{ url('remove-from-cart') }}',
                method: "DELETE",
                data: {_token: '{{ csrf_token() }}', id: ele.attr("data-id")},
                success: function (response) {
                    $.get('', function (result) {
                        $('.box__popup-basket .wrapper-popup').replaceWith($(result).filter('.box__popup-basket').html());

                        if ($('.box__basket-item').length === 0)
                            location.reload();

                        ele.closest('.wrapper__baskets-item').remove();
                    }, 'html');
                }
            });
        });

        $('body').on('click', '.showMore', function () {
            let currentPage = parseInt($('#current_page').val()) + 1;

            if (currentPage > 1)
                $.ajax({
                    dataType: 'html',
                    async: true,
                    type: 'GET',
                    url: "?page="+currentPage+"&loadhtml=true",
                    beforeSend: function () {
                        $('.ajax-loading').show();
                    }
                }).done(function (data) {
                    $('#current_page').val(currentPage);

                    if ($(data).find('.row').children().length < 20)
                        $('.showMore').hide();

                    $('.ajax-loading').hide();
                    $('#productData > .row').append($(data).find('#productData > .row').html());

                    loaded = 0;

                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    // console.log('No response from server');
                });
        }).on('click', '[data-btn-popup]', function () {
            let _self = $(this);
            $('[data-popup]').each(function () {
                if ($(this).data('popup') !== _self.data('btn-popup'))
                    $(this).iziModal('close');
            });
        })

        $(document).mouseup(function (e){ // событие клика по веб-документу
            let div = $("#productData"); // тут указываем ID элемента
            if (!div.is(e.target) // если клик был не по нашему блоку
                && div.has(e.target).length === 0) { // и не по его дочерним элементам
                div.hide(); // скрываем его
            }
        });
    });

    $(window).on('scroll', function () {
        if ($("#productFind").length && ($(window).scrollTop() >= ($('#productFind').offset().top + $('#productFind')[0].clientHeight) - 800) && loaded === 0) {
            loaded = 1;

            $('.showMore').trigger('click');
        }
    })
</script>

