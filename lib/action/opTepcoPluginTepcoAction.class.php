<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * Tepco actions.
 *
 * @package    OpenPNE
 * @subpackage Tepco
 * @author     @hiroyaxxx
 * @version    SVN: $Id: actions.class.php 9301 2008-05-27 01:08:46Z dwhittle $
 */
class opTepcoPluginTepcoActions extends sfActions
{
  public function executeGetData(sfWebRequest $request)
  {
    $urlTepco  = "http://www.tepco.co.jp/forecast/html/images/juyo-j.csv";
    $cacheFile = sfConfig::get('sf_cache_dir') . "/opTepcoPluginData";
    $timeFile  = sfConfig::get('sf_cache_dir') . "/opTepcoPluginTime";

    if ($this->makeCache($urlTepco, $cacheFile, $timeFile))
    {
      if (FALSE == ($fp = fopen($cacheFile, "r")))
      {
        return;
      }
    }

    $update  = fgets($fp, 128);
    $dummy   = fgets($fp, 128);
    $peakinf = fgets($fp, 128);
    $dummy   = fgets($fp, 128);
    $dummy   = fgets($fp, 128);

    $a = split(',', $peakinf);
    $capacity = $a[0];

    $data = array();
    while (($buf = fgets($fp, 128)) !== false)
    {
      $a = split(',', $buf);
      $data[] = array('date'=>$a[0], 'time'=>$a[1], 'current'=>$a[2], 'previous'=>$a[3]);
    }

    $date = "";
    $time = "";
    $curr = 0;
    $prev = 0;

    foreach($data as $val)
    {
      if ($val['current'] == 0)
      {
        break;
      }
      $date = $val['date'];
      $time = $val['time'];
      $curr = doubleval($val['current']);
      $prev = doubleval($val['previous']);
    }

    $rate = round($curr * 100.0 / $capacity, 0);
    $g = (int)round($rate);

    $imgFile = "skin00.jpg";
    $color = "#0d0";

    if ($rate >= 95)
    {
      $imgFile = "skin95.jpg";
      $color = "#f00";
    }
    else if ($rate >= 90)
    {
      $imgFile = "skin90.jpg";
      $color = "#f00";
    }
    else if ($rate >= 85)
    {
      $imgFile = "skin85.jpg";
      $color = "#fa0";
    }
    else if ($rate >= 80)
    {
      $imgFile = "skin80.jpg";
      $color = "#fa0";
    }
    else if ($rate >= 60)
    {
      $imgFile = "skin60.jpg";
    }
    else if ($rate >= 40)
    {
      $imgFile = "skin40.jpg";
    }
    else if ($rate >= 20)
    {
      $imgFile = "skin20.jpg";
    }

    preg_match('/(.*)\/.*\/.*/', $date, $year);
    preg_match('/.*\/(.*)\/.*/', $date, $month);
    preg_match('/.*\/.*\/(.*)/', $date, $day);
    preg_match('/(.*):(.*)/', $time, $time2);

    $toJson = array (
      'year'     => $year[1],
      'month'    => sprintf("%02d", $month[1]),
      'day'      => sprintf("%02d", $day[1]),
      'time'     => sprintf("%02d:%02d", $time2[1], $time[2]),
      'used'     => $curr,
      'capacity' => $capacity,
      'rate'     => $rate,
      'img'      => $imgFile,
      'color'    => $color,
    );

    $this->getResponse()->setHttpHeader("X-JSON", '('.json_encode($toJson).')');
    return sfView::HEADER_ONLY;
  }


  function makeCache($urlTepco, $cacheFile, $timeFile, $updateSeconds = 600)
  {
    $oldTime = 0;
    $newTime = strtotime(date("Y/m/d g:i"));
    $dataTepco = '';
  
    if (file_exists($timeFile) && file_exists($cacheFile))
    {
      $h = fopen("$timeFile", "r");
      $oldTime = strtotime(fgets($h, 128));
      fclose($h);
  
      if ($newTime - $oldTime < $updateSeconds)
      {
        return 1;
      }
    }
  
    if (($responce = @file_get_contents($urlTepco)) == FALSE)
    {
      // URL先のファイルが開けない等
      return 0;
    }
  
    $h = fopen("$timeFile", "w");
    fputs($h, date("Y/m/d g:i"));
    fclose($h);
  
    // Cache作成、UTF8で保存
    $utf8Text = mb_convert_encoding($responce, 'utf8', 'sjis-win');
    $h = fopen($cacheFile, "w");
    fwrite($h, $utf8Text);
    fclose($h);
    return 2;
  }

}
