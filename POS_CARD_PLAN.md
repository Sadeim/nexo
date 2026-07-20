# POS — PlutoPay Terminal (In‑Person Card) — خطة التنفيذ

> 🔴 **TEST MODE فقط.** هذه المرحلة كلها بمفاتيح `sk_test_ / pk_test_` ومحاكي الجهاز
> (`/v1/terminal/simulate-payment`). **ممنوع منعاً باتاً** أي كارت حقيقي أو `live` key.
> الانتقال إلى live هو تاسك منفصل بعد اعتماد نتائج الاختبار.
>
> **هذه وثيقة خطة فقط — لا كود قبل موافقتكم.**

---

## 0) ملاحظة توثيق (يجب تثبيتها قبل الكود)

أسماء الـ endpoints والحقول أدناه مأخوذة حرفياً من مواصفاتكم/توثيق PlutoPay
(`guides/terminal.html`, `webhooks.html`, `testing.html`). **قبل بدء أي كود** سنفتح
الصفحات الثلاث ونثبّت:
- الأسماء الدقيقة للحقول في كل response (اسم حقل `payment_intent_id`، `reader_id`، `status`).
- ترويسة التوقيع بالضبط (`X-PlutoPay-Signature`؟ اسم `X-PlutoPay-Delivery`؟ صيغة `t=..,v1=..`؟).
- نص دالة `verifyPlutoPaySignature` الجاهزة (سننسخها كما هي).
- كروت الاختبار وأكواد الفشل الدقيقة.

أي اختلاف عن الافتراضات هنا يُصحَّح في الكود، لا في هذه الخطة.

---

## 1) المبدأ الحاكم

- **الخادم مصدر الحقيقة للمبلغ.** المبلغ يُحسب من السلة عبر `CartCalculator` الموجود ويُؤخذ
  `total_cents` (سنتات صحيحة). لا يُقرأ أي مبلغ من المتصفح إطلاقاً. (نفس نمط `TransactionController::store` الحالي.)
- **الفاتورة لا تُعتبر مدفوعة إلا بعد webhook `payment.succeeded` موثّق التوقيع.** رد الطلبات الثلاثة
  لا يُغلق البيع.
- **كل المال integer cents.** الحد الأدنى **50 سنت** (يُرفض قبل إنشاء أي دفعة).
- **Idempotency** على إنشاء الدفعة، و**idempotency** على معالجة الـ webhook.

---

## 2) تدفق الحالات (State Machine) للمعاملة الكرتية

```
[cashier يضغط Card]
      │
      ▼
(1) إنشاء pos_transaction  status = awaiting_payment  (pending في السلة، لم يُخصم شيء)
      │   نحسب total_cents خادمياً ونتحقق ≥ 50
      ▼
(2) POST /v1/terminal/connection-token        → connection_token
(3) POST /v1/terminal/create-payment          → payment_intent_id, status=pending
      │   Idempotency-Key = uuid المعاملة
      │   نخزّن payment_intent_id على الصف
      ▼
(4) POST /v1/terminal/process-payment         → بدأ القراءة على الجهاز
      │   الواجهة تعرض: "جارٍ انتظار الكارت…"  وتبدأ polling على status
      ▼
   ┌──────────── webhook (مصدر الحقيقة) ────────────┐
   │ payment.succeeded → status=completed, reference=payment_id  │
   │ payment.failed    → status=failed, failure_reason           │
   └────────────────────────────────────────────────┘
      │
      ▼
الواجهة (polling) ترى الحالة الجديدة → popup نجاح / رسالة فشل
```

الحالات الممكنة لعمود `status`:
`awaiting_payment` → (`processing`) → `completed` | `failed` | `canceled` | (يبقى `awaiting_payment` عند تعذّر التأكيد).

`cash` يبقى كما هو: يُنشأ مباشرة `completed`.

---

## 3) تغييرات قاعدة البيانات (migrations جديدة — إضافية فقط)

**لا تعديل مدمّر.** migration جديد يوسّع الجدول القائم:

`pos_transactions` — إضافة/توسيع:
| العمود | النوع | الغرض |
|---|---|---|
| `status` | توسيع الـ enum ليشمل `awaiting_payment, processing, failed, canceled` (بالإضافة للحالي `completed, pending`) | دورة حياة الكارت |
| `payment_intent_id` | `string nullable` (index) | ربط بردّ create-payment والـ webhook |
| `currency` | `string(3) default 'usd'` | صريح مع الجهاز |
| `amount_cents` | `unsignedInteger nullable` | المبلغ المُرسل للجهاز بالسنتات (تدقيق/تصالح) |
| `failure_reason` | `string nullable` | سبب `card_declined` / `insufficient_funds` |
| `reference` | (موجود) | يُملأ بـ payment id عند النجاح |

جدول جديد `pos_payment_webhook_events` (idempotency + سجل تدقيق):
| العمود | النوع |
|---|---|
| `id` | pk |
| `delivery_id` | `string unique` — من `X-PlutoPay-Delivery` (fallback: `body.id`) |
| `event_type` | `string` — `payment.succeeded` … |
| `payment_intent_id` | `string nullable index` |
| `payload` | `json` (raw مُخزّن للتدقيق) |
| `processed_at` | `timestamp nullable` |
| timestamps | |

`unique(delivery_id)` هو ما يمنع المعالجة المكررة على مستوى قاعدة البيانات.

---

## 4) الإعدادات والمفاتيح

`.env` (لا تُرفع، لا تُكتب في الكود):
```
PLUTOPAY_SECRET_KEY=sk_test_xxx
PLUTOPAY_PUBLISHABLE_KEY=pk_test_xxx
PLUTOPAY_WEBHOOK_SECRET=whsec_test_xxx
PLUTOPAY_TERMINAL_ID=tmr_test_xxx
PLUTOPAY_READER_ID=rdr_test_xxx
PLUTOPAY_BASE_URL=https://api.plutopayus.com
PLUTOPAY_ENV=test          # حارس: الكود يرفض العمل إن لم يبدأ المفتاح بـ sk_test_
```
تُقرأ عبر `config/services.php` → `config('services.plutopay.*')`. **حارس صريح في الكود**:
إذا `PLUTOPAY_SECRET_KEY` لا يبدأ بـ `sk_test_` → استثناء يمنع أي طلب (سياج ضد استخدام live بالخطأ).

---

## 5) المكونات الجديدة في الكود (بنية مقترحة)

```
app/Services/Pos/Payment/
  PlutoPayClient.php        // غلاف HTTP: connectionToken(), createPayment(), processPayment(), simulatePayment(), retrievePayment()
  PlutoPaySignature.php     // verifyPlutoPaySignature(rawBody, header, secret, tolerance=300)
  Exceptions/PlutoPayException.php

app/Http/Controllers/Pos/
  CardPaymentController.php  // start(): ينشئ pending + ينفّذ الخطوات 2‑4 ؛ status(): للـ polling
app/Http/Controllers/Webhooks/
  PlutoPayWebhookController.php

app/Http/Requests/Pos/StartCardPaymentRequest.php   // نفس شكل StoreTransactionRequest للـ items
```

مسارات جديدة:
```
routes/pos.php  (داخل middleware pos)
  POST  /pos/card/start              → CardPaymentController@start
  GET   /pos/card/{transaction}/status → CardPaymentController@status   (polling)

routes/web.php  (عام، بلا CSRF، بلا auth)
  POST  /webhooks/plutopay          → PlutoPayWebhookController@handle
```
🔴 مسار الـ webhook يجب **استثناؤه من CSRF** (`VerifyCsrfToken $except`) لأنه يأتي من خارج.
الأمان يأتي من **توقيع HMAC**، لا من CSRF.

---

## 6) تفاصيل الـ webhook (أمان حرج)

`PlutoPayWebhookController@handle`:
1. اقرأ **raw body** (`$request->getContent()`) **قبل أي `json_decode`**.
2. `verifyPlutoPaySignature(raw, header, secret)`:
   - HMAC‑SHA256 على raw body، مقارنة بـ `hash_equals` (زمن ثابت).
   - نافذة زمنية **300 ثانية** على timestamp التوقيع (منع replay). خارجها → رفض.
   - توقيع غير صالح → **HTTP 400** فوراً، بلا معالجة.
3. de‑dupe: `insert` في `pos_payment_webhook_events` بـ `delivery_id` فريد.
   - لو موجود مسبقاً → أرجع **200** دون إعادة معالجة (idempotent).
4. عالج حسب `event_type`:
   - `payment.succeeded`: جد الصف بـ `payment_intent_id`؛ إن كان `awaiting_payment/processing`
     → `completed` + `reference = payment_id`. (لو `completed` أصلاً، لا شيء.)
   - `payment.failed`: → `failed` + `failure_reason`. **لا يكتمل البيع.**
   - كل تحديث داخل `DB::transaction` + `processed_at`.
5. أرجع **2xx بسرعة** (المعالجة خفيفة؛ لا مكالمات خارجية داخل الـ handler).

---

## 7) الواجهة (Cashier UX)

- زر **Card** يصبح فعّالاً (`onclick="POS.payCard()"`)، ونحذف "soon".
- عند الضغط:
  1. `busy = true` (يمنع الضغط المزدوج بصرياً — والـ idempotency يمنعه خادمياً، انظر §8).
  2. `POST /pos/card/start` → يرجّع `transaction_id`.
  3. عرض حالة **"جارٍ انتظار الكارت على الجهاز…"** (overlay جديد مشابه لـ success modal).
  4. **Polling** كل ~2ث على `GET /pos/card/{id}/status` حتى:
     - `completed` → نفس `showSuccess()` الحالي.
     - `failed` → رسالة "فشل الدفع: {السبب}" مع زر "حاول مجدداً".
     - انتهاء مهلة (مثلاً 90ث بلا نتيجة) → "قيد الانتظار — تحقّق يدوياً"، **لا تُعتبر مدفوعة**،
       ولا تُمسح السلة.
- السلة لا تُفرّغ إلا عند `completed` فقط.

---

## 8) السيناريوهات ومعالجتها (مطلوبة صراحةً)

