# PROJECT_OVERVIEW — nexo-0001

> ملخص فحص كامل للمشروع (قراءة فقط، بدون أي تعديل على الكود).
> تاريخ الفحص: 2026-07-19

---

## 1. نظرة عامة (What is this?)

`nexo-0001` هو تطبيق **Laravel 11** يمثّل موقعًا تعريفيًا/خدميًا (Business/Agency website) مع **لوحة تحكم إدارية (Admin Dashboard)** غنية جدًا لإدارة المحتوى، بالإضافة إلى **نظام حجز مواعيد (Booking system)** مع فتحات زمنية ديناميكية وتذكيرات بريدية.

رغم أن اسمه "أول سلسلة SaaS"، فإن الكود الحالي **ليس SaaS بعد**:
- لا يوجد multi-tenancy (لا فصل بين عملاء/مؤسسات).
- لا يوجد نظام اشتراكات أو دفع فعّال (رغم وجود إشارات إليه — انظر قسم الديون التقنية).
- هو حاليًا موقع single-tenant + CMS + Booking. أي أنه **قالب/أساس (boilerplate)** سيُبنى فوقه منتج SaaS لاحقًا.

---

## 2. الـ Tech Stack

### Backend
- **PHP** `^8.2`
- **Laravel Framework** `^11.0`
- **Laravel Passport** `^12.3` — OAuth2 / API tokens (مُسجّل كـ provider لكن API غير مُفعّلة فعليًا)
- **Laravel Socialite** `^5.18` + `socialiteproviders/apple` — تسجيل دخول اجتماعي (Facebook / Google / Apple) — مُهيّأ في env لكن غير مربوط بروتات فعّالة
- **spatie/laravel-permission** `^6.9` — الأدوار والصلاحيات (Roles/Permissions)
- **maatwebsite/excel** `^3.1` — تصدير/استيراد Excel
- **intervention/image** `2.7` — معالجة الصور
- **php-ffmpeg/php-ffmpeg** `^1.3` — معالجة الفيديو
- **laravel/ui** `^4.5` — سكافولدنج المصادقة
- **laravel/tinker**

### Frontend
- **Bootstrap 5** (`^5.2.3`) + `@popperjs/core`
- **Sass** — التنسيقات
- **Vite** `^5.0` + `laravel-vite-plugin` — البناء (entry: `resources/sass/app.scss`, `resources/js/app.js`)
- **Axios**
- محرك القوالب: **Blade** (server-side rendered، وليس SPA)

### Database
- **MySQL** (`DB_CONNECTION=mysql`, DB name: `kaya`)
- SQLite مُعطّل في phpunit.xml (مُعلّق) — الاختبارات تعمل على نفس اتصال MySQL حاليًا.

### Infrastructure / Services (من ملفات الإعداد)
- **Sessions / Cache / Queue**: كلها على `database` driver (لا Redis فعليًا رغم إعداداته الموجودة).
- **Mail**: `MAIL_MAILER=log` حاليًا (البريد يُكتب في اللوج، لا يُرسل فعليًا).
- **Filesystem**: `public` disk (مع دعم AWS S3 من الإعدادات لكن غير مُكوّن).
- **Scheduler**: مهمة `SendBookingReminders` تعمل كل دقيقة (`->everyMinute()`).

---

## 3. نقاط الدخول (Entry Points) وكيف يشتغل

### Bootstrap
- **`public/index.php`** → **`bootstrap/app.php`** هو نقطة تهيئة Laravel 11 الجديدة.
- التوجيه (routing) مُعرّف يدويًا في `bootstrap/app.php` عبر closure يحمّل:
  - `routes/web.php` (middleware: `web`) — الواجهة الأمامية العامة
  - `routes/admin.php` (middleware: `web`) — لوحة التحكم
  - `routes/api.php` (middleware: `api`) — **فارغة تمامًا** (مجموعة `api/v1` بلا أي روت)
  - `routes/console.php` — الأوامر والجدولة

