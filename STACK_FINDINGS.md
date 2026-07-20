# STACK_FINDINGS — nexo-0001 (المرحلة 1: فحص المشروع)

> فحص قراءة-فقط لأجل بناء نظام POS. كل القيم أدناه مُستخرجة من الملفات الفعلية، لا تخمين.
> تاريخ الفحص: 2026-07-19

---

## 1. الـ Tech Stack (من الملفات الفعلية)

**المصدر:** `composer.json`, `package.json`, `bootstrap/app.php`, `config/*`, `.env`.

### Backend
- **PHP** `^8.2` — (`composer.json`)
- **Laravel Framework** `^11.0` — (`composer.json`)
- الحزم الفعّالة المسجّلة في `bootstrap/providers.php`:
  - `spatie/laravel-permission ^6.9` — الأدوار/الصلاحيات (**سنستخدمها للـ POS**)
  - `maatwebsite/excel ^3.1`, `intervention/image 2.7`, `php-ffmpeg/php-ffmpeg ^1.3`
  - `laravel/passport ^12.3`, `laravel/socialite ^5.18` — مسجّلة لكن **غير مُفعّلة عمليًا** (API فارغة، روتات social معطّلة)

### Frontend
- **Blade** (server-side rendered) — لا SPA.
- **الواجهة الأمامية العامة (NEXO landing): Tailwind CSS** — يُحمَّل عبر ملف مُجمّع `public/frontend_assets/src/output.css` (المصدر `input.css` يستخدم `@import "tailwindcss"` + `@theme`). لا يوجد `tailwind.config.js` (Tailwind v4، الإعداد داخل CSS عبر `@theme`).
- **لوحة التحكم (Admin): قالب Metronic** (Bootstrap) — `public/admin_assets/css/style.bundle.css` + DataTables + FullCalendar + TinyMCE/Summernote.
- **أدوات البناء:** Vite `^5.0` + `laravel-vite-plugin` (entry: `resources/sass/app.scss`, `resources/js/app.js`, يعتمد Bootstrap 5 + Sass). ملاحظة: واجهة NEXO لا تمرّ عبر Vite — الـ Tailwind مُجمّع مسبقًا كـ `output.css` ثابت.

> **قرار مبكّر لـ POS:** واجهة الموقع العامة على **Tailwind**، بينما لوحة الأدمن على **Bootstrap/Metronic**. لازم نقرّر أين تعيش صفحة الـ POS (انظر POS_PLAN.md).

### Database
- **MySQL** — (`.env`: `DB_CONNECTION=mysql`, `DB_DATABASE=kaya`, root بلا كلمة مرور — بيئة XAMPP محلية).
- **53 migration** + **31 Seeder**.

### Services / Infra (من `config` و `.env`)
- Session / Cache / Queue: كلها على `database` driver.
- Mail: `MAIL_MAILER=log` (لا إرسال فعلي حاليًا) — لا علاقة مباشرة بالـ POS.
- Queue: `database` (يحتاج `queue:work` — يهمّنا فقط لو أضفنا jobs، والـ POS غالبًا لا يحتاج).

---

## 2. كيف يشتغل المشروع ونقاط الدخول

- **Bootstrap:** `public/index.php` → `bootstrap/app.php` (نمط Laravel 11).
- **التوجيه مُعرّف يدويًا** في `bootstrap/app.php` عبر closure يحمّل:
  - `routes/web.php` (middleware `web`) — واجهة NEXO العامة (الرئيسية، الخدمات، المعرض، تواصل، الحجز).
  - `routes/admin.php` (middleware `web`) — لوحة التحكم، كلها تحت `prefix=admin`.
  - `routes/api.php` (middleware `api`) — **فارغة تمامًا**.
  - `routes/console.php` — أمر `SendBookingReminders` مجدول.
- **Middleware aliases** (في `bootstrap/app.php`): `admin` → `App\Http\Middleware\Admin`, بالإضافة إلى `role`/`permission`/`role_or_permission` من Spatie.
- التشغيل محليًا: `php artisan serve` (أو XAMPP على `D:\xampp\htdocs\nexo`)، والأصول عبر `npm run dev`/`build`.

