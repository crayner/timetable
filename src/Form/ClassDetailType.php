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
 * Date: 1/01/2021
 * Time: 11:20
 */
namespace App\Form;

use App\Form\Listeners\SelectedAsPreferredChoiceListener;
use App\Form\Transform\ItemTransForm;
use App\Form\Type\PreferredChoiceType;
use App\Form\Type\SelectedAsPreferredChoiceType;
use App\Items\ClassDetail;
use App\Items\Grade;
use App\Items\Staff;
use App\Provider\ProviderFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class ClassDetailType
 * @package App\Form
 * @author Craig Rayner <craig@craigrayner.com>
 */
class ClassDetailType extends AbstractType
{
    /**
     * buildForm
     * 1/01/2021 11:44
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('name', TextType::class,
                [
                    'label' => 'Class Name',
                    'constraints' => [
                        new NotBlank(),
                        new Length(['max' => 8]),
                    ],
                ]
            )
            ->add('capacity', IntegerType::class,
                [
                    'help' => 'Set this value to zero to stop checks of student numbers in assigned classes.',
                    'constraints' => [
                        new Range(['min' => 0]),
                    ],
                ]
            )
            ->add('teachers', PreferredChoiceType::class,
                [
                    'label' => 'Teachers',
                    'choices' => ProviderFactory::create(Staff::class)->getStaffChoices(),
                    'placeholder' => 'Please select...',
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'choice_translation_domain' => false,
                    'required' => false,
                    'multiple' => true,
                    'class' => Staff::class,
                ]
            )
        ;
        $builder->get('teachers')->addEventSubscriber(new SelectedAsPreferredChoiceListener(Staff::class));
    }

    /**
     * configureOptions
     * 1/01/2021 11:46
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'translation_domain' => 'messages',
                    'data_class' => ClassDetail::class,
                ]
            )
        ;
    }
}
