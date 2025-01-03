<?php

namespace App\Actions\Fortify;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Illuminate\Http\Request;
use App\Models\User;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function update($user, array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'telephone' => ['nullable', 'string', 'max:20'],
            'show_phone' => ['boolean'],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if (
            $input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail
        ) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'firstname' => $input['firstname'],
                'email' => $input['email'],
                'telephone' => $input['telephone'],
                'show_phone' => $input['show_phone'] ?? false,
            ])->save();
        }
    }

    protected function updateVerifiedUser($user, array $input)
    {
        $user->forceFill([
            'name' => $input['name'],
            'firstname' => $input['firstname'],
            'email' => $input['email'],
            'telephone' => $input['telephone'] ?? null,
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }

    protected function updateProfileInformation(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'telephone' => ['nullable', 'string', 'max:20'],
            'show_phone' => ['boolean'],
        ]);

        $user->fill($validated);
        $user->save();
    }
}
