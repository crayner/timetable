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
 * Date: 15/12/2020
 * Time: 08:29
 */
namespace App\Form;

use App\Form\Transform\ItemTransForm;
use App\Items\Grade;
use App\Items\Line;
use App\Manager\TimetableManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class LineListType
 * @package App\Form
 * @author Craig Rayner <craig@craigrayner.com>
 */
class LineType extends AbstractType
{
    /**
     * @var TimetableManager
     */
    private TimetableManager $manager;

    /**
     * LineListType constructor.
     * @param TimetableManager $manager
     */
    public function __construct(TimetableManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * getChoices
     * 16/12/2020 15:31
     * @return mixed
     */
    public function getGradeChoices()
    {
        return $this->manager->getDataManager()->getGrades();
    }

    /**
     * buildForm
     * 15/12/2020 08:42
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
                [
                    'label' => 'Line Name',
                    'constraints' => [
                        new NotBlank(),
                        new Length(['max' => 6]),
                    ],
                ]
            )
            ->add('id', HiddenType::class)
            ->add('grades', ChoiceType::class,
                [
                    'label' => 'Grade/Year/Form',
                    'choices' => $this->getGradeChoices(),
                    'placeholder' => 'Please select...',
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'multiple' => true,
                    'expanded' => true,
                    'attr' => ['class' => 'text-right labels-inline'],
                    'choice_translation_domain' => false,
                ]
            )
        ;
        $builder->get('grades')->addModelTransformer(new ItemTransForm(Grade::class, true));
    }

    /**
     * configureOptions
     * 15/12/2020 08:39
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'translation_domain' => 'messages',
                    'data_class' => Line::class,
                ]
            )
        ;
    }
}
