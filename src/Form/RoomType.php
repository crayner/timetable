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

use App\Items\Room;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class RoomType
 * @package App\Form
 * @author Craig Rayner <craig@craigrayner.com>
 */
class RoomType extends AbstractType
{
    /**
     * buildForm
     * 15/12/2020 13:31
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('name', TextType::class,
                [
                    'label' => 'Room Name',
                ]
            )
            ->add('capacity', IntegerType::class,
                [
                    'label' => 'Room Capacity',
                    'help' => 'When set to zero, checks of capacity are not implemented.',
                    'constraints' => [
                        new Range(['min' => 0]),
                    ],
                ]
            )
        ;
    }

    /**
     * configureOptions
     * 15/12/2020 13:31
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'translation_domain' => 'messages',
                    'data_class' => Room::class,
                ]
            )
        ;
    }
}
