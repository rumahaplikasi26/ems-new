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
                                        <h5 class="text-primary">Lupa Password</h5>
                                        <p>Masukkan email untuk reset password.</p>
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
                                @if ($success)
                                    <div class="alert alert-success text-center">
                                        <i class="mdi mdi-check-circle-outline font-size-20"></i>
                                        <p class="mt-2 mb-1">Password berhasil direset!</p>
                                        <p class="mb-1">Password baru untuk <strong>{{ $email }}</strong>:</p>
                                        <h4 class="fw-bold text-dark mt-2 mb-3">
                                            <code class="fs-4">{{ $newPassword }}</code>
                                        </h4>
                                        <p class="text-muted small">Simpan password ini dan segera ganti setelah login.</p>
                                    </div>
                                    <div class="mt-3 d-grid">
                                        <a href="{{ route('login') }}" class="btn btn-primary waves-effect waves-light">
                                            Kembali ke Login
                                        </a>
                                    </div>
                                @else
                                    <form class="form-horizontal" wire:submit="resetPassword">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email" placeholder="Masukkan email anda" wire:model="email" autocomplete="email">
                                            @error('email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="mt-3 d-grid">
                                            <button class="btn btn-primary waves-effect waves-light" type="submit"
                                                wire:loading.attr="disabled">
                                                <span wire:loading.remove>Reset Password</span>
                                                <span wire:loading>Memproses...</span>
                                            </button>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <a href="{{ route('login') }}" class="text-muted">
                                                <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Login
                                            </a>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
