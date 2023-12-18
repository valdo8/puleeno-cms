<?php

use Phpmig\Migration\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $this->table('users', function ($table) {
            $table->increments('id');
            $table->string('username', 255);
            $table->string('password', 255);
            $table->string('email', 255);
            $table->string('name', 255);
            $table->string('avatar', 255);
            $table->timestamps();
        });
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $this->dropTable('users');
    }
}
