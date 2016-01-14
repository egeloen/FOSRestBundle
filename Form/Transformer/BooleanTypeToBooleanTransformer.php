<?php

/*
 * This file is part of the FOSRestBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\RestBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BooleanTypeToBooleanTransformer implements DataTransformerInterface
{
    /**
     * @var array
     */
    private $trueValues;

    /**
     * @var array
     */
    private $falseValues;

    /**
     * @param array $trueValues
     * @param array $falseValues
     */
    public function __construct(array $trueValues, array $falseValues)
    {
        $this->trueValues = $trueValues;
        $this->falseValues = $falseValues;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if ($value === null) {
            return;
        }

        if (is_bool($value)) {
            return $value;
        }

        throw new TransformationFailedException(sprintf(
            'The boolean type expects a boolean or null value, got "%s"',
            is_object($value) ? get_class($value) : gettype($value)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (in_array($value, $this->trueValues, true)) {
            return true;
        }

        if (in_array($value, $this->falseValues, true)) {
            return false;
        }

        throw new TransformationFailedException('The boolean type could not be reverse transformed.');
    }
}
