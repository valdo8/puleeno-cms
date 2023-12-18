<?php

namespace App\Models;

use Opis\ORM\Entity;

class User extends Entity
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $avatar;

    /**
     * @var \DateTime
     */
    public $created_at;

    /**
     * @var \DateTime
     */
    public $updated_at;

    /**
     * Get the table name.
     *
     * @return string
     */
    public function table(): string
    {
        return 'users';
    }

    /**
     * Get the primary key.
     *
     * @return string
     */
    public function primaryKey(): string
    {
        return 'id';
    }

    /**
     * Get the timestamps.
     *
     * @return bool
     */
    public function timestamps(): bool
    {
        return true;
    }

    /**
     * Get the fillable properties.
     *
     * @return array
     */
    public function fillable(): array
    {
        return [
            'username',
            'password',
            'email',
            'name',
            'avatar',
        ];
    }
}