### الواجهة الأمامية (`routes/web.php`)
- `/` الرئيسية، `/service`، `/gallery`، `/contact_us`
- إرسال: تواصل (`contact.submit`)، استشارة (`consult.store`)، نشرة بريدية (`newsletter.subscribe`)
- الحجز: `POST /bookings` (`bookings.store`) + `GET /bookings/available-slots` (فتحات متاحة ديناميكيًا)
- المتحكم: `App\Http\Controllers\Frontend\HomeController`

> ملاحظة: `routes/web.php` يشير إلى `MessageController` و `RegisterController` (في use statements) لكن روتات المصادقة/التسجيل **كلها مُعلّقة (commented out)**. الموقع العام حاليًا بلا تسجيل دخول للمستخدمين.

### لوحة التحكم (`routes/admin.php`)
- دخول الأدمن: `GET/POST admin/login`, `POST admin/logout` (guard: `admin`)
- كل شيء تحت `prefix=admin` + middleware `['auth:admin','admin']`
- **Resource controllers شبه كاملة (~30 كيان)** لكل من: products, attributes, attribute_values, categories, admins, users, roles, settings, static_pages, newsletters, services, testimonials, achievements, banners, blogs, clients, features, faqs, reasons, teams, contacts, skills, galleries(works), how, abouts, bookings, events, instagrams, menu_items, approaches, sliders, sections.
- كل كيان له مسار `data/datatables` (jQuery DataTables server-side) + كثير منها `activate/{id}`.
- تقويم الحجوزات: `bookings/calendar` + `bookings/calendar-events`.

### المصادقة (Auth guards — `config/auth.php`)
- `web` / `user` → provider `users`
- `admin` → provider `admins` (نموذج منفصل `App\Models\Admin` بجدول `admins`، guard_name = admin، يستخدم Spatie HasRoles)
- `user_api` → passport (غير مُستخدم فعليًا)
- بنية **admin منفصلة عن users** — تصميم لوحة تحكم مزدوجة المصادقة.

### أوامر Console
- `php artisan project:init` — **أمر التهيئة الرئيسي** (migrate:fresh + db:seed + config:clear). ⚠️ معطوب حاليًا — انظر الديون التقنية.
- `php artisan` (SendBookingReminders) — مجدول كل دقيقة.

---

## 4. طبقة الدومين (Models / Domain)

**~39 Model** تشمل:
- **CMS/Content**: About, Achievement, Approach, Banner, Blog, Client, Event, Faq, Feature, How, Instagram, MenuItem, Reason, ReasonTab, Section, Service, Skill, Slider, StaticPage, Team, Testimonial, Work.
- **E-commerce (جزئي، غير مربوط بواجهة)**: Product, ProductImage, Category, Attribute, AttributeValue, Variant, Inventory.
- **Booking/CRM**: Booking, Consultation, Contact/UserMessages, Newsletter.
- **Auth/ACL**: Admin, User, Role, Permission (Spatie).
- **Misc**: Country, OpeningHour, Setting.

بنية موحّدة: كثير من الـ Models تحمل `public $resource = XResource::class` و trait مشترك `App\Traits\ModelTrait` (نمط داخلي متكرر عبر المشروع).

### منطق الأعمال البارز — نظام الحجز
- `App\Services\BookingSlotService` — الخدمة الوحيدة في `app/Services`:
  - مدة الموعد ثابتة **20 دقيقة** (`BOOKING_DURATION_MINUTES`).
  - ساعات العمل **مضمّنة في الكود (hard-coded)**: الأحد 10:00–16:40، باقي الأيام 9:00–19:40.
  - يحسب الفتحات المتاحة، يمنع التداخل، ويستثني الماضي لليوم الحالي.
- بعد الحجز: يُرسل بريد تأكيد للعميل + إشعار للأدمن، ويجدول `SendBookingReminderJob` قبل الموعد بـ30 دقيقة (queue: database).
- تذكير إضافي عبر `SendBookingReminders` command مجدول كل دقيقة (يوجد مساران للتذكير — تكرار محتمل، انظر الديون).

### Mail
`AdminBookingNotification`, `AdminNotification`, `BookingConfirmationMail`, `BookingReminderMail`, `ContactReplyMail`, `VerificationCodeMail`.

---

