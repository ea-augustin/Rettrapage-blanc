<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $encoder;

    /**
     * UserFixtures constructor.
     * @param UserPasswordHasherInterface $encoder
     */
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {
        $users = [
            [
                "username" => "user",
                "firstname" => "user",
                "lastname" => "user",
                "age" => DateTime::createFromFormat("Y-m-d", "1995-06-06"),
                "password" => "user",
                "email" => "test@gmail.com",
                "isAdmin" => false
            ],
            [
                "username" => "admin",
                "firstname" => "admin",
                "lastname" => "admin",
                "age" => DateTime::createFromFormat("Y-m-d", "1984-06-06"),
                "password" => "admin",
                "email" => "admin@gmail.com",
                "isAdmin" => true

            ]
        ];

        foreach ($users as $user) {
            $object = new User();
            $object->setUsername($user["username"]);
            $object->setFirstname($user["firstname"]);
            $object->setLastname($user["lastname"]);
            $object->setAge($user["age"]);
            $object->setEmail($user["email"]);
            $object->setPassword($this->encoder->hashPassword($object, $user["password"]));

            if ($user["isAdmin"]) {
                $object->setRoles(["ROLE_ADMIN"]);
            }else{
                $object->setRoles(["ROLE_USER"]);
            }
            $this->addReference("user_" . $user["username"], $object);

            $manager->persist($object);
        }

        $manager->flush();
    }
}
