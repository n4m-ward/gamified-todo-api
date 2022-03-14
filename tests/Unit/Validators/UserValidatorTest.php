<?php

namespace Tests\Unit\Validators;

use App\Dto\UserDto;
use App\Models\User;
use App\Validators\UserValidator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserValidatorTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var UserDto
     */
    private UserDto $userDto;
    /**
     * @var UserValidator
     */
    private UserValidator $userValidator;

    public function setUp(): void
    {
        parent::setUp();
        $this->userDto = new UserDto();
        $this->userValidator = new UserValidator();
    }

    public function testValidateCreateAccountReturnTrueIfOneParamIsNull(): void
    {
        $errorMessage = 'Error: Parametro [email] invalido!';
        $params = [
          'name' => 'name test',
          'password' => '********',
          'confirmPassword' => '********',
        ];
        $this->userDto->attachValues($params);
        $result = $this->userValidator::validateCreateAccount($this->userDto);

        $this->assertTrue($result['error']);
        $this->assertEquals($result['message'], $errorMessage);
    }

    public function testValidateCreateAccountReturnTruefOneParamIsEmpty(): void
    {
        $errorMessage = 'Error: Parametro [email] invalido!';
        $params = [
            'name' => 'name test',
            'email' => '',
            'password' => '********',
            'confirmPassword' => '********',
        ];
        $this->userDto->attachValues($params);
        $result = $this->userValidator::validateCreateAccount($this->userDto);

        $this->assertTrue($result['error']);
        $this->assertEquals($result['message'], $errorMessage);
    }

    public function testValidateCreateAccountReturnTrueifEmaildoesNotHaveArroba(): void
    {
        $errorMessage = 'Error: Parametro [email] invalido!';
        $params = [
            'name' => 'name test',
            'email' => 'testemail.com',
            'password' => '********',
            'confirmPassword' => '********',
        ];
        $this->userDto->attachValues($params);
        $result = $this->userValidator::validateCreateAccount($this->userDto);

        $this->assertTrue($result['error']);
        $this->assertEquals($result['message'], $errorMessage);
    }

    public function testValidateCreateAccountReturnTrueifEmaildoesNotHaveDot(): void
    {
        $errorMessage = 'Error: Parametro [email] invalido!';
        $params = [
            'name' => 'name test',
            'email' => 'test@emailcom',
            'password' => '********',
            'confirmPassword' => '********',
        ];
        $this->userDto->attachValues($params);
        $result = $this->userValidator::validateCreateAccount($this->userDto);

        $this->assertTrue($result['error']);
        $this->assertEquals($result['message'], $errorMessage);
    }

    public function testValidateCreateAccountReturnTrueIfOneParamIsAnBackSpace(): void
    {
        $errorMessage = 'Error: Parametro [email] invalido!';
        $params = [
            'name' => 'name test',
            'email' => ' ',
            'password' => '********',
            'confirmPassword' => '********',
        ];
        $this->userDto->attachValues($params);
        $result = $this->userValidator::validateCreateAccount($this->userDto);

        $this->assertTrue($result['error']);
        $this->assertEquals($result['message'], $errorMessage);
    }

    public function testValidateCreateAccountReturnFalseIfAllParamsAreValid(): void
    {
        $message = 'Usuario cadastrado!';
        $params = [
            'name' => 'name test',
            'email' => 'valid@email.com',
            'password' => '********',
            'confirmPassword' => '********',
        ];
        $this->userDto->attachValues($params);
        $result = $this->userValidator::validateCreateAccount($this->userDto);

        $this->assertFalse($result['error']);
        $this->assertEquals($result['message'], $message);
    }

    public function testValidateCreateAccountReturnTrueIfPasswordsDoesntMatch(): void
    {
        $errorMessage = 'Error: Senhas não conferem!';
        $params = [
            'name' => 'name test',
            'email' => 'test@email.com',
            'password' => 'senha',
            'confirmPassword' => 'senha2',
        ];
        $this->userDto->attachValues($params);
        $result = $this->userValidator::validateCreateAccount($this->userDto);

        $this->assertTrue($result['error']);
        $this->assertEquals($result['message'], $errorMessage);
    }

    public function testValidateLoginReturnTrue(): void
    {
        $params = [
            'email' => 'test@gmail.com',
            'password' => 'testPassword',
        ];
        User::factory()->create([
            'email' => $params['email'],
            'password' => md5($params['password']),
        ]);
        $this->userDto->attachValues($params);
        $result = $this->userValidator::validateLogin($this->userDto);

        $this->assertTrue($result);
    }

    public function testValidateLoginReturnFalseIfUserDoesNotExistInDatabase(): void
    {
        $params = [
            'email' => 'test@gmail.com',
            'password' => 'testPassword',
        ];
        $this->userDto->attachValues($params);
        $result = $this->userValidator::validateLogin($this->userDto);

        $this->assertFalse($result);
    }

    public function testValidateLoginReturnFalseIfPasswordIsIncorrect(): void
    {
        $params = [
            'email' => 'test@gmail.com',
            'password' => 'testPassword',
        ];
        User::factory()->create([
            'email' => $params['email'],
            'password' => md5('wrongPassword'),
        ]);
        $this->userDto->attachValues($params);
        $result = $this->userValidator::validateLogin($this->userDto);

        $this->assertFalse($result);
    }

    public function testValidateLoginReturnFalseIfEmailIsNull(): void
    {
        $params = [
            'password' => 'testPassword',
        ];
        $this->userDto->attachValues($params);
        $result = $this->userValidator::validateLogin($this->userDto);

        $this->assertFalse($result);
    }

    public function testValidateLoginReturnFalseIfPasswordIsNull(): void
    {
        $params = [
            'email' => 'test@gmail.com',
        ];
        $this->userDto->attachValues($params);
        $result = $this->userValidator::validateLogin($this->userDto);

        $this->assertFalse($result);
    }
}
