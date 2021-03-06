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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class LoadTimetableType
 * @package App\Form
 * @author Craig Rayner <craig@craigrayner.com>
 */
class UploadStaffType extends AbstractType
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
                    'label' => 'Staff Name List Upload',
                    'help' => 'A text file, with a staff member name on each line.',
                    'required' => false,
                    'constraints' => [
                        new NotBlank(),
                        new File(['maxSize' => '20k', 'mimeTypes' => ['text/plain']]),
                    ],
                ]
            )
            ->add('upload', SubmitType::class,
                [
                    'label' => 'Upload Staff',
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
