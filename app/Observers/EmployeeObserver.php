<?php

namespace App\Observers;

use App\Models\Employee;

/**
 * Keeps the cached public-barber list honest.
 *
 * The list is cached for ten minutes because it is read on every page. Ten
 * minutes is far too long to wait for a barber the owner has just published,
 * or — worse — to keep showing one they have just hidden or deleted. So any
 * write to an employee drops the cache and the next request rebuilds it.
 *
 * Deliberately not narrowed to "only when public_profile changed": `saved`
 * also fires for a rename, a new photo or a sort-order change, all of which
 * the cached rows carry. Dropping a cheap cache entry too often costs one
 * query; dropping it too rarely shows the public the wrong thing.
 */
class EmployeeObserver
{
    public function saved(Employee $employee): void
    {
        Employee::forgetPublicBarbersCache();
    }

    public function deleted(Employee $employee): void
    {
        Employee::forgetPublicBarbersCache();
    }
}
