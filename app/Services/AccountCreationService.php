<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AccountCreationService
{
    /**
     * Create a user account from checkout and link it to the customer record.
     * 
     * @param Customer $customer The customer record to link
     * @param string $password The plain text password
     * @param string|null $name Optional name (defaults to customer's full name)
     * @return User The created user
     * @throws \Exception If account creation fails
     */
    public function createFromCheckout(Customer $customer, string $password, ?string $name = null): User
    {
        // Check if customer already has a user account
        if ($customer->user_id !== null) {
            throw new \Exception('This email already has an account. Please sign in instead.');
        }

        // Check if a user with this email already exists
        $existingUser = User::where('email', $customer->email)->first();
        
        if ($existingUser !== null) {
            // If user exists but customer is not linked, link them
            if ($customer->user_id === null) {
                $customer->user_id = $existingUser->id;
                $customer->save();
                
                Log::info('Linked existing user to customer', [
                    'user_id' => $existingUser->id,
                    'customer_id' => $customer->id,
                    'email' => $customer->email,
                ]);
                
                return $existingUser;
            }
            
            throw new \Exception('This email already has an account. Please sign in instead.');
        }

        // Create new user
        $userName = $name ?? trim("{$customer->first_name} {$customer->last_name}");
        
        $user = User::create([
            'name' => $userName,
            'email' => $customer->email,
            'password' => Hash::make($password),
        ]);

        // Link customer to user
        $customer->user_id = $user->id;
        $customer->save();

        Log::info('Created user account from checkout', [
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'email' => $customer->email,
        ]);

        return $user;
    }

    /**
     * Authenticate the user after account creation.
     * 
     * @param User $user The user to authenticate
     * @return void
     */
    public function authenticateUser(User $user): void
    {
        Auth::login($user);
        
        Log::info('Authenticated user after account creation', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }
}

