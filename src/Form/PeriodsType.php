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

use App\Manager\DataManager;
use App\Validator\DuplicateItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PeriodsType
 *
 * @package App\Form
 * @author Craig Rayner <craig@craigrayner.com>
 * 8/01/2021 10:45
 */
class PeriodsType extends AbstractType
{
    /**
     * buildForm
     * 8/01/2021 11:00
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('periods', CollectionType::class,
                [
                    'entry_type' => PeriodType::class,
                    'constraints' => [
                        new DuplicateItem(),
                        new DuplicateItem(['fields' => ['sequence']]),
                    ],
                ]
            )
            ->add('savePeriods', SubmitType::class,
                [
                    'label' => 'Save Periods',
                ]
            )
        ;
    }

    /**
     * configureOptions
     * 8/01/2021 11:00
     *
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
