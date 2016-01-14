<?php

/*
 * This file is part of the FOSRestBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\RestBundle\Tests\Form\Extension;

use FOS\RestBundle\Form\Extension\BooleanExtension;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BooleanExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->formFactory = Forms::createFormFactoryBuilder()
            ->addTypeExtension(new BooleanExtension())
            ->getFormFactory();
    }

    public function testCheckboxType()
    {
        $viewTransformers = $this->createCheckboxForm()->getConfig()->getViewTransformers();

        $this->assertCount(1, $viewTransformers);
        $this->assertInstanceOf(
            'Symfony\Component\Form\Extension\Core\DataTransformer\BooleanToStringTransformer',
            $viewTransformers[0]
        );
    }

    public function testApiType()
    {
        $viewTransformers = $this->createCheckboxForm(array('type' => BooleanExtension::TYPE_API))
            ->getConfig()
            ->getViewTransformers();

        $this->assertCount(1, $viewTransformers);
        $this->assertInstanceOf(
            'FOS\RestBundle\Form\Transformer\BooleanTypeToBooleanTransformer',
            $viewTransformers[0]
        );
    }

    /**
     * @param bool  $expected
     * @param mixed $data
     *
     * @dataProvider defaultValidProvider
     */
    public function testApiTypeWithDefaultValues($expected, $data)
    {
        $form = $this->createCheckboxForm(array('type' => BooleanExtension::TYPE_API));
        $form->submit($data);

        $this->assertSame($expected, $form->getData());
    }

    /**
     * @param bool  $expected
     * @param mixed $data
     * @param array $trueValues
     * @param array $falseValues
     *
     * @dataProvider customValidProvider
     */
    public function testApiTypeWithCustomValues($expected, $data, array $trueValues, array $falseValues)
    {
        $form = $this->createCheckboxForm(array(
            'type' => BooleanExtension::TYPE_API,
            'true_values' => $trueValues,
            'false_values' => $falseValues,
        ));

        $form->submit($data);

        $this->assertSame($expected, $form->getData());
    }

    /**
     * @param mixed $data
     *
     * @dataProvider defaultInvalidProvider
     */
    public function testInvalidApiTypeWithDefaultValues($data)
    {
        $form = $this->createCheckboxForm(array('type' => BooleanExtension::TYPE_API));
        $form->submit($data);

        $this->assertNull($form->getData());
    }

    /**
     * @param mixed $data
     * @param array $trueValues
     * @param array $falseValues
     *
     * @dataProvider customInvalidProvider
     */
    public function testInvalidApiTypeWithCustomValues($data, $trueValues, $falseValues)
    {
        $form = $this->createCheckboxForm(array(
            'type' => BooleanExtension::TYPE_API,
            'true_values' => $trueValues,
            'false_values' => $falseValues,
        ));

        $form->submit($data);

        $this->assertNull($form->getData());
    }

    public static function defaultValidProvider()
    {
        return array(
            array(true, true),
            array(true, 1),
            array(true, '1'),
            array(true, 'true'),
            array(true, 'yes'),
            array(true, 'on'),
            array(false, false),
            array(false, 0),
            array(false, '0'),
            array(false, 'false'),
            array(false, 'no'),
            array(false, 'off'),
            array(false, ''),
            array(false, null),
    );
    }

    public static function customValidProvider()
    {
        $trueValues = array('1', 'y');
        $falseValues = array('0', 'n', null);

        return array(
            array(true, true, $trueValues, $falseValues),
            array(true, 1, $trueValues, $falseValues),
            array(true, '1', $trueValues, $falseValues),
            array(true, 'y', $trueValues, $falseValues),
            array(false, false, $trueValues, $falseValues),
            array(false, 0, $trueValues, $falseValues),
            array(false, '0', $trueValues, $falseValues),
            array(false, 'n', $trueValues, $falseValues),
            array(false, null, $trueValues, $falseValues),
        );
    }

    public static function defaultInvalidProvider()
    {
        return array(
            array('foo'),
            array(1.2),
            array(new \stdClass()),
            array(array('foo' => 'bar')),
        );
    }

    public static function customInvalidProvider()
    {
        $trueValues = array('y');
        $falseValues = array('n');

        return array(
            array(true, $trueValues, $falseValues),
            array(false, $trueValues, $falseValues),
        );
    }

    /**
     * @param array $options
     *
     * @return FormInterface
     */
    private function createCheckboxForm(array $options = array())
    {
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $type = 'Symfony\Component\Form\Extension\Core\Type\CheckboxType';
        } else {
            $type = 'checkbox';
        }

        return $this->formFactory->create($type, null, $options);
    }
}
