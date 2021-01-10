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
 * Date: 13/12/2020
 * Time: 09:14
 */
namespace App\Form;

use App\Manager\DataManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class BasicSettingsType
 * @package App\Form
 * @author Craig Rayner <craig@craigrayner.com>
 */
class BasicSettingsType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     * 13/12/2020 09:26
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('staffCount', IntegerType::class,
                [
                    'label' => 'Staff Count',
                    'help' => 'Change this number for the system to add Staff.'
                ]
            )
            ->add('roomCount', IntegerType::class,
                [
                    'label' => 'Room Count',
                    'help' => 'Change this number for the system to add Rooms.'
                ]
            )
            ->add('gradeCount', IntegerType::class,
                [
                    'label' => 'Grade/Year/Form Count',
                    'help' => 'Change this nuber for the system to add Years/Grades/Forms.'
                ]
            )
            ->add('dayCount', IntegerType::class,
                [
                    'label' => 'Number of Days in the Timetable',
                    'disabled' => true,
                ]
            )
            ->add('periodCount', IntegerType::class,
                [
                    'label' => 'Default Periods in a Day',
                    'constraints' => [
                        new Range(['min' => 1]),
                    ],
                    'disabled' => true,
                ]
            )
            ->add('studentsPerGrade', IntegerType::class,
                [
                    'label' => 'Students per Grade',
                    'help' => 'If changed, this value is applied to all grades. Set this value to zero to stop checks of student numbers in assigned classes.',
                    'constraints' => [
                        new Range(['min' => 0]),
                    ],
                ]
            )
            ->add('roomCapacity', IntegerType::class,
                [
                    'label' => 'Student Capacity per Room',
                    'help' => 'If changed, this value is applied to all rooms. A room with zero capacity is ignored in student number checks.',
                    'constraints' => [
                        new Range(['min' => 0]),
                    ],
                ]
            )
            ->add('submit', SubmitType::class,
                [
                    'label' => 'Update Basic Settings',
                ]
            )
        ;
    }

    /**
     * configureOptions
     * @param OptionsResolver $resolver
     * 13/12/2020 09:16
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'translation_domain' => 'messages',
                    'data_class' => DataManager::class,
                ]
            )
        ;
    }
}
