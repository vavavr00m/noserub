<?php

class AppError extends ErrorHandler {
    public function _outputMessage($template) {
        if(!Configure::read('debug')) {
            $this->log($this->controller->pageTitle . ': ' . $this->controller->params['controller']);
            $template = 'not_found';
        }
        return parent::_outputMessage($template);
    }
}