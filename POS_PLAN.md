# POS_PLAN — nexo-0001 (المرحلة 2: تحليل السيناريوهات والخطة)

> خطة تنفيذ نظام POS للموقع، مبنية على `STACK_FINDINGS.md`.
> **لا يوجد أي كود بعد.** هذا المستند للمراجعة والموافقة قبل المرحلة 3.
> تاريخ: 2026-07-19

---

## 0. ملخّص النطاق (Scope)

**المطلوب الآن:**
- تسجيل دخول موظف (username/password) على guard `admin` الموجود.
- بعد الدخول: صفحة POS تعرض الخدمات كأزرار.
- اختيار خدمة → تُضاف للـ Cart بسعرها المخزّن، مع إمكانية تعديل السعر لهذه المعاملة فقط (custom price) دون تغيير السعر الأصلي.
- Cart: إضافة/حذف/تعديل كمية أو سعر، حساب Subtotal و Total.
- أزرار الدفع Cash / Card (Card = بنية فقط، بلا منطق فعلي الآن).
- المعاملة تُنسب تلقائيًا للموظف المسجّل دخول (لا قائمة "Select Employee").
- كل الواجهة بألوان NEXO عبر design tokens موحّدة.

**خارج النطاق الآن (مؤجّل لتاسك منفصل):** منطق الدفع بالكارت الفعلي، نظام النقاط (Points)، التقارير/الإحصائيات، إدارة المخزون.

---

## 1. قرارات معماريّة مقترحة (تفاصيلها في القسم 9 للموافقة)

| # | القرار | المقترح (الأبسط الآن) |
|---|---|---|
| D1 | بيئة واجهة POS | **Tailwind** كصفحة مستقلة بألوان NEXO (منفصلة عن Metronic) |
| D2 | طريقة دخول الموظف | **مسار POS login مستقل** يقبل `username`+password على guard `admin`، دون المساس بدخول الأدمن الحالي (email) |
| D3 | الصلاحيات | **دور Spatie واحد `cashier`** (guard_name=admin) بصلاحية `pos.access` فقط |
| D4 | سعر الخدمة | إضافة عمود `price DECIMAL(10,2)` لجدول `services` عبر migration جديد |
| D5 | تخزين المعاملة | جدولان جديدان: `pos_transactions` + `pos_transaction_items` (سطر لكل بند) |

---

## 2. نموذج البيانات المقترح (Data Model)

### 2.1 تعديل جدول `services` (migration جديد — لا نعدّل القديم)
```
ALTER services ADD price DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER description
```
- `DECIMAL` وليس float → دقّة مالية.
- `default 0` حتى لا تنكسر السجلات الموجودة؛ الخدمات القديمة ستحتاج تسعيرًا من لوحة الأدمن (نضيف حقل price لفورم الخدمة — تعديل بسيط على `ServiceController`/الفورم/الـ Request/الـ Resource).

### 2.2 جدول `pos_transactions` (رأس الفاتورة)
| العمود | النوع | ملاحظة |
|---|---|---|
| id | bigint PK | |
| admin_id | FK → admins.id | الموظف (يُملأ تلقائيًا من الجلسة) |
| subtotal | DECIMAL(10,2) | مجموع البنود |
| total | DECIMAL(10,2) | = subtotal الآن (لا ضريبة/خصم في هذه المرحلة) |
| payment_method | enum('cash','card') | |
| status | enum('completed','pending') default 'completed' | card قد يبقى pending لاحقًا |
| reference | string nullable | مرجع دفع الكارت مستقبلًا |
| created_at/updated_at | timestamps | |

### 2.3 جدول `pos_transaction_items` (بنود الفاتورة)
| العمود | النوع | ملاحظة |
|---|---|---|
| id | bigint PK | |
| pos_transaction_id | FK → pos_transactions.id (cascade) | |
| service_id | FK → services.id (restrict/nullable) | |
| service_name | string | **snapshot** للاسم وقت البيع |
| original_price | DECIMAL(10,2) | السعر المخزّن وقت البيع (snapshot) |
| unit_price | DECIMAL(10,2) | السعر الفعلي المُحتسب (custom إن وُجد) |
| quantity | int default 1 | |
| line_total | DECIMAL(10,2) | = unit_price × quantity |

