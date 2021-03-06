<?php
    if ((include "cfg.php")!=TRUE){
        die ("Please run install.php first");
    }
    //General-purpose functions
    function imagettfstroketext(&$image, $size, $angle, $x, $y, &$textcolor, &$strokecolor, $fontfile, $text, $px) {
        for($c1 = ($x-abs($px)); $c1 <= ($x+abs($px)); $c1++)
            for($c2 = ($y-abs($px)); $c2 <= ($y+abs($px)); $c2++)
                $bg = imagettftext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);
    
       return imagettftext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
    }
    function redirect($url){
        ob_end_clean();
        header('Location: '.$url);
        die;
    }
    function getIP(){
        $ip=$_SERVER['REMOTE_ADDR'];
        return openssl_encrypt($ip, IP_CIPHER, IP_ENC_KEY);
    }
    function getRawIP(){
        $ip=$_SERVER['REMOTE_ADDR'];
        return $ip;
    }
    function ipCIDR ($IP, $CIDR) {
        list ($net, $mask) = explode ("/", $CIDR);
        $ip_net = ip2long ($net);
        $ip_mask = ~((1 << (32 - $mask)) - 1);
        $ip_ip = ip2long ($IP);
        $ip_ip_net = $ip_ip & $ip_mask;
        return ($ip_ip_net == $ip_net);
    }
    //ban
    function checkIPBan(){
        $ip=getRawIP();
        $file=openDatabase(DATABASE_PATH."/ip_ban.csv");
        while ($current=fgets($file)){
            $current=str_replace("\n", "", $current);
            $current=str_replace("\r", "", $current);
            if($current==$ip){
                redirect(IP_BAN_REDIRECT);
                die();
            }
            if(ipCIDR($ip, $current)){
                redirect(IP_BAN_REDIRECT);
                die();
            }
        }
        fclose($file);
    }
    function checkFvisitBan(){
        $file=openDatabase(DATABASE_PATH."/fvisit_ban.csv");
        while ($current=fgets($file)){
            $current=str_replace("\n", "", $current);
            $current=str_replace("\r", "", $current);
            if($current==$_SESSION["fvisit"]){
                redirect(FVISIT_BAN_REDIRECT);
                die();
            }
        }
        fclose($file);
    }
    //captcha
    function drawCaptcha(){
        session_start();
        $img=imagecreatetruecolor(450, 150);
        $ci=rand(0,2);
        $k=$_SESSION["incorrectPath"];
        for ($i=0; $i<3; $i++){
            if ($i==$ci){
                $s=glob($_SESSION["correctPath"]."/*");
                shuffle($s);
                $current=array_pop($s);
                $ext=strtolower(pathinfo($current, PATHINFO_EXTENSION));
                switch ($ext){
                    case "jpeg":
                    case "jpg":
                        $temp=imagecreatefromjpeg(realpath($current));
                        break;
                    case "png":
                        $temp=imagecreatefrompng(realpath($current));
                        break;
                }
                $size=getimagesize($current);
                imagecopyresampled ($img , $temp , 150*($i) , 0 , 0 , 0 , 150, 150 , $size[0] , $size[1]);
                imagedestroy($temp);
                imagettfstroketext($img, rand(CAPTCHA_MIN_FONT, CAPTCHA_MAX_FONT), rand(CAPTCHA_MIN_ANGLE,CAPTCHA_MAX_ANGLE) , (150*$i)+rand(0,50), rand(40,140),imagecolorallocate( $img ,255,255,255),imagecolorallocate( $img ,0,0,0), CAPTCHA_FONT, $_SESSION["correctNumber"], 1); 
            }else{
                $b=array_pop($k);
                $b=glob($b."/*");
                shuffle($b);
                $current=array_pop($b);
                $ext=strtolower(pathinfo($current, PATHINFO_EXTENSION));
                switch ($ext){
                    case "jpeg":
                    case "jpg":
                        $temp=imagecreatefromjpeg( realpath( $current));
                    break;
                        case "png":
                        $temp=imagecreatefrompng( realpath( $current));
                    break;
                }
                $size=getimagesize($current);
                imagecopyresampled($img , $temp , 150*($i) , 0 , 0 , 0 , 150, 150 , $size[0] , $size[1]);
                imagedestroy($temp);
                imagettfstroketext($img, rand(CAPTCHA_MIN_FONT, CAPTCHA_MAX_FONT), rand(CAPTCHA_MIN_ANGLE,CAPTCHA_MAX_ANGLE) , (150*$i)+rand(0,50), rand(40,140),imagecolorallocate( $img ,255,255,255),imagecolorallocate( $img ,0,0,0), CAPTCHA_FONT, rand(0, CAPTCHA_MAX_RANDOM), 1); 
            }
        }
        header('Content-Type: image/jpg');
        for ($i=0; $i<CAPTCHA_NOISE; $i++){
            imagesetpixel ( $img , rand(0,450) , rand(0,150) , imagecolorallocatealpha ( $img , rand(40,255-40)  , rand(40,255-40)  , rand(40,255-40)  , rand(100,110)));
        }
        $temp=imagecreatetruecolor(450, 150);
        $amp=rand(CAPTCHA_MIN_AMP, CAPTCHA_MAX_AMP);
        $freq=rand(CAPTCHA_MIN_FREQ, CAPTCHA_MAX_FREQ)*0.01;
        imagefill ($temp , 0 , 0 ,imagecolorallocate( $temp ,255,255,255));
        for ($i=0; $i<450; $i++){
            imagecopy ($temp, $img  , $i , ($amp/2)+sin($i*$freq)*$amp , $i , 0 , 1 , 150) ;
        }
        $amp=rand(CAPTCHA_MIN_AMP, CAPTCHA_MAX_AMP);
        $freq=rand(CAPTCHA_MIN_FREQ, CAPTCHA_MAX_FREQ)*0.01;
        for ($i=0; $i<150; $i++){
            imagecopy ($img, $temp , ($amp/2)+sin($i*$freq)*$amp , $i,  0 , $i , 450, 1) ;
        }
        imagejpeg($img);
        imagedestroy($img);
        imagedestroy($temp);
        die();
    }
    function generateCaptcha(){
        if (checkPro()){
            return file_get_contents(TEMPLATE_HTML."/pro.html");
        }
        if ($_SESSION["captchaError"]>CAPTCHA_ERROR){
            ob_end_clean();
            echo file_get_contents(ERROR_HTML."/captcha-bot.html");
            die();
        }
        $categories = glob (CAPTCHA_PATH."/*", GLOB_ONLYDIR);
        shuffle($categories);
        $category=array_pop($categories);
        $categories = glob ($category."/*", GLOB_ONLYDIR);
        shuffle($categories);
        $_SESSION["correctPath"]=array_pop($categories);
        $correctName=pathinfo($_SESSION["correctPath"], PATHINFO_FILENAME);
        $incorrect[]=array_pop($categories);
        $incorrect[]=array_pop($categories);
        $_SESSION["incorrectPath"]=$incorrect;
        $_SESSION["correctNumber"]=rand(0, CAPTCHA_MAX_RANDOM);
        return str_replace("<!-- CAPTCHA-IMAGE -->", $_SERVER["PHP_SELF"]."?captcha=".rand(1,9999999999) ,str_replace("<!-- CAPTCHA-CORRECT -->", $correctName,file_get_contents(TEMPLATE_HTML."/captcha.html")));
        
    }
    function checkCaptcha(){
        if (checkPro()){
            return TRUE;
        }
        if ($_SESSION["captchaError"]>CAPTCHA_ERROR){
            ob_end_clean();
            echo file_get_contents(ERROR_HTML."/captcha-bot.html");
            die();
        }elseif ($_SESSION["correctNumber"]==htmlspecialchars($_POST["captchaVerify"])){
            $_SESSION["captchaError"]=0;
            return true;
        }else{
            $_SESSION["captchaError"]+=1;
            ob_end_clean();
            echo file_get_contents(ERROR_HTML."/captcha-wrong.html");
            die();
        }
    }
    if (isset($_GET["captcha"])){
        drawCaptcha();
    }
    //user information
    function getUserInfo(){
        if(!isset($_COOKIE["sign"])){
            newUser();
            return false;
        }elseif (hash("sha512", $_COOKIE["fvisit"].$_COOKIE["lvisit"].USER_SALT.$_COOKIE["postq"].ENC_COOKIES.$_COOKIE["lpost"])!=$_COOKIE["sign"]){
            newUser();
            return false;
        }elseif (time()- ((ENC_COOKIES)?openssl_decrypt($_COOKIE["lvisit"], USER_CIPHER, LAST_VISIT_KEY):$_COOKIE["lvisit"])>USER_COOKIE_EXPIRE){
            newUser();
            return false;
        }
        session_start();
        if(ENC_COOKIES){
            $_SESSION["fvisit"]=openssl_decrypt($_COOKIE["fvisit"], USER_CIPHER, FIRST_VISIT_KEY);
            $_SESSION["postq"]=openssl_decrypt($_COOKIE["postq"], USER_CIPHER, POST_QUANTITY_KEY);
            $_SESSION["lvisit"]=openssl_decrypt($_COOKIE["lvisit"], USER_CIPHER, LAST_VISIT_KEY);
            $_SESSION["lvisit"]=openssl_decrypt($_COOKIE["lpost"], USER_CIPHER, LAST_POST_KEY);
        }else{
            $_SESSION["fvisit"]=$_COOKIE["fvisit"];
            $_SESSION["postq"]=$_COOKIE["postq"];
            $_SESSION["lvisit"]=$_COOKIE["lvisit"];
            $_SESSION["lpost"]=$_COOKIE["lpost"];
        }
    }
    function newUser(){
        if (file_get_contents(DATABASE_PATH."/newUser")!="1"){
            die("No new users are allowed.");
        }
        if ($_POST["cookieAccept"]=="YES"){
            $_SESSION["fvisit"]=time();
            $_SESSION["postq"]=0;
            $_SESSION["lpost"]=0;
            writeUserInfo();
            redirect($_SERVER["PHP_SELF"]);
            die();
        }else{
            echo str_replace("<!-- URL -->", $_SERVER["PHP_SELF"] ,file_get_contents(TEMPLATE_HTML."/header.html")).file_get_contents(TEMPLATE_HTML."/cookie-and-rules.html");
            die();
        }
    }
    function writeUserInfo(){
        if(ENC_COOKIES){
            $fvisit=openssl_encrypt($_SESSION["fvisit"], USER_CIPHER, FIRST_VISIT_KEY);
            $postq=openssl_encrypt($_SESSION["postq"], USER_CIPHER, POST_QUANTITY_KEY);
            $lvisit=openssl_encrypt(time(), USER_CIPHER, LAST_VISIT_KEY);
            $lpost=openssl_encrypt($_SESSION["lpost"], USER_CIPHER, LAST_POST_KEY);
        }else{
            $fvisit=$_SESSION["fvisit"];
            $postq=$_SESSION["postq"];
            $lvisit=time();
            $lpost=$_SESSION["lpost"];
        }
        $time=time();
        setCookie("fvisit", $fvisit, $time+USER_COOKIE_EXPIRE, "", "", HTTPS_COOKIE, HTTP_ONLY_COOKIE);
        setCookie("lvisit", $lvisit, $time+USER_COOKIE_EXPIRE, "", "", HTTPS_COOKIE, HTTP_ONLY_COOKIE);
        setCookie("postq", $postq, $time+USER_COOKIE_EXPIRE, "", "", HTTPS_COOKIE, HTTP_ONLY_COOKIE);
        setCookie("lpost", $lpost, $time+USER_COOKIE_EXPIRE, "", "", HTTPS_COOKIE, HTTP_ONLY_COOKIE);
        setCookie("sign", hash("sha512",$fvisit.$lvisit.USER_SALT.$postq.ENC_COOKIES.$lpost), $time+USER_COOKIE_EXPIRE, "", "", HTTPS_COOKIE, HTTP_ONLY_COOKIE);
        if (isset($_POST["postName"])){
            setCookie("postName", getName(), $time+USER_COOKIE_EXPIRE, "", "", HTTPS_COOKIE, HTTP_ONLY_COOKIE);
        }
    }
    function checkPro(){
        if(PRO_AND){
            return $_SESSION["postq"]>PRO_POST_QUANTITY && time()-$_SESSION["fvisit"]>PRO_TIME;
        }else{
            return $_SESSION["postq"]>PRO_POST_QUANTITY || time()-$_SESSION["fvisit"]>PRO_TIME;
        }
    }
    function checkImagePosting(){
        if(IMG_AND){
            return $_SESSION["postq"]>IMG_POST_QUANTITY && time()-$_SESSION["fvisit"]>IMG_TIME;
        }else{
            return $_SESSION["postq"]>IMG_POST_QUANTITY || time()-$_SESSION["fvisit"]>IMG_TIME;
        }
    }
    function checkCooldown(){
        if(time()-$_SESSION["fvisit"]>INIT_DELAY){
            if(checkPro()){
                return time()-$_SESSION["lpost"]>PRO_DELAY;
            }else{
                return time()-$_SESSION["lpost"]>NORM_DELAY; 
            }
        }else{
            return FALSE;
        }
    }
    //database 
    function openDatabase($file, $write=false){
        if ($write){
             if ($f=fopen($file, "r+")){
                return $f;
            }else{
                ob_end_clean();
                echo file_get_contents(ERROR_HTML."/database-error.html");
                die();
            }
        }else{
            if ($f=fopen($file, "r")){
                return $f;
            }else{
                ob_end_clean();
                echo file_get_contents(ERROR_HTML."/database-error.html");
                die();
            }
        }
    }
    function getRow ($file){
        $str=fgets($file);
        if (!feof($file)){
            $str=explode("\t", str_replace("\n", "",$str));
            return $str;
        }else{
            return FALSE;
        }
    }
    function putRow ($file, $data){
        foreach ($data as $info){
            fwrite($file, $info."\t");
        }
        fseek ($file, ftell($file)-1);
        fwrite($file, "\n");
    }
    function showThreads(){
        $replies=array();
        $threads=array();
        $template=file_get_contents(TEMPLATE_HTML."/thread.html");
        $image_template=file_get_contents(TEMPLATE_HTML."/thread-image.html");
        $file=openDatabase(DATABASE_PATH."/posts.csv", FALSE);
        while($current = getRow($file)){
            if($current[1]!="T"){
                $replies[$current[1]]+=1;
            }else{
                $replies[$current[0]]+=1;
                $threads[$current[0]]=$current;
            }
        }
        fclose($file);
        foreach ($replies as $thread=>$replies_num){
            $current=$threads[$thread];
            if($img=glob(IMAGE_PATH."/images/".$current[0].".*")){
                    $output=$image_template;
                    $output=str_replace("<!-- IMG -->", $img[0], $output);
                    if ($thumb=glob(IMAGE_PATH."/thumbnails/".$current[0].".jpg")){
                        $output=str_replace("<!-- THUMB -->", $thumb[0], $output);
                    }else{
                        $ext=strtolower(pathinfo($img[0], PATHINFO_EXTENSION));
                        switch ($ext){
                            case "webm":
                            $output=str_replace("<!-- THUMB -->",ASSET_PATH."/webm.jpg",$output);
                            break;
                            case "mp4":
                            $output=str_replace("<!-- THUMB -->",ASSET_PATH."/mp4.jpg",$output);
                            break;
                            case "pdf":
                            $output=str_replace("<!-- THUMB -->",ASSET_PATH."/pdf.jpg",$output);
                            break;
                            default:
                            $output=str_replace("<!-- THUMB -->",ASSET_PATH."/big.jpg",$output);
                        }
                    }
                }else{
                    $output=$template;
                }
                $output=str_replace("<!-- TOPIC -->", $current[7],$output);
                $output=str_replace("<!-- REPLIES -->", (int) $replies_num-1,$output);
                $output=str_replace("<!-- ID -->", $current[0], $output);
                if (strstr($current[3], "userage")){
                    if($days=(int) filter_var($current[3], FILTER_SANITIZE_NUMBER_INT)){
                        $current[3]=ANONYMOUS_NAME;
                        if (time()-$days*3600*24<$_SESSION["fvisit"]){
                            $current[2]=USER_AGE_ERROR;
                        }
                    }
                }
                $output=str_replace("<!-- NAME -->", $current[3], $output);
                $output=str_replace("<!-- TEXT -->", $current[2], $output);
                $output=str_replace("<!-- THREADURL -->", $_SERVER["PHP_SELF"]."?thread=".$current[0], $output);
                echo $output;
        }
    }
    function showThread($id){
        $replies=array();
        $template=file_get_contents(TEMPLATE_HTML."/post.html");
        $image_template=file_get_contents(TEMPLATE_HTML."/post-image.html");
        $file=openDatabase(DATABASE_PATH."/posts.csv", FALSE);
        while($current = getRow($file)){
            if ($current[0]==$id){
                if ($current[1]=="T"){
                    $replies[]=$current;
                }else{
                    return showThread($current[1]);
                }
            }elseif($current[1]==$id){
                $replies[]=$current;
            }
        }
        fclose($file);
        if (sizeof($replies)==0){
            ob_end_clean();
            echo file_get_contents(ERROR_HTML."/not-found.html");
            die();
        }
        $replies=array_reverse($replies);
        $OPtemplate=file_get_contents(TEMPLATE_HTML."/thread.html");
        $OPimage_template=file_get_contents(TEMPLATE_HTML."/thread-image.html");
        foreach($replies as $current){
            if($current[0]!=$id){
                if($img=glob(IMAGE_PATH."/images/".$current[0].".*")){
                        $output=$image_template;
                        $output=str_replace("<!-- IMG -->", $img[0], $output);
                        if ($thumb=glob(IMAGE_PATH."/thumbnails/".$current[0].".jpg")){
                            $output=str_replace("<!-- THUMB -->", $thumb[0], $output);
                        }else{
                            $ext=strtolower(pathinfo($img[0], PATHINFO_EXTENSION));
                            switch ($ext){
                                case "webm":
                                $output=str_replace("<!-- THUMB -->",ASSET_PATH."/webm.jpg",$output);
                                break;
                                case "mp4":
                                $output=str_replace("<!-- THUMB -->",ASSET_PATH."/mp4.jpg",$output);
                                break;
                                case "pdf":
                                $output=str_replace("<!-- THUMB -->",ASSET_PATH."/pdf.jpg",$output);
                                break;
                                default:
                                $output=str_replace("<!-- THUMB -->",ASSET_PATH."/big.jpg",$output);
                            }
                        }
                    }else{
                        $output=$template;
                    }
                    if (strstr($current[3], "userage")){
                    if($days=(int) filter_var($current[3], FILTER_SANITIZE_NUMBER_INT)){
                        $current[3]=ANONYMOUS_NAME;
                        if (time()-$days*3600*24<$_SESSION["fvisit"]){
                            $current[2]=USER_AGE_ERROR;
                        }
                    }
                    }
                    if (strstr($current[3], "userage")){
                    if($days=(int) filter_var($current[3], FILTER_SANITIZE_NUMBER_INT)){
                        $current[3]=ANONYMOUS_NAME;
                        if (time()-$days*3600*24<$_SESSION["fvisit"]){
                            $current[2]=USER_AGE_ERROR;
                        }
                    }
                    }
                    $output=str_replace("<!-- ID -->", $current[0], $output);
                    $output=str_replace("<!-- NAME -->", $current[3], $output);
                    $output=str_replace("<!-- TEXT -->", $current[2], $output);
                    echo $output;
            }else{
                if($img=glob(IMAGE_PATH."/images/".$current[0].".*")){
                        $output=$OPimage_template;
                        $output=str_replace("<!-- IMG -->", $img[0], $output);
                        if ($thumb=glob(IMAGE_PATH."/thumbnails/".$current[0].".jpg")){
                            $output=str_replace("<!-- THUMB -->", $thumb[0], $output);
                        }else{
                            $ext=strtolower(pathinfo($img[0], PATHINFO_EXTENSION));
                            switch ($ext){
                                case "webm":
                                $output=str_replace("<!-- THUMB -->",ASSET_PATH."/webm.jpg",$output);
                                break;
                                case "mp4":
                                $output=str_replace("<!-- THUMB -->",ASSET_PATH."/mp4.jpg",$output);
                                break;
                                case "pdf":
                                $output=str_replace("<!-- THUMB -->",ASSET_PATH."/pdf.jpg",$output);
                                break;
                                default:
                                $output=str_replace("<!-- THUMB -->",ASSET_PATH."/big.jpg",$output);
                            }
                        }
                    }else{
                        $output=$OPtemplate;
                    }
                    $output=str_replace("<!-- TOPIC -->", $current[7],$output);
                    $output=str_replace("<!-- REPLIES -->", (int) sizeof($replies)-1,$output);
                    $output=str_replace("<!-- ID -->", $current[0], $output);
                    $output=str_replace("<!-- NAME -->", $current[3], $output);
                    $output=str_replace("<!-- TEXT -->", $current[2], $output);
                    $output=str_replace("<!-- THREADURL -->", $_SERVER["PHP_SELF"]."?thread=".$current[0], $output);
                    $threadNumber=$current[0];
                    echo $output;
            }
        }
        return $threadNumber;
    }
    function showStream(){
        $out="";
        $template=file_get_contents(TEMPLATE_HTML."/thread.html");
        $image_template=file_get_contents(TEMPLATE_HTML."/thread-image.html");
        $file=openDatabase(DATABASE_PATH."/posts.csv", FALSE);
        while ($data=getRow($file)){
            if($img=glob(IMAGE_PATH."/images/".$data[0].".*")){
                $current=$image_template;
                $current=str_replace("<!-- IMG -->", $img[0], $current);
                if ($thmb=glob(IMAGE_PATH."/thumbnails/".$data[0].".jpg")){
                    $current=str_replace("<!-- THUMB -->", IMAGE_PATH."/thumbnails/".$data[0].".jpg", $current);
                }else{
                    $ext=strtolower(pathinfo($img[0], PATHINFO_EXTENSION));
                    switch ($ext){
                        case "webm":
                        $current=str_replace("<!-- THUMB -->",ASSET_PATH."/webm.jpg",$current);
                        break;
                        case "mp4":
                        $current=str_replace("<!-- THUMB -->",ASSET_PATH."/mp4.jpg",$current);
                        break;
                        case "pdf":
                        $current=str_replace("<!-- THUMB -->",ASSET_PATH."/pdf.jpg",$current);
                        break;
                        default:
                        $current=str_replace("<!-- THUMB -->",ASSET_PATH."/big.jpg",$current);
                    }
                }
            }else{
                $current=$template;
            }
            if (strstr($data[3], "userage")){
                    if($days=(int) filter_var($data[3], FILTER_SANITIZE_NUMBER_INT)){
                        $data[3]=ANONYMOUS_NAME;
                        if (time()-$days*3600*24<$_SESSION["fvisit"]){
                            $data[2]=USER_AGE_ERROR;
                        }
                    }
                }
            $current=str_replace("<!-- REPLIES -->", "other",$current);
            $current=str_replace("<!-- ID -->", $data[0], $current);
            $current=str_replace("<!-- NAME -->", $data[3], $current);
            $current=str_replace("<!-- TEXT -->", $data[2], $current);
            $current=str_replace("<!-- THREADURL -->", $_SERVER["PHP_SELF"]."?thread=".$data[0], $current);
            echo $current;
        }
        fclose($file);
    }
    function showForm($thread, $captcha="", $topic=false){
        if (!checkImagePosting()){
            $noup=file_get_contents(TEMPLATE_HTML."/no-upload.html");
        }else{
            $noup="";
        }
        if(!$topic){
            
            echo str_replace("<!-- UPLOAD -->", $noup, str_replace("<!-- NAME -->", $_COOKIE["postName"], str_replace("<!-- CAPTCHA -->", $captcha, str_replace("<!-- THREAD -->", "$thread", file_get_contents(TEMPLATE_HTML."/form.html")))));
        }else{
            echo str_replace("<!-- UPLOAD -->", $noup, str_replace("<!-- NAME -->", $_COOKIE["postName"], str_replace("<!-- CAPTCHA -->", $captcha, str_replace("<!-- THREAD -->", "$thread", file_get_contents(TEMPLATE_HTML."/form-thread.html")))));
        }   
    }
    //post handling
    function checkUpload(){
        if(!checkImagePosting()){
            return FALSE;
        }
        $temp=getimagesize($_FILES["postFile"]["tmp_name"]);
        $ext=strtolower(pathinfo($_FILES["postFile"]["name"], PATHINFO_EXTENSION));
        if(!in_array($ext, ALLOWED_EXT)){
            return FALSE;
        }
        if ($_FILES["postFile"]["size"]>MAX_IMG_SIZE){
            return FALSE;
        }
        return TRUE;
    }
    function getPost(){
        $txt=$_POST["postText"];
        if (strlen($txt)>MAX_TEXT_LENGTH){
            return FALSE;
        }
        if (substr_count($txt, "\n")>MAX_TEXT_ENDL){
            return FALSE;
        }
        if (strstr($txt, "\t")){
            return FALSE;
        }
        if (strstr($txt, "\u{200E}")){
            return FALSE;
        }
        if (strstr($txt, "\u{200F}")){
            return FALSE;
        }
        $txt=htmlspecialchars($txt)."\n";
        $txt=preg_replace('/(&gt;&gt;(\d+))/', '<a href="?thread=\\2#\\2"><font color="blue" class="bluetext">\\1</font></a>' , strip_tags($txt)); 
        $txt=preg_replace('/^(&lt;&lt;(.*))\n/m', '<font color="#ee6b00" class="orangetext">\\1</font>' . "\n", $txt);
        $txt=preg_replace('/^(&gt;(.*))\n/m', '<font color="green" class="greentext">\\1</font>' . "\n", $txt);   
        $txt=preg_replace('/^(&lt;(.*))\n/m', '<font color="red" class="redtext">\\1</font>' . "\n", $txt);
        $txt=substr($txt, 0, -1);
        $txt=str_replace("\n", "<br>", $txt);
        $txt=str_replace("\r", "", $txt);
        return $txt;
    } 
    function getName(){
        $txt=$_POST["postName"];
        if (strlen($txt)>128){
            return FALSE;
        }
        if ($txt == ""){
            return ANONYMOUS_NAME;
        }
        return preg_replace("/[^A-Za-z0-9_ .:]/", '', $txt);
    }
    function getTopic(){
        $txt=$_POST["postTopic"];
        if (strlen($txt)>512){
            return FALSE;
        }
        return preg_replace("/[^A-Za-z0-9_ .:]/", '', $txt);
    }
    function downloadImage($post){
        $ext=strtolower(pathinfo($_FILES["postFile"]["name"], PATHINFO_EXTENSION));
        if(!in_array($ext, ALLOWED_EXT)){
            return FALSE;
        }
        move_uploaded_file($_FILES["postFile"]["tmp_name"], IMAGE_PATH."/images/".$post.".".$ext);
        list($ogX, $ogY) = getimagesize(realpath(IMAGE_PATH."/images/".$post.".".$ext));
        if ($ogX*$ogY>2500*2500){
            return TRUE;
        }
        switch ($ext){
            case "jpg":
            case "jpeg":
            $og=imagecreatefromjpeg(realpath(IMAGE_PATH."/images/".$post.".".$ext));
            break;
            case "png":
            $og=imagecreatefrompng(realpath(IMAGE_PATH."/images/".$post.".".$ext));
            break;
            case "gif":
            $og=imagecreatefromgif(realpath(IMAGE_PATH."/images/".$post.".".$ext));
            break;
            case "webp":
            $og=imagecreatefromwebp(realpath(IMAGE_PATH."/images/".$post.".".$ext));
            break;
            default:
            return TRUE;
        }
        if ($ogX>300){
            $ngX=300;
            $ngY=$ogY*(300/$ogX);
        }elseif ($ogY>300){
            $ngY=300;
            $ngX=$ogX*(300/$ogY);
        }else{
            $ngY=$ogY;
            $ngX=$ogX;
        }
        $thumb=imagecreatetruecolor($ngX, $ngY);
        imagefill($thumb, 1,1, imagecolorallocate($thumb, 127,127,127));
        imagecopyresampled($thumb, $og, 0,0,0,0,$ngX, $ngY, $ogX, $ogY);
        imagejpeg($thumb, IMAGE_PATH."/thumbnails/".$post.".jpg", 60);
        imagedestroy($thumb);
        imagedestroy($og);
        return TRUE;
    }
    function posting(){
        $txt=getPost();
        $name=getName();
        $checkUpload=checkUpload();
        if ((!$checkUpload && strlen($txt) < 1)||($txt===FALSE)){
            ob_end_clean();
            echo file_get_contents(ERROR_HTML."/illegal.html");
            die;
        }
        if (!checkCooldown()){
            ob_end_clean();
            echo file_get_contents(ERROR_HTML."/cooldown.html");
            die;
        }
        //DB open
        $db=openDatabase(DATABASE_PATH."/posts.csv", TRUE);
        flock($db, LOCK_EX);
        $tempRand=rand(999,9999999);
        $temp=fopen (DATABASE_PATH."/temp".$tempRand.".csv", "x+");
        flock($temp, LOCK_EX);
        //Read DB
        $lastPost=getRow($db)[0];
        if($_POST["threadNumber"]!="T"){
            $threadFound=FALSE;
        }else{
            $threadFound=TRUE;
        }
        putRow($temp, array($lastPost+1, $_POST["threadNumber"], $txt, $name, getIP(), time(), $_SESSION["fvisit"], getTopic()));
        rewind($db);
        $threads=array();
        $unlink=array();
        while ($current=getRow($db)){
            if(!in_array($current[1], $threads) && sizeof($threads)<MAX_THREADS && $current[1]!="T"){
                $threads[]=$current[1];
            }
            if(!in_array($current[0], $threads) && sizeof($threads)<MAX_THREADS && $current[1]=="T"){
                $threads[]=$current[0];
            }
            if(in_array($current[1], $threads) || in_array($current[0], $threads)){
                putRow($temp, $current);
            }
            if($current[1]=="T" && $current[0]==$_POST["threadNumber"]){
                $threadFound=TRUE;
            }
            if(!in_array($current[1], $threads) && !in_array($current[0], $threads)){
                $unlink[]=glob(IMAGE_PATH."/images/".$current[0].".*")[0];
                $unlink[]=glob(IMAGE_PATH."/thumbnails/".$current[0].".*")[0];
            }
        }
        if($threadFound){
            if($checkUpload){
                if(!(downloadImage($lastPost+1))){
                    unlink (DATABASE_PATH."/temp".$tempRand.".csv");
                    echo file_get_contents(ERROR_HTML."/illegal.html");
                    die;
                }
            }
            foreach ($unlink as $current){
                unlink($current);
            }
            unlink (DATABASE_PATH."/posts.csv");
            rename(DATABASE_PATH."/temp".$tempRand.".csv", DATABASE_PATH."/posts.csv");
            $_SESSION["lpost"]=time();
            $_SESSION["postq"]+=1;
            writeUserInfo();
            redirect($_SERVER["PHP_SELF"]."?thread=".($lastPost+1)."#".($lastPost+1));
            die();
        }else{
            unlink (DATABASE_PATH."/temp".$tempRand.".csv");
            echo file_get_contents(ERROR_HTML."/illegal.html");
            die;
        }
    }
    //  MAIN 
    
    checkIPBan();
    getUserInfo();
    checkFvisitBan();
    if (isset($_POST["postButton"])){
        checkCaptcha();
        posting();
    }
    writeUserInfo();
    echo str_replace("<!-- URL -->", $_SERVER["PHP_SELF"], file_get_contents(TEMPLATE_HTML."/header.html"));
    echo file_get_contents(TEMPLATE_HTML."/nav-top.html");
    if (isset($_GET["thread"])){
        showForm(showThread($_GET["thread"]), generateCaptcha());
        goto footer;
    }
    if (isset($_GET["stream"])){
        showStream();
        goto footer;
    }
    showForm("T", generateCaptcha(), TRUE);
    showThreads();
    footer:
    echo file_get_contents(TEMPLATE_HTML."/nav-bottom.html");
    echo "<center>BBWARPS V0.4</center><hr>".str_replace("<!-- URL -->", $_SERVER["PHP_SELF"],file_get_contents(TEMPLATE_HTML."/footer.html"));
?>
