<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Controller;

/**
 * ControllerAware
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
interface ControllerAware
{

    /**
     * sets controller
     *
     * @param  \Jentin\Mvc\Controller\ControllerInterface $controller
     * @return ControllerAware
     */
    public function setController(ControllerInterface $controller);

    /**
     * gets controller
     *
     * @return \Jentin\Mvc\Controller\ControllerInterface
     */
    public function getController();

}