> **مبدأ:** نخزّن snapshot للاسم والسعر داخل البند، حتى لو تغيّر/حُذف السعر الأصلي للخدمة لاحقًا تبقى الفاتورة صحيحة تاريخيًا. وهذا يضمن أن custom price **لا يمسّ** جدول services.

---

## 3. طبقة منطق الأموال (نقطة واحدة قابلة للاختبار) ⭐

**`App\Services\Pos\CartCalculator`** (أو `PosPricingService`) — كل الحساب هنا، لا في المتحكم ولا الواجهة:
- `normalizePrice($input): string` — يتحقق ويُطبّع السعر (رفض غير الرقمي/السالب، تقريب لخانتين).
- `lineTotal(unit_price, qty)` — بحساب دقيق.
- `subtotal(items[])` و `total(...)`.
- **الدقة:** كل الحسابات بـ `BCMath` (bcmul/bcadd، scale=2) أو أعداد صحيحة بالسنتات — **لا floating point**. القيم تُخزّن DECIMAL.
- هذه الطبقة هي وحدها من ستُغطّى باختبارات آلية (المرحلة التيست).

مصدر الحقيقة للحساب هو **الخادم دائمًا**: الواجهة تحسب للعرض فقط، والخادم يعيد الحساب من `service_id` + custom price المُرسل قبل الحفظ (لا نثق بالـ total القادم من المتصفح).

---

## 4. المصادقة والصلاحيات (بناءً على الموجود)

- **guard:** نعيد استخدام `admin` (لا guard جديد).
- **الدخول (D2):** مسار `pos/login` جديد يستخدم `Auth::guard('admin')->attempt(['username'=>..,'password'=>..])`. لا نلمس `Admin\Auth\LoginController` الحالي.
- **الدور (D3):** دور `cashier` (guard_name=admin) + صلاحية `pos.access`. الموظف يُنشأ كسجل `admins` (username+password+status) ويُمنح دور cashier. الأدمن الحالي (دور admin) يملك كل الصلاحيات فيصل أيضًا.
- **الحماية:** مجموعة روتات POS خلف `['auth:admin', 'permission:pos.access']` (أو middleware مخصّص `pos`). أي وصول بلا دخول → redirect لـ `pos/login`.
- **username فريد:** جدول admins حاليًا `username` **nullable وغير unique**. سنضيف قيد unique (migration) + التحقق عند إنشاء الموظف.

---

## 5. تدفّق الشاشة (UX Flow)

1. `/pos/login` → إدخال username+password.
2. نجاح → `/pos` (الشاشة الرئيسية):
   - يسار/أعلى: شبكة أزرار الخدمات (اسم + سعر).
   - يمين: الـ Cart (البنود، الكمية، السعر، حذف، Subtotal، Total).
   - أسفل: زرّا Cash / Card + زر Clear.
   - ترويسة: اسم الموظف المسجّل + زر Logout. (لا Select Employee إطلاقًا.)
3. اختيار خدمة → تُضاف للـ Cart. أيقونة/حقل لتعديل السعر (custom price) لكل بند.
4. Cash → تأكيد → حفظ المعاملة (status=completed) → رسالة نجاح + تفريغ Cart.
5. Card → (الآن) زر موجود؛ يفتح نفس مسار الحفظ لكن payment_method=card status=pending أو معطّل بـ tooltip "قريبًا" — **حسب قرار D-card أدناه**.

---

## 6. السيناريوهات و edge cases (كل واحد + الحل المقترح)

