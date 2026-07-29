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
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HowWorkController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\EmployeeReportController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\PosOrderController;
use App\Http\Controllers\Admin\PosServiceController;
use App\Http\Controllers\Admin\PosSettingController;
use App\Http\Controllers\Admin\PosTransactionController;
use App\Http\Controllers\Admin\TransactionsController;


/* ------------------------------------- Auth Routes --------------------------------- */

Route::get('admin/login', [LoginController::class, 'showLoginForm'])->middleware('guest:admin')->name('admin.login');
Route::post('admin/login', [LoginController::class, 'login'])->middleware('guest:admin')->name('admin.login.post');
Route::post('admin/logout', [LoginController::class, 'logout'])->name('admin.logout');
/* ------------------------------------- Admin Dashboard --------------------------------- */
Route::group(['middleware' => ['auth:admin', 'admin', 'admin.role'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    /* ------------------------------------- User Accounts (admins + roles) --------------------------------- */
    Route::group(['prefix' => 'accounts', 'as' => 'accounts.'], function () {
        Route::get('data/datatables', [AccountController::class, 'datatable'])->name('datatable');
        Route::post('activate/{id}', [AccountController::class, 'activate'])->name('active');
    });
    Route::resource('accounts', AccountController::class)->except(['show']);

    /* ------------------------------------- POS Transactions (Web POS, read-only) --------------------------------- */
    Route::group(['prefix' => 'pos-transactions', 'as' => 'pos_transactions.'], function () {
        Route::get('/', [PosTransactionController::class, 'index'])->name('index');
        Route::get('data/datatables', [PosTransactionController::class, 'datatable'])->name('datatable');
        Route::get('{id}', [PosTransactionController::class, 'show'])->name('show');
    });

    /* ------------------------------------- POS Orders (Flutter mobile app, read-only) --------------------------------- */
    Route::group(['prefix' => 'pos-orders', 'as' => 'pos_orders.'], function () {
        Route::get('/', [PosOrderController::class, 'index'])->name('index');
        Route::get('data/datatables', [PosOrderController::class, 'datatable'])->name('datatable');
        Route::get('{id}', [PosOrderController::class, 'show'])->name('show');
    });

    /* ------------------------------------- Transactions (full POS ledger) --------------------------------- */
    Route::get('transactions', [TransactionsController::class, 'index'])->name('transactions.index');

    /* ------------------------------------- Employees (POS "who served") --------------------------------- */
    Route::group(['prefix' => 'employees', 'as' => 'employees.'], function () {
        Route::get('data/datatables', [EmployeeController::class, 'datatable'])->name('datatable');
        Route::post('activate/{id}', [EmployeeController::class, 'activate'])->name('active');
    });
    Route::resource('employees', EmployeeController::class)->except(['show']);

    /* ------------------------------------- POS Services (separate catalog for the tablet) --------------------------------- */
    Route::group(['prefix' => 'pos-services', 'as' => 'pos_services.'], function () {
        Route::get('data/datatables', [PosServiceController::class, 'datatable'])->name('datatable');
        Route::post('activate/{id}',  [PosServiceController::class, 'activate'])->name('active');
        Route::post('import',         [PosServiceController::class, 'importFromWebsite'])->name('import');
    });
    Route::resource('pos-services', PosServiceController::class)
        ->parameters(['pos-services' => 'pos_service'])
        ->names('pos_services')
        ->except(['show']);

    /* ------------------------------------- POS Settings (card fee, ...) --------------------------------- */
    Route::group(['prefix' => 'pos-settings', 'as' => 'pos_settings.'], function () {
        Route::get('/',  [PosSettingController::class, 'index'])->name('index');
        Route::post('/', [PosSettingController::class, 'update'])->name('update');
    });

    /* ------------------------------------- Reports --------------------------------- */
    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
        Route::get('/',         [ReportsController::class, 'index'])->name('index');
        Route::get('sales',     [ReportsController::class, 'sales'])->name('sales');
        Route::get('services',  [ReportsController::class, 'services'])->name('services');
        Route::get('cashiers',  [ReportsController::class, 'cashiers'])->name('cashiers');

        Route::group(['prefix' => 'employees', 'as' => 'employees.'], function () {
            Route::get('/',                  [EmployeeReportController::class, 'index'])->name('index');
            Route::post('pay',               [EmployeeReportController::class, 'pay'])->name('pay');
            Route::delete('payments/{id}',   [EmployeeReportController::class, 'destroyPayment'])->name('pay.destroy');
            Route::get('{id}/history',       [EmployeeReportController::class, 'history'])->name('history');
        });
    });

    /* ------------------------------------- Product Routes --------------------------------- */
    Route::resource('products', ProductController::class);
    Route::group(['prefix' => 'products', 'as' => 'products.'], function () {
        Route::get('data/datatables', [ProductController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- Product Routes --------------------------------- */

    /* ------------------------------------- Attribute Routes --------------------------------- */
    Route::resource('attributes', AttributeController::class);
    Route::group(['prefix' => 'attributes', 'as' => 'attributes.'], function () {
        Route::get('data/datatables', [AttributeController::class, 'datatable'])->name('datatable');
        Route::post('activate/{id}', [AttributeController::class, 'activate'])->name('active');
        Route::get('{id}/values', [AttributeController::class, 'getAttributeValues'])->name('values');
    });
    /* ------------------------------------- Attribute Routes --------------------------------- */

    /* ------------------------------------- AttributeValue Routes --------------------------------- */
    Route::resource('attribute_values', AttributeValueController::class);
    Route::group(['prefix' => 'attribute_values', 'as' => 'attribute_values.'], function () {
        Route::get('data/datatables', [AttributeValueController::class, 'datatable'])->name('datatable');
        Route::post('activate/{id}', [AttributeValueController::class, 'activate'])->name('active');
    });
    /* ------------------------------------- AttributeValue Routes --------------------------------- */

    /* ------------------------------------- Category Routes --------------------------------- */
    Route::resource('categories', CategoryController::class);
    Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
        Route::get('data/datatables', [CategoryController::class, 'datatable'])->name('datatable');
        Route::post('activate/{id}', [CategoryController::class, 'activate'])->name('active');
    });
    /* ------------------------------------- Category Routes --------------------------------- */

    /* ------------------------------------- Admin Routes --------------------------------- */
    Route::resource('admins', AdminController::class);
    Route::group(['prefix' => 'admins', 'as' => 'admins.'], function () {
        Route::get('data/datatables', [AdminController::class, 'datatable'])->name('datatable');
        Route::post('activate/{id}', [AdminController::class, 'activate'])->name('active');
        Route::post('bluck/delete', [AdminController::class, 'bluckDestroy'])->name('bluck_delete');
    });
    /* ------------------------------------- Admin Routes --------------------------------- */

    /* ------------------------------------- User Routes --------------------------------- */
    Route::resource('users', UserController::class);
    Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
        Route::get('data/datatables', [UserController::class, 'datatable'])->name('datatable');
        Route::post('activate/{id}', [UserController::class, 'activate'])->name('active');
        Route::post('bluck/delete', [UserController::class, 'bluckDestroy'])->name('bluck_delete');
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
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('update', [SettingController::class, 'update'])->name('update');
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
        Route::get('data/datatables', [NewsletterController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- Newsletter Routes --------------------------------- */

    /* ------------------------------------- Service Routes --------------------------------- */
    Route::resource('services', ServiceController::class);
    Route::group(['prefix' => 'services', 'as' => 'services.'], function () {
        Route::get('data/datatables', [ServiceController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- Service Routes --------------------------------- */

    /* ------------------------------------- Testimonial Routes --------------------------------- */
    Route::resource('testimonials', TestimonialController::class);
    Route::group(['prefix' => 'testimonials', 'as' => 'testimonials.'], function () {
        Route::get('data/datatables', [TestimonialController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- Testimonial Routes --------------------------------- */

    /* ------------------------------------- achievement Routes --------------------------------- */
    Route::resource('achievements', AchievementController::class);
    Route::group(['prefix' => 'achievements', 'as' => 'achievements.'], function () {
        Route::get('data/datatables', [AchievementController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- achievement Routes --------------------------------- */

    /* ------------------------------------- banner Routes --------------------------------- */
    Route::resource('banners', BannerController::class);
    Route::group(['prefix' => 'banners', 'as' => 'banners.'], function () {
        Route::get('data/datatables', [BannerController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- banner Routes --------------------------------- */

    /* ------------------------------------- blog Routes --------------------------------- */
    Route::resource('blogs', BlogController::class);
    Route::group(['prefix' => 'blogs', 'as' => 'blogs.'], function () {
        Route::get('data/datatables', [BlogController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- blog Routes --------------------------------- */

    /* ------------------------------------- client Routes --------------------------------- */
    Route::resource('clients', ClientController::class);
    Route::group(['prefix' => 'clients', 'as' => 'clients.'], function () {
        Route::get('data/datatables', [ClientController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- client Routes --------------------------------- */

    /* ------------------------------------- feature Routes --------------------------------- */
    Route::resource('features', FeatureController::class);
    Route::group(['prefix' => 'features', 'as' => 'features.'], function () {
        Route::get('data/datatables', [FeatureController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- feature Routes --------------------------------- */

    /* ------------------------------------- faq Routes --------------------------------- */
    Route::resource('faqs', FaqController::class);
    Route::group(['prefix' => 'faqs', 'as' => 'faqs.'], function () {
        Route::get('data/datatables', [FaqController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- faq Routes --------------------------------- */

    /* ------------------------------------- reason Routes --------------------------------- */
    Route::resource('reasons', ReasonController::class);
    Route::group(['prefix' => 'reasons', 'as' => 'reasons.'], function () {
        Route::get('data/datatables', [ReasonController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- reason Routes --------------------------------- */

    /* ------------------------------------- team Routes --------------------------------- */
    Route::resource('teams', TeamController::class);
    Route::group(['prefix' => 'teams', 'as' => 'teams.'], function () {
        Route::get('data/datatables', [TeamController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- team Routes --------------------------------- */

    /* ------------------------------------- contact Routes --------------------------------- */
    Route::resource('contacts', ContactController::class);
    Route::group(['prefix' => 'contacts', 'as' => 'contacts.'], function () {
        Route::get('data/datatables', [ContactController::class, 'datatable'])->name('datatable');
        Route::post('reply/{contact}/reply', [ContactController::class, 'reply'])->name('reply');
    });
    /* ------------------------------------- contact Routes --------------------------------- */

    /* ------------------------------------- skill Routes --------------------------------- */
    Route::resource('skills', SkillController::class);
    Route::group(['prefix' => 'skills', 'as' => 'skills.'], function () {
        Route::get('data/datatables', [SkillController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- skill Routes --------------------------------- */

    /* ------------------------------------- work Routes --------------------------------- */
    Route::resource('galleries', WorkController::class);
    Route::group(['prefix' => 'galleries', 'as' => 'galleries.'], function () {
        Route::get('data/datatables', [WorkController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- work Routes --------------------------------- */
    /* -------------------------------------How work Routes --------------------------------- */
    Route::resource('how', HowWorkController::class);
    Route::group(['prefix' => 'how', 'as' => 'how.'], function () {
        Route::get('data/datatables', [HowWorkController::class, 'datatable'])->name('datatable');
    });
    /* -------------------------------------How work Routes --------------------------------- */


    /* ------------------------------------- about Routes --------------------------------- */
    Route::resource('abouts', AboutController::class);
    Route::group(['prefix' => 'abouts', 'as' => 'abouts.'], function () {
        Route::get('data/datatables', [AboutController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- about Routes --------------------------------- */

    /* ------------------------------------- booking Routes --------------------------------- */

    Route::group(['prefix' => 'bookings', 'as' => 'bookings.'], function () {
        Route::get('data/datatables', [BookingController::class, 'datatable'])->name('datatable');
        Route::get('calendar', [BookingController::class, 'calendar'])->name('calendar');
        Route::get('calendar-events', [BookingController::class, 'calendarEvents'])->name('events');
    });
    Route::resource('bookings', BookingController::class);


    /* ------------------------------------- booking Routes --------------------------------- */

    /* ------------------------------------- event Routes --------------------------------- */
    Route::resource('events', EventController::class);
    Route::group(['prefix' => 'events', 'as' => 'events.'], function () {
        Route::get('data/datatables', [EventController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- event Routes --------------------------------- */

    /* ------------------------------------- instagram Routes --------------------------------- */
    Route::resource('instagrams', InstagramController::class);
    Route::group(['prefix' => 'instagrams', 'as' => 'instagrams.'], function () {
        Route::get('data/datatables', [InstagramController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- instagram Routes --------------------------------- */

    /* ------------------------------------- menutem Routes --------------------------------- */
    Route::resource('menu_items', MenuItemController::class);
    Route::group(['prefix' => 'menu_items', 'as' => 'menu_items.'], function () {
        Route::get('data/datatables', [MenuItemController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- menutem Routes --------------------------------- */

    /* ------------------------------------- slider Routes --------------------------------- */
    Route::resource('approaches', ApproachController::class);
    Route::group(['prefix' => 'approaches', 'as' => 'approaches.'], function () {
        Route::get('data/datatables', [ApproachController::class, 'datatable'])->name('datatable');
    });
    /*
        /* ------------------------------------- slider Routes --------------------------------- */
    Route::resource('sliders', SliderController::class);
    Route::group(['prefix' => 'sliders', 'as' => 'sliders.'], function () {
        Route::get('data/datatables', [SliderController::class, 'datatable'])->name('datatable');
    });
    /* ------------------------------------- slider Routes --------------------------------- */


    Route::post('sections/{id}/toggle', [SectionController::class, 'toggle'])->name('sections.toggle');
    Route::post('sections/{section}/update', [SectionController::class, 'update'])->name('sections.update');

    Route::get('about_page', [HomeController::class, 'aboutPage'])->name('about_page.index');
    Route::get('contact_page', [HomeController::class, 'contactPage'])->name('contact_page.index');
});
