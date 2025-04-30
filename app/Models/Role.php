<?php

namespace App\Models;

use App\Http\Resources\Admin\RoleResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;

    public $resource = RoleResource::class;
    
    public function scopeSearch($query, $request)
    {
        if (!empty($request->search['value'])) {
            $search = '%' . $request->search['value'] . '%';
            return $query->where('name', 'LIKE' , '%' . $search . '%');
        }
        return $query;
    }

 

}
