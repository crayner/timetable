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

use App\Manager\DataManager;
use App\Validator\DuplicateItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RoomsType
 * @package App\Form
 * @author Craig Rayner <craig@craigrayner.com>
 */
class RoomsType extends AbstractType
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
            ->add('rooms', CollectionType::class,
                [
                    'entry_type' => RoomType::class,
                    'constraints' => [
                        new DuplicateItem(),
                    ],
                ]
            )
            ->add('saveRooms', SubmitType::class,
                [
                    'label' => 'Save Rooms',
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
                    'data_class' => DataManager::class,
                ]
            )
        ;
    }
}
