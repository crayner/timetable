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
 * Date: 2/01/2021
 * Time: 16:36
 */
namespace App\Form\Listeners;

use App\Form\Transform\ItemTransForm;
use App\Form\Type\PreferredChoiceType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvents;

/**
 * Class SelectedAsPreferredChoiceListener
 * @package App\Form\Listeners
 * @author Craig Rayner <craig@craigrayner.com>
 */
class SelectedAsPreferredChoiceListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private string $class;

    /**
     * SelectedAsPreferredChoiceListener constructor.
     * @param string $class
     */
    public function __construct(string $class = '')
    {
        $this->class = $class;
    }
    /**
     * getSubscribedEvents
     * 2/01/2021 16:41
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
        ];
    }

    /**
     * onPreSetData
     * 2/01/2021 16:39
     * @param PreSetDataEvent $event
     */
    public function onPreSetData(PreSetDataEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $options = $form->getConfig()->getOptions();
        $parent = $form->getParent();
        $name = $form->getName();
        $parent->remove($name);

        $find = [];
        foreach ($data as $item) $find[] = $item->getId();
        $choices = new ArrayCollection($options['choices']);
        $preferred = $choices->partition(function($key,$item) use ($find) {
            return in_array($item->getId(), $find);
        });

        $options['choices'] = array_merge(array_values($preferred[0]->toArray()), array_values($preferred[1]->toArray()));

        $parent->add($name, PreferredChoiceType::class, $options);
    }
}