| # | السيناريو | الحل المقترح |
|---|---|---|
| E1 | **لا توجد خدمات مخزّنة** | حالة فارغة: رسالة "لا توجد خدمات متاحة" + (إن كان أدمن) رابط لإضافة خدمة. أزرار الدفع معطّلة. |
| E2 | **خدمات بلا سعر (price=0 من الافتراضي)** | تُعرض بشارة "غير مسعّرة"؛ إضافتها للسلة ممكنة لكن تتطلب custom price > 0 قبل الدفع، أو تُخفى — **قرار D-price0 أدناه**. |
| E3 | **الدفع والـ Cart فارغ** | زرّا Cash/Card معطّلان (disabled) عندما لا بنود؛ وتحقق خادمي يرفض الحفظ 422 لو وصل cart فارغ. |
| E4 | **custom price = 0** | مرفوض (يجب > 0). رسالة تحقق واضحة. |
| E5 | **custom price سالب** | مرفوض في الواجهة والخادم. |
| E6 | **custom price نص غير رقمي** | مرفوض؛ `normalizePrice` يفشل → 422. الحقل input نوعه number + تحقق خادمي regex/`numeric`. |
| E7 | **custom price ضخم/غير معقول** | سقف أعلى منطقي (مثلًا ≤ 1,000,000) قابل للضبط؛ يتجاوزه → تحذير/رفض. |
| E8 | **custom price بكسور كثيرة** | يُقرّب لخانتين عشريتين (خطوة السنت). |
| E9 | **إضافة نفس الخدمة أكثر من مرة** | **المقترح:** تزيد الكمية (quantity++) بدل تكرار سطر — أبسط للعرض. لكن لو للبند سعر مخصّص مختلف، يُنشأ سطر منفصل. — **قرار D-dup أدناه**. |
| E10 | **حذف بند / تفريغ السلة** | مدعوم؛ إعادة حساب فورية. |
| E11 | **الجلسة انتهت أثناء العمل** | عند إرسال الدفع بعد انتهاء الجلسة → 401/redirect لـ login مع حفظ رسالة "انتهت الجلسة، سجّل الدخول". لا نفقد… (السلة client-side؛ نوضّح أنها قد تُفقد). |
| E12 | **وصول `/pos` بلا دخول** | middleware يمنع ويعيد التوجيه للـ login. |
| E13 | **خدمة حُذفت/عُطّلت بين التحميل والدفع** | الخادم يتحقق من وجود service_id وقت الحفظ؛ إن اختفت → رسالة و إزالة البند. (snapshot يحمي الفواتير القديمة.) |
| E14 | **دقة الأموال (float)** | كل الحساب بـ DECIMAL + BCMath/سنتات. لا `+`/`*` على floats. مُغطّى باختبارات. |
| E15 | **إرسال مزدوج (double submit) للدفع** | تعطيل زر الدفع أثناء الإرسال + (اختياري) idempotency token لمنع فاتورة مكرّرة. |
| E16 | **تلاعب بالسعر من المتصفح** | الخادم لا يثق بالـ total المُرسل؛ يعيد الحساب بالكامل من البنود المُتحقّق منها. |

---

## 7. الملفات التي ستُنشأ / تُعدّل (المرحلة 3)

### جديدة
- `database/migrations/xxxx_add_price_to_services_table.php`
- `database/migrations/xxxx_add_username_unique_to_admins_table.php`
- `database/migrations/xxxx_create_pos_transactions_table.php`
- `database/migrations/xxxx_create_pos_transaction_items_table.php`
- `app/Models/PosTransaction.php`, `app/Models/PosTransactionItem.php`
- `app/Services/Pos/CartCalculator.php` ⭐ (منطق الأموال — قابل للاختبار)
- `app/Http/Controllers/Pos/AuthController.php` (login/logout للموظف)
- `app/Http/Controllers/Pos/PosController.php` (عرض الشاشة)
- `app/Http/Controllers/Pos/TransactionController.php` (حفظ المعاملة)
- `app/Http/Requests/Pos/StoreTransactionRequest.php` (التحقق)
- `app/Http/Middleware/Pos.php` أو استخدام `permission:pos.access`
- `routes/pos.php` (أو قسم داخل web/admin) — يُسجّل في `bootstrap/app.php`
- `resources/views/pos/login.blade.php`, `resources/views/pos/index.blade.php`, وربما layout `resources/views/pos/layouts/app.blade.php`
- أصول Tailwind لـ POS: `public/pos_assets/...` أو دمج في build (حسب D1) + ملف design tokens للألوان
- `database/seeders/PosSeeder.php` (دور cashier + صلاحية + موظف تجريبي + تسعير خدمات تجريبي)
- `tests/Unit/Pos/CartCalculatorTest.php` ⭐ (اختبارات المنطق الحسّاس)

