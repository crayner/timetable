<?php
/**
 * Created by PhpStorm.
 *
 * Timetable Creator
 * (c) 2020-2020 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Licence: MIT
 * User: Craig Rayner
 * Date: 19/12/2020
 * Time: 09:50
 */
namespace App\Form;

use App\Manager\DataManager;
use App\Validator\UserPasswordContent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CreateTimetableType
 * @package App\Form
 * @author Craig Rayner <craig@craigrayner.com>
 */
class CreateTimetableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
                [
                    'label' => 'Timetable Name',
                    'help' => 'The timetable name must match the dataFile name, or the system will generate a new timetable data file.',
                    'constraints' => [
                        new NotBlank(),
                        new Length(['max' => 75]),
                    ],
                ]
            )
            ->add('password', RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'first_options'  => ['label' => 'Password'],
                    'second_options' => ['label' => 'Repeat Password'],
                    'constraints' => [
                        new NotBlank(),
                        new Length(['max' => 75]),
                        new UserPasswordContent(['minSpecial' => 0]),
                    ],
                ]
            )
            ->add('submit', SubmitType::class,
                [
                    'label' => 'Create Timetable',
                ]
            )
        ;
    }

    /**
     * configureOptions
     * 19/12/2020 09:53
     * @param OptionsResolver $resolver
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
