<script>
    let pageNumber = 1

    function fetchLazy() {
        const url = `{{route('merch.lazy-pages', $preorder->id)}}?page=${pageNumber}&category={{$currentCategory->id}}&subcategory={{$currentsubCategory->id}}`

        return fetch(url)
            .then(response => response.text())
            .then(data => {
                if ($(data).length) {
                    $('.products-container').append(data)
                    addListeners()
                } else {
                    observer.unobserve($('.products-container').children().last()[0])
                }
            })
    }

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                pageNumber++
                fetchLazy()
                observer.unobserve(entry.target)

            }
        })
    }, {
        root: null,
        rootMargin: '0px',
        treshold: 0.1
    })

    function addListeners() {
        $('.merch-change-qty').off('click')
        $('.merch-change-qty').click(function () {
            const el = $(this)
            const productId = el.data('id')
            const input = $(`input[data-product-id="${productId}"]`)
            switch (el.data('op')) {
                case "increment":
                    input.val(Number(input.val()) + Number(input.attr('step')))
                    break
                case "decrement":
                    input.val(input.val() - input.attr('step'))
                    break
            }
        })
        $('.qty-append').off('click')
        $('.qty-append').click(function () {
            const el = $(this)
            const productId = el.data('id')
            const input = $(`input[data-product-id="${productId}"]`)
            const operation = Number(input.val()) > 0 ? "increment" : "decrement"
            const table = $('.preorder-main-data').first()
            const preorderId = table.data('preorder-id')
            let qty = Number(input.val())
            if (qty < 0) {
                qty = -qty
            }
            $(`#merch-product-${productId} .wrapper-total-quantity`).text('Обновляется..')
            $.post(`/merch/product/${productId}`, {operation, qty})
                .then((resp) => {
                    input.val(0)
                    $(`#merch-product-${productId} .wrapper__baskets-info`).html($(resp).find('.wrapper__baskets-info').html())
                    $(`#merch-product-${productId} .wrapper-total-quantity`).html($(resp).find('.wrapper-total-quantity').html())
                    $.get(`/merch/preorder/${preorderId}/table`).then((resp) => {
                        table.html(resp.html())
                    })
                    if (operation === 'increment')
                        el.children('button').addClass('ifcart')
                })
        })
        $('.wrapper__baskets-info').off('mouseenter')
        $('.wrapper__baskets-info').mouseenter(function () {
            $(this).find('.baskets-info-floating').show(300);
        })
        $('.wrapper__baskets-info').off('mouseleave')
        $('.wrapper__baskets-info').mouseleave(function () {
            $(this).find('.baskets-info-floating').hide(300);
        })
        observer.observe($('.products-container').children().last()[0])
    }

    addListeners()
    var stickyOffset = $('.preorder-main-data').offset().top;
    $(window).scroll(function () {
        var sticky = $('.preorder-main-data'),
            scroll = $(window).scrollTop();

        if (scroll >= stickyOffset) sticky.addClass('fixed');
        else sticky.removeClass('fixed');
    });


</script>
<? //Да, тут не только JS, увы.?>
<style>
    .baskets-info-floating {
        position: absolute;
        top: 20px;
        left: 20px;
        width: 580px;
        border: 1px solid #239700;
        padding: 5px;
        z-index: 10000;
        background-color: white;
        display: none;
    }

    .baskets-info-floating * {
        font-size: 0.9rem;
    }

    .product-description {
        font-size: 0.7rem;
    }

    .fixed {
        position: fixed;
        top: 75px;
        right: 10%;
        left: 10%;
        z-index: 1000;
        background-color: white;
        width: 100%;
    }

    ul.characteristics li {
        margin: 5px 0 !important;
    }
    .push-to-cart.ifcart {
        background: #A16C21;
        color: #fff !important;
    }
</style>