### مُعدّلة (تعديل بسيط)
- `bootstrap/app.php` — تسجيل روتات/alias الـ POS.
- `database/seeders/DatabaseSeeder.php` — استدعاء PosSeeder.
- `database/seeders/PermissionSeeder.php` — إضافة صلاحية `pos.access`.
- فورم/متحكم/Request/Resource الخدمة في الأدمن — إضافة حقل `price` (اختياري لكن لازم عمليًا لتسعير الخدمات).
- `phpunit.xml` — (لا تعديل غالبًا؛ suite Unit جاهزة).

> **لن نلمس** أي متحكم/موديل خارج نطاق POS إلا التعديلات البسيطة أعلاه.

---

## 8. ترتيب التنفيذ خطوة بخطوة (المرحلة 3)

1. **الهجرات + الموديلات**: price للـ services، username unique، جدولا POS، موديلاتها.
2. **`CartCalculator`** (منطق الأموال) **+ اختباراته الآلية** — نشغّلها ونثبت نجاحها أولًا (TDD للجزء المالي).
3. **الصلاحيات والـ Seeder**: دور cashier + صلاحية pos.access + موظف تجريبي + تسعير خدمات تجريبي.
4. **المصادقة**: مسار pos/login + logout + middleware الحماية.
5. **شاشة POS (view)**: عرض الخدمات + Cart + أزرار الدفع، بألوان NEXO عبر tokens.
6. **حفظ المعاملة**: StoreTransactionRequest + TransactionController (Cash يعمل، Card بنية فقط) مع إعادة الحساب الخادمي.
7. **تمرير edge cases** (القسم 6) وربط رسائل التحقق.
8. **إعادة تشغيل الاختبارات** وإثبات نجاحها قبل الحديث عن live.

بعد كل خطوة ذات منطق مالي، نشغّل اختبارات `CartCalculator` ونُظهر النتيجة.

---

## 9. قرارات تحتاج رأيكم قبل البدء (⚠️ نتوقف هنا)

1. **D1 — بيئة الواجهة:** Tailwind مستقل بألوان NEXO ✅ (مقترحنا) أم دمج داخل لوحة Metronic؟
2. **D2 — الدخول:** مسار POS login مستقل بـ username ✅ (مقترحنا) أم تحويل دخول الأدمن كله لـ username؟
3. **D3 — الصلاحيات:** دور واحد `cashier` متساوٍ ✅ (مقترحنا) أم cashier + دور إدارة POS؟
4. **D-card — زر الكارت الآن:** نجعله (أ) معطّلًا مع "قريبًا"، أم (ب) يحفظ المعاملة payment_method=card بحالة pending دون معالجة فعلية؟ (مقترحنا: أ — أوضح وأأمن.)
5. **D-price0 — خدمة سعرها 0:** (أ) تُعرض وتتطلب custom price>0 قبل الدفع ✅، أم (ب) تُخفى من الشاشة حتى تُسعّر؟
6. **D-dup — تكرار نفس الخدمة:** (أ) زيادة الكمية ✅ (مقترحنا)، أم (ب) سطر منفصل لكل نقرة؟
7. **العملة وعدد الخانات:** نفترض خانتين عشريتين (سنت). ما العملة/الرمز المطلوب عرضه؟ (SAR / $ ...؟)
8. **الكمية (quantity):** هل نسمح بكمية > 1 للخدمة الواحدة أصلًا، أم كل خدمة تُباع مرة واحدة لكل معاملة؟

---

*نهاية المرحلة 2. لن أكتب أي كود حتى نتّفق على القرارات أعلاه (أو توافقوا على المقترحات كما هي).*
