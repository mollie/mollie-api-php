<?php

use PHPUnit\Framework\TestCase;
use Mollie\Api\Http\Data\EmailDetails;

class EmailDetailsTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created_from_array()
    {
        $object = EmailDetails::fromArray($data = [
            'subject' => 'Test Subject',
            'body' => 'Test Body',
        ]);

        $this->assertInstanceOf(EmailDetails::class, $object);

        foreach ($data as $key => $value) {
            $this->assertSame($value, $object->$key);
        }
    }
}
