<?php

// *************************************************************************

// *                                                                       *

// * iBilling -  Accounting, Billing Software                              *

// * Copyright (c) Sadia Sharmin. All Rights Reserved                      *

// *                                                                       *

// *************************************************************************

// *                                                                       *

// * Email: sadiasharmin3139@gmail.com                                                *

// * Website: http://www.sadiasharmin.com                                  *

// *                                                                       *

// *************************************************************************

// *                                                                       *

// * This software is furnished under a license and may be used and copied *

// * only  in  accordance  with  the  terms  of such  license and with the *

// * inclusion of the above copyright notice.                              *

// * If you Purchased from Codecanyon, Please read the full License from   *

// * here- http://codecanyon.net/licenses/standard                         *

// *                                                                       *

// *************************************************************************

class Password
{
    // Use strong bcrypt for new/rehash
    private const ALG = PASSWORD_BCRYPT;
    private const OPTIONS = ['cost' => 12];

    // Identify whether a hash is modern (bcrypt/argon) vs old crypt()
    public static function isModernHash(string $hash): bool
    {
        // Modern PHP hashes start with $2y$, $2a$, $2b$, $argon2i$, $argon2id$, etc.
        return (strpos($hash, '$2y$') === 0)      ||
               (strpos($hash, '$2a$') === 0)      ||
               (strpos($hash, '$2b$') === 0)      ||
               (strpos($hash, '$argon2') === 0);
    }

    // Create a modern hash
    public static function _crypt(string $password): string
    {
        return password_hash($password, self::ALG, self::OPTIONS);
    }

    // Verify against either modern or legacy hash
    public static function _verify(string $user_input, string $stored_hash): bool
    {
        if (self::isModernHash($stored_hash)) {
            // Modern path
            return password_verify($user_input, $stored_hash);
        }

        // Legacy path (old crypt with fixed/short salt). IMPORTANT:
        // Old DES-based crypt uses ONLY the first 8 chars -> your bug.
        // We must still support it for existing users, but we will migrate on success.
        // For verification, provide the *stored hash* as the salt parameter as before.
        return (crypt($user_input, $stored_hash) === $stored_hash);
    }

    // Whether the stored hash should be rehashed to modern
    public static function needsRehash(string $stored_hash): bool
    {
        if (!self::isModernHash($stored_hash)) {
            return true;
        }
        return password_needs_rehash($stored_hash, self::ALG, self::OPTIONS);
    }

    // Generate an 8-char strong-ish random password (kept as-is from your code)
    public static function _gen(): string
    {
        return substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz@#!123456789', 8)), 0, 8);
    }
}