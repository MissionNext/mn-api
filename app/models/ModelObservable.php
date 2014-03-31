<?php

namespace MissionNext\Models;


use Illuminate\Database\Eloquent\Model;
use MissionNext\Api\Exceptions\ModelObservableException;
use MissionNext\Models\Observers\ModelObserverInterface;

abstract class ModelObservable extends Model {

    /**
     * @var ModelObserverInterface
     */
    private  $observer;

    private  $onSaved = [];

    /**
     * @return ModelObserverInterface
     */
    public function observer()
    {

        return $this->observer;
    }

    /**
     * @param ModelObserverInterface $observer
     *
     * @return $this
     */
    public function setObserver(ModelObserverInterface $observer)
    {
        $this->observer = $observer;

        return $this->observer;
    }

    /**
     * @param callable $handler
     *
     * @return array
     *
     * @throws \MissionNext\Api\Exceptions\ModelObservableException
     */
    public function onSaved(\Closure $handler)
    {
        if (is_callable($handler)) {
            $this->onSaved[] = $handler;
        } else {
            throw new ModelObservableException("Attached handler is not callable ", ModelObservableException::ON_SAVED);
        }

        return $this->onSaved;
    }

    /**
     * @return array
     */
    public function getOnSaved()
    {

        return $this->onSaved;
    }

} 