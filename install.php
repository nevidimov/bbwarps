<center>
<h1>BBWARPS install</h1></center>
Welcome to BBWARPS install tool.<br><br>
Dependencies: PHP-GD, OpenSSL.<br>
JS Dependencies (optional): linkify, jquery. (for more information check out html-templates/header.html 
<br><br>
Please configure following settings: <br>
NOTE: Board name and logo should be changed in HTML-templates.<br>
<form action="install.php" method="POST">
<textarea name="config" style="width:100%; height:100vh;">
&lt;?php
    date_default_timezone_set('UTC');
    const ERROR_HTML = &quot;html-error&quot;;
    const TEMPLATE_HTML = &quot;html-templates&quot;;
    const ASSET_PATH=&quot;assets&quot;;
    //config - captcha
    const CAPTCHA_PATH = &quot;captcha&quot;;
    const CAPTCHA_ERROR = 5;
    const CAPTCHA_MAX_RANDOM = 99999;
    const CAPTCHA_FONT = &quot;assets/font.ttf&quot;;
    const CAPTCHA_NOISE = 450*150;
    const CAPTCHA_MIN_AMP = 1;
    const CAPTCHA_MAX_AMP = 3;
    const CAPTCHA_MIN_FREQ = 3;
    const CAPTCHA_MAX_FREQ = 5;
    const CAPTCHA_MIN_FONT = 13;
    const CAPTCHA_MAX_FONT = 20;
    const CAPTCHA_MIN_ANGLE = -8;
    const CAPTCHA_MAX_ANGLE = 8;
    //config - user information
    const LAST_VISIT_KEY = &quot;!!CHANGE ME !!&quot;;
    const FIRST_VISIT_KEY = &quot;!!CHANGE ME !!&quot;;
    const POST_QUANTITY_KEY = &quot;!!CHANGE ME !!&quot;;
    const LAST_POST_KEY = &quot;!!CHANGE ME !!&quot;;
    const USER_SALT = &quot;!!CHANGE ME !!&quot;;
    const ENC_COOKIES = TRUE;
    const USER_CIPHER = &quot;AES-128-CBC&quot;;
    const USER_COOKIE_EXPIRE = 3600*24*30;
    const HTTPS_COOKIE = FALSE;
    const HTTP_ONLY_COOKIE = TRUE;
    //config - database
    const DATABASE_PATH = &quot;database&quot;;
    const MAX_THREADS = 50;
    const IP_ENC_KEY = &quot;!!CHANGE ME !!&quot;;
    const IP_CIPHER = &quot;AES-128-CBC&quot;;
    const IMAGE_PATH = &quot;images&quot;;
    //config posting
    const PRO_DELAY=10;
    const NORM_DELAY=60;
    const INIT_DELAY=90;
    const PRO_POST_QUANTITY= 5;
    const PRO_AND=TRUE;
    const PRO_TIME=600;
    //config - image posting
    const IMG_POST_QUANTITY = 2; 
    const IMG_AND = TRUE;
    const IMG_TIME = 600;
    const MAX_IMG_SIZE=4096*1024;
    const ALLOWED_EXT=array(&quot;jpg&quot;, &quot;jpeg&quot;, &quot;png&quot;, &quot;gif&quot;, &quot;pdf&quot;, &quot;webp&quot;, &quot;webm&quot;, &quot;mp4&quot;);
    //config - text posting
    const MAX_TEXT_LENGTH = 5000;
    const MAX_TEXT_ENDL = 30;
    //config - bans
    const IP_BAN_REDIRECT = &quot;https://www.google.com/search?q=how+to+become+homosexual&quot;;
    const FVISIT_BAN_REDIRECT = &quot;https://www.google.com/search?q=how+to+become+homosexual&quot;;
    //Default strings
    const ANONYMOUS_NAME = &quot;Anonymous&quot;;
    const USER_AGE_ERROR = &quot;[hidden]&quot;;
?&gt;
</textarea><br>
<input type="submit" value="Install">
</form>
<?php 
    if (isset($_POST["config"])){
    file_put_contents("cfg.php", $_POST["config"]);
    }
?>
