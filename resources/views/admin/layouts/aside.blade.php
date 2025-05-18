<div class="aside" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}"
    data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle" style="top: 0;">


    <div class="aside-menu flex-column-fluid">
        <div class="hover-scroll-overlay-y " id="kt_aside_menu_wrapper" data-kt-scroll="true"
            data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="{default: '#kt_aside_toolbar, #kt_aside_footer', lg: '#kt_header, #kt_aside_toolbar, #kt_aside_footer'}"
            data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="5px">
            <div class="menu aside-toolbar menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500"
                id="#kt_aside_menu" data-kt-menu="true">
                {{-- <div class="aside-user d-flex align-items-sm-center justify-content-center py-5">
                    <div class="symbol symbol-50px">
                        <img src="{{ $settings->valueOf('company_logo') }}" alt="" style="width: auto;" />
                    </div>
                </div> --}}
                <div class="aside-user d-flex align-items-sm-center justify-content-center py-5">
                    <div class="symbol symbol-50px">
                        <img src="{{ asset('admin_assets/media/svg/misc/infography.svg') }}" alt=""
                            style="width: auto;" />
                    </div>
                    <div class="aside-user-info flex-row-fluid flex-wrap ms-5">
                        <div class="d-flex">
                            <div class="flex-grow-1 me-2">
                                <a href="#"
                                    class="text-white text-hover-primary fs-6 fw-bold">{{ auth()->user()->name }}</a>
                                <span
                                    class="text-gray-600 fw-semibold d-block fs-8 mb-1">{{ auth()->user()->email }}</span>
                                <div class="d-flex align-items-center text-success fs-9">
                                    <span class="bullet bullet-dot bg-success me-1"></span>online
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if (auth()->user()->can('view_home'))
                    <div class="menu-item">
                        <a class="menu-link {{ $is_active == 'home' ? 'active' : '' }}"
                            href="{{ route('admin.home') }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22.876" height="23.778"
                                        viewBox="0 0 22.876 23.778">
                                        <g id="Iconly_Curved_Home" data-name="Iconly/Curved/Home"
                                            transform="translate(1 1)">
                                            <g id="Home">
                                                <path id="Stroke_1" data-name="Stroke 1" d="M0,.549H3.057"
                                                    transform="translate(9.275 14.896)" fill="none" stroke="#fff"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-miterlimit="10" stroke-width="2" />
                                                <path id="Stroke_2" data-name="Stroke 2"
                                                    d="M0,12.754c0-6.132.669-5.7,4.267-9.041C5.842,2.446,8.292,0,10.408,0s4.614,2.434,6.2,3.713c3.6,3.337,4.266,2.91,4.266,9.041,0,9.024-2.133,9.024-10.438,9.024S0,21.778,0,12.754Z"
                                                    fill="none" stroke="#fff" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-miterlimit="10" stroke-width="2" />
                                            </g>
                                        </g>
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title">{{ __('admin.menu.home') }}</span>
                        </a>
                    </div>
                @endif

                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ $is_active_parent == 'home' ? 'here show' : '' }}">
                    <span class="menu-link menu-accordion">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="fa-solid fa-home"></i>
                            </span>
                        </span>
                        <span class="menu-title">Website Home Page</span>
                        <span class="menu-arrow"></span>
                    </span>
                    @if (auth()->user()->can('view_sliders'))
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ $is_active == 'sliders' ? 'active' : '' }}"
                                    href="{{ route('admin.sliders.index') }}">
                                    <span class="menu-bullet">
                                        <i class="fa-solid fa-sliders"></i>
                                    </span>
                                    <span class="menu-title">{{ __('admin.global.sliders') }}</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    @if (auth()->user()->can('view_categories'))
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ $is_active == 'categories' ? 'active' : '' }}"
                                    href="{{ route('admin.categories.index') }}">
                                    <span class="menu-bullet">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </span>
                                    <span class="menu-title">{{ __('admin.global.categories') }}</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    @if (auth()->user()->can('view_services'))
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ $is_active == 'services' ? 'active' : '' }}"
                                    href="{{ route('admin.services.index') }}">
                                    <span class="menu-bullet">
                                        <i class="fa-solid fa-screwdriver-wrench"></i>
                                    </span>
                                    <span class="menu-title">{{ __('admin.global.services') }}</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    @if (auth()->user()->can('view_testimonials'))
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ $is_active == 'testimonials' ? 'active' : '' }}"
                                    href="{{ route('admin.testimonials.index') }}">
                                    <span class="menu-bullet">
                                        <i class="fa-solid fa-comments"></i>
                                    </span>
                                    <span class="menu-title">{{ __('admin.global.testimonials') }}</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    @if (auth()->user()->can('view_works'))
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ $is_active == 'works' ? 'active' : '' }}"
                                    href="{{ route('admin.works.index') }}">
                                    <span class="menu-bullet">
                                        <i class="fa-solid fa-briefcase"></i>
                                    </span>
                                    <span class="menu-title">{{ __('admin.global.works') }}</span>
                                </a>
                            </div>
                        </div>
                    @endif
                    @if (auth()->user()->can('view_how'))
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ $is_active == 'how' ? 'active' : '' }}"
                                   href="{{ route('admin.how.index') }}">
                                    <span class="menu-bullet">
                                        <i class="fa-solid fa-briefcase"></i>
                                    </span>
                                    <span class="menu-title">{{ __('admin.global.how') }}</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    @if (auth()->user()->can('view_teams'))
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ $is_active == 'teams' ? 'active' : '' }}"
                                    href="{{ route('admin.teams.index') }}">
                                    <span class="menu-bullet">
                                        <i class="fa-solid fa-users"></i>
                                    </span>
                                    <span class="menu-title">{{ __('admin.global.teams') }}</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    @if (auth()->user()->can('view_faqs'))
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ $is_active == 'faqs' ? 'active' : '' }}"
                                    href="{{ route('admin.faqs.index') }}">
                                    <span class="menu-bullet">
                                        <i class="fa-solid fa-question-circle"></i>
                                    </span>
                                    <span class="menu-title">{{ __('admin.global.faqs') }}</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    @if (auth()->user()->can('view_abouts'))
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ $is_active == 'abouts' ? 'active' : '' }}"
                                    href="{{ route('admin.abouts.index') }}">
                                    <span class="menu-bullet">
                                        <i class="fa-solid fa-info-circle"></i>
                                    </span>
                                    <span class="menu-title">{{ __('admin.global.abouts') }}</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    @if (auth()->user()->can('view_clients'))
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ $is_active == 'clients' ? 'active' : '' }}"
                                    href="{{ route('admin.clients.index') }}">
                                    <span class="menu-bullet">
                                        <i class="fa-solid fa-handshake"></i>
                                    </span>
                                    <span class="menu-title">{{ __('admin.global.clients') }}</span>
                                </a>
                            </div>
                        </div>
                    @endif

                </div>

                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ $is_active_parent == 'about' ? 'here show' : '' }}">
                    <span class="menu-link menu-accordion">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="fa-solid fa-home"></i>
                            </span>
                        </span>
                        <span class="menu-title">About Page</span>
                        <span class="menu-arrow"></span>
                    </span>
                    @if (auth()->user()->can('view_skills'))
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ $is_active == 'skills' ? 'active' : '' }}"
                                    href="{{ route('admin.skills.index') }}">
                                    <span class="menu-bullet">
                                        <i class="fa-solid fa-skills"></i>
                                    </span>
                                    <span class="menu-title">{{ __('admin.global.skills') }}</span>
                                </a>
                            </div>
                        </div>
                    @endif
                    @if (auth()->user()->can('view_reasons'))
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ $is_active == 'reasons' ? 'active' : '' }}"
                                    href="{{ route('admin.reasons.index') }}">
                                    <span class="menu-bullet">
                                        <i class="fa-solid fa-reasons"></i>
                                    </span>
                                    <span class="menu-title">{{ __('admin.global.reasons') }}</span>
                                </a>
                            </div>
                        </div>
                    @endif
                    @if (auth()->user()->can('view_approaches'))
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ $is_active == 'approaches' ? 'active' : '' }}"
                                    href="{{ route('admin.approaches.index') }}">
                                    <span class="menu-bullet">
                                        <i class="fa-solid fa-approaches"></i>
                                    </span>
                                    <span class="menu-title">Approaches</span>
                                </a>
                            </div>
                        </div>
                    @endif
                    @if (auth()->user()->can('view_how'))
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ $is_active == 'how' ? 'active' : '' }}"
                                    href="{{ route('admin.how.index') }}">
                                    <span class="menu-bullet">
                                        <i class="fa-solid fa-how"></i>
                                    </span>
                                    <span class="menu-title">How</span>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
                {{-- @if (auth()->user()->can('view_achievements'))
                    <div
                        class="menu-item menu-accordion {{ $is_active_parent == 'achievements' ? 'here show' : '' }}">
                        <span class="menu-link {{ $is_active == 'achievements' ? 'active' : '' }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fa-solid fa-trophy"></i>
                                </span>
                            </span>
                            <span class="menu-title">
                                <a class="{{ $is_active == 'achievements' ? 'active' : '' }}"
                                    href="{{ route('admin.achievements.index') }}">
                                    <span class="menu-title">{{ __('admin.global.achievements') }}</span>
                                </a>
                            </span>
                        </span>
                    </div>
                @endif --}}
                {{-- @if (auth()->user()->can('view_banners'))
                    <div class="menu-item menu-accordion {{ $is_active_parent == 'banners' ? 'here show' : '' }}">
                        <span class="menu-link {{ $is_active == 'banners' ? 'active' : '' }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fa-solid fa-image"></i>
                                </span>
                            </span>
                            <span class="menu-title">
                                <a class="{{ $is_active == 'banners' ? 'active' : '' }}"
                                    href="{{ route('admin.banners.index') }}">
                                    <span class="menu-title">{{ __('admin.global.banners') }}</span>
                                </a>
                            </span>
                        </span>
                    </div>
                @endif --}}

                @if (auth()->user()->can('view_blogs'))
                    <div class="menu-item menu-accordion {{ $is_active_parent == 'blogs' ? 'here show' : '' }}">
                        <span class="menu-link {{ $is_active == 'blogs' ? 'active' : '' }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fa-solid fa-blog"></i>
                                </span>
                            </span>
                            <span class="menu-title">
                                <a class="{{ $is_active == 'blogs' ? 'active' : '' }}"
                                    href="{{ route('admin.blogs.index') }}">
                                    <span class="menu-title">{{ __('admin.global.blogs') }}</span>
                                </a>
                            </span>
                        </span>
                    </div>
                @endif
                {{-- @if (auth()->user()->can('view_bookings'))
                    <div class="menu-item menu-accordion {{ $is_active_parent == 'bookings' ? 'here show' : '' }}">
                        <span class="menu-link {{ $is_active == 'bookings' ? 'active' : '' }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fa-solid fa-calendar-check"></i>
                                </span>
                            </span>
                            <span class="menu-title">
                                <a class="{{ $is_active == 'bookings' ? 'active' : '' }}"
                                    href="{{ route('admin.bookings.index') }}">
                                    <span class="menu-title">{{ __('admin.global.bookings') }}</span>
                                </a>
                            </span>
                        </span>
                    </div>
                @endif --}}

                {{-- @if (auth()->user()->can('view_contacts'))
                    <div class="menu-item menu-accordion {{ $is_active_parent == 'contacts' ? 'here show' : '' }}">
                        <span class="menu-link {{ $is_active == 'contacts' ? 'active' : '' }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fa-solid fa-address-book"></i>
                                </span>
                            </span>
                            <span class="menu-title">
                                <a class="{{ $is_active == 'contacts' ? 'active' : '' }}"
                                    href="{{ route('admin.contacts.index') }}">
                                    <span class="menu-title">{{ __('admin.global.contacts') }}</span>
                                </a>
                            </span>
                        </span>
                    </div>
                @endif --}}

                {{-- @if (auth()->user()->can('view_events'))
                    <div class="menu-item menu-accordion {{ $is_active_parent == 'events' ? 'here show' : '' }}">
                        <span class="menu-link {{ $is_active == 'events' ? 'active' : '' }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </span>
                            </span>
                            <span class="menu-title">
                                <a class="{{ $is_active == 'events' ? 'active' : '' }}"
                                    href="{{ route('admin.events.index') }}">
                                    <span class="menu-title">{{ __('admin.global.events') }}</span>
                                </a>
                            </span>
                        </span>
                    </div>
                @endif
                @if (auth()->user()->can('view_features'))
                    <div class="menu-item menu-accordion {{ $is_active_parent == 'features' ? 'here show' : '' }}">
                        <span class="menu-link {{ $is_active == 'features' ? 'active' : '' }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </span>
                            </span>
                            <span class="menu-title">
                                <a class="{{ $is_active == 'features' ? 'active' : '' }}"
                                    href="{{ route('admin.features.index') }}">
                                    <span class="menu-title">{{ __('admin.global.features') }}</span>
                                </a>
                            </span>
                        </span>
                    </div>
                @endif
                @if (auth()->user()->can('view_instagrams'))
                    <div class="menu-item menu-accordion {{ $is_active_parent == 'instagrams' ? 'here show' : '' }}">
                        <span class="menu-link {{ $is_active == 'instagrams' ? 'active' : '' }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fa-brands fa-instagram"></i>
                                </span>
                            </span>
                            <span class="menu-title">
                                <a class="{{ $is_active == 'instagrams' ? 'active' : '' }}"
                                    href="{{ route('admin.instagrams.index') }}">
                                    <span class="menu-title">{{ __('admin.global.instagrams') }}</span>
                                </a>
                            </span>
                        </span>
                    </div>
                @endif --}}

                {{-- @if (auth()->user()->can('view_menu_items'))
                    <div class="menu-item menu-accordion {{ $is_active_parent == 'menu_items' ? 'here show' : '' }}">
                        <span class="menu-link {{ $is_active == 'menu_items' ? 'active' : '' }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fa-solid fa-bars"></i>
                                </span>
                            </span>
                            <span class="menu-title">
                                <a class="{{ $is_active == 'menu_items' ? 'active' : '' }}"
                                    href="{{ route('admin.menu_items.index') }}">
                                    <span class="menu-title">{{ __('admin.global.menu_items') }}</span>
                                </a>
                            </span>
                        </span>
                    </div>
                @endif --}}
                @if (auth()->user()->can('view_newsletters'))
                    <div class="menu-item menu-accordion {{ $is_active_parent == 'newsletters' ? 'here show' : '' }}">
                        <span class="menu-link {{ $is_active == 'newsletters' ? 'active' : '' }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fa-solid fa-envelope-open-text"></i>
                                </span>
                            </span>
                            <span class="menu-title">
                                <a class="{{ $is_active == 'newsletters' ? 'active' : '' }}"
                                    href="{{ route('admin.newsletters.index') }}">
                                    <span class="menu-title">{{ __('admin.global.newsletters') }}</span>
                                </a>
                            </span>
                        </span>
                    </div>
                @endif

                {{-- @if (auth()->user()->can('view_reasons'))
                    <div class="menu-item menu-accordion {{ $is_active_parent == 'reasons' ? 'here show' : '' }}">
                        <span class="menu-link {{ $is_active == 'reasons' ? 'active' : '' }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fa-solid fa-bullseye"></i>
                                </span>
                            </span>
                            <span class="menu-title">
                                <a class="{{ $is_active == 'reasons' ? 'active' : '' }}"
                                    href="{{ route('admin.reasons.index') }}">
                                    <span class="menu-title">{{ __('admin.global.reasons') }}</span>
                                </a>
                            </span>
                        </span>
                    </div>
                @endif --}}

                {{-- @if (auth()->user()->can('view_skills'))
                    <div class="menu-item menu-accordion {{ $is_active_parent == 'skills' ? 'here show' : '' }}">
                        <span class="menu-link {{ $is_active == 'skills' ? 'active' : '' }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fa-solid fa-brain"></i>
                                </span>
                            </span>
                            <span class="menu-title">
                                <a class="{{ $is_active == 'skills' ? 'active' : '' }}"
                                    href="{{ route('admin.skills.index') }}">
                                    <span class="menu-title">{{ __('admin.global.skills') }}</span>
                                </a>
                            </span>
                        </span>
                    </div>
                @endif --}}

                @if (auth()->user()->can('view_static_pages'))
                    <div
                        class="menu-item menu-accordion {{ $is_active_parent == 'static_pages' ? 'here show' : '' }}">
                        <span class="menu-link {{ $is_active == 'static_pages' ? 'active' : '' }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fa-solid fa-file-alt"></i>
                                </span>
                            </span>
                            <span class="menu-title">
                                <a class="{{ $is_active == 'static_pages' ? 'active' : '' }}"
                                    href="{{ route('admin.static_pages.index') }}">
                                    <span class="menu-title">{{ __('admin.menu.static_pages') }}</span>
                                </a>
                            </span>
                        </span>
                    </div>
                @endif
                {{-- <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ $is_active_parent == 'user_management' ? 'here show' : '' }}">
                    <span class="menu-link menu-accordion">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="fa-solid fa-user"></i>
                            </span>
                        </span>
                        <span class="menu-title">{{ __('admin.global.user_management') }}</span>
                        <span class="menu-arrow"></span>
                    </span>
                    @if (auth()->user()->can('view_admins'))
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ $is_active == 'admins' ? 'active' : '' }}"
                                    href="{{ route('admin.admins.index') }}">
                                    <span class="menu-bullet">
                                        <i class="fa-solid fa-user-shield"></i>
                                    </span>
                                    <span class="menu-title">{{ __('admin.menu.admins') }}</span>
                                </a>
                            </div>
                        </div>
                    @endif
                    @if (auth()->user()->can('view_users'))
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ $is_active == 'users' ? 'active' : '' }}"
                                    href="{{ route('admin.users.index') }}">
                                    <span class="menu-bullet">
                                        <i class="fa-solid fa-users"></i>
                                    </span>
                                    <span class="menu-title">{{ __('admin.menu.users') }}</span>
                                </a>
                            </div>
                        </div>
                    @endif
                </div> --}}
                {{-- @if (auth()->user()->can('view_roles'))
                    <div class="menu-item menu-accordion {{ $is_active_parent == 'roles' ? 'here show' : '' }}">
                        <span class="menu-link {{ $is_active == 'roles' ? 'active' : '' }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fa-solid fa-user-tag"></i>
                                </span>
                            </span>
                            <span class="menu-title">
                                <a class="{{ $is_active == 'roles' ? 'active' : '' }}"
                                    href="{{ route('admin.roles.index') }}">
                                    <span class="menu-title">{{ __('admin.menu.roles') }}</span>
                                </a>
                            </span>
                        </span>
                    </div>
                @endif --}}

                @if (auth()->user()->can('view_settings'))
                    <div class="menu-item menu-accordion {{ $is_active_parent == 'settings' ? 'here show' : '' }}">
                        <span class="menu-link {{ $is_active == 'settings' ? 'active' : '' }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fa-solid fa-cogs"></i>
                                </span>
                            </span>
                            <span class="menu-title">
                                <a class="{{ $is_active == 'settings' ? 'active' : '' }}"
                                    href="{{ route('admin.settings.index') }}">
                                    <span class="menu-title">{{ __('admin.form.settings') }}</span>
                                </a>
                            </span>
                        </span>
                    </div>
                @endif
                {{-- <div class="menu-item menu-accordion {{ $is_active_parent == 'logout' ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="fa-solid fa-font-awesome"></i>
                            </span>
                        </span>
                        <span class="menu-title">
                            <a class="{{ $is_active == 'logout' ? 'active' : '' }}"
                                href="aaaaa.logout.index')}}">
                                <span class="menu-title">{{ __('admin.menu.logout') }}</span>
                            </a>
                        </span>
                    </span>
                </div> --}}
            </div>
        </div>
    </div>
</div>
