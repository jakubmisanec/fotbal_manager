<?php

declare(strict_types=1);

namespace App\Model\SiteAuthenticator;

use \Nette\Security\IAuthenticator;

class UserAuthenticator implements IAuthenticator {
    
    private $database;
    private $passwords;
    
    private const
        TABLE_NAME = 'users',
        COLUMN_ID = 'id',
        COLUMN_NAME = 'username',
        COLUMN_PASSWORD_HASH = 'password',
        COLUMN_EMAIL = 'email',
        COLUMN_ROLE = 'role';

    public function __construct(\Nette\Database\Context $database, \Nette\Security\Passwords $passwords) {
        $this->database = $database;
	$this->passwords = $passwords;
    }


    public function authenticate(array $credentials): \Nette\Security\IIdentity {
            $username = $credentials[0];
            $password = null;
            if (count($credentials) > 1) {
                $password = $credentials[1];
            }

            $row = $this->database->table(self::TABLE_NAME)
                    ->where(self::COLUMN_NAME, $username)
                    ->fetch();

            if (!$row) {
                    throw new \Nette\Security\AuthenticationException('Uživatelské jméno je neplatné!');
            }

            if ($password != null) {
                if (!$this->passwords->verify($password, $row->password)) {
                        throw new \Nette\Security\AuthenticationException('Heslo je neplatné!');
                }
            }

            return new \Nette\Security\Identity(
                    $row->id,
                    $row->role,
                    ['username' => $row->username, 'timeshift' => $row->timeshift, 'money' => $row->money, 'email' => $row->email]
            );
    }

}