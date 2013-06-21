<?php
$ds = DIRECTORY_SEPARATOR;
ini_set('date.timezone', 'Europe/Moscow');
$yii = dirname(__FILE__) . $ds . 'yii-1.1.12' . $ds . 'framework' . $ds . 'yii.php';
$config = dirname(__FILE__) . $ds . 'protected' . $ds . 'config' . $ds . 'main.php';
// remove the following line when in production mode
defined('YII_DEBUG') or define('YII_DEBUG', true);
ini_set('display_errors', 'on');
ini_set('error_reporting', 'E_ALL');
ini_set('date.timezone', 'Europe/Moscow');
ini_set('error_reporting', '-1');
require_once($yii);

Yii::createWebApplication($config)->run();





