<?php

namespace Tests\Unit\Controller;

use App\Dto\UserDto;
use App\Enums\ErrorEnum;
use App\Http\Controllers\UserController;
use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserControllerUnitTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var UserController
     */
    private UserController $userController;
    /**
     * @var UserDto
     */
    private UserDto $userDto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userDto = new UserDto();
        $this->userController = new UserController($this->userDto);
    }

    public function testCreateAccountWorks(): void
    {
        $responseMessage = 'Usuario cadastrado!';
        $createAccountParams = [
            'name' => 'test name',
            'email' => 'test@email.com',
            'password' => 'test',
            'confirmPassword' => 'test',
        ];

        $request = $this->getFakeRequest($createAccountParams, 'POST');
        $result = $this->userController->createUser($request);
        $httpCode = $result->getStatusCode();
        $result = $result->getOriginalContent();
        $userIsInserted = User::where('email', $createAccountParams['email'])
            ->where('password', md5($createAccountParams['password']))
            ->exists();

        $this->assertFalse($result['error']);
        $this->assertEquals(Response::HTTP_OK, $httpCode);
        $this->assertEquals($responseMessage, $result['message']);
        $this->assertTrue($userIsInserted);
    }

    public function testValidateCreateAccountReturnTrueIfOneParamIsNull(): void
    {
        $errorMessage = 'Error: Parametro [email] invalido!';
        $params = [
            'name' => 'name test',
            'password' => '********',
            'confirmPassword' => '********',
        ];
        $request = $this->getFakeRequest($params, 'POST');
        $result = $this->userController->createUser($request);
        $result = $result->getOriginalContent();

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
        $request = $this->getFakeRequest($params, 'POST');
        $result = $this->userController->createUser($request);
        $result = $result->getOriginalContent();

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
        $request = $this->getFakeRequest($params, 'POST');
        $result = $this->userController->createUser($request);
        $result = $result->getOriginalContent();

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
        $request = $this->getFakeRequest($params, 'POST');
        $result = $this->userController->createUser($request);
        $result = $result->getOriginalContent();

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
        $request = $this->getFakeRequest($params, 'POST');
        $result = $this->userController->createUser($request);
        $result = $result->getOriginalContent();

        $this->assertTrue($result['error']);
        $this->assertEquals($result['message'], $errorMessage);
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
        $request = $this->getFakeRequest($params, 'POST');
        $result = $this->userController->createUser($request);
        $result = $result->getOriginalContent();

        $this->assertTrue($result['error']);
        $this->assertEquals($result['message'], $errorMessage);
    }

    public function testLoginWorks(): void
    {
        $params = [
          'email' => 'test@gmail.com',
          'password' => 'passwordTest',
        ];
        User::factory()->create([
            'email' => $params['email'],
            'password' => md5($params['password']),
        ]);
        $request = $this->getFakeRequest($params, 'POST');
        $result = $this->userController->login($request)->getOriginalContent();
        $tokenExist = LoginToken::where('token', $result['token'])->exists();

        $this->assertFalse($result['error']);
        $this->assertTrue($tokenExist);
    }

    public function testLoginFailsIfUserDoesNotExistInDatabase(): void
    {
        $params = [
            'email' => 'test@gmail.com',
            'password' => 'testPassword',
        ];
        $request = $this->getFakeRequest($params, 'POST');
        $result = $this->userController->login($request)->getOriginalContent();

        $this->assertTrue($result['error']);
        $this->assertEquals(ErrorEnum::LOGIN_FAIL, $result['message']);
    }

    public function testLoginFailsIfPasswordIsIncorrect(): void
    {
        $params = [
            'email' => 'test@gmail.com',
            'password' => 'testPassword',
        ];
        User::factory()->create([
            'email' => $params['email'],
            'password' => md5('wrongPassword'),
        ]);
        $request = $this->getFakeRequest($params, 'POST');
        $result = $this->userController->login($request)->getOriginalContent();;

        $this->assertTrue($result['error']);
        $this->assertEquals(ErrorEnum::LOGIN_FAIL, $result['message']);
    }

    public function testLoginFailsIfEmailIsNull(): void
    {
        $params = [
            'password' => 'testPassword',
        ];
        $request = $this->getFakeRequest($params, 'POST');
        $result = $this->userController->login($request)->getOriginalContent();

        $this->assertTrue($result['error']);
        $this->assertEquals(ErrorEnum::LOGIN_FAIL, $result['message']);
    }

    public function testLoginFailsIfPasswordIsNull(): void
    {
        $params = [
            'email' => 'test@gmail.com',
        ];
        $request = $this->getFakeRequest($params, 'POST');
        $result = $this->userController->login($request)->getOriginalContent();

        $this->assertTrue($result['error']);
        $this->assertEquals(ErrorEnum::LOGIN_FAIL, $result['message']);
    }
}
