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
 * Date: 10/12/2020
 * Time: 13:33
 */
namespace App\Form;

use App\Validator\UserPasswordContent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class NewTimetableType
 * @package App\Form
 * @author Craig Rayner <craig@craigrayner.com>
 */
class NewTimetableType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     * 10/12/2020 13:35
     */
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
            ->add('password', PasswordType::class,
                [
                    'label' => 'Password',
                    'help' => 'The password can be ignored if you have not previously created a timetable.',
                    'required' => false,
                    'constraints' => [
                        new Length(['max' => 75]),
                        new UserPasswordContent(),
                    ],
                ]
            )
            ->add('submit', SubmitType::class,
                [
                    'label' => 'Start Now!',
                ]
            )
        ;
    }

    /**
     * configureOptions
     * @param OptionsResolver $resolver
     * 10/12/2020 15:08
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'translation_domain' => 'messages',
            ]
        );
    }
}