---

## 3. نظام المستخدمين / المصادقة الموجود حاليًا ⭐ (نبني فوقه، لا نكرّره)

**يوجد نظاما مصادقة منفصلان (multi-guard) — `config/auth.php`:**

| Guard | Provider | الجدول/الموديل | الحالة |
|---|---|---|---|
| `web` / `user` | `users` | `App\Models\User` (جدول `users`) | موجود، لكن **روتات login/register للمستخدم كلها مُعلّقة** في `web.php` |
| **`admin`** | `admins` | **`App\Models\Admin`** (جدول `admins`) | ✅ **فعّال بالكامل** — هذا ما سنبني عليه |
| `user_api` | passport | `users` | غير مستخدم |

### تفاصيل نظام الأدمن (الأنسب لموظفي الـ POS)
- **Login:** `App\Http\Controllers\Admin\Auth\LoginController` — يستخدم trait `AuthenticatesUsers`، guard `admin`، session-based.
  - ⚠️ **الدخول حاليًا بـ `email` + `password`** (وليس username)، رغم أن جدول `admins` فيه عمود `username` (nullable وغير مستخدم في الدخول).
  - المطلوب في الـ POS: **username/password**. الحقل موجود بالفعل — نحتاج فقط تعديل/إضافة منطق دخول يعتمد `username` (سنقرّره في الخطة، دون تكرار النظام).
- **الحماية:** `App\Http\Middleware\Admin` → يتحقق `Auth::guard('admin')->check()`، وإلا يعيد التوجيه لـ `admin.login`. مجموعة روتات الأدمن محميّة بـ `['auth:admin','admin']`.
- **جدول `admins`** (migration `0001_01_10_000010`): `id, name, email(unique,nullable), email_verified_at, username(nullable), password(nullable), phone_code, phone, status(default 1), image, remember_token, timestamps`.
- **الأدوار/الصلاحيات:** `App\Models\Admin` يستخدم `Spatie\Permission\Traits\HasRoles` مع `guard_name = 'admin'`.
  - `Admin::USER_TYPES = ['0'=>'admin', '1'=>'user']`.
  - `RoleSeeder` ينشئ أدوارًا لكل نوع، و`AdminSeeder` ينشئ Super Admin (`admin@gmail.com` / `123456789`) ويمنحه دور `admin` بكل صلاحيات guard `admin`.
  - ⇒ البنية جاهزة لإضافة دور جديد مثل `cashier`/`employee` بـ `guard_name=admin` بسهولة.

> **الخلاصة للـ POS:** نبني تسجيل دخول الموظفين على **guard `admin` الموجود** (كل موظف = سجل في `admins`). لا حاجة لإنشاء نظام مستخدمين جديد. القرار المتبقي: هل الموظف يدخل بـ username أم email، وكيف نميّز "موظف POS" عن "أدمن لوحة التحكم" (دور Spatie). — مفصّل في POS_PLAN.md.

---

## 4. كيف الخدمات (Services) مخزّنة حاليًا ⭐

**الموديل:** `App\Models\Service` — **جدول `services`** (migration `2025_04_14_105611_create_services_table.php`).

### الحقول الفعلية في الجدول:
| الحقل | النوع | ملاحظة |
|---|---|---|
| `id` | bigint PK | |
| `name` | string | اسم الخدمة |
| `description` | text nullable | |
| `icon` | string nullable | اسم أيقونة/ملف |
| `image` | string nullable | مسار صورة |
| `is_featured` | boolean default false | تُستخدم للتمييز بين خدمات عادية/باقات في الواجهة |
| `created_at/updated_at` | timestamps | |

- **`fillable`** في الموديل: `name, description, status, icon, image, is_featured`
  (⚠️ ملاحظة: `status` موجود في `fillable` لكنه **غير موجود في المigration** — عمود وهمي).
