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
 * @version 1.0
 * @copyright Copyright: 2009-2010 Artem Prilutskiy.
 * @author Artem Prilutskiy <cyanide.burnout@gmail.com>
 * @access public
 * @package PHP Aptitude
 */

  error_reporting(E_ALL);
  require_once("config.php");

  /**
   * rescanPackages осуществляет сканирование пакетов в папке репозитория,
   * "выниманием" данных файла control и дополнительным обогащением до формата
   * Packages.
   */
  function rescanPackages($target = "")
  {
    $result = "";
    $files = scandir(PACKAGES_FOLDER);
    foreach ($files as $file)
    {
      $path = PACKAGES_FOLDER."/".$file;
      if (preg_match("/^.+[.]deb$/", $file) && is_file($path))
      {
        $output = shell_exec("dpkg-deb --info ${path} control");
        if (empty($target) ||
            (strpos($output, "Architecture: all") !== false) ||
            (strpos($output, "Architecture: ".$target) !== false))
        {
          // Фикс некорректного описания пакета маинтейнерами
          while (strpos($output, "\n\n"))
            $output = str_replace("\n\n", "\n", $output);
          while (strpos($output, "\n \n"))
            $output = str_replace("\n \n", "\n .\n", $output);
          // Сборка записи
          $result .= $output;
          $result .= "Filename: ./${file}\n";
          $result .= "Size: ".filesize($path)."\n";
          $result .= "MD5sum: ".md5_file($path)."\n";
          $result .= "\n";
        }
      }
    }
    return $result;
  }

  /**
   * getChangeLog "вытаскивает" из пакета changelog.
   */
  function getChangeLog($packages, $name)
  {
    $result = "";
    if (array_key_exists($name, $packages))
    {
      $path = PACKAGES_FOLDER."/".basename($packages[$name]["Filename"]);
      if (is_file($path))
      {
        $commands = array("dpkg-deb --fsys-tarfile ${path} | tar -xO --wildcards ./usr/share/doc/*/changelog.gz | gzip -dc",
            "dpkg-deb --fsys-tarfile ${path} | tar -xO --wildcards ./usr/share/doc/*/changelog.Debian.gz | gzip -dc");
        foreach ($commands as $command)
        {
          $result = shell_exec($command);
          if (!empty($result))
            break;
        }
      }
    }
    return $result;
  }

  /**
   * getPackagesToken возвращяет "слепок" текущего состояния папки репозитория.
   */
  function getPackagesToken()
  {
    $cast = "TOKEN\n";
    $files = scandir(PACKAGES_FOLDER);
    foreach ($files as $file)
    {
      $path = PACKAGES_FOLDER."/".$file;
      if (preg_match("/^.+[.]deb$/", $file) && is_file($path))
      {
        $date = filectime($path);
        $size = filesize($path);
        $cast .= "${file}:${size}:${date}\n";
      }
    }
    return md5($cast);
  }

  /**
   * cachedScanPackages использует Memcached для кэширования данных, возвращаемых rescanPackages.
   * Стратегия обновления данных строится:
   * а) на сроке хранения данных в кэше,
   * б) на факте изменений файлов пакетов в папке репозитория.
   */
  function cachedScanPackages($target)
  {
    global $memcache;
    $packet = $memcache->get(CACHE_OUTPUT_PREFIX.$target);
    $token = getPackagesToken();

    if (is_array($packet))
      if ($packet["TOKEN"] == $token)
        return $packet["OUTPUT"];

    $output = rescanPackages($target);
    $packet = array("TOKEN" => $token, "OUTPUT" => $output);
    $memcache->set(CACHE_OUTPUT_PREFIX.$target, $output, NULL, CACHE_OUTPUT_EXPIRES);
    return $output;
  }

  /**
   * scanPackages возвращает индекс репозитория используя в зависимости от конфигурации
   * функции cachedScanPackages или rescanPackages. В случае, если определены статические
   * индексы пакетов - функция зачитает индексный файл.
   */
  function scanPackages($target)
  {
    global $memcache;
    if (defined("PACKAGES_INDEX_".$target))
      return file_get_contents(constant("PACKAGES_INDEX_".$target));
    if (isset($memcache))
      return cachedScanPackages($target);
    return rescanPackages($target);
  }

  /**
   * getPackages возвращает индекс репозитория в виде ассоциативного массива вида
   * [имя пакета][имя параметра] => значения. Имена параметров преобразуются - 
   * знак "-" заменяется на "_" для корректной работы с Smarty.
   */
  function getPackages($targets = DEFAULT_TARGET, $locales = DEFAULT_LOCALE)
  {
    $packages = array();
    $locales = array_reverse(explode(",", strtolower(str_replace("-", "_", $locales))));
    foreach (explode(",", $targets) as $target)
    {
      $contents = explode("\n", str_replace("\r", "", scanPackages($target)));
      $values = array();
      $ranks = array();
      foreach ($contents as $line)
      {
        if (empty($line) && (count($values) > 1))
        {
          $name = $values["Package"];
          $packages[$name] = $values;
          $values = array();
          $ranks = array();
          unset($key);
        }
        if (preg_match("/^([\\w-]+)[-](\\w{2,3}[_]\\w{2})[:]\\s(.*)$/", $line, $matches))
        {
          $key = str_replace("-", "_", $matches[1]);
          $rank = array_search(strtolower($matches[2]), $locales);
          if (($rank !== FALSE) &&
              (!array_key_exists($key, $ranks) ||
              ($rank > $ranks[$key])))
          {
            $values[$key] = trim($matches[3]);
            $ranks[$key] = $rank;
            continue;
          }
          unset($key);
          continue;
        }
        if (preg_match("/^([\\w-]+)[:]\\s(.*)$/", $line, $matches))
        {
          $key = str_replace("-", "_", $matches[1]);
          if (!array_key_exists($key, $values))
          {
            $values[$key] = trim($matches[2]);
            continue;
          }
          unset($key);
          continue;
        }
        if (preg_match("/^\\s(.+)$/", $line, $matches) && isset($key))
        {
          if (!empty($values[$key]))
            $values[$key] .= "\n";
          if ($matches[1] != ".")
            $values[$key] .= $matches[1];
        }
      }
    }
    return $packages;
  }

  if (class_exists("Memcache") && defined("CACHE_HOST"))
  {
    $memcache = new Memcache();
    if (!$memcache->connect(CACHE_HOST, CACHE_PORT))
      unset($memcache);
  }

?>
