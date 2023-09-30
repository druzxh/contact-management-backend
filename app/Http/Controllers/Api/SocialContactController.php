<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Models\SocialContact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SocialContactController extends ApiController
{
    public function addSocialContact(Request $request)
    {
        if (Auth::check()) {

            $rules = [
                'social_name' => 'required|string|max:255',
                'social_url' => 'required|string|max:255',
                'social_contact_code' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->sendError(1, 'Validate erorr', $validator->errors());
            }

            $social = new SocialContact([
                'social_code' => generateCode('SOCIALS'),
                'social_name' => $request->social_name,
                'social_url' => $request->social_url,
                'social_contact_code' => $request->social_contact_code,
            ]);

            $social->save();

            return $this->sendCreatedResponse(1, 'Data Created Successfully');

        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }

    }

    public function allSocialContact(Request $request)
    {
        if (Auth::check()) {
            $socials = SocialContact::where('social_is_delete', 0)
                ->with('contacts')
                ->get();
            return $this->sendResponse(1, 'Data retrieved Successfully', $socials);

        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }

    public function detailSocialContact($social_code)
    {
        if (Auth::check()) {
            $social = SocialContact::where('social_code', $social_code)
                ->where('social_is_delete', 0)
                ->with('contacts')
                ->first();

            if (!$social) {
                return $this->sendError(3, 'Social Contact not found', []);
            }

            return $this->sendResponse(1, 'Social Contact details retrieved successfully', $social);
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }


    public function updateSocialContact(Request $request)
    {
        if (Auth::check()) {
            $rules = [
                'social_name' => 'required|string|max:255',
                'social_url' => 'required|string|max:255',
                'social_code' => 'required',
                'social_contact_code' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->sendError(1, 'Validation error', $validator->errors());
            }

            $social = SocialContact::where('social_code', $request->social_code)
                ->where('social_is_delete', 0)
                ->first();

            if (!$social) {
                return $this->sendError(3, 'Contact not found', []);
            }

            $social->social_name = $request->social_name;
            $social->social_url = $request->social_url;
            $social->social_contact_code = $request->social_contact_code;
            $social->save();

            return $this->sendResponse(1, 'Contact updated successfully', $social);
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }

    public function deleteSocialContact(Request $request)
    {
        if (Auth::check()) {
            $rules = [
                'social_code' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->sendError(1, 'Validation error', $validator->errors());
            }

            $social = SocialContact::where('social_code', $request->social_code)
                ->where('social_is_delete', 0)
                ->first();

            if (!$social) {
                return $this->sendError(3, 'Social Contact not found', []);
            }

            $social->update(['social_is_delete' => 1]);

            return $this->sendResponse(1, 'Social Contact deleted successfully');
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }
}