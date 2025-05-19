<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\CreateContactRequest;
use App\Mail\ContactReplyMail;
use App\Models\Section;
use App\Models\Contact;
use App\Models\UserMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\SaveImageTrait;
use Exception;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view_contacts|add_contacts', ['only' => ['index', 'store']]);
        $this->middleware('permission:add_contacts', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_contacts', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_contacts', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('admin.contacts.index');
    }

    public function datatable(Request $request) 
    {
        $items = UserMessages::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        return view('admin.contacts.create');
    }

    public function store(CreateContactRequest $request)
    {
        try {
            DB::beginTransaction();
       
            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function edit($id)
    {
        $contact = UserMessages::findOrFail($id);
        return view('admin.contacts.create', compact('contact'));
    }
    public function update(CreateContactRequest $request, $id)
    {
        try {
            DB::beginTransaction();
          
            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }
    public function destroy($id)
    {
        UserMessages::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }

    public function reply(Request $request, UserMessages $contact)
    {
        $request->validate([
            'reply' => 'required|string',
        ]);

        Mail::to($contact->email)->send(new ContactReplyMail($contact, $request->reply));

        return back()->with('success', 'Reply sent successfully.');
    }
}
