<div>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">{{ __('ems.welcome_back') }}</h5>
                                        <p>{{ __('ems.sign_in_to_continue') }} {{ config('setting.app_title') }}.</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{ asset('images/profile-img.png') }}" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="auth-logo">
                                <div class="auth-logo-light">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{{ config('setting.app_logo_small_light') }}" alt=""
                                                class="rounded-circle" height="34">
                                        </span>
                                    </div>
                                </div>

                                <div class="auth-logo-dark">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{{ config('setting.app_logo_small_dark') }}" alt=""
                                                class="rounded-circle" height="34">
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2">
                                <form class="form-horizontal" wire:submit="login">

                                    <div class="mb-3">
                                        <label for="username" class="form-label">{{ __('ems.username_or_email') }}</label>
                                        <input type="text" class="form-control" id="username"
                                            placeholder="{{ __('ems.enter_username_or_email') }}" wire:model="username" autocomplete="username">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">{{ __('ems.password') }}</label>
                                        <div class="input-group auth-pass-inputgroup">
                                            <input type="password" class="form-control" placeholder="{{ __('ems.enter_password') }}" aria-label="Password" aria-describedby="password-addon" wire:model="password" autocomplete="current-password">
                                            <button class="btn btn-light" type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                        </div>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember-check"
                                            wire:model="remember">
                                        <label class="form-check-label" for="remember-check">
                                            {{ __('ems.remember_me') }}
                                        </label>
                                    </div>

                                    <div class="mt-3 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">{{ __('ems.log_in') }}</button>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <a href="#" class="text-muted"><i class="mdi mdi-lock me-1"></i> {{ __('ems.forgot_password') }}</a>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                    <div class="mt-5 text-center">

                        <div>
                            <p>{{ __('ems.dont_have_account') }} <a href="auth-register.html" class="fw-medium text-primary">
                                    {{ __('ems.signup_now') }} </a> </p>
                            <p>{{ config('setting.app_copyright') }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
