<?php

namespace App\Traits\Rule;

use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

trait CustomValidationRulesTrait
{
    /**
     * Check the phone with phone code is unique and doesn't soft deleted.
     * 
     * @param string $table : the table name
     * @param string $phone_code : phone code number
     * @param string $phone : phone number
     * @param string $ignore_id : id that will ignored  (this parameter will be use in update request)
     * @usage : $this->checkPhone('second_db.table_name', $this->request->get('phone_code') , $this->request->get('phone'))
     */
    public function checkPhone($table, $phone_code ,$phone, $ignore_id = null) 
    {
        $rule = Rule::unique($table)->where(function ($query) use($phone_code , $phone) {
                     $query->where('phone_code', $phone_code)->where('phone', $phone);
                    });
        if($ignore_id != null) {
            $rule = $rule->ignore($ignore_id);
        }
        return $rule;
    }
}
