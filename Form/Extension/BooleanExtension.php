<?php

/*
 * This file is part of the FOSRestBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\RestBundle\Form\Extension;

use FOS\RestBundle\Form\Transformer\BooleanTypeToBooleanTransformer;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BooleanExtension extends AbstractTypeExtension
{
    const TYPE_API = 'api';
    const TYPE_CHECKBOX = 'checkbox';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['type'] === self::TYPE_API) {
            $builder
                ->resetViewTransformers()
                ->addViewTransformer(new BooleanTypeToBooleanTransformer(
                    $options['true_values'],
                    $options['false_values']
                ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'type' => self::TYPE_CHECKBOX,
            'true_values' => array('1', 'true', 'yes', 'on'),
            'false_values' => array('0', 'false', 'no', 'off', '', null),
        ));

        // Symfony >= 2.6
        if (method_exists($resolver, 'isMissing')) {
            $resolver
                ->setAllowedValues('type', array(self::TYPE_API, self::TYPE_CHECKBOX))
                ->setAllowedTypes('true_values', 'array')
                ->setAllowedTypes('false_values', 'array')
            ;
        } else {
            $resolver
                ->setAllowedValues(array('type' => array(self::TYPE_API, self::TYPE_CHECKBOX)))
                ->setAllowedTypes(array(
                    'true_values' => 'array',
                    'false_values' => 'array',
                ))
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        // Symfony >= 2.8
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            return 'Symfony\Component\Form\Extension\Core\Type\CheckboxType';
        }

        return 'checkbox';
    }
}
