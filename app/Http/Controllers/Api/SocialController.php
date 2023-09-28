<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Models\Social;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SocialController extends ApiController
{
    public function addSocial(Request $request)
    {
        if (Auth::check()) {

            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email',
                'phone' => 'required',
                'company' => 'required',
                'contact_social_code' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->sendError(1, 'Validate erorr', $validator->errors());
            }

            $contact = new Social([
                'contact_code' => generateCode('CONTACTS'),
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'contact_social_code' => $request->contact_social_code,
                'contact_users_code' => ''
            ]);

            $contact->save();

            return $this->sendCreatedResponse(1, 'Register Successfully');

        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }

    }
}