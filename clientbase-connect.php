<?php
/**
 * Plugin Name: ClentBase Connect
 * Plugin URI: https://github.com/drnoisier/wp-plugin-clientbase-connect
 * Description: WordPress-плагин, предназначенный для экспорта данных о пользователях в CRM-систему на платформе "Клиентская база" .
 * Version: 0.34
 * Author: Дмитрий Шумилин
 * Author URI: mailto://dmitri.shumilinn@yandex.ru
 */
/**
 *    Copyright (C) 2020  Dmitry Shumilin (dmitri.shumilinn@yandex.ru)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

require_once plugin_dir_path(__FILE__).'classes/interfaces/ClientBaseAPIInterface.php';
require_once plugin_dir_path(__FILE__).'classes/interfaces/CBConnectTableInterface.php';
require_once plugin_dir_path(__FILE__).'classes/interfaces/CBCDataTakerInterface.php';

require_once plugin_dir_path(__FILE__).'classes/ClientBaseAPI.php';
require_once plugin_dir_path(__FILE__).'classes/CBConnectTable.php';
require_once plugin_dir_path(__FILE__).'classes/CBCDataTaker.php';

define('CBAPI_CREATE', 'create');
define('CBAPI_READ', 'read');
define('CBAPI_UPDATE', 'update');
define('CBAPI_DELETE', 'delete');

if (!defined('BOOTSTRAP_CSS_DIR')) define('BOOTSTRAP_CSS_DIR', plugin_dir_path(__FILE__).'css/bootstrap.min.css');

if (!defined('BOOTSTRAP_JS_DIR')) define('BOOTSTRAP_JS_DIR', plugin_dir_path(__FILE__).'js/bootstrap.min.js');

if (!defined('POPPER_DIR')) define('POPPER_DIR', plugin_dir_path(__FILE__).'js/popper.min.js');

if (!defined('JQUERY_DIR')) define('JQUERY_DIR', plugin_dir_path(__FILE__).'js/jquery-3.5.1.js');

if (!file_exists(BOOTSTRAP_CSS_DIR)) file_put_contents(BOOTSTRAP_CSS_DIR, file_get_contents('https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css'));

if (!file_exists(BOOTSTRAP_JS_DIR)) file_put_contents(BOOTSTRAP_JS_DIR, file_get_contents('https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js'));

if (!file_exists(POPPER_DIR)) @file_put_contents(POPPER_DIR, file_get_contents('https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js'));

if (!file_exists(JQUERY_DIR)) file_put_contents(JQUERY_DIR, file_get_contents('https://code.jquery.com/jquery-3.5.1.js'));

session_start(['name' => 'clientbase_connect_session']);

$cbc_csrf_session_status = session_status();

switch ($cbc_csrf_session_status) {
    case PHP_SESSION_ACTIVE:
        
        define('CBC_CSRF', true);

        $cbc_csrf_hash_key = 'key_'.time();
        $cbc_csrf_hash_value = password_hash('zfgsw4tergzfdga4yz0zd943423sdsdg3', PASSWORD_DEFAULT);

        $_SESSION[$cbc_csrf_hash_key] = $cbc_csrf_hash_value;

        break;

    default:
        define('CBC_CSRF', false);

        if ($cbc_csrf_session_status === PHP_SESSION_NONE) {

            $cbc_csrf_log_time = time();
            $cbc_csrf_log_name = $cbc_csrf_log_time.'.log';

            if (file_exists(plugin_dir_path(__FILE__).'logs/'.$cbc_csrf_log_name)) $cbc_csrf_log_content = file_get_contents(plugin_dir_path(__FILE__).'logs/'.$cbc_csrf_log_name)."\n";
            else $cbc_csrf_log_content = '';

            file_put_contents(plugin_dir_path(__FILE__).'logs/'.$cbc_csrf_log_name, $cbc_csrf_log_content.date('Y-m-D H:i:s').': An error occurred while creating the session.');

        }

        break;
    
}
