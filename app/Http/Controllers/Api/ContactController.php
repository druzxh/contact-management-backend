<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContactController extends ApiController
{
    public function addContact(Request $request)
    {
        if (Auth::check()) {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email',
                'phone' => 'required',
                'company' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->sendError(1, 'Validate erorr', $validator->errors());
            }

            $users_code = Auth::user()->users_code;
            $contact = new Contact([
                'contact_code' => generateCode('CONTACTS'),
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'contact_social_code' => generateCode('SOCIALCONTACT'),
                'contact_users_code' => $users_code,
                'contact_group_code' => $request->contact_group_code
            ]);

            $contact->save();
            return $this->sendCreatedResponse(1, 'Data created Successfully');

        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }

    }

    public function allContact(Request $request)
    {
        if (Auth::check()) {
            $user_code = Auth::user()->users_code;
            $contacts = Contact::where('contact_users_code', $user_code)
                ->where('isDelete', 0)
                ->get();
            return $this->sendResponse(1, 'Data retrieved Successfully', $contacts);

        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }

    public function detailContact($contact_code)
    {
        if (Auth::check()) {
            $user_code = Auth::user()->users_code;
            $contact = Contact::where('contact_users_code', $user_code)
                ->where('contact_code', $contact_code)
                ->where('isDelete', 0)
                ->first();

            if (!$contact) {
                return $this->sendError(3, 'Contact not found', []);
            }

            return $this->sendResponse(1, 'Contact details retrieved successfully', $contact);
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }

    public function updateContact(Request $request)
    {
        if (Auth::check()) {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email',
                'phone' => 'required',
                'company' => 'required',
                'contact_code' => 'required',
                'contact_group_code' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->sendError(1, 'Validation error', $validator->errors());
            }

            $user_code = Auth::user()->users_code;
            $contact = Contact::where('contact_users_code', $user_code)
                ->where('contact_code', $request->contact_code)
                ->where('isDelete', 0)
                ->first();

            if (!$contact) {
                return $this->sendError(3, 'Contact not found', []);
            }

            $contact->name = $request->name;
            $contact->email = $request->email;
            $contact->phone = $request->phone;
            $contact->company = $request->company;
            $contact->contact_group_code = $request->contact_group_code;
            $contact->save();

            return $this->sendResponse(1, 'Contact updated successfully', $contact);
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }

    public function deleteContact(Request $request)
    {
        if (Auth::check()) {
            $rules = [
                'contact_code' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->sendError(1, 'Validation error', $validator->errors());
            }

            $user_code = Auth::user()->users_code;
            $contact = Contact::where('contact_users_code', $user_code)
                ->where('contact_code', $request->contact_code)
                ->first();

            if (!$contact) {
                return $this->sendError(3, 'Contact not found', []);
            }

            $contact->update(['isDelete' => 1]);

            return $this->sendResponse(1, 'Contact deleted successfully');
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }
}