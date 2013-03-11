<?php

class Controller extends SBaseController {

    public $layout = 'column1';
    public $menu = array();
    public $breadcrumbs = array();

    public function init() {
        $cs = Yii::app()->clientScript;
        $cs->registerCoreScript('jquery');
        $cs->registerCoreScript('jquery.ui');
        $cs->registerScriptFile($this->createUrl('/js/jquery.md5.js'));
        $cs->registerScriptFile($this->createUrl('/js/jquery.scrollTo.min.js'));
        $cs->registerScriptFile($this->createUrl('/js/spin.js'));
    }

}