| السيناريو | المعالجة |
|---|---|
| **الجهاز غير متصل / لا reader** | فشل الخطوة 2/4 يرجّع خطأ واضح؛ المعاملة تُترك `awaiting_payment` أو تُلغى `canceled`؛ رسالة "الجهاز غير متصل". لا خصم. |
| **العميل ألغى / الكارت مرفوض** (`card_declined`, `insufficient_funds`) | يصل webhook `payment.failed` → `status=failed` + `failure_reason`؛ الواجهة تعرض السبب وتتيح إعادة المحاولة (معاملة جديدة). |
| **الـ webhook تأخّر / لم يصل** | الواجهة بعد المهلة → "قيد الانتظار". وظيفة **reconciliation** مجدولة تستدعي `retrievePayment(payment_intent_id)` وتوفّق الحالة. لا تُعتبر مدفوعة قبل ذلك. |
| **انقطاع الشبكة بين الخطوات** | كل خطوة داخل try/catch؛ عند الفشل تبقى المعاملة `awaiting_payment` مع `payment_intent_id` محفوظاً (إن وُجد) → قابلة للتصالح. لا نُنشئ دفعة ثانية للمبلغ نفسه (الـ Idempotency‑Key يحمي). |
| **ضغط Card مرتين** | **Idempotency‑Key = uuid المعاملة** يُرسل لـ create-payment؛ نفس المفتاح لا يُنشئ دفعتين لدى PlutoPay. خادمياً: `start` يُعاد استخدام نفس الصف `awaiting_payment` بدل إنشاء صف جديد إن كانت هناك دفعة نشطة لنفس السلة/الجلسة. النتيجة: **خصم واحد فقط**. |
| **المبلغ < 50 سنت** | يُرفض في `start` **قبل** أي مكالمة PlutoPay → 422 "الحد الأدنى للدفع بالكارت 0.50$". |
| **نجح في PlutoPay وفشل تحديث قاعدتنا** | الـ webhook idempotent؛ PlutoPay يعيد الإرسال عند عدم 2xx → نعالجه لاحقاً. زائداً وظيفة reconciliation دورية تلتقط أي `payment_intent_id` نجح لدى PlutoPay ولم يُعلَّم عندنا وتوفّقه. سجل webhook الخام محفوظ للتدقيق. |

---

## 9) الاختبار (TEST MODE فقط)

**اختبارات آلية (PHPUnit):**
1. `CartCalculator` → `total_cents` صحيح (موجود؛ نضيف حالات حدّية للكارت والحد 50 سنت).
2. `PlutoPaySignature`: توقيع **صالح** يُقبل، **غير صالح** يُرفض، **منتهي (خارج 300ث)** يُرفض.
3. webhook **idempotency**: نفس `delivery_id` مرتين → معالجة واحدة، لا تغيير مزدوج.
4. `payment.succeeded` → `completed`؛ `payment.failed` → `failed`.
5. **الفاتورة لا تُعتبر مدفوعة إلا بعد succeeded** (اختبار حالة قبل/بعد الـ webhook).
6. رفض `< 50` سنت؛ رفض `card` بلا مفتاح `sk_test_`.

**اختبار يدوي (حلقة testing.html كاملة):**
تسجيل webhook تجريبي → `create-payment` → `POST /v1/terminal/simulate-payment` بكارت اختبار
(`4242…` ينجح، `4000…0002` مرفوض) → استقبال `payment.succeeded/failed` → التحقق من التوقيع →
رؤية الحالة تتحدّث في الـ POS.

🔴 **لا انتقال إلى live keys في هذه المرحلة إطلاقاً.**

---

## 10) قائمة التسليم (عند الموافقة، بهذا الترتيب)

1. migrations (توسيع `pos_transactions` + `pos_payment_webhook_events`).
2. `config/services.php` + مفاتيح `.env` + حارس `sk_test_`.
3. `PlutoPaySignature` + اختباراتها (أولاً — الأمان).
4. `PlutoPayClient`.
5. `CardPaymentController` (`start` + `status`) + Request + المسارات.
6. `PlutoPayWebhookController` + استثناء CSRF + de‑dupe.
7. وظيفة reconciliation (command/scheduled).
8. الواجهة: تفعيل زر Card + overlay انتظار + polling.
9. الحلقة الكاملة بالمحاكي + بقية الاختبارات الآلية.

---

### أسئلة مفتوحة قبل الكود (تحتاج تأكيدكم)
1. **`reader_id` / `terminal_id`**: ثابتان في `.env` (جهاز واحد) أم يُختاران من الواجهة؟ (الخطة تفترض ثابتين.)
2. **الـ polling** مقبول، أم تفضّلون SSE/websocket؟ (الخطة تفترض polling — أبسط وأمتن على الاستضافة المشتركة.)
3. **مهلة انتظار الكارت** في الواجهة: 90 ثانية مناسبة؟
4. **عنوان الـ webhook العام** الذي سنسجّله في لوحة PlutoPay: `https://nexobarbers.com/webhooks/plutopay` — صحيح؟

**بانتظار موافقتكم على هذه الخطة قبل كتابة أي كود.**
