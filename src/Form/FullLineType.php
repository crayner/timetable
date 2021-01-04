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
 * Date: 3/01/2021
 * Time: 09:37
 */
namespace App\Form;

use App\Form\Transform\ItemTransForm;
use App\Items\Day;
use App\Items\Grade;
use App\Items\Line;
use App\Manager\TimetableManager;
use App\Provider\ProviderFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FullLineType
 * @package App\Form
 * @author Craig Rayner <craig@craigrayner.com>
 */
class FullLineType extends AbstractType
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
                    'choice_translation_domain' => false,
                    'attr' => ['class' => 'text-right labels-inline'],
                    'multiple' => true,
                    'expanded' => true,
                ]
            )
            ->add('classCount', IntegerType::class,
                [
                    'label' => 'Class Count',
                    'help' => 'Create the classes for this line by changing this number.',
                    'attr' => [
                        'onChange' => 'this.form.submit()'
                    ],
                ]
            )
            ->add('days', ChoiceType::class,
                [
                    'label' => 'Days in Timetable',
                    'help' => 'Limit placement in the timetable to the selected items. No selection = full selection.',
                    'choices' => ProviderFactory::create(Day::class)->all(),
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'expanded' => true,
                    'required' => false,
                    'multiple' => true,
                    'choice_translation_domain' => false,
                    'attr' => ['class' => 'text-right labels-inline'],
                ]
            )
            ->add('periods', ChoiceType::class,
                [
                    'label' => 'Periods in Timetable',
                    'help' => 'Limit placement in the timetable to the selected items. No selection = full selection.',
                    'choices' => $this->getPeriodList(),
                    'expanded' => true,
                    'required' => false,
                    'multiple' => true,
                    'choice_translation_domain' => false,
                    'attr' => ['class' => 'text-right labels-inline'],
                ]
            )
            ->add('placementCount', IntegerType::class,
                [
                    'label' => 'Placement Count',
                    'help' => 'How many times will this line be placed in the timetable.'
                ]
            )
            ->add('classes', CollectionType::class,
                [
                    'label' => false,
                    'required' => false,
                    'entry_type' => LineClassType::class,
                ]
            )
            ->add('saveLine', SubmitType::class,
                [
                    'label' => 'Save Line',
                ]
            )
        ;
        $builder->get('grades')->addModelTransformer(new ItemTransForm(Grade::class, true));
        $builder->get('days')->addModelTransformer(new ItemTransForm(Day::class, true));
        ;
    }

    /**
     * configureOptions
     * 3/01/2021 09:38
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'translation_domain' => 'messages',
                'data_class' => Line::class,
            ]
        );
    }

    /**
     * getPeriodList
     * 3/01/2021 10:52
     * @return array
     */
    private function getPeriodList(): array
    {
        $result = [];
        for ($i=1; $i<=$this->manager->getDataManager()->getMaxDayPeriods(); $i++) {
            $result[$i] = $i;
        }
        return $result;
    }
}
