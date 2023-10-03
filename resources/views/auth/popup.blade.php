<div class="box__popup" data-popup="authorization">
    <div class="wrapper-popup">
        <div class="btn__close"><button aria-label="Закрыть попап" data-btn-closepopup><span></span></button></div>
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-6 text-left">
                        <h2>Авторизация</h2>
                    </div>
                    <div class="col-6 text-right">
                        <div class="box__popup-button"><button data-btn-popup="registration">Регистрация</button></div>
                    </div>
                </div>
                <div class="box__form">
                    <form method="post" action="{{ route('login') }}">
                        @csrf
                        <input type="hidden" name="type" value="auth">
                        <div class="row">
                            <div class="col-12">
                                <div class="box__input">
                                    <label class="login"><span class="login-switch">Войти с помощью Email</span></label>
                                    <input class="phone-mask" type="text" name="phon" placeholder="Номер телефона">
                                </div>
                                <input class="type" type="hidden" name="type" value="phon">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="box__input">
                                    <input type="password" data-input-password name="password" placeholder="Пароль">
                                    <label><span class="password-control" title="Показать пароль"></span></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-center">
                                <div class="box__popup-button margin-15"><button type="button" data-btn-popup="resetpass">Восстановить пароль</button></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12"><div class="btn"><button type="submit">Войти</button></div></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box__popup" data-popup="registration">
    <div class="wrapper-popup">
        <div class="btn__close"><button aria-label="Закрыть попап" data-btn-closepopup><span></span></button></div>
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-6 text-left">
                        <h2>Регистрация</h2>
                    </div>
                    <div class="col-6 text-right">
                        <div class="box__popup-button"><button data-btn-popup="authorization">Авторизация</button></div>
                    </div>
                </div>
                <div class="box__form">
                    <label for="" class="label label-info">{{ setting('site.register_text') }}</label>
                    <form action="{{ route('register') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="box__input"><input type="email" name="email" placeholder="Электронная почта"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="box__input"><input type="text" name="name" placeholder="ФИО"><label class="required"></label></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="box__input"><input type="text" name="phon" class="phone-mask" placeholder="Номер телефона"><label class="required"></label></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="box__input"><input type="text" name="city" placeholder="Населённый пункт"><label class="required"></label></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="box__input" style="height: 45px;">
                                    <input type="password" data-input-password name="password" placeholder="Пароль">
                                    <label><span class="password-control" title="Показать пароль"></span></label><br/>
                                </div>
                                <span style="font-size: 14px;position:relative;top:-15px;">Минимальная длина пароля 8 символов</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="box__input">
                                    <input type="password" data-input-password name="password_confirmation" placeholder="Повторите пароль">
                                    <label><span class="password-control" title="Показать пароль"></span></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="box__checkbox margin-15">
                                    <div class="wrapper-checked">
                                        <label>
                                            <input type="checkbox" onchange="checkAgree()" name="agree" required>
                                            <span>
                                                <span class="box__checkbox-icon"></span>
                                                <span class="box__checkbox-text">Я согласен на обработку <a
                                                        href="">персональных данных</a></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12"><div class="btn"><button disabled>Регистрация</button></div></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/4.0.9/jquery.inputmask.bundle.min.js"></script>
    <script>
        $(() => {
            $('.phone-mask').inputmask('+7 999 999 99-99');
        })
    </script>
</div>

<div class="box__popup" data-popup="resetpass">
    <div class="wrapper-popup">
        <div class="btn__close"><button aria-label="Закрыть попап" data-btn-closepopup><span></span></button></div>
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2>Восстановление пароля</h2>
                    </div>
                </div>
                <div class="box__form">
                    <form method="post" id="reset-password-form" action="{{ route('reset-password') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="box__input"><input type="email" name="email" placeholder="Электронная почта"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="box__description">После заполнения формы мы отправим новый пароль к Вам на почту.</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12"><div class="btn"><button type="submit">Восстановить</button></div></div>
                        </div>
                    </form>
                    <div id="success-message" class="alert alert-success" style="display:none;"></div>
                </div>
            </div>
        </div>
    </div>
</div>




<link rel="stylesheet" href="{{ asset('js/libs/noty/lib/noty.css') }}">
<link rel="stylesheet" href="{{ asset('js/libs/noty/lib/themes/mint.css') }}">
<script src="{{ asset('js/libs/noty/lib/noty.min.js') }}"></script>

@if ($errors->any())
    <script>
        @foreach ($errors->all() as $error)
            new Noty({
                theme: 'mint',
                text: '{{ $error }}',
                type: 'error'
            }).show();
        @endforeach
    </script>
@endif

<script>
    $(function() {
        $('#reset-password-form').submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#success-message').text(response.success).show();
                }
            });
        });
    });
</script>

<script>
    function checkAgree() {
        if (!$('[data-popup="registration"]').find('[name=agree]').is(':checked')) {
            new Noty({
                theme: 'mint',
                text: 'Соглашение на обработку персональных данных является обязательным полем!',
                type: 'error'
            }).show();
            $('[data-popup="registration"]').find('button').attr('disabled', true);
        } else
            $('[data-popup="registration"]').find('button').attr('disabled', false);
    }
</script>
