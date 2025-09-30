<?php

namespace VM\Application\User\Persistence\Seeder;

use mysqli;
use VM\Application\User\Business\UserFacade;
use VM\Application\User\Persistence\Shared\Transfer\UserCriteriaTransfer;
use VM\Application\User\Persistence\Shared\Transfer\UserTransfer;
use VM\Domain\Enums\UserRoleEnum;
use VM\Infrastructure\Core\Database\Seeder\SeederInterface;

class UserSeeder implements SeederInterface
{
    public function run(mysqli $conn): void
    {
        $users = [
            ["Manager One", "manager", '', "manager@example.com", "secret123456", "manager"],
            ["Employee One", "employee", "12345678", "employee@example.com", "secret12346", "employee"],
        ];

        $stmt = $conn->prepare("
            INSERT INTO users (full_name, username, code, email, password, role)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE id=id
        ");

        foreach ($users as [$fullName, $username, $code, $email, $plainPassword, $role]) {
            $password = password_hash($plainPassword, PASSWORD_BCRYPT);
            $stmt->bind_param("ssssss", $fullName, $username, $code, $email, $password, $role);
            $stmt->execute();
        }

        $stmt->close();
    }
}