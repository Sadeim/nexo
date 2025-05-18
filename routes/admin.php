<?php

use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\AchievementController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\InstagramController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\ReasonController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\StaticPageController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WorkController;
use App\Http\Controllers\Admin\ApproachController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HowWorkController;

    /* ------------------------------------- Auth Routes --------------------------------- */
    Route::get('admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('admin/login', [LoginController::class, 'login'])->name('admin.login.post');
    Route::post('admin/logout', [LoginController::class, 'logout'])->name('admin.logout');
    /* ------------------------------------- Admin Dashboard --------------------------------- */
    Route::group(['middleware' => ['auth:admin', 'admin'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');

        /* ------------------------------------- Category Routes --------------------------------- */
        Route::resource('categories', CategoryController::class);
        Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
            Route::get('data/datatables', [CategoryController::class , 'datatable'])->name('datatable');
            Route::post('activate/{id}', [CategoryController::class, 'activate'])->name('active');
        });
        /* ------------------------------------- Category Routes --------------------------------- */

        /* ------------------------------------- Admin Routes --------------------------------- */
        Route::resource('admins', AdminController::class);
        Route::group(['prefix' => 'admins', 'as' => 'admins.'], function () {
            Route::get('data/datatables', [AdminController::class, 'datatable'])->name('datatable');
            Route::post('activate/{id}', [AdminController::class, 'activate'])->name('active');
            Route::post('bluck/delete', [AdminController::class , 'bluckDestroy'])->name('bluck_delete');
        });
        /* ------------------------------------- Admin Routes --------------------------------- */

        /* ------------------------------------- User Routes --------------------------------- */
        Route::resource('users', UserController::class);
        Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
            Route::get('data/datatables', [UserController::class, 'datatable'])->name('datatable');
            Route::post('activate/{id}', [UserController::class, 'activate'])->name('active');
            Route::post('bluck/delete', [UserController::class , 'bluckDestroy'])->name('bluck_delete');
        });
        /* ------------------------------------- User Routes --------------------------------- */

        /* ------------------------------------- Role Routes --------------------------------- */
        Route::resource('roles', RoleController::class);
        Route::group(['prefix' => 'roles', 'as' => 'roles.'], function () {
            Route::get('data/datatables', [RoleController::class, 'datatable'])->name('datatable');
        });
        /* ------------------------------------- Role Routes --------------------------------- */

        /* ------------------------------------- Settings Routes --------------------------------- */
        Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
            Route::get('/', [SettingController::class , 'index'])->name('index');
            Route::post('update', [SettingController::class , 'update'])->name('update');
        });
        /* ------------------------------------- Settings Routes --------------------------------- */

        /* ------------------------------------- StaticPages Routes --------------------------------- */
        Route::resource('static_pages', StaticPageController::class);
        Route::group(['prefix' => 'static_pages', 'as' => 'static_pages.'], function () {
            Route::get('data/datatables', [StaticPageController::class, 'datatable'])->name('datatable');
            Route::post('activate/{id}', [StaticPageController::class, 'activate'])->name('active');
        });
        /* ------------------------------------- StaticPages Routes --------------------------------- */

        /* ------------------------------------- Newsletter Routes --------------------------------- */
        Route::resource('newsletters', NewsletterController::class);
        Route::group(['prefix' => 'newsletters', 'as' => 'newsletters.'], function () {
            Route::get('data/datatables', [NewsletterController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- Newsletter Routes --------------------------------- */

        /* ------------------------------------- Service Routes --------------------------------- */
        Route::resource('services', ServiceController::class);
        Route::group(['prefix' => 'services', 'as' => 'services.'], function () {
            Route::get('data/datatables', [ServiceController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- Service Routes --------------------------------- */

        /* ------------------------------------- Testimonial Routes --------------------------------- */
        Route::resource('testimonials', TestimonialController::class);
        Route::group(['prefix' => 'testimonials', 'as' => 'testimonials.'], function () {
            Route::get('data/datatables', [TestimonialController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- Testimonial Routes --------------------------------- */

        /* ------------------------------------- achievement Routes --------------------------------- */
        Route::resource('achievements', AchievementController::class);
        Route::group(['prefix' => 'achievements', 'as' => 'achievements.'], function () {
            Route::get('data/datatables', [AchievementController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- achievement Routes --------------------------------- */

        /* ------------------------------------- banner Routes --------------------------------- */
        Route::resource('banners', BannerController::class);
        Route::group(['prefix' => 'banners', 'as' => 'banners.'], function () {
            Route::get('data/datatables', [BannerController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- banner Routes --------------------------------- */

        /* ------------------------------------- blog Routes --------------------------------- */
        Route::resource('blogs', BlogController::class);
        Route::group(['prefix' => 'blogs', 'as' => 'blogs.'], function () {
            Route::get('data/datatables', [BlogController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- blog Routes --------------------------------- */

        /* ------------------------------------- client Routes --------------------------------- */
        Route::resource('clients', ClientController::class);
        Route::group(['prefix' => 'clients', 'as' => 'clients.'], function () {
            Route::get('data/datatables', [ClientController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- client Routes --------------------------------- */

        /* ------------------------------------- feature Routes --------------------------------- */
        Route::resource('features', FeatureController::class);
        Route::group(['prefix' => 'features', 'as' => 'features.'], function () {
            Route::get('data/datatables', [FeatureController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- feature Routes --------------------------------- */

        /* ------------------------------------- faq Routes --------------------------------- */
        Route::resource('faqs', FaqController::class);
        Route::group(['prefix' => 'faqs', 'as' => 'faqs.'], function () {
            Route::get('data/datatables', [FaqController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- faq Routes --------------------------------- */

        /* ------------------------------------- reason Routes --------------------------------- */
        Route::resource('reasons', ReasonController::class);
        Route::group(['prefix' => 'reasons', 'as' => 'reasons.'], function () {
            Route::get('data/datatables', [ReasonController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- reason Routes --------------------------------- */

        /* ------------------------------------- team Routes --------------------------------- */
        Route::resource('teams', TeamController::class);
        Route::group(['prefix' => 'teams', 'as' => 'teams.'], function () {
            Route::get('data/datatables', [TeamController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- team Routes --------------------------------- */

        /* ------------------------------------- skill Routes --------------------------------- */
        Route::resource('skills', SkillController::class);
        Route::group(['prefix' => 'skills', 'as' => 'skills.'], function () {
            Route::get('data/datatables', [SkillController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- skill Routes --------------------------------- */

        /* ------------------------------------- work Routes --------------------------------- */
        Route::resource('works', WorkController::class);
        Route::group(['prefix' => 'works', 'as' => 'works.'], function () {
            Route::get('data/datatables', [WorkController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- work Routes --------------------------------- */
        /* -------------------------------------How work Routes --------------------------------- */
        Route::resource('how', HowWorkController::class);
        Route::group(['prefix' => 'how', 'as' => 'how.'], function () {
            Route::get('data/datatables', [HowWorkController::class , 'datatable'])->name('datatable');
        });
        /* -------------------------------------How work Routes --------------------------------- */


        /* ------------------------------------- about Routes --------------------------------- */
        Route::resource('abouts', AboutController::class);
        Route::group(['prefix' => 'abouts', 'as' => 'abouts.'], function () {
            Route::get('data/datatables', [AboutController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- about Routes --------------------------------- */

        /* ------------------------------------- booking Routes --------------------------------- */
        Route::resource('bookings', BookingController::class);
        Route::group(['prefix' => 'bookings', 'as' => 'bookings.'], function () {
            Route::get('data/datatables', [BookingController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- booking Routes --------------------------------- */

        /* ------------------------------------- event Routes --------------------------------- */
        Route::resource('events', EventController::class);
        Route::group(['prefix' => 'events', 'as' => 'events.'], function () {
            Route::get('data/datatables', [EventController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- event Routes --------------------------------- */

        /* ------------------------------------- instagram Routes --------------------------------- */
        Route::resource('instagrams', InstagramController::class);
        Route::group(['prefix' => 'instagrams', 'as' => 'instagrams.'], function () {
            Route::get('data/datatables', [InstagramController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- instagram Routes --------------------------------- */

        /* ------------------------------------- menutem Routes --------------------------------- */
        Route::resource('menu_items', MenuItemController::class);
        Route::group(['prefix' => 'menu_items', 'as' => 'menu_items.'], function () {
            Route::get('data/datatables', [MenuItemController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- menutem Routes --------------------------------- */

        /* ------------------------------------- slider Routes --------------------------------- */
        Route::resource('approaches', ApproachController::class);
        Route::group(['prefix' => 'approaches', 'as' => 'approaches.'], function () {
            Route::get('data/datatables', [ApproachController::class , 'datatable'])->name('datatable');
        });
        /*
        /* ------------------------------------- slider Routes --------------------------------- */
        Route::resource('sliders', SliderController::class);
        Route::group(['prefix' => 'sliders', 'as' => 'sliders.'], function () {
            Route::get('data/datatables', [SliderController::class , 'datatable'])->name('datatable');
        });
        /* ------------------------------------- slider Routes --------------------------------- */
        
        
        Route::post('sections/{id}/toggle', [SectionController::class, 'toggle'])->name('sections.toggle');
        Route::post('sections/{section}/update', [SectionController::class, 'update'])->name('sections.update');

});
