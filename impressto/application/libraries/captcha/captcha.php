<?php
/**
 * Description of Era Captcha Class
 * 
 * This class generate captcha images.
 *
 * @author Erdem Arslan <erdemsaid@gmail.com>
 * @copyright (c) 2012, Erdem Arslan
 * @version v.3.0
 * 
 * 
 * SPECIAL THANKS AND OTHER LICENSE
 * 
 * #############################################################################
 * # Copyright (c) 2012,  DBA Astigmatic (AOETI) "Oregano"                     #                
 * # @author Brian J. Bonislawsky <astigma@astigmatic.com>                     #
 * # @license Brian J. Bonislawsky                                             #
 * # Commercial-use friendly                                                   #
 * #############################################################################
 * 
 * 
 */
session_start();
class Captcha {
    // Class Variables
    
    # Encrypt Variables
    protected $salt                 = 'Era_Captcha';
    private $password; 
    private $blacklist              = array('password','img','data','width','lang','blacklist','height');                // Which variables doesn't redefine?
    
    # This variables for saving captcha key. For more information please look PHP Cookies and Sessions.
    # You can choose only cookie, only session or both of cookie and session validation.
    protected $validate_type        = 'both';                                     // You will be set cookie, session or both
    
    # Session Variables
    protected $session_name         = '_era_captcha_';                              // Your session name
    
    # Cookie variables!
    protected $cookie_name          = '_era_captcha_';                              // Your cookie name
    protected $cookie_expire        = '360';                                        // in seconds - 0 (zero) expire when close browser.
    protected $cookie_path          = '/';                                          // cookie path
    protected $cookie_domain        = '';                                           // cookie domain
    protected $cookie_secure        = false;                                        // boolean (true or false)
    protected $cookie_httponly      = false;                                        // boolean (true or false)
    
    # Captcha Variables
    protected $font_dir             = './fonts';
    protected $wordlist             = 'wordlist.php';
    protected $random_chars_lenght  = 7;
    protected $security_type        = 0;                                            // 0 - random | 1 - math | 2 - Random words and numbers | 3 - dictionary
    protected $font_size            = '14';
    protected $bgcolor              = array('#FFFFFF','#CDEB8B','#C3D9FF','#EEEEEE','#F9F7ED');
    protected $colors               = array('#D01F3C','#356AA0','#3F4C6B','#6BBA70','#73880A','#C79810','#D15600','#008C00','#FF1A00');
    
    # Log Variables
    protected $log                  = true;
    protected $log_dir              = './logs';
    protected $log_file             = 'logs';
    protected $log_ext              = 'log';
    protected $log_daily            = true;
    protected $log_level            = 2;                                            // 0 - All (Error + Warning + Info) | 1 - Error | 2 - Warning + Error


    private $height                 = '30';
    private $width                  = '20';
    private $img;
    private $data;
    
    
    
    
    
    ############################################################################
    # CLASS MAIN FUNCTIONS                                                     #
    # DONT EDIT BELOW                                                          #
    ############################################################################
    
    /**
     * @access public
     */
    public function __construct($array=array(),$value=null) {
        # Ask Mcrypt Encrypt Library
        if (!function_exists('mcrypt_encrypt'))
        {
            $this->_log('Your server not support Mcrypt Encrypt Library. This class require Mcrypt Encrypt Library.', 1);
            die();
        }
        # Ask GD Library
        elseif (!function_exists('gd_info'))
        {
            $this->_log('Your server not support GD Library. This class require GD Library.', 1);
            die();
        } else {
            # Set variables if defined!
            $this->set_variables($array,$value);
            # Generate Password
            $this->_prepare_password();
            # Recalculate width and height variables with font size
            $this->width = $this->font_size + 10;
            $this->height= $this->font_size + 20;
            $this->_log('Class started successfully. Every thing gone ok.', 3);
        }
    }
    
    /**
     * @access public
     * @param array,string $array,$value This function redefine class variables.
     */
    public function set_variables($array,$value=null) {
        
        
        if (is_array($array))
        {
            if (count($array) > 0) {
                $this->_prep_variables($array);
            }
        } else {
            $new_array = array();
            $new_array[$array] = $value;
            $this->_prep_variables($new_array);
        }
    }
    