## 5. قاعدة البيانات — الحالة

- **53 migration**، آخرها من `2026-05-08` (add_reminder_sent_at_to_bookings).
- **31 Seeder** + `DatabaseSeeder` — بيانات وهمية شاملة لكل كيانات الـ CMS (بما فيها PermissionSeeder / RoleSeeder / AdminSeeder).
- جداول Passport OAuth موجودة (5 migrations) رغم أن الـ API معطّلة.
- ملاحظة تصميم: عدة تعديلات لاحقة على `bookings` (إضافة service_id، reminder_sent_at) و `works` (is_featured) — تطوّر تدريجي وليس مخطط أولي.

---

## 6. الإعدادات و الـ Env / Secrets المطلوبة

الملف `.env` موجود ومطابق تقريبًا لـ `.env.example`. المفاتيح المهمة:

| المفتاح | القيمة الحالية | ملاحظة |
|---|---|---|
| `APP_KEY` | مُعرّف (base64) | ⚠️ **نفس المفتاح موجود في .env.example** — تسريب مفتاح، يجب توليد مفتاح جديد للإنتاج |
| `DB_DATABASE` | `kaya` | MySQL، مستخدم root بلا كلمة مرور (إعداد XAMPP محلي) |
| `MAIL_MAILER` | `log` | البريد لا يُرسل فعليًا — يحتاج SMTP للإنتاج |
| `MAIL_ADMIN_EMAIL` | **فارغ** | ⚠️ إشعارات الأدمن للحجوزات ستفشل/تذهب لعنوان فارغ |
| `QUEUE_CONNECTION` | `database` | يتطلب `php artisan queue:work` لعمل التذكيرات |
| `FACEBOOK/GOOGLE/APPLE_*` | قيم placeholder (`xxxx`) | Social login غير مُهيّأ فعليًا |
| `AWS_*` | فارغة | تخزين S3 غير مُكوّن |
| `PROJECT_INITIALIZED` | `false` | يتحكم في أمر `project:init` |

**Secrets مطلوبة للإنتاج (غير موجودة حاليًا):** بيانات SMTP حقيقية، `MAIL_ADMIN_EMAIL`, مفاتيح OAuth الاجتماعية الحقيقية، `APP_KEY` جديد، وربما بيانات AWS.

---

## 7. حالة المشروع: جاهز مقابل ناقص

### ✅ جاهز / يعمل
- بنية Laravel 11 حديثة وسليمة (routing عبر bootstrap/app.php).
- لوحة تحكم إدارية كاملة تقريبًا (~30 كيان CRUD + DataTables + 114 view في admin).
- نظام صلاحيات وأدوار (Spatie) مع seeders.
- نظام الحجز end-to-end: فتحات ديناميكية + تخزين + بريد تأكيد + تذكير مجدول.
- محتوى الواجهة الأمامية (home/services/gallery/contact) مع أقسام قابلة للتحكم (Sections).
- دعم لغتين `ar` / `en` (`resources/lang`).
- بيانات seed شاملة لتشغيل تجريبي فوري.

### ⚠️ ناقص / غير مكتمل
- **API فارغة تمامًا** (`routes/api.php` بلا روتات) رغم تجهيز Passport.
- **مصادقة المستخدم العام معطّلة** (كل روتات login/register/OTP مُعلّقة في web.php).
- **الجزء التجاري (Products/Variants/Inventory/Attributes)** موجود في DB + Admin لكن **بلا واجهة عرض/شراء/سلة** (لا cart، لا checkout، لا orders).
- **لا نظام SaaS فعلي**: لا اشتراكات، لا tenancy، لا دفع — رغم أن `project:init` يشير إلى وجودها (broken imports).
- **Social login** مُهيّأ جزئيًا لكن غير مربوط بروتات.
- **الاختبارات شبه معدومة**: فقط `ExampleTest` × 2 (Unit + Feature).

---

## 8. المشاكل الواضحة والديون التقنية (Technical Debt)

