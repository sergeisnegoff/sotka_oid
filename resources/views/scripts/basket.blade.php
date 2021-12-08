<script>
    $(function () {
        $('body').on('change', 'input[name*=quantity]', function () {
            let _self = $(this);
            let id = $(this).data('id');

            $.post("{{ route('cart.updateQty') }}", {id: id, qty: $(this).val()}, function (result) {
                _self.closest('.col-9').find('.box__price').text(result.itemAmount + ' ₽');

                $('.box__popup-basket').find('.box__price').text(result.totalAmount + ' ₽')

                $.get('/basket/load', function (html) {
                    $('body').find('.box__popup-basket .wrapper-popup-center').html(html);

                    $('.box__card-quality').text($('body').find('.box__popup-basket .wrapper-popup-center').find('.box__basket-item').length);
                    let total = 0;
                    $('.box__popup-basket').find('.wrapper-popup-center').find('.box__basket-item').each(function () {
                        total += parseFloat($(this).find('.box__price').text().replace(/[^\d.-]/g, ''));
                    })

                    $('[data-popup="basket"] .wrapper-popup-bottom .box__price').text(Math.round(total * 100) / 100 + ' ₽')
                    _self.closest('.wrapper__baskets-item').find('.wrapper__baskets-cost').text(result.itemAmount + ' ₽');
                    $('.wrapper__bascket-bottom .box__bascket-total').find('h4').find('b').text(result.totalAmount + ' ₽')
                });
            }, 'json')

        }).on('click', '.box__basket-item .btn__quality-minus, .wrapper__baskets-item .btn__quality-minus', function () {
            let _self = $(this);
            let id = $(this).closest('.box__basket-item').find('.remove-from-cart').data('id');

            if (typeof id == "undefined") {
                id = $(this).data('id')
                console.log($(this).data('id'))
            }
            $.post("{{ route('cart.updateQty') }}", {
                id: id,
                qty: _self.closest('.box__quality').find('input[name*=quantity]').val()
            }, function (result) {
                _self.closest('.box__basket-item').find('.box__price').text(result.itemAmount + ' ₽');
                _self.closest('.wrapper__baskets-item').find('.wrapper__baskets-cost').text(result.itemAmount + ' ₽');

                $('.box__popup-basket .wrapper-popup-bottom').find('.box__price').text(result.totalAmount + ' ₽')
                $('.wrapper__bascket-bottom .box__bascket-total').find('h4').find('b').text(result.totalAmount + ' ₽')


                $.get('/basket/load', function (html) {
                    $('body').find('.box__popup-basket .wrapper-popup-center').html(html);

                    $('.box__card-quality').text($('body').find('.box__popup-basket .wrapper-popup-center').find('.box__basket-item').length);
                    let total = 0;
                    $('.box__popup-basket').find('.wrapper-popup-center').find('.box__basket-item').each(function () {
                        total += parseFloat($(this).find('.box__price').text().replace(/[^\d.-]/g, ''));
                    })

                    $('[data-popup="basket"] .wrapper-popup-bottom .box__price').text(Math.round(total * 100) / 100 + ' ₽')
                    $('#total-price').text(Math.round(total * 100) / 100 + ' ₽');
                });
            }, 'json')
        }).on('click', '.box__basket-item .btn__quality-plus, .wrapper__baskets-item .btn__quality-plus', function () {
            let _self = $(this);
            let id = $(this).closest('.box__basket-item').find('.remove-from-cart').data('id');

            if (typeof id == "undefined")
                id = $(this).data('id')

            // console.log(_self.closest('.box__quality').find('input[name*=quantity]').val());
            $.post("{{ route('cart.updateQty') }}", {
                id: id,
                qty: _self.closest('.box__quality').find('input[name*=quantity]').val()
            }, function (result) {
                _self.closest('.box__basket-item').find('.box__price').text(result.itemAmount + ' ₽');
                _self.closest('.wrapper__baskets-item').find('.wrapper__baskets-cost').text(result.itemAmount + ' ₽');

                $('.box__popup-basket .wrapper-popup-bottom').find('.box__price').text(result.totalAmount + ' ₽')
                $('.wrapper__bascket-bottom .box__bascket-total').find('h4').find('b').text(result.totalAmount + ' ₽')

                $.get('/basket/load', function (html) {
                    $('body').find('.box__popup-basket .wrapper-popup-center').html(html);

                    $('.box__card-quality').text($('body').find('.box__popup-basket .wrapper-popup-center').find('.box__basket-item').length);
                    let total = 0;
                    $('.box__popup-basket').find('.wrapper-popup-center').find('.box__basket-item').each(function () {
                        total += parseFloat($(this).find('.box__price').text().replace(/[^\d.-]/g, ''));
                    })

                    $('[data-popup="basket"] .wrapper-popup-bottom .box__price').text(Math.round(total * 100) / 100 + ' ₽');
                    $('#total-price').text(Math.round(total * 100) / 100 + ' ₽');
                });
            }, 'json')
        })
    })
</script>
