<?php

namespace App\Auth;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;

class OfflineUserProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     * Overridden to handle database connection failures.
     */
    public function retrieveById($identifier)
    {
        try {
            return parent::retrieveById($identifier);
        } catch (\Throwable $e) {
            Log::warning('Offline POS: Database connection failed during Auth. Providing dummy user.', ['error' => $e->getMessage()]);
            
            // Return a dummy user object so the app doesn't crash
            $model = $this->createModel();
            $model->id = $identifier;
            $model->name = 'Offline Admin';
            $model->email = 'admin@example.com';
            $model->role = 'admin'; // Assuming admin role for offline access
            $model->exists = true;
            
            return $model;
        }
    }

    /**
     * Retrieve a user by the given credentials.
     * Overridden to handle database connection failures.
     */
    public function retrieveByCredentials(array $credentials)
    {
        try {
            return parent::retrieveByCredentials($credentials);
        } catch (\Throwable $e) {
            // If DB is down during login, we can't really "login" unless we have a local cache of credentials.
            // For now, let's just let it fail or log it.
            Log::error('Offline POS: Database connection failed during Login attempt.');
            throw $e;
        }
    }
}