- علاقات: `Service hasMany Booking`. يوجد scope `search`.
- الـ Seeder ينشئ 4 خدمات (Home Renovation ...) بدون أي سعر.

### 🔴 أخطر نتيجة لبناء الـ POS:
**لا يوجد حقل سعر (`price`) للخدمات — إطلاقًا.**
- تحققت من: `Service` model, `ServiceResource`, `ServiceSeeder`, وكل الـ migrations (`grep price` عبر migrations لا يُظهر جدول services؛ فقط menu_items / products / variants فيها price).
- ⇒ نظام الـ POS يبيع خدمات لها سعر، لكن الخدمات الحالية **بلا سعر مخزّن**.
- **قرار مطلوب (في الخطة):** إضافة عمود `price` (decimal) إلى جدول `services` عبر migration جديد — وهذا التعديل الوحيد غير القابل للتفادي على المخطط الحالي. (السعر الأصلي المخزّن يبقى ثابتًا، والـ custom price لكل معاملة لا يغيّره — متوافق مع المتطلبات.)

---

## 5. الألوان الرسمية لـ NEXO (hex دقيقة من الملف الفعلي) ⭐

**المصدر القاطع:** `public/frontend_assets/src/input.css` → كتلة Tailwind v4 `@theme` (وتأكّدت من استخدامها فعليًا في `output.css` المُجمّع وفي `home.blade.php`).

| الاسم (token) | Hex | الاستخدام |
|---|---|---|
| **evergreen** | `#283326` | أخضر داكن — لون أساسي/خلفيات داكنة/نصوص |
| **ivory** | `#F8EDD2` | كريمي فاتح — الخلفية الرئيسية/النص على الداكن |
| **crimson** | `#8C1C13` | أحمر قرمزي — لون تمييز/أزرار CTA |

**تأكيد الاستخدام الفعلي:**
- في `output.css` المُجمّع: `#283326`×3, `#F8EDD2`×9, `#8C1C13`×1 (+ عبر أسماء utility: evergreen×11, ivory×27, crimson×7).
- في `home.blade.php`: `evergreen`×10, `ivory`×30, `crimson`×3.

### الخطوط الرسمية (من نفس `@theme`):
| token | القيمة |
|---|---|
| `--font-league-gothic` | `'League Gothic'` (عناوين — الأكثر استخدامًا) |
| `--font-inter` | `'Inter'` |
| `--font-poppins` | `'Poppins'` |
| breakpoint إضافي | `--breakpoint-3xl: 1920px` |

> **قرار مبكّر لـ POS:** ألوان الـ POS ستُبنى كـ design tokens من هذه القيم الثلاث بالضبط (`#283326`, `#F8EDD2`, `#8C1C13`) — تُعرّف مرة واحدة (CSS variables / Tailwind `@theme`) ولا تُكتب يدويًا. الطريقة الدقيقة (Tailwind مثل الواجهة، أم Bootstrap مثل الأدمن) قرار في POS_PLAN.md.

---

## 6. ملاحظات تؤثر على تصميم الـ POS (خلاصة قابلة للتنفيذ)

1. **المصادقة:** جاهزة عبر guard `admin` + Spatie (guard_name `admin`). نضيف دور `cashier`/`employee` — لا نبني نظامًا جديدًا.
2. **username:** العمود موجود في `admins` لكن الدخول الحالي بالـ email. يحتاج قرار/تعديل بسيط.
3. **السعر:** **يجب إضافة `price` لجدول services** (migration جديد) — لا مفرّ منه.
4. **الواجهة:** ازدواج بيئتين (Tailwind للموقع / Bootstrap للأدمن). لازم نحسم بيئة صفحة الـ POS.
5. **حساب الأموال:** MySQL — سنستخدم `decimal` للأسعار وحسابات دقيقة (لا float) في طبقة منطق واحدة.

*لم يُعدَّل أي ملف آخر في هذه المرحلة. الخطوة التالية: POS_PLAN.md (المرحلة 2)، ثم ننتظر موافقتكم قبل أي كود.*
