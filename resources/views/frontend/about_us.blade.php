@extends('frontend.layout')

@section('content')
    <!-- Page Header Start -->
    <div class="page-header parallaxie" style="background-image: url('{{ $sections['about_page']?->image ? asset($sections['about_page']->image) : asset($about->image1) }}');">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Page Header Box Start -->
                    <div class="page-header-box">
                        <h1 class="text-anime-style-3" data-cursor="-opaque">About us</h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">about us</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- Page Header Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    @if ($sections['approaches_section']?->is_active)
        <!-- Our Approch Section Start -->
        <div class="our-approch bg-radius-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="our-approch-image">
                            <div class="our-approch-img-1">
                                <figure class="image-anime">
                                    <img src="{{ asset($approach->image_1) }}" alt="">
                                </figure>
                            </div>
                            <div class="our-approch-img-2">
                                <figure class="image-anime">
                                    <img src="{{ asset($approach->image_2) }}" alt="">
                                </figure>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="our-approch-content">
                            <div class="section-title">
                                <h3 class="wow fadeInUp">{{ $approach->title }}</h3>
                                <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $approach->subtitle }}</h2>
                            </div>

                            <div class="our-approch-tab">
                                <div class="our-approch-tab-nav wow fadeInUp" data-wow-delay="0.2s">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link btn-default btn-highlighted active" id="mission-tab" data-bs-toggle="tab" data-bs-target="#mission" type="button" role="tab">our mission</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link btn-default btn-highlighted" id="vision-tab" data-bs-toggle="tab" data-bs-target="#vision" type="button" role="tab">our vision</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link btn-default btn-highlighted" id="value-tab" data-bs-toggle="tab" data-bs-target="#value" type="button" role="tab">our value</button>
                                        </li>
                                    </ul>
                                </div>

                                <div class="approch-box tab-content" id="myTabContent">
                                    <!-- Mission -->
                                    <div class="approch-item tab-pane fade show active" id="mission" role="tabpanel">
                                        <div class="approch-tab-content">
                                            <div class="approch-tab-content-header">
                                                <p>{{ $approach->mission_description }}</p>
                                            </div>
                                            <div class="approch-tab-content-list">
                                                <ul>
                                                    @foreach ($approach->mission_points as $point)
                                                        <li>{{ $point }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Vision -->
                                    <div class="approch-item tab-pane fade" id="vision" role="tabpanel">
                                        <div class="approch-tab-content">
                                            <div class="approch-tab-content-header">
                                                <p>{{ $approach->vision_description }}</p>
                                            </div>
                                            <div class="approch-tab-content-list">
                                                <ul>
                                                    @foreach ($approach->vision_points as $point)
                                                        <li>{{ $point }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Value -->
                                    <div class="approch-item tab-pane fade" id="value" role="tabpanel">
                                        <div class="approch-tab-content">
                                            <div class="approch-tab-content-header">
                                                <p>{{ $approach->value_description }}</p>
                                            </div>
                                            <div class="approch-tab-content-list">
                                                <ul>
                                                    @foreach ($approach->value_points as $point)
                                                        <li>{{ $point }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- /.approch-box -->
                            </div><!-- /.our-approch-tab -->
                        </div><!-- /.our-approch-content -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Our Approch Section End -->
    @endif

    @if ($sections['skills_section']?->is_active)
        <!-- Our Skill Section Start -->
        <div class="our-skill bg-radius-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="our-skill-content">
                            <!-- Section Title Start -->
                            <div class="section-title">
                                <h3 class="wow fadeInUp">our skill</h3>
                                <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $sections['skills_section']->title }}</h2>
                                <p class="wow fadeInUp" data-wow-delay="0.2s">
                                    {{ $sections['skills_section']->description }}
                                </p>
                            </div>
                            <!-- Section Title End -->

                            <!-- About SkillBar Start -->
                            <div class="our-skillbars">
                                <!-- Skills Progress Bar Start -->
                                <div class="skills-progress-bar">
                                    <!-- Skill Item Start -->
                                    <div class="skillbar" data-percent="{{$skills->percent1}}%">
                                        <div class="skill-data">
                                            <div class="skill-title">{{$skills->text1}}</div>
                                            <div class="skill-no">{{$skills->percent1}}%</div>
                                        </div>
                                        <div class="skill-progress">
                                            <div class="count-bar"></div>
                                        </div>
                                    </div>
                                    <!-- Skill Item End -->
                                </div>
                                <!-- Skills Progress Bar End -->

                                <!-- Skills Progress Bar Start -->
                                <div class="skills-progress-bar">
                                    <!-- Skill Item Start -->
                                    <div class="skillbar" data-percent="{{$skills->percent2}}%">
                                        <div class="skill-data">
                                            <div class="skill-title">{{$skills->text2}}</div>
                                            <div class="skill-no">{{$skills->percent2}}%</div>
                                        </div>
                                        <div class="skill-progress">
                                            <div class="count-bar"></div>
                                        </div>
                                    </div>
                                    <!-- Skill Item End -->
                                </div>
                                <!-- Skills Progress Bar End -->

                                <!-- Skills Progress Bar Start -->
                                <div class="skills-progress-bar">
                                    <!-- Skill Item Start -->
                                    <div class="skillbar" data-percent="{{$skills->percent3}}%">
                                        <div class="skill-data">
                                            <div class="skill-title">{{$skills->text3}}</div>
                                            <div class="skill-no">{{$skills->percent3}}%</div>
                                        </div>
                                        <div class="skill-progress">
                                            <div class="count-bar"></div>
                                        </div>
                                    </div>
                                    <!-- Skill Item End -->
                                </div>
                                <!-- Skills Progress Bar End -->
                            </div>
                            <!-- About SkillBar End -->
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <!-- Our Skill Image Start -->
                        <div class="our-skill-image">
                            <div class="our-skill-img-1">
                                <figure class="image-anime">
                                    <img src="{{ asset($skills->image) }}" alt="">
                                </figure>
                            </div>

                            <div class="our-skill-img-2">
                                <figure class="image-anime">
                                    <img src="{{ asset($skills->image2) }}" alt="">
                                </figure>
                            </div>

                            <div class="our-skill-img-3">
                                <figure class="image-anime">
                                    <img src="{{ asset($skills->image3) }}" alt="">
                                </figure>
                            </div>
                        </div>
                        <!-- Our Skill Image End -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Our Skill Section End -->
    @endif

    <!-- Quick Fact Section Start -->
{{--    <div class="quick-facts bg-radius-section">--}}
{{--        <div class="container">--}}
{{--            <div class="row section-row align-items-center">--}}
{{--                <div class="col-lg-6">--}}
{{--                    <!-- Section Title Start -->--}}
{{--                    <div class="section-title dark-section">--}}
{{--                        <h3 class="wow fadeInUp">Some facts</h3>--}}
{{--                        <h2 class="text-anime-style-3" data-cursor="-opaque">{{$how->name}}</h2>--}}
{{--                    </div>--}}
{{--                    <!-- Section Title End -->--}}
{{--                </div>--}}

{{--                <div class="col-lg-6">--}}
{{--                    <!-- Section Title Content Start -->--}}
{{--                    <div class="section-title-content dark-section wow fadeInUp" data-wow-delay="0.2s">--}}
{{--                        <p>{{$how->description}}</p>--}}
{{--                    </div>--}}
{{--                    <!-- Section Title Content End -->--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="row align-items-center">--}}
{{--                <div class="col-lg-3 col-md-6 col-6 order-lg-1 order-md-1 order-1">--}}
{{--                    <!-- Fact Counter Box Start -->--}}
{{--                    <div class="fact-counter-box">--}}
{{--                        <!-- Fact Counter Item Start -->--}}
{{--                        <div class="fact-counter-item">--}}
{{--                            <h3>{{$how->tap1_name}}</h3>--}}
{{--                            <h2><span class="counter">{{$how->tap1_number}}</span>+</h2>--}}
{{--                            <p>{{$how->tap1_content}}</p>--}}
{{--                        </div>--}}
{{--                        <!-- Fact Counter Item End -->--}}

{{--                        <!-- Fact Counter Item Start -->--}}
{{--                        <div class="fact-counter-item">--}}
{{--                            <h3>{{$how->tap2_name}}</h3>--}}
{{--                            <h2><span class="counter">{{$how->tap2_number}}</span>k</h2>--}}
{{--                            <p>{{$how->tap2_content}}</p>--}}
{{--                        </div>--}}
{{--                        <!-- Fact Counter Item End -->--}}
{{--                    </div>--}}
{{--                    <!-- Fact Counter Box End -->--}}
{{--                </div>--}}


{{--                <div class="col-lg-3 col-md-6 col-6 order-lg-3 order-md-2 order-2">--}}
{{--                    <!-- Fact Counter Box Start -->--}}
{{--                    <div class="fact-counter-box">--}}
{{--                        <!-- Fact Counter Item Start -->--}}
{{--                        <div class="fact-counter-item">--}}
{{--                            <h3>{{$how->tap3_name}}</h3>--}}
{{--                            <h2><span class="counter">{{$how->tap3_number}}</span>k+</h2>--}}
{{--                            <p>{{$how->tap3_content}}</p>--}}
{{--                        </div>--}}
{{--                        <!-- Fact Counter Item End -->--}}

{{--                        <!-- Fact Counter Item Start -->--}}
{{--                        <div class="fact-counter-item">--}}
{{--                            <h3>{{$how->tap4_name}}</h3>--}}
{{--                            <h2><span class="counter">{{$how->tap4_number}}</span>%</h2>--}}
{{--                            <p>{{$how->tap4_content}}</p>--}}
{{--                        </div>--}}
{{--                        <!-- Fact Counter Item End -->--}}

{{--                    </div>--}}

{{--                    <!-- Fact Counter Box End -->--}}
{{--                </div>--}}
{{--                <div class="col-lg-6 order-lg-2 order-md-3 order-3">--}}
{{--                    <!-- Quick Fact image Start -->--}}
{{--                    <div class="quick-fact-image">--}}
{{--                        <img src="{{ asset($how->image) }}" alt="">--}}
{{--                    </div>--}}
{{--                    <!-- Quick Fact image End -->--}}
{{--                </div>--}}

{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    <!-- Quick Fact Section End -->

    @if ($sections['reasons_section']?->is_active)
        <!-- Why Choose Us Section Start -->
        <div class="why-choose-us">
            <div class="why-choose-box bg-radius-section">
                <div class="container">
                    <div class="row section-row align-items-end">
                        <div class="col-lg-6">
                            <!-- Section Title Start -->
                            <div class="section-title">
                                <h3 class="wow fadeInUp">{{ $sections['reasons_section']->title }}</h3>
                                <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $sections['reasons_section']->description }}</h2>
                            </div>
                            <!-- Section Title End -->
                        </div>

                        <div class="col-lg-6">
                            <!-- Why Choose Image Start -->
                            <div class="why-choose-image">
                                <figure class="image-anime">
                                    <img src="{{ asset($sections['reasons_section']->image) }}" alt="">
                                </figure>
                            </div>
                            <!-- Why Choose Image End -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Why Choose Project Box Start -->
                            <div class="why-choose-project-box">
                                @foreach ($reasons as $reason)
                                    <!-- Why Choose Project Item Start -->
                                    <div class="why-choose-project-item wow fadeInUp">
                                        <div class="icon-box">
                                            <img src="{{ asset($reason->icon) }}" style="" alt="">
                                            {{-- <i class="{{$reason->icon}}"></i> --}}
                                        </div>

                                        <div class="why-choose-project-content">
                                            <h3>{{$reason->title}}</h3>
                                            <p>{{$reason->text}}</p>
                                        </div>
                                    </div>
                                    <!-- Why Choose Project Item End -->
                                @endforeach

                                <!-- Why Choose Project Item End -->
                            </div>
                            <!-- Why Choose Project Box End -->

                            <!-- Why Choose Footer Start -->
                            <div class="why-choose-footer wow fadeInUp" data-wow-delay="0.8s">
                                <p>{{ $sections['reasons_section']->note }}</p>
                            </div>
                            <!-- Why Choose Footer End -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Why Choose Us Section End -->
    @endif
@endsection