1. **⛔ أمر `project:init` معطوب (blocker):**
   `app/Console/Commands/InitializeProject.php` يستورد أصنافًا غير موجودة:
   `App\Models\PaymentGateway`, `App\Models\Subscription`, `App\Services\Payment\StripeDirectChargeService`.
   → تشغيل `php artisan` قد يفشل عند تحميل الأمر (والـ README يوصي بهذا الأمر كخطوة تثبيت أولى). يبدو أن هذه بقايا من مشروع SaaS آخر تم نسخ الأمر منه.

2. **🧪 اختبارات مفقودة/محذوفة:**
   `.phpunit.result.cache` يذكر اختبارات (`BookingSlotServiceTest`, `SmokeTest`) **غير موجودة** في مجلد `tests/`. أي أن اختبارات كُتبت ثم فُقدت. التغطية الفعلية الحالية = 0 اختبار حقيقي.

3. **🔁 ازدواجية منطق التذكير بالحجز:**
   يوجد مساران متوازيان: `SendBookingReminderJob` (مجدول عبر delay في الـ queue) **و** أمر `SendBookingReminders` المجدول كل دقيقة. احتمال إرسال تذكير مزدوج أو منطق متضارب — يحتاج توحيد.

4. **🕒 ساعات العمل والمدة مضمّنة في الكود:**
   `BookingSlotService` يحتوي أوقات العمل ومدة 20 دقيقة كأرقام ثابتة، رغم وجود Model `OpeningHour` مخصص لذلك. المنطق غير مربوط بقاعدة البيانات → غير قابل للتهيئة من لوحة التحكم.

5. **📧 إشعارات الحجز هشّة:**
   `MAIL_ADMIN_EMAIL` فارغ و `MAIL_MAILER=log`. إرسال بريد الأدمن يعتمد على `config('mail.admin_email')` وقد يفشل بصمت (مغلّف في try/catch يكتب warning فقط).

6. **🔑 تسريب `APP_KEY`:**
   نفس `APP_KEY` مكتوب في `.env` و `.env.example` (مُتتبَّع في git). يجب عدم مشاركة مفاتيح حقيقية في المستودع.

7. **🧹 كود ميّت / معلّق كثير:**
   دالة `storeBooking` مكرّرة (نسخة مُعلّقة كاملة + نسخة فعّالة)، وروتات مصادقة مُعلّقة في web.php، و use statements لأصناف غير مستخدمة (CartController, OrderController في api.php). تعليقات مختلطة عربي/إنجليزي.

8. **🗂️ مجلدات فارغة/بقايا تخطيط:** `docs/tasks/pos/` و `stubs/` و `.github/` فارغة — تشير إلى ميزة POS مخطط لها لم تبدأ.

9. **📜 تاريخ Git غير منظّم:** رسائل commit كثيرة بلا معنى (`0`, `00`, `a`, `01`) + merge/revert لميزة `BOOKINGS_ENABLED` (أُضيفت ثم أُلغيت). يصعّب تتبّع تطور المشروع.

10. **🌐 `api.php` يستورد controllers غير مستخدمة** (`Api\V1\HomeController`, `AuthController`, `CartController`, `OrderController`) داخل مجموعة روتات فارغة — يجب التحقق من وجود هذه الأصناف فعلًا (محتمل مراجع معطوبة أخرى).

---

## 9. توصيات للجلسة القادمة (قبل بناء طبقة SaaS)

- إصلاح `project:init` (إزالة/استبدال مراجع Payment/Subscription المعطوبة) — بلوكر للتثبيت.
- توليد `APP_KEY` جديد وإزالته من `.env.example`، تعبئة `MAIL_ADMIN_EMAIL`.
- توحيد منطق تذكير الحجز في مسار واحد.
- ربط ساعات العمل بـ `OpeningHour` / `Settings` بدل الأرقام الثابتة.
- استعادة/كتابة اختبارات `BookingSlotService` و smoke tests (البنية جاهزة في phpunit.xml).
- تحديد بوضوح: هل الـ SaaS سيُبنى فوق طبقة Products الموجودة أم كطبقة tenancy/subscriptions جديدة.

---

*تم إنشاء هذا الملف تلقائيًا من فحص قراءة-فقط. لم يُعدَّل أي ملف آخر في هذه الجلسة.*
