<?php

namespace Amethyst\Tests\Managers;

class AttributeSchemaLongTextTest extends AttributeSchemaCommonTest
{
    public function testBasicLongText()
    {
        $this->resetFields();

        $this->commonField('text', 'LongText', ['Ah yes, a text']);

        $this->resetFields();
    }
}