    /**
     * @access public
     * @return image Create captcha images :)
     */
    public function create() {
        # Prepare Header Content Type
        header('Content-Type:image/png');
        # Prepare Security Type
        $this->_prep_security_type();
        # Recalculate width
        $width = ($this->width + (strlen($this->data['decrypt']) * $this->font_size));
        # Create image area
        $this->img = imagecreatetruecolor($width,$this->height);
        # Define background color
        $bgcolor = $this->_get_color($this->bgcolor);
        $bg = imagecolorallocate($this->img,$bgcolor[0],$bgcolor[1],$bgcolor[2]);
        # Fill background color to image
	imagefill($this->img,0,0,$bg);
        # Get Fonts in array
        $fonts = $this->_get_fonts();
        //die(print_r($fonts));
        # Captcha chars
        $chars = $this->data['decrypt'];
        # Paint chars with different color
	for($i = 0 ; $i < strlen($chars) ; $i++)
	{
            # Select a random font
            $font   = $fonts[rand(0,count($fonts)-1)];
            # Select a random color
            $color  = $this->colors[rand(0,count($this->colors)-1)];
            # Change HEX color to RGB Color
            $color  = $this->_hex2rgb($color);
            $color  = imagecolorallocate($this->img,$color[0],$color[1],$color[2]);
            imagettftext($this->img, $this->font_size, 0, $this->font_size + ($i * $this->font_size), ($this->font_size+10), $color, $font ,$chars[$i]);
	}
        # Create Session or Cookie Data
        $this->_save_captcha_data();
        # Create Image Finally
        imagepng($this->img);
        # Clear progress memory
        imagedestroy($this->img);
    }
    
    
    /**
     * @access public
     * @param string,string $captcha,$salt
     * @return boolean This function return boolean type data. True or false
     */
    public function validate($captcha,$salt=NULL) {
        $return = false;
        if ($salt !== NULL) {
            $this->salt = $salt;
        }
        switch ($this->validate_type) {
            case 'session' :
                $session = $_SESSION[$this->session_name];
                $decrypted = $this->decrypt($session);
                if ($decrypted) {
                    if ($decrypted == $captcha) {
                        $return = true;
                    } else {
                        $return = false;
                    }
                } else {
                    $return = false;
                }
                
            break;
        
            case 'cookie' :
                $cookie = $_COOKIE[$this->cookie_name];
                $decrypted = $this->decrypt($cookie);
                if ($decrypted) {
                    if ($decrypted == $captcha) {
                        $return = true;
                    } else {
                        $return = false;
                    }
                } else {
                    $return = false;
                }
            break;
        
            case 'both' :
                $session = $_SESSION[$this->session_name];
                $cookie = $_COOKIE[$this->cookie_name];
                $decrypted_s = $this->decrypt($session);
                $decrypted_c = $this->decrypt($cookie);
                if ($decrypted_s == $decrypted_c)
                {
                    if ($decrypted_s == $captcha) {
                        $return = true;
                    } else {
                        $return = false;
                    }
                } else {
                    $return = false;
                }
            break;
        }
        
        return $return;
    }
    
    

    
    
    ############################################################################
    # ENCRYPTING FUNCTIONS                                                     #
    ############################################################################
    
    /**
     * @access private
     * @param string $decrypted Value for encrypting.
     * @return string This function return encrypted value
     * @license http://us.php.net/manual/en/book.mcrypt.php#107483 Anonymous
     */
    private function encrypt($decrypted) {
        $key = hash('SHA256', $this->salt . $this->password, true);
        srand(); $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
        if (strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22) return false;
        $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted . md5($decrypted), MCRYPT_MODE_CBC, $iv));
        return $iv_base64 . $encrypted;
   }
   
   /**
    * @access private
    * @param string $encrypt Value for decrypting.
    * @return string This function return decrypted value.
    * @license http://us.php.net/manual/en/book.mcrypt.php#107483 Anonymous
    */
   private function decrypt($encrypted) {
        $key = hash('SHA256', $this->salt . $this->password, true);
        $iv = base64_decode(substr($encrypted, 0, 22) . '==');
        $encrypted = substr($encrypted, 22);
        $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
        $hash = substr($decrypted, -32);
        $decrypted = substr($decrypted, 0, -32);
        if (md5($decrypted) != $hash)
        {
            $this->_log('Encrypted value dont decrypt with this salt and password. Please check them!', 2);
            return false;
        } else {
            return $decrypted;
        }
    }
    
    ############################################################################
    # PREPAIRING FUNCTIONS                                                     #
    ############################################################################
    
    /**
     * @access private
     * @return string This function define $this->password variable.
     */
    private function _prepare_password()
    {
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $this->password = md5($ip_address . '_' . $user_agent);
        return;
    }
    
    /**
     * @access private
     * @param array $array Setting Values
     * @return none
     */
    private function _prep_variables($array)
    {
        if (is_array($array) and count($array) > 0)
        {
            foreach($array as $key=>$value)
            {
                foreach ($this->blacklist as $b) {
                    if ($b != $key) {
                        if (property_exists(__CLASS__, $key))
                        {
                            $this->$key = $value;
                        }
                    }
                    
                }
                
            }
        }
    }
    
    /**
     * @access private
     * @param none
     * @return none Redefine $this->fonts;
     */
    private function _get_fonts() {
        if (is_dir($this->font_dir)) {
            $files = glob($this->font_dir.'/*.ttf');
            if (count($files) > 0) {
                return $files;
            } else {
                $this->_log('Your font directory is empty. Please put some font file to font directory.', 1);
                die();
            }
        } else {
            $this->_log('Font directory not found! Please define your font directory correct.', 1);
            die();
        }
    }
    
    /**
     * @access private
     * @return array Returned random chars and numbers combination.
     */
    private function _word() {
        $key = '';
        $chr = 'abcdefghijkmnprstuxvyz23456789ABCDEFGHJKLMNPRSTUWXYZ';
	for($i = 0 ; $i <= $this->random_chars_lenght; $i++)
	{
            $key .= $chr[rand(0,strlen($chr)-1)];
	}
	return array(
		'encrypt' => $this->encrypt($key),
        	'decrypt' => $key
	);
    }
    
    /**
     * @access private
     * @return array Returned math functions.
     */
    private function _math() {
        $number_1 = rand(1, 20);
        $number_2 = rand(1, 20);
        
        $what_i_do = array('+','-','*');
        $wid = rand(0,2);
        
        $return = array();
        
        if ($what_i_do[$wid] == '-') {
            if ($number_1 < $number_2) {
                $return['encrypt'] = $this->encrypt($number_2 - $number_1);
                $return['decrypt'] = $number_2 . ' - ' . $number_1 . ' = ?';
            } else {
                $return['encrypt'] = $this->encrypt($number_1 - $number_2);
                $return['decrypt'] = $number_1 . '  -  ' . $number_2 . '  =  ?';
            }
        } elseif ($what_i_do[$wid] == '*') {
            if ($number_1 < 11 and $number_2 < 11) {
                $return['encrypt'] = $this->encrypt($number_1 * $number_2);
                $return['decrypt'] = $number_1 . '  x  ' . $number_2 . '  =  ?';
            } else {
                $return['encrypt'] = $this->encrypt($number_1 + $number_2);
                $return['decrypt'] = $number_1 . '  +  ' . $number_2 . '  =  ?';
            }
        } else {
            $return['encrypt'] = $this->encrypt($number_1 + $number_2);
            $return['decrypt'] = $number_1 . '  +  ' . $number_2 . '  =  ?';
        }
        
        return $return;
    }
    
    /**
     * @access private
     * @return array Returned two different words.
     */
    private function _dictionary() {
        if (file_exists($this->wordlist)) {
            $dictionary = require($this->wordlist);
        } else {
            $this->_log('Your wordlist file not exists. Please choose your wordlist file correctly.', 1);
            die();
        }
        
        if (is_array($dictionary)) {
            $word_1 = $dictionary[rand(0, (count($dictionary) - 1))];
            $word_2 = $dictionary[rand(0, (count($dictionary) - 1))];

            return array(
                    'encrypt' => $this->encrypt($word_1 . ' ' . $word_2),
                    'decrypt' => $word_1 . '  ' . $word_2
            );
        } else {
            $this->_log('Your wordlist file is empty or file content is not array.', 1);
            die();
        }
    }
    
    /**
     * @access private
     * @return array This function is choose security type and return captcha data to $this->data;
     */
    private function _prep_security_type() {
        switch ($this->security_type) {
            default:
            case 0:
                $random = rand(1,3);
                if ($random == 1) {
                    $this->data = $this->_math();
                } elseif ($random == 2) {
                    $this->data = $this->_word();
                } else {
                    $this->data = $this->_dictionary();
                }
            break;
        
            case 1:
                $this->data = $this->_math();
            break;
        
            case 2:
                $this->data = $this->_word();
            break;
        
            case 3:
                $this->data = $this->_dictionary();
            break;
        }
    }
    
    /**
     * @access private
     * @param array $array Color array in rgb
     * @return array Return an array with RGB color R, G and B values
     */
    private function _get_color($array) {
        if (is_array($array)) {
            $random_color = $array[rand(0, count($array) - 1)];
            return $this->_hex2rgb($random_color);
        } else {
            $this->_log('Your color value dont seems an array. Please check it.', 1);
            die();
        }
    }
    
    /**
     * @access private
     * @param string $hex HEX Color value
     * @return array Return RGB Color in array(0=>R,1=>G,2=>B)
     * @license http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/ c.bavota
     */
    private function _hex2rgb($hex) {
      $hex = str_replace("#", "", $hex);

      if(strlen($hex) == 3) {
         $r = hexdec(substr($hex,0,1).substr($hex,0,1));
         $g = hexdec(substr($hex,1,1).substr($hex,1,1));
         $b = hexdec(substr($hex,2,1).substr($hex,2,1));
      } else {
         $r = hexdec(substr($hex,0,2));
         $g = hexdec(substr($hex,2,2));
         $b = hexdec(substr($hex,4,2));
      }
      return array($r, $g, $b); // returns an array with the rgb values
   }
   
   /**
    * @access private
    * @return none This function save captcha data
    */
   private function _save_captcha_data() {
       $data = $this->data;
       switch ($this->validate_type) {
           default:
           case 'session' :
               $_SESSION[$this->session_name] = $data['encrypt'];
           break;
       
           case 'cookie' :
               $this->cookie_expire == 0 ? $expire = 0 : $expire = time() + $this->cookie_expire;
               setcookie($this->cookie_name, $data['encrypt'], $expire, $this->cookie_path, $this->cookie_domain, $this->cookie_secure, $this->cookie_httponly);
           break;
       
           case 'both' :
               # Create Session
               $_SESSION[$this->session_name] = $data['encrypt'];
               # Create Cookie
               $this->cookie_expire == 0 ? $expire = 0 : $expire = time() + $this->cookie_expire;
               setcookie($this->cookie_name, $data['encrypt'], $expire, $this->cookie_path, $this->cookie_domain, $this->cookie_secure, $this->cookie_httponly);
           break;
       }
   }
   
   /**
    * @access private
    * @param string,int $data,$lvl write in log
    * @return none This function only save logs.
    */
   private function _log($data,$lvl) {
       if ($this->log) {
           if (is_dir($this->log_dir)) {
               # Set level information
               if ($lvl == 1) {
                    $type = '[ERROR]';
               } elseif ($lvl == 2) {
                    $type = '[WARNING]';
               } else {
                    $type = '[INFO]';
               }
               $date = '[' . date('d.m.Y H:i:s',time()) . ']';
               
               if ($this->log_level == 0)
               {
                   # All logs
                   $data = $type . ' ' . $date . ' ' . $data . "\n";
                   $this->_write_log($data);
               } elseif ($this->log_level == 1) {
                   # Only Errors
                   if ($lvl == 1) {
                       $data = $type . ' ' . $date . ' ' . $data . "\n";
                       $this->_write_log($data);
                   }
               } else {
                   # Only Error and Warnings
                   if ($lvl == 1 or $lvl == 2) {
                       $data = $type . ' ' . $date . ' ' . $data . "\n";
                       $this->_write_log($data);
                   }
               }
           } else {
               # if we dont find log dir, redefine main folder and make a error!
               $this->log_dir = './';
               $this->_log('Your log dir not correct. We use your main folder for logs.', 1);
               $this->_log($data, $lvl);
           }
       }
   }
   
   /**
    * @access private
    * @param string $data
    * @return none
    */
   private function _write_log($data) {
       if ($this->log_daily) {
           $file = date('d-m-Y',time()) . '.' . $this->log_ext;
       } else {
           $file = $this->log_file . '.' . $this->log_ext;
       }
       
       $fopen = fopen($this->log_dir . DIRECTORY_SEPARATOR . $file, "a");
       fputs($fopen, $data);
       fclose($fopen);
   }

} // end class