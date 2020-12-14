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
 * Date: 14/12/2020
 * Time: 12:17
 */
namespace App\Form;

use App\Manager\TimetableDataManager;
use App\Validator\Days;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DaysType
 * @package App\Form
 * @author Craig Rayner <craig@craigrayner.com>
 */
class DaysType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     * 14/12/2020 14:28
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('days', CollectionType::class,
                [
                    'entry_type' => DayType::class,
                    'constraints' => [
                        new Days(),
                    ],
                ]
            )
            ->add('saveDays', SubmitType::class);
    }

    /**
     * configureOptions
     * @param OptionsResolver $resolver
     * 14/12/2020 14:26
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'translation_domain' => 'messages',
                    'data_class' => TimetableDataManager::class,
                ]
            )
        ;
    }
}
