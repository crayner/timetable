<?php
/**
 * Created by PhpStorm.
 *
 * timetable
 * (c) 2020 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 14/12/2020
 * Time: 12:17
 */
namespace App\Form;

use App\Items\Day;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class DayType
 * @package App\Form
 * @author Craig Rayner <craig@craigrayner.com>
 */
class DayType extends AbstractType
{
    /**
     * buildForm
     * 14/12/2020 14:54
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('name', TextType::class)
            ->add('periods', IntegerType::class,
                [
                    'constraints' => [
                        new Range(['min' => 1]),
                    ],
                ]
            )
        ;
    }

    /**
     * configureOptions
     * @param OptionsResolver $resolver
     * 14/12/2020 12:19
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'translation_domain' => 'messages',
                    'data_class' => Day::class,
                ]
            )
        ;
    }
}