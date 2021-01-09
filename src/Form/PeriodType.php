<?php
/**
 * Created by PhpStorm.
 *
 * timetable
 * (c) 2021 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 8/01/2021
 * Time: 10:44
 */
namespace App\Form;

use App\Form\Transform\ItemTransForm;
use App\Items\Period;
use App\Provider\ProviderFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class PeriodType
 *
 * @package App\Form
 * @author Craig Rayner <craig@craigrayner.com>
 * 8/01/2021 11:16
 */
class PeriodType extends AbstractType
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
            ->add('sequence', IntegerType::class,
                [
                    'constraints' => [
                        new Range(['min' => 1]),
                    ],
                ]
            )
            ->add('doubleWith', ChoiceType::class,
                [
                    'label' => 'Double with Period',
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'choice_translation_domain' => false,
                    'multiple' => true,
                    'required' => false,
                    'choices' => ProviderFactory::create(Period::class)->all(),
                ]
            )
        ;
        $builder->get('doubleWith')->addModelTransformer(new ItemTransForm(Period::class, true));
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
                    'data_class' => Period::class,
                ]
            )
        ;
    }
}