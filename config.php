<?php

/**
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @version 1.1
 * @copyright Copyright: 2009-2011 Artem Prilutskiy.
 * @author Artem Prilutskiy <cyanide.burnout@gmail.com>
 * @access public
 * @package PHP Aptitude
 */

  // Путь к пакетам.
  define("PACKAGES_FOLDER", dirname(__FILE__)."/packages");
  // Поддерживаемые типы архитектур.
  define("PACKAGES_TARGET", "all,armel,i386");
  // Тип архетиктуры, который будет отображаться браузером пакетов.
  define("DEFAULT_TARGET", "all,amd64,i686,armel");
  // Список локалей, для которых браузер пакетов будет отображать локализованые значения.
  define("DEFAULT_LOCALE", "ru_RU,en_EN");
  // Тип архитектуры для Cydia (при использовании репозиториев для Cydia)
  define("CYDIA_TARGET", "iphoneos-arm");

  // Настройки представления репозитория.
  define("REPOSITORY_NAME", "domain.com");
  define("REPOSITORY_DESCRIPTION", "My Packages");
  define("REPOSITORY_LOCATION", "http://domain.com/repository/repository.php/");

  // Дополнительные настройки дистрибьюции, необходимые для .install-файлов.
  // При обработке запросов PHP Aptitude игнорирует даннные опции.
  define("INSTALL_DISTRIBUTION", "squeeze");
  define("INSTALL_COMPONENTS", "extra");

  // Настройки memcached.
  // Если данные константы не определены - memcached использоваться не будет.
  define("CACHE_HOST", "localhost");
  define("CACHE_PORT", 11211);

  // Префиксы и опции хранения данных в memcached.
  define("CACHE_OUTPUT_PREFIX", "repository::");
  define("CACHE_OUTPUT_EXPIRES", 3600);

  /**
  * Статические индексы пакетов.
  * Если данные константы определены - будут использоваться индексы, 
  * сохраненные на файловой системе. Рекомендуется использовать данную опцию на нагруженых сайтах.
  * Для генерации индексов рекомендуется использовать скрипт scan.php.
  */
  // define("PACKAGES_INDEX_armel", dirname(__FILE__)."/packages/Packages-armel");
  // define("PACKAGES_INDEX_i386", dirname(__FILE__)."/packages/Packages-i386");
  // define("PACKAGES_INDEX_all", dirname(__FILE__)."/packages/Packages-all");

?>
