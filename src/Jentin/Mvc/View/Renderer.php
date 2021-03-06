<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\View;

use Jentin\Mvc\Plugin\PluginBrokerInterface;
use Jentin\Mvc\Plugin\Pluggable;

/**
 * Renderer
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class Renderer implements RendererInterface, Pluggable
{

    /**
     * template file extension
     * @var string
     */
    protected $fileExtension = 'phtml';
    /**
     * template path
     * @var string
     */
    protected $path = '';
    /**
     * template variables
     * @var array
     */
    protected $vars = array();
    /**
     * @var PluginBrokerInterface
     */
    protected $pluginBroker;
    /**
     * @var callable
     */
    protected $escapeCallback;
    /**
     * @var string
     */
    protected $encoding = 'UTF-8';


    /**
     * constructor
     *
     * @param PluginBrokerInterface $pluginBroker
     */
    public function __construct(PluginBrokerInterface $pluginBroker = null)
    {
        $this->pluginBroker = $pluginBroker;
    }


    /**
     * sets file extension
     *
     * @param  string $fileExtension
     * @return $this
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;
        return $this;
    }


    /**
     * gets file extension
     *
     * @return  string
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }


    /**
     * sets template path
     *
     * @param  string  $path
     * @return $this
     */
    public function setTemplatePath($path)
    {
        $this->path = $path;
        return $this;
    }


    /**
     * gets template path
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->path;
    }


    /**
     * sets template encoding
     *
     * @param  string  $encoding
     * @return $this
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }


    /**
     * gets template encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }


    /**
     * sets vars
     *
     * @param   array $vars
     * @return  Renderer
     */
    public function setVars(array $vars)
    {
        $this->vars = $vars;
        return $this;
    }


    /**
     * gets vars
     *
     * @return array
     */
    public function getVars()
    {
        return $this->vars;
    }


    /**
     * sets escape callback for template variables
     *
     * @param  callable $escapeCallback
     * @return $this
     */
    public function setEscapeCallback($escapeCallback)
    {
        $this->escapeCallback = $escapeCallback;
        return $this;
    }


    /**
     * gets escape callback for template variables
     *
     * @return array
     */
    public function getEscapeCallback()
    {
        if (null === $this->escapeCallback) {
            $this->setEscapeCallback(function($value, Renderer $renderer) {
                return htmlspecialchars($value, ENT_COMPAT, $renderer->getEncoding());
            });
        }
        return $this->escapeCallback;
    }


    /**
     * sets plugin broker
     *
     * @param  PluginBrokerInterface $pluginBroker
     * @return $this
     */
    public function setPluginBroker(PluginBrokerInterface $pluginBroker)
    {
        $this->pluginBroker = $pluginBroker;
        return $this;
    }


    /**
     * gets plugin broker
     *
     * @return PluginBrokerInterface
     */
    public function getPluginBroker()
    {
        return $this->pluginBroker;
    }


    /**
     * gets (and loads) plugin by a given name
     *
     * @param   string  $name
     * @return  object|callable
     */
    public function plugin($name)
    {
        $plugin = $this->pluginBroker->load($name);
        if ($plugin instanceof RendererAware && $this !== $plugin->getRenderer()) {
            $plugin->setRenderer($this);
        }
        return $plugin;
    }


    /**
     * execute plugin
     *
     * * If the helper does not define __invoke, it will be returned
     * * If the helper does define __invoke, it will be called as a function
     *
     * @param  string $method
     * @param  array  $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        $plugin = $this->plugin($method);
        if (is_callable($plugin)) {
            return call_user_func_array($plugin, $args);
        }
        return $plugin;
    }


    /**
     * sets template variable
     *
     * @param  string $name
     * @param  mixed  $value
     * @return $this
     */
    public function __set($name, $value)
    {
        $this->vars[$name] = $value;
        return $this;
    }


    /**
     * gets the (escaped) value of a template variable
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->esc($name);
    }


    /**
     * gets escaped value of a template variable
     *
     * @param  string $name
     * @return mixed
     */
    public function esc($name)
    {
        $value = $this->raw($name);
        return $this->escape($value);
    }


    /**
     * gets the raw value of a template variable
     *
     * @param  string $name
     * @return mixed
     */
    public function raw($name)
    {
        if (!array_key_exists($name, $this->vars)) {
            trigger_error("Template variable '$name' does not exist", E_USER_NOTICE);
            return null;
        }
        return $this->vars[$name];
    }


    /**
     * returns true, if view var is set
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->vars[$name]);
    }


    /**
     * escapes value if string
     *
     * @param  mixed $value
     * @return mixed
     */
    public function escape($value)
    {
        if (!is_string($value)) {
            return $value;
        }
        $escapeCallback = $this->getEscapeCallback();
        return call_user_func($escapeCallback, $value, $this);
    }


    /**
     * gets template file
     *
     * @param  string $name template name without extension
     * @return string
     */
    public function getFile($name)
    {
        $name = str_replace('..', '', $name);
        $file = $this->path . '/' . $name;
        if (!empty($this->fileExtension)) {
            $file .= '.' . $this->fileExtension;
        }
        return $file;
    }


    /**
     * renders view template
     *
     * @param  string $name
     * @param  array  $vars
     * @return string
     * @throws RendererException
     *   if file could not be found
     *   if file could not be read
     */
    public function render($name, array $vars = null)
    {
        $file = $this->getFile($name);
        if (!is_file($file)) {
            throw new RendererException('Could not find view template at: ' . $file);
        }
        if (!is_readable($file)) {
            throw new RendererException('Could not read view template at: ' . $file);
        }

        if ($vars) {
            $this->vars = array_merge($this->vars, $vars);
        }

        ob_start();
        /** @noinspection PhpIncludeInspection */
        include $file;
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }


}
