<?php
/**
 * Created by PhpStorm.
 *
 * Timetable Creator
 * (c) 2020-2021 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Licence: MIT
 * User: Craig Rayner
 * Date: 2/01/2021
 * Time: 17:00
 */

namespace App\Form\Type;

use App\Form\Transform\ItemTransForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreferredChoiceType extends AbstractType
{
    /**
     * getParent
     * 2/01/2021 17:00
     * @return string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * buildForm
     * 2/01/2021 17:01
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ItemTransForm($options['class'], $options['multiple']));
    }

    /**
     * configureOptions
     * 2/01/2021 17:02
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'class',
        ]);
    }
}
