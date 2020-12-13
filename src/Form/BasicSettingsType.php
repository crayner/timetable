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
 * Date: 13/12/2020
 * Time: 09:14
 */
namespace App\Form;

use App\Manager\TimetableDataManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BasicSettingsType
 * @package App\Form
 * @author Craig Rayner <craig@craigrayner.com>
 */
class BasicSettingsType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     * 13/12/2020 09:26
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('staffCount', IntegerType::class,
                [
                    'label' => 'Staff Count',
                ]
            )
            ->add('roomCount', IntegerType::class,
                [
                    'label' => 'Room Count',
                ]
            )
            ->add('submit', SubmitType::class,
                [
                    'label' => 'Update Basic Settings',
                ]
            )
        ;
    }

    /**
     * configureOptions
     * @param OptionsResolver $resolver
     * 13/12/2020 09:16
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
