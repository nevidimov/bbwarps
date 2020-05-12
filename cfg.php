<?php
    date_default_timezone_set('UTC');
    const ERROR_HTML = "html-error";
    const TEMPLATE_HTML = "html-templates";
    const ASSET_PATH="assets";
    //config - captcha
    const CAPTCHA_PATH = "captcha";
    const CAPTCHA_ERROR = 5;
    const CAPTCHA_MAX_RANDOM = 99999;
    const CAPTCHA_FONT = "assets/font.ttf";
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
    const LAST_VISIT_KEY = "!!CHANGE ME !!";
    const FIRST_VISIT_KEY = "!!CHANGE ME !!";
    const POST_QUANTITY_KEY = "!!CHANGE ME !!";
    const LAST_POST_KEY = "!!CHANGE ME !!";
    const USER_SALT = "!!CHANGE ME !!";
    const ENC_COOKIES = TRUE;
    const USER_CIPHER = "AES-128-CBC";
    const USER_COOKIE_EXPIRE = 3600*24*30;
    const HTTPS_COOKIE = FALSE;
    const HTTP_ONLY_COOKIE = TRUE;
    //config - database
    const DATABASE_PATH = "database";
    const MAX_THREADS = 50;
    const IP_ENC_KEY = "!!CHANGE ME !!";
    const IP_CIPHER = "AES-128-CBC";
    const IMAGE_PATH = "images";
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
    const ALLOWED_EXT=array("jpg", "jpeg", "png", "gif", "pdf", "webp", "webm", "mp4");
    //config - text posting
    const MAX_TEXT_LENGTH = 5000;
    const MAX_TEXT_ENDL = 30;
    //config - bans
    const IP_BAN_REDIRECT = "https://www.google.com/search?q=how+to+become+homosexual";
    const FVISIT_BAN_REDIRECT = "https://www.google.com/search?q=how+to+become+homosexual";
    //Default strings
    const ANONYMOUS_NAME = "Anonymous";
    const USER_AGE_ERROR = "[hidden]";
?>
