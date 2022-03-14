<?php

namespace Tests\Unit\Dto;

use Tests\TestCase;

class DtoUnitTest extends TestCase
{
    /**
     * @var DtoForTest
     */
    private $dto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dto = new DtoForTest();
    }

    public function testAttachValuesWorks(): void
    {
        $name = 'test';
        $email = 'test@gmail.com';
        $this->dto->attachValues([
            'name' => $name,
            'email' => $email,
        ]);

        $this->assertEquals($this->dto->name, $name);
        $this->assertEquals($this->dto->email, $email);
    }

    public function testAttachValuesDoesNotWorkWithAnParamInexistent(): void
    {
        $invalidParam = 'test';
        $this->dto->attachValues([
            'invalidParam' => $invalidParam,
        ]);

        $result = $this->dto->invalidParam ?? null;

        $this->assertNull($result);
    }
}
