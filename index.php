<?php
date_timezone_set("Europe/Istanbul"); //Set timezone

/* FUNCTION FARM */
function url(){
  $protocol = 'http';
  if(isset($_SERVER['HTTPS']))
      $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";

  return $protocol . "://" . $_SERVER['SERVER_NAME'];
}
function clear_open_graph_cache() {
  $vars = array('id' => url(), 'scrape' => 'true');
  $body = http_build_query($vars);

  $fp = fsockopen('ssl://graph.facebook.com', 443);
  fwrite($fp, "POST / HTTP/1.1\r\n");
  fwrite($fp, "Host: graph.facebook.com\r\n");
  fwrite($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
  fwrite($fp, "Content-Length: ".strlen($body)."\r\n");
  fwrite($fp, "Connection: close\r\n");
  fwrite($fp, "\r\n");
  fwrite($fp, $body);
  fclose($fp);
}
function randomDate($start_date, $end_date) {
  $min = strtotime($start_date);
  $max = strtotime($end_date);
  $val = rand($min, $max);
  return date('d-m-Y H:i:s', $val);
}
function diffDate($date2, $date1) {
  $diff = abs(strtotime($date2) - strtotime($date1));

  $years = floor($diff / (365*60*60*24));
  $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
  $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
  $str = [
          "year" => "%d yıl",
          "month" => "%d ay",
          "day" => "%d gün"
          ];
  $msg = "";
  if ($years != 0)
    $msg .= sprintf($str["year"], $years).' ';
  if ($months != 0)
    $msg .= sprintf($str["month"], $months).' ';
  if ($days != 0)
    $msg .= sprintf($str["day"], $days).' ';
  return $msg."sonra öleceksin.";
}
function countDown($date) {
  $d = explode("-", $date);
  $ds = explode(" ", $d[2]);
  return $ds[0]."-".$d[1]."-".$d[0]." ".$ds[1];
}
/* FUNCTION FARM */

/* DEATH DATE VARS */
$today = date('d-m-Y H:i:s');
$death_date = randomDate($today, "31-12-2100 00:00:00");
$death_time = diffDate($death_date, $today);
/* DEATH DATE VARS */

if(strpos($_SERVER["HTTP_USER_AGENT"], "facebookexternalhit/") !== false || strpos($_SERVER["HTTP_USER_AGENT"], "Facebot") !== false) { //detecting facebook preview:
  clear_open_graph_cache(); //clearing scraper cache
  ?>
<title><?=$death_date?></title>
<meta charset="utf-8"/>
<meta name="description" content="<?=$death_time?>"/>
<?php
  exit();
} //Normal Visitors:
?>
<!DOCTYPE html>
<html>
<head>
  <title>Ne zaman öleceksin?</title>
  <meta charset="utf-8"/>
</head>
<body>
  <div style="width:450px; margin:0 auto;">
      <h1 id="death"><?=$death_time?></h1>
      <h2> --> <?=$death_date?></h2>
      Kopirayt © <?=url()?> | Created by <a href="https://www.tahribat.com/Members/end">End</a>
  </div>
<script>
var countDownDate = new Date("<?=countDown($death_date)?>").getTime();
var x = setInterval(function() {
    var now = new Date().getTime();
    var distance = countDownDate - now;
    
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    var years = 0;
    var months = 0;
    if (days > 365) {
        years = Math.floor(days / 365);
        days = days % 365;
    }
    if (days > 30) {
        months = Math.floor(days / 30);
        days = days % 30;
    }
    document.getElementById("death").innerHTML = years + " yıl " + months + " ay " + days + " gün " + hours + " saat "
    + minutes + " dakika " + seconds + " saniye sonra öleceksin.";
    
    if (distance < 0) {
        clearInterval(x);
        document.getElementById("death").innerHTML = "ÖLDÜN!";
    }
}, 1000);
</script>
</body>
</html>
