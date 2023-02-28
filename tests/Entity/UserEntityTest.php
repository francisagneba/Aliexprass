<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserEntityTest extends KernelTestCase
{
    private const EMAIL_CONSTRAIN_MESSAGE = 'L\'email "atchoum-du-974@gmail" n\'est pas valide.';
    private const NOT_BLANK_CONSTRAINT_MESSAGE = 'Veuillez saisir une valeur.';
    private const INVALID_EMAIL_VALUE = 'atchoum-du-974@gmail';
    private const VALID_EMAIL_VALUE = 'atchoum-du-974@gmail.com';
    private ValidatorInterface $validator;

    protected function setUp(): void{
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    public function testUserEntityIsValid(): void{
        $user = new User();
        $user->setEmail(self::VALID_EMAIL_VALUE);

        $this->getValidationErrors($user,0);
    }

    public function testUserEntityIsInvalidBecauseNoEmailEntered(): void{

        $user = new User();
        $user->setPassword(self::VALID_EMAIL_VALUE);
        $errors = $this->getValidationErrors($user,1);
        $this->assertEquals(self::NOT_BLANK_CONSTRAINT_MESSAGE, $errors[0]->getMessage());

    }

    private function getValidationErrors(User $user, int $numberOfExpectedErrors): ConstraintViolationList{

        $errors = $this->validator->validate($user);

        $this->assertCount($numberOfExpectedErrors, $errors);

        return $errors;

    }
}
