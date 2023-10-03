<script>
    $(function () {
        //orders-tab
        $('body').on('change', '#orders-tab input[name*=quantity]', function () {
            let _self = $(this);
            let id = $(this).data('id');

            $.post("{{ route('cart.updateQty') }}", {id: id, qty: $(this).val()}, function (result) {

                $.get('/basket/load', function (html) {
                    $('body').find('#orders-tab').html(html);
                    updateTotalPriceInHead();
                });
            }, 'json')

        }).on('click', '#orders-tab  .box__basket-item .btn__quality-minus', function () {
            let _self = $(this);
            let id = $(this).closest('.box__basket-item').find('.remove-from-cart').data('id');

            if (typeof id == "undefined") {
                id = $(this).data('id')
            }
            $.post("{{ route('cart.updateQty') }}", {
                id: id,
                qty: _self.closest('.box__quality').find('input[name*=quantity]').val()
            }, function (result) {
                $.get('/basket/load', function (html) {
                    $('body').find('#orders-tab').html(html);
                    updateTotalPriceInHead();
                });
            }, 'json')
        }).on('click', '#orders-tab .box__basket-item .btn__quality-plus', function () {
            let _self = $(this);
            let id = $(this).closest('.box__basket-item').find('.remove-from-cart').data('id');

            if (typeof id == "undefined")
                id = $(this).data('id')

            $.post("{{ route('cart.updateQty') }}", {
                id: id,
                qty: _self.closest('.box__quality').find('input[name*=quantity]').val()
            }, function (result) {

                $.get('/basket/load', function (html) {
                    $('body').find('#orders-tab').html(html);
                    updateTotalPriceInHead();
                });
            }, 'json')
        })

        //preorders-tab
        $('body').on('change', '#preorders-tab input[name*=quantity]', function () {
            let _self = $(this);
            let id = $(this).data('id');

            $.post("{{ route('cart.updatePreOrderQty') }}", {id: id, qty: $(this).val()}, function (result) {

                $.get('/profile', function (html) {
                    $('body').find('#preorders-tab').html($(html).find('#preorders-tab').html());
                    updateTotalPriceInHead();
                });
            }, 'json')

        }).on('click', '#preorders-tab  .box__basket-item .btn__quality-minus', function () {
            let _self = $(this);
            let id = $(this).closest('.box__basket-item').find('.remove-from-cart').data('id');

            if (typeof id == "undefined") {
                id = $(this).data('id')
            }
            $.post("{{ route('cart.updatePreOrderQty') }}", {
                id: id,
                qty: _self.closest('.box__quality').find('input[name*=quantity]').val()
            }, function (result) {
                $.get('/profile', function (html) {
                    $('body').find('#preorders-tab').html($(html).find('#preorders-tab').html());
                    updateTotalPriceInHead();
                });
            }, 'json')
        }).on('click', '#preorders-tab .box__basket-item .btn__quality-plus', function () {
            let _self = $(this);
            let id = $(this).closest('.box__basket-item').find('.remove-from-cart').data('id');

            if (typeof id == "undefined")
                id = $(this).data('id')

            $.post("{{ route('cart.updatePreOrderQty') }}", {
                id: id,
                qty: _self.closest('.box__quality').find('input[name*=quantity]').val()
            }, function (result) {

                $.get('/profile', function (html) {
                    $('body').find('#preorders-tab').html($(html).find('#preorders-tab').html());
                    updateTotalPriceInHead();
                });
            }, 'json')
        })
    })

    function updateTotalPriceInHead()
    {
        let totalPriceOrders = $('#totalPriceOrders').val();
        totalPriceOrders = totalPriceOrders ? totalPriceOrders : 0;
        let totalPricePreOrders = $('#totalPricePreOrders').val();
        totalPricePreOrders = totalPricePreOrders ? totalPricePreOrders : 0;
        console.log('totalPriceOrders', totalPriceOrders)
        console.log('totalPricePreOrders', totalPricePreOrders)
        let totalProductsPrice = Number(totalPriceOrders) + Number(totalPricePreOrders) + ' ₽';
        $('#total-price').html(totalProductsPrice)
    }

</script>
