<div class="navbar-header" data-tour="header">
    <div class="d-flex">
        <!-- LOGO -->
        <div class="navbar-brand-box">
            <a href="index.html" class="logo logo-dark">
                <span class="logo-sm">
                    <img src="{{ config('setting.app_logo_small_dark') }}" alt="" height="30">
                </span>
                <span class="logo-lg">
                    <img src="{{ config('setting.app_logo_full_dark') }}" alt="" height="25">
                </span>
            </a>

            <a href="index.html" class="logo logo-light">
                <span class="logo-sm">
                    <img src="{{config('setting.app_logo_small_light')}}" alt=""
                        height="30">
                </span>
                <span class="logo-lg">
                    <img src="{{config('setting.app_logo_full_light')}}" alt=""
                        height="25">
                </span>
            </a>
        </div>

        <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
            <i class="fa fa-fw fa-bars"></i>
        </button>

        <!-- App Search-->
        <form class="app-search d-none d-lg-block">
            <div class="position-relative">
                <input type="text" class="form-control" id="searchDropdown" placeholder="{{ __('ems.search') }}...">
                <span class="bx bx-search-alt"></span>
            </div>
        </form>

        @livewire('component.page.dropdown-roles')

        {{-- <div class="dropdown dropdown-mega d-none d-lg-block ms-2">
            <button type="button" class="btn header-item waves-effect" data-bs-toggle="dropdown" aria-haspopup="false"
                aria-expanded="false">
                <span key="t-megamenu">Mega Menu</span>
                <i class="mdi mdi-chevron-down"></i>
            </button>
            <div class="dropdown-menu dropdown-megamenu">
                <div class="row">
                    <div class="col-sm-8">

                        <div class="row">
                            <div class="col-md-4">
                                <h5 class="font-size-14" key="t-ui-components">UI Components</h5>
                                <ul class="list-unstyled megamenu-list">
                                    <li>
                                        <a href="javascript:void(0);" key="t-lightbox">Lightbox</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-range-slider">Range Slider</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-sweet-alert">Sweet Alert</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-rating">Rating</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-forms">Forms</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-tables">Tables</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-charts">Charts</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="col-md-4">
                                <h5 class="font-size-14" key="t-applications">Applications</h5>
                                <ul class="list-unstyled megamenu-list">
                                    <li>
                                        <a href="javascript:void(0);" key="t-ecommerce">Ecommerce</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-calendar">Calendar</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-email">Email</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-projects">Projects</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-tasks">Tasks</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-contacts">Contacts</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="col-md-4">
                                <h5 class="font-size-14" key="t-extra-pages">Extra Pages</h5>
                                <ul class="list-unstyled megamenu-list">
                                    <li>
                                        <a href="javascript:void(0);" key="t-light-sidebar">Light
                                            Sidebar</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-compact-sidebar">Compact
                                            Sidebar</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-horizontal">Horizontal
                                            layout</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-maintenance">Maintenance</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-coming-soon">Coming Soon</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-timeline">Timeline</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-faqs">FAQs</a>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="row">
                            <div class="col-sm-6">
                                <h5 class="font-size-14" key="t-ui-components">UI Components</h5>
                                <ul class="list-unstyled megamenu-list">
                                    <li>
                                        <a href="javascript:void(0);" key="t-lightbox">Lightbox</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-range-slider">Range Slider</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-sweet-alert">Sweet Alert</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-rating">Rating</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-forms">Forms</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-tables">Tables</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" key="t-charts">Charts</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="col-sm-5">
                                <div>
                                    <img src="{{ asset('images/megamenu-img.png') }}" alt=""
                                        class="img-fluid mx-auto d-block">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div> --}}
    </div>

    <div class="d-flex">

        <!-- <div class="dropdown d-inline-block d-lg-none ms-2">
            <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="mdi mdi-magnify"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                aria-labelledby="page-header-search-dropdown">

                <form class="p-3">
                    <div class="form-group m-0">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search ..."
                                aria-label="Recipient's username">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit"><i
                                        class="mdi mdi-magnify"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div> -->

        <div class="dropdown d-inline-block">
            <button type="button" class="btn header-item waves-effect" data-bs-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <img id="header-lang-img" src="{{ asset('images/flags/us.jpg') }}" alt="Header Language"
                    height="16">
            </button>
            <div class="dropdown-menu dropdown-menu-end">

                <!-- item-->
                <a href="javascript:void(0);" class="dropdown-item notify-item language" data-lang="id">
                    <img src="{{ asset('images/flags/indonesia.jpg') }}" alt="user-image" class="me-1" height="12">
                    <span class="align-middle">{{ __('ems.indonesia') }}</span>
                </a>
                <!-- item-->
                <a href="javascript:void(0);" class="dropdown-item notify-item language" data-lang="en">
                    <img src="{{ asset('images/flags/us.jpg') }}" alt="user-image" class="me-1" height="12">
                    <span class="align-middle">{{ __('ems.english') }}</span>
                </a>
            </div>
        </div>

        {{-- <div class="dropdown d-none d-lg-inline-block ms-1">
            <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="bx bx-customize"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <div class="px-lg-2">
                    <div class="row g-0">
                        <div class="col">
                            <a class="dropdown-icon-item" href="#">
                                <img src="{{ asset('images/brands/github.png') }}" alt="Github">
                                <span>GitHub</span>
                            </a>
                        </div>
                        <div class="col">
                            <a class="dropdown-icon-item" href="#">
                                <img src="{{ asset('images/brands/bitbucket.png') }}" alt="bitbucket">
                                <span>Bitbucket</span>
                            </a>
                        </div>
                        <div class="col">
                            <a class="dropdown-icon-item" href="#">
                                <img src="{{ asset('images/brands/dribbble.png') }}" alt="dribbble">
                                <span>Dribbble</span>
                            </a>
                        </div>
                    </div>

                    <div class="row g-0">
                        <div class="col">
                            <a class="dropdown-icon-item" href="#">
                                <img src="{{ asset('images/brands/dropbox.png') }}" alt="dropbox">
                                <span>Dropbox</span>
                            </a>
                        </div>
                        <div class="col">
                            <a class="dropdown-icon-item" href="#">
                                <img src="{{ asset('images/brands/mail_chimp.png') }}" alt="mail_chimp">
                                <span>Mail Chimp</span>
                            </a>
                        </div>
                        <div class="col">
                            <a class="dropdown-icon-item" href="#">
                                <img src="{{ asset('images/brands/slack.png') }}" alt="slack">
                                <span>Slack</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="dropdown d-lg-inline-block ms-1">
            @livewire('component.change-theme')
        </div>

        <div class="dropdown d-none d-lg-inline-block ms-1">
            <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                <i class="bx bx-fullscreen"></i>
            </button>
        </div>

        @livewire('component.notification-header')

        <div class="dropdown d-inline-block">
            <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img class="rounded-circle header-profile-user" src="{{ $avatar ?? asset('images/users/avatar-1.jpg') }}"
                    alt="Header Avatar">
                <span class="d-none d-xl-inline-block ms-1" key="t-henry">{{ $name }}</span>
                <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <!-- item-->
                @can('view:profile')
                    <a class="dropdown-item" href="{{ route('profile.index') }}"><i
                            class="bx bx-user font-size-16 align-middle me-1"></i>
                        <span key="t-profile">{{ __('ems.profile') }}</span></a>
                @endcan
                {{-- <a class="dropdown-item" href="#"><i class="bx bx-wallet font-size-16 align-middle me-1"></i>
                    <span key="t-my-wallet">My
                        Wallet</span></a> --}}
                {{-- <a class="dropdown-item d-block" href="#"><span class="badge bg-success float-end">11</span><i
                        class="bx bx-wrench font-size-16 align-middle me-1"></i> <span
                        key="t-settings">Settings</span></a> --}}
                {{-- <a class="dropdown-item" href="#"><i
                        class="bx bx-lock-open font-size-16 align-middle me-1"></i>
                    <span key="t-lock-screen">Lock screen</span></a> --}}
                <div class="dropdown-divider"></div>
                @livewire('auth.logout', ['user' => $user], key('logout-' . $user->id))
            </div>
        </div>

        {{-- <div class="dropdown d-inline-block">
            <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                <i class="bx bx-cog bx-spin"></i>
            </button>
        </div> --}}

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Language switcher functionality
    const languageLinks = document.querySelectorAll('.language');
    const headerLangImg = document.getElementById('header-lang-img');
    
    // Set current language flag based on current locale
    const currentLang = '{{ app()->getLocale() }}';
    const flagMap = {
        'id': '{{ asset("images/flags/indonesia.jpg") }}',
        'en': '{{ asset("images/flags/us.jpg") }}'
    };
    
    if (flagMap[currentLang]) {
        headerLangImg.src = flagMap[currentLang];
    }
    
    // Handle language change
    languageLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const selectedLang = this.getAttribute('data-lang');
            
            // Show loading state
            const originalText = this.querySelector('.align-middle').textContent;
            this.querySelector('.align-middle').textContent = '{{ __("ems.loading") }}';
            
            // Make AJAX request to change language
            fetch('{{ route("language.switch") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    locale: selectedLang
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update flag
                    if (flagMap[selectedLang]) {
                        headerLangImg.src = flagMap[selectedLang];
                    }
                    
                    // Reload page to apply new language
                    window.location.reload();
                } else {
                    // Show error message
                    alert('{{ __("ems.error_changing_language") }}: ' + (data.message || 'Unknown error'));
                    this.querySelector('.align-middle').textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __("ems.error_changing_language") }}. Please try again.');
                this.querySelector('.align-middle').textContent = originalText;
            });
        });
    });
});
</script>
