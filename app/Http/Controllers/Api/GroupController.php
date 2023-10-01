<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GroupController extends ApiController
{
    public function addGroup(Request $request)
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
            $group = new Group([
                'group_code' => generateCode('GROUPS'),
                'group_users_code' => $users_code,
                'group_name' => $request->group_name,
                'group_description' => $request->group_description,
            ]);

            $group->save();
            return $this->sendCreatedResponse(1, 'Data created Successfully', $group);

        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }

    }

    public function allGroup(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $group = Group::where('group_users_code', $user->users_code)
                ->where('is_delete', 0)
                ->with('contacts')
                ->get();

            // dd($group);
            return $this->sendResponse(1, 'Data retrieved Successfully', $group);

        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }

    public function detailGroup($group_code)
    {
        if (Auth::check()) {
            $users_code = Auth::user()->users_code;
            $group = Group::where('group_code', $group_code)
                ->where('group_users_code', $users_code)
                ->where('is_delete', 0)
                ->with('contacts')
                ->first();

            if (!$group) {
                return $this->sendError(3, ' not found', []);
            }

            return $this->sendResponse(1, 'Group  details retrieved successfully', $group);
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }

    public function updateGroup(Request $request)
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

            $group = Group::where('group_code', $request->group_code)
                ->where('is_delete', 0)
                ->first();
            $user_code = Auth::user()->users_code;

            if (!$group) {
                return $this->sendError(3, ' not found', []);
            }

            $group->group_users_code = $user_code;
            $group->group_name = $request->group_name;
            $group->group_description = $request->group_description;
            $group->save();

            return $this->sendResponse(1, 'Group updated successfully', $group);
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }

    public function deleteGroup(Request $request)
    {
        if (Auth::check()) {
            $rules = [
                'group_code' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->sendError(1, 'Validation error', $validator->errors());
            }

            $group = Group::where('group_code', $request->group_code)->first();

            if (!$group) {
                return $this->sendError(3, 'Group  not found', []);
            }

            $group->update(['is_delete' => 1]);

            return $this->sendResponse(1, 'Group  deleted successfully', $group);
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
            $group = Group::where('group_code', $group_code)
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

            $contact_group[] = [
                'contact_group' => $group,
                'contact' => $contact
            ];

            return $this->sendResponse(1, 'Contact added to group successfully', $contact_group);
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
            $group = Group::where('group_code', $group_code)
                ->where('group_users_code', $user_code)
                ->first();

            $contact = Contact::where('contact_code', $contact_code)
                ->where('contact_users_code', $user_code)
                ->first();

            if (!$group || !$contact) {
                return $this->sendError(3, 'Group or Contact not found', []);
            }

            if (!$group->contacts()->where('contact_id', $contact->id)->exists()) {
                return $this->sendResponse(1, 'Contact not found in the group');
            }

            $group->contacts()->detach($contact);

            $contact_group[] = [
                'contact_group' => $group,
                'contact' => $contact
            ];

            return $this->sendResponse(1, 'Contact removed from group successfully', $contact_group);
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }


}