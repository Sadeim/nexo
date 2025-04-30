<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Newsletter;
use Illuminate\Support\Facades\DB;

class NewsletterController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_newsletters|add_newsletters', ['only' => ['index','store']]);
        $this->middleware('permission:add_newsletters', ['only' => ['create','store']]);
        $this->middleware('permission:edit_newsletters', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_newsletters', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('admin.newsletters.index');
    }

    public function datatable(Request $request)
    {
        $items = Newsletter::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {

    }

    public function store(Request $request)
    {
        
    }

    public function edit($id)
    {
         
    }

    public function update(Request $request, $id)
    {
 
    }

    public function destroy($id)
    {
        Newsletter::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'));
    }

    public function bluckDestroy(Request $request)
    {
        $ids = $request->id;
        Newsletter::destroy($ids);
        return $this->response_api(200, __('admin.form.deleted_successfully'));
    }
}
