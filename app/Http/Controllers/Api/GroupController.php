<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Models\GroupContact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GroupController extends ApiController
{
    public function addContactGroup(Request $request)
    {
        if (Auth::check()) {
            $rules = [
                'group_name' => 'required|string|max:255',
                'group_description' => 'required|string|max:255',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->sendError(1, 'Validate erorr', $validator->errors());
            }

            $users_code = Auth::user()->users_code;
            $groupContact = new GroupContact([
                'group_code' => generateCode('GROUPCONTACTS'),
                'group_users_code' => $users_code,
                'group_name' => $request->group_name,
                'group_description' => $request->group_description,
            ]);

            $groupContact->save();
            return $this->sendCreatedResponse(1, 'Data created Successfully');

        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }

    }

    public function allContactGroup(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $contacts = GroupContact::where('group_users_code', $user->users_code)->get();

            return $this->sendResponse(1, 'Data retrieved Successfully', $contacts);

        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }

    public function detailContactGroup($group_code)
    {
        if (Auth::check()) {
            $users_code = Auth::user()->users_code;
            $groupContact = GroupContact::where('group_code', $group_code)->where('group_users_code', $users_code)->first();

            if (!$groupContact) {
                return $this->sendError(3, 'Contact not found', []);
            }
            $totalContacts = $groupContact->group_total;

            return $this->sendResponse(1, 'Contact details retrieved successfully', $groupContact);
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }

    public function updateContactGroup(Request $request)
    {
        if (Auth::check()) {
            $rules = [
                'group_name' => 'required|string|max:255',
                'group_description' => 'required|string|max:255',
                'group_code' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->sendError(1, 'Validation error', $validator->errors());
            }

            $groupContact = GroupContact::where('group_code', $request->group_code)->first();
            $user_code = Auth::user()->users_code;

            if (!$groupContact) {
                return $this->sendError(3, 'Contact not found', []);
            }

            $groupContact->group_users_code = $user_code;
            $groupContact->group_name = $request->group_name;
            $groupContact->group_description = $request->group_description;
            $groupContact->save();

            return $this->sendResponse(1, 'Contact updated successfully', $groupContact);
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }

    public function deleteContactGroup(Request $request)
    {
        if (Auth::check()) {
            $rules = [
                'group_code' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->sendError(1, 'Validation error', $validator->errors());
            }

            $groupContact = GroupContact::where('group_code', $request->group_code)->first();

            if (!$groupContact) {
                return $this->sendError(3, 'Contact not found', []);
            }

            $groupContact->delete();

            return $this->sendResponse(1, 'Contact deleted successfully');
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }
}