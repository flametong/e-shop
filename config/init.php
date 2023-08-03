<?php

// 0 - Production mode
// 1 - Debug mode
const DEBUG = 0;

define("ROOT", dirname(__DIR__));
const WWW = ROOT . '/public';
const APP = ROOT . '/app';
const CORE = ROOT . '/vendor/ishop';
const HELPERS = ROOT . '/vendor/ishop/helpers';
const CACHE = ROOT . '/tmp/cache';
const LOGS = ROOT . '/tmp/logs';
const CONFIG = ROOT . '/config';
const LAYOUT = 'ishop';
const PATH = 'http://ishop.loc';
const ADMIN = 'http://ishop.loc/admin';
const NO_IMAGE = 'uploads/no_image.jpg';

require_once ROOT . '/vendor/autoload.php';