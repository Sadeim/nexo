<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * People, as opposed to bookings.
 *
 * Until now a booking carried a name and an email and that was the whole
 * record of a customer: book twice and you were two strangers. This is the
 * row that survives between appointments, and the thing an SMS list is made
 * of.
 *
 * The phone is the identity. It is the one detail a customer always has, it
 * is the thing a text message is sent to, and it is unique here on purpose —
 * two rows for one number would mean two halves of a history and one of them
 * silently missing from the list.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Digits only, normalised through App\Support\Phone before it
            // ever reaches here, so '(224) 669-8063' and '+1 224 669 8063'
            // cannot become two customers.
            $table->string('phone', 32)->unique();

            /*
             * Nullable, which the booking form is not.
             *
             * Step one of the modal asks for a phone and nothing else, and
             * writes the row immediately — the whole point being to keep the
             * number of someone who then changes their mind. That row has no
             * name yet. The form still requires a first name before a booking
             * is made; the column cannot, or the number would be thrown away
             * at exactly the moment it is worth most.
             */
            $table->string('first_name', 120)->nullable();
            $table->string('last_name', 120)->nullable();
            $table->string('email')->nullable();

            /*
             * Consent, and the evidence for it. Unchecked by default and
             * written only when the customer ticks the box, with the time and
             * the place it was given — "we have their number" and "we may
             * text them" are different facts and this shop needs to be able
             * to tell them apart years later.
             */
            $table->boolean('sms_marketing_consent')->default(false);
            $table->timestamp('sms_consent_at')->nullable();
            $table->string('consent_source', 64)->nullable();

            // Denormalised from bookings so a list of 5,000 customers does
            // not become 5,000 counting queries.
            $table->unsignedInteger('bookings_count')->default(0);
            $table->timestamp('last_booked_at')->nullable();

            $table->timestamps();

            // "Who have we not seen in a while" is the question this table
            // exists to answer.
            $table->index('last_booked_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
