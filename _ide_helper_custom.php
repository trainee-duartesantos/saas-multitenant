<?php
namespace Illuminate\Support\Facades {
    /**
     * @see \Illuminate\Auth\AuthManager
     * @see \Illuminate\Contracts\Auth\Factory
     * @see \Illuminate\Contracts\Auth\Guard
     * @see \Illuminate\Contracts\Auth\StatefulGuard
     */
    class Auth {
        /**
         * Get the currently authenticated user.
         *
         * @return \App\Models\User|null
         */
        public static function user() {}
        
        /**
         * Determine if the current user is authenticated.
         *
         * @return bool
         */
        public static function check() {}
    }
}

namespace Illuminate\Http {
    class Request {
        /**
         * Get the user making the request.
         *
         * @return \App\Models\User|null
         */
        public function user() {}
    }
}