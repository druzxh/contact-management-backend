<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Models\Contact;
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
            $contacts = GroupContact::where('group_users_code', $user->users_code)
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

    public function detailContactGroup($group_code)
    {
        if (Auth::check()) {
            $users_code = Auth::user()->users_code;
            $groupContact = GroupContact::where('group_code', $group_code)
                ->where('group_users_code', $users_code)
                ->where('isDelete', 0)
                ->first();

            if (!$groupContact) {
                return $this->sendError(3, 'Contact not found', []);
            }
            $totalContacts = $groupContact->group_total;

            return $this->sendResponse(1, 'Group Contact details retrieved successfully', $groupContact);
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

            $groupContact = GroupContact::where('group_code', $request->group_code)
                ->where('isDelete', 0)
                ->first();
            $user_code = Auth::user()->users_code;

            if (!$groupContact) {
                return $this->sendError(3, 'Contact not found', []);
            }

            $groupContact->group_users_code = $user_code;
            $groupContact->group_name = $request->group_name;
            $groupContact->group_description = $request->group_description;
            $groupContact->save();

            return $this->sendResponse(1, 'Group Contact updated successfully', $groupContact);
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
                return $this->sendError(3, 'Group Contact not found', []);
            }

            $groupContact->update(['isDelete' => 1]);

            return $this->sendResponse(1, 'Group Contact deleted successfully');
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }

    public function insertContactToGroup(Request $request)
    {
        if (Auth::check()) {
            $user_code = Auth::user()->users_code;
            $group_code = $request->group_code;
            $contact_code = $request->contact_code;
            $group = GroupContact::where('group_code', $group_code)
                ->where('group_users_code', $user_code)
                ->first();

            $contact = Contact::where('contact_code', $contact_code)
                ->where('contact_users_code', $user_code)
                ->first();

            if (!$group || !$contact) {
                return $this->sendError(3, 'Group or Contact not found', []);
            }

            if ($group->contacts()->where('contact_id', $contact->id)->exists()) {
                return $this->sendResponse(1, 'Contact is already in the group');
            }

            $group->contacts()->attach($contact->id, [
                'contact_code' => $contact->contact_code,
                'group_code' => $group->group_code,
            ]);

            return $this->sendResponse(1, 'Contact added to group successfully');
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }

    public function removeContactFromGroup(Request $request)
    {
        if (Auth::check()) {
            $user_code = Auth::user()->users_code;
            $group_code = $request->group_code;
            $contact_code = $request->contact_code;
            $group = GroupContact::where('group_code', $group_code)
                ->where('group_users_code', $user_code)
                ->first();

            $contact = Contact::where('contact_code', $contact_code)
                ->where('contact_users_code', $user_code)
                ->first();

            if (!$group || !$contact) {
                return $this->sendError(3, 'Group or Contact not found', []);
            }

            $group->contacts()->detach($contact);

            return $this->sendResponse(1, 'Contact removed from group successfully');
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }


}