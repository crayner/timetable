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
 * Date: 12/12/2020
 * Time: 08:39
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
 * Class LoadTimetableType
 * @package App\Form
 * @author Craig Rayner <craig@craigrayner.com>
 */
class LoadTimetableType extends AbstractType
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
            ->add('dataFile', FileType::class,
                [
                    'label' => 'Timetable Data File',
                    'help' => 'If used and valid, this file will be saved to the server for use against the name in the file.',
                    'required' => false,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add('password', PasswordType::class,
                [
                    'label' => 'File Password',
                    'help' => 'The password is for the uploaded data file. This is ignored if the file secret is valid.  If the secret is not valid, then the password will be required to unlock the uploaded file on the site.',
                    'required' => false,
                    'constraints' => [
                        new Length(['max' => 75]),
                        new UserPasswordContent(),
                    ],
                ]
            )
            ->add('submit', SubmitType::class,
                [
                    'label' => 'Load Timetable',
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
