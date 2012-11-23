<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Event;

use Jentin\Mvc\Response\ResponseInterface;
use Jentin\Mvc\Controller\ControllerInterface;

/**
 * ControllerResultEvent.php
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class ControllerResultEvent extends MvcEvent
{

    /**
     * @var \Jentin\Mvc\Controller\ControllerInterface
     */
    private $controller;
    /**
     * @var mixed
     */
    private $controllerResult;


    /**
     * constructor
     * @param \Jentin\Mvc\Controller\ControllerInterface $controller
     * @param mixed $controllerResult
     */
    public function __construct(ControllerInterface $controller, $controllerResult)
    {
        $this->controller = $controller;
        $this->controllerResult = $controllerResult;
    }


    /**
     * gets controller
     *
     * @return \Jentin\Mvc\Controller\ControllerInterface
     */
    public function getController()
    {
        return $this->controller;
    }


    /**
     * sets controller result
     *
     * @param $controllerResult
     * @return \Jentin\Mvc\Event\ControllerResultEvent
     */
    public function setControllerResult($controllerResult)
    {
        $this->controllerResult = $controllerResult;
        return $this;
    }


    /**
     * gets controller result
     *
     * @return mixed
     */
    public function getControllerResult()
    {
        return $this->controllerResult;
    }


    /**
     * sets response
     *
     * @param  \Jentin\Mvc\Response\ResponseInterface $response
     * @return \Jentin\Mvc\Event\RouteEvent
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->stopPropagation();
        return parent::setResponse($response);
    }

}