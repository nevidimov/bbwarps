# bbwarps
(anonymous) bulletin board with automated retard protection system written in PHP, that uses CSV-like file as database
## Configuring settings
```php
    const ERROR_HTML = "html-error"; // Path to folder with error HTML templates
    const TEMPLATE_HTML = "html-templates";// Path to folder with HTML templates for post, header, etc.
    const ASSET_PATH="assets";
    //config - captcha
    const CAPTCHA_PATH = "captcha"; // Path to folder with captcha images
    const CAPTCHA_ERROR = 5; // Amount of wrong attemps before user is blocked
    const CAPTCHA_MAX_RANDOM = 99999; //Max random number
    const CAPTCHA_FONT = "assets/font.ttf"; //Font path
    const CAPTCHA_NOISE = 450*150; //Noise level. Higher - more noise
    const CAPTCHA_MIN_AMP = 1; //Distortion amplitude (minimal value)
    const CAPTCHA_MAX_AMP = 3; // --//-- (max value)
    const CAPTCHA_MIN_FREQ = 3; //Distrotion frequency (minimal value)
    const CAPTCHA_MAX_FREQ = 5;// --//-- (max value)
    const CAPTCHA_MIN_FONT = 13; // Min font size
    const CAPTCHA_MAX_FONT = 20; // Max font size
    const CAPTCHA_MIN_ANGLE = -8; //Min text angle
    const CAPTCHA_MAX_ANGLE = 8; // Max text angle
    //config - user information
    const LAST_VISIT_KEY = "!!CHANGEME!!"; //Change those keys!!!!!!!!!!!
    const FIRST_VISIT_KEY = "!!CHANGEME!!";
    const POST_QUANTITY_KEY = "!!CHANEME!!";
    const LAST_POST_KEY = "!!CHANGEME!!";
    const USER_SALT = "!!CHANGEME!!";
    const ENC_COOKIES = TRUE; // Encrypt cookies?
    const USER_CIPHER = "AES-128-CBC"; 
    const USER_COOKIE_EXPIRE = 3600*24*30;
    const HTTPS_COOKIE = FALSE;
    const HTTP_ONLY_COOKIE = TRUE;
    //config - database
    const DATABASE_PATH = "database"; //Path to database folder
    const MAX_THREADS = 50; //Maximum amount of threads to be alive (not tested)
    const IP_ENC_KEY = "!!CHANGEME!!"; 
    const IP_CIPHER = "AES-128-CBC";
    const IMAGE_PATH = "images"; //Path to folder with images
    //config posting
    const PRO_DELAY=10;  //Cooldown time for old user
    const NORM_DELAY=60; //Cooldown for newbie
    const INIT_DELAY=120;//Inital cooldown for newbie
    const PRO_POST_QUANTITY=10; //Amount of posts requred to become an old poster
    const PRO_AND=TRUE;// Posts AND time, or Post OR time?
    const PRO_TIME=1800;//Amount of time needed to become an old user
    //config - image posting
    const IMG_POST_QUANTITY = 5; //Amount of posts required before you can post images
    const IMG_AND = TRUE; // Post AND time, OR Post OR time?
    const IMG_TIME = 3600; // Cooldown before new user can post images
    const MAX_IMG_SIZE=4096*1024; //Max image size, in bytes I think
    const ALLOWED_EXT=array("jpg", "jpeg", "png", "gif", "pdf", "webp", "webm", "mp4");
    //config - text posting
    const MAX_TEXT_LENGTH = 5000; //Max post length
    const MAX_TEXT_ENDL = 15; // Max amount of breaklines per post
```
It should be self-explanatory, I think. In case it is not, feel free to ask me via issues.  
Also you can rename "Index.php" to anything you want, it should still work

## Admin panel

Do it yourself. This way it will be more secure (because it is not standartized in any way), and you will have features YOU need. Plus you will get an idea how this engine acutally works. 

## Lazy setup
Just download the repository, it should work
You need to restrict access to database/posts.csv if you value privacy of your users
Oh, and you might need gb PHP lib and openSSL PHP lib. Maybe something else, I don't remember

## DEMO

http://shittyboard.cba.pl/
