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
 * Time: 10:48
 */
namespace App\Form;

use App\Manager\TimetableDataManager;
use App\Validator\DuplicateName;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StaffMemberType
 * @package App\Form
 * @author Craig Rayner <craig@craigrayner.com>
 */
class StaffMemberType extends AbstractType
{
    /**
     * buildForm
     * 15/12/2020 08:32
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('staff', CollectionType::class,
                [
                    'entry_type' => StaffType::class,
                    'constraints' => [
                        new DuplicateName(),
                    ],
                ]
            )
            ->add('saveStaff', SubmitType::class,
                [
                    'label' => 'Save Staff',
                ]
            )
        ;
    }

    /**
     * configureOptions
     * 15/12/2020 08:31
     * @param OptionsResolver $resolver
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