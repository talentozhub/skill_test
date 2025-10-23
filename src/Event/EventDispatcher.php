<?php

class EventDispatcher
{
    private $listeners = array();

    public function addListener($eventName, $listener)
    {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = array();
        }

        $this->listeners[$eventName][] = $listener;
    }

    public function dispatch($event)
    {
        $eventName = get_class($event);
        if (isset($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $listener) {
                $object = $listener[0];
                $method = $listener[1];

                $object->$method($event);
            }
        }
    }
}
