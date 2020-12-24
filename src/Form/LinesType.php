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
 * Date: 16/12/2020
 * Time: 15:17
 */
namespace App\Form;

use App\Manager\DataManager;
use App\Validator\DuplicateName;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LinesType
 * @package App\Form
 * @author Craig Rayner <craig@craigrayner.com>
 */
class LinesType extends AbstractType
{
    /**
     * buildForm
     * 16/12/2020 15:19
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lines', CollectionType::class,
                [
                    'entry_type' => LineType::class,
                    'constraints' => [
                        new DuplicateName(),
                    ],
                ]
            )
            ->add('saveLines', SubmitType::class,
                [
                    'label' => 'Save Lines',
                ]
            )
        ;
    }

    /**
     * configureOptions
     * 16/12/2020 15:18
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
