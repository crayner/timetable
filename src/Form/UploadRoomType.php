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
 * Class UploadRoomType
 * @package App\Form
 * @author Craig Rayner <craig@craigrayner.com>
 */
class UploadRoomType extends AbstractType
{
    /**
     * buildForm
     * 15/12/2020 13:34
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dataFile', FileType::class,
                [
                    'label' => 'Room Name List Upload',
                    'help' => 'A text file, with a room name, capacity on each line. The name and capacity must be separated by a comma. Capacity is optional.',
                    'required' => false,
                    'constraints' => [
                        new NotBlank(),
                        new File(['maxSize' => '20k', 'mimeTypes' => ['text/plain']]),
                    ],
                ]
            )
            ->add('upload', SubmitType::class,
                [
                    'label' => 'Upload Rooms',
                ]
            )
        ;
    }

    /**
     * configureOptions
     * 15/12/2020 13:34
     * @param OptionsResolver $resolver
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
