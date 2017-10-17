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

  require_once("config.php");
  require_once("packages.php");

  function sendIndexData($data, $type)
  {
    $length = strlen($data);
    header("Content-Type: $type");
    header("Content-Length: $length");
    print($data);
  }

  function sendPackageIndex($output, $type)
  {
    if (($type == "Packages") || ($type == "Sources"))
    {
      sendIndexData($output, "text/plain");
      return true;
    }
    if (($type == "Packages.gz") || ($type == "Sources.gz"))
    {
      sendIndexData(gzencode($output, 9), "application/x-gzip");
      return true;
    }
    if (($type == "Packages.bz2") || ($type == "Sources.bz2"))
    {
      sendIndexData(bzcompress($output), "application/x-bzip");
      return true;
    }
    return false;
  }

  function sendPackageFile($path)
  {
    $file = @fopen($path, "rb");
    if ($file !== FALSE)
    {
      $length = filesize($path);
      header("Content-Type: application/octet-stream");
      header("Content-Length: $length");
      while(!feof($file))
      {
        print(fread($file, 8192));
        flush();
        if (connection_status() != 0)
          break;
      }
      @fclose($file);
      return true;
    }
    return false;
  }

  if (isset($_SERVER["PATH_INFO"]))
  {
    $path = $_SERVER["PATH_INFO"];
    $sent = false;

    if (preg_match("!^/dists/([\\w-]+)/([\\w-]+)/binary-(\\w+)/(Packages(?:[.]gz|[.]bz2)?)$!", $path, $matches))
      $sent = sendPackageIndex(scanPackages($matches[3]), $matches[4]);

    if (preg_match("!^/dists/([\\w-]+)/([\\w-]+)/source/(Sources(?:[.]gz|[.]bz2)?)$!", $path, $matches))
      $sent = sendPackageIndex("", $matches[3]);

    if (preg_match("!^/(Packages(?:[.]gz|[.]bz2)?)$!", $path, $matches))
      $sent = sendPackageIndex(scanPackages(CYDIA_TARGET), $matches[1]);

    if (preg_match("!^.*(/.+[.]deb)$!", $path, $matches))
      $sent = sendPackageFile(PACKAGES_FOLDER.$matches[1]);

    if (!$sent)
    {
      header("HTTP/1.0 404 Not Found");
      print("<h1>PHP Aptitude Repository</h1>");
      print("<h2>Unknown resource request</h2>");
    }
  }

?>
