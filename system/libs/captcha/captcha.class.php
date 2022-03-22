<?php
    class CBCaptcha {
        const ICON_CAPTCHA = 'icon_captcha';
        const CAPTCHA_NOISE = 'icon_noise';
        const CAPTCHA_ICON_PATH = 'icon_path';
        const CAPTCHA_FIELD_HASH = 'captcha-hf';
        const CAPTCHA_FIELD_ID = 'captcha-idhf';

        private static $error;
        private static $captcha_id = 0;
        private static $session;
        private static $error_messages = array(
            'You\'ve selected the wrong image.',
            'No image has been selected.',
            'You\'ve not submitted any form.',
            'The captcha ID was invalid.'
        );

        public static function setIconsFolderPath($file_path) {
            $_SESSION[self::ICON_CAPTCHA][self::CAPTCHA_ICON_PATH] = (is_string($file_path)) ? $file_path : '';
        }

        public static function setIconNoiseEnabled($noise) {
            $_SESSION[self::ICON_CAPTCHA][self::CAPTCHA_NOISE] = (is_bool($noise)) ? $noise : false;
        }

        public static function setErrorMessages($wrongIcon = '', $noImage = '', $noForm = '', $invalidId = '') {
            if(!empty($wrongIcon) && is_string($wrongIcon)) {
                self::$error_messages[0] = $wrongIcon;
            }
            if(!empty($noImage) && is_string($noImage)) {
                self::$error_messages[1] = $noImage;
            }
            if(!empty($noForm) && is_string($noForm)) {
                self::$error_messages[2] = $noForm;
            }
            if(!empty($invalidId) && is_string($invalidId)) {
                self::$error_messages[3] = $invalidId;
            }
        }

        public static function getErrorMessage() {
            return self::$error;
        }

        public static function getCaptchaData($theme, $captcha_id) {
            $a = mt_rand(1, 91);
            $b = 0;

            self::$captcha_id = self::tryCreateSession($captcha_id, $theme);

            while($b === 0) {
                $c = mt_rand(1, 91);

                if($c !== $a) {
                    $b = $c;
                }
            }

            $d = -1;
            $e = array();
            while($d === -1) {
                $f = mt_rand(1, 5);
                $g = (self::$session->last_clicked > -1) ? self::$session->last_clicked : 0;

                if($f !== $g) {
                    $d = $f;
                }
            }

            for($i = 1; $i <= 5; $i++) {
                $e[] = self::getImageHash('icon-' . (($i === $d) ? $a : $b) . '-' . $i);
            }

            self::$session->clear();
            self::$session->hashes = array($a, $b, $e);
            self::$session->correct_hash = $e[$d - 1];
            self::$session->icon_requests = 0;
            self::$session->save();

            return json_encode($e);
        }

        public static function validateSubmission($post) {
            if(!empty($post)) {
                if(!isset($post[self::CAPTCHA_FIELD_ID]) || !is_numeric($post[self::CAPTCHA_FIELD_ID])
                    || !CaptchaSession::exists($post[self::CAPTCHA_FIELD_ID])) {
                    self::$error = json_encode(array('id' => 4, 'error' => self::$error_messages[3]));
                    return false;
                }

                self::$captcha_id = self::tryCreateSession($post[self::CAPTCHA_FIELD_ID]);

                if(!empty($post[self::CAPTCHA_FIELD_HASH])) {
                    if(self::$session->completed === true && self::getCorrectIconHash() === $post[self::CAPTCHA_FIELD_HASH]) {
                        return true;
                    } else {
                        self::$error = json_encode(array('id' => 1, 'error' => self::$error_messages[0]));
                    }
                } else {
                    self::$error = json_encode(array('id' => 2, 'error' => self::$error_messages[1]));
                }
            } else {
                self::$error = json_encode(array('id' => 3, 'error' => self::$error_messages[2]));
            }

            return false;
        }

        public static function setSelectedAnswer($post) {
            if(!empty($post)) {

                if(!isset($post['cID']) || !is_numeric($post['cID'])) {
                    return false;
                }

                self::$captcha_id = self::tryCreateSession($post['cID']);
                if(isset($post['pC']) && (self::getCorrectIconHash() === $post['pC'])) {
                    self::$session->completed = true;
                    self::$session->clear();
                    self::$session->save();

                    return true;
                } else {
                    self::$session->completed = false;
                    self::$session->save();

                    if(in_array($post['pC'], self::$session->hashes[2])) {
                        $i = array_search($post['pC'], self::$session->hashes[2]);
                        self::$session->last_clicked = $i + 1;
                    }
                }
            }

            return false;
        }

        public static function getIconFromHash($hash = null, $captcha_id = null) {
            if(!empty($hash) && (isset($captcha_id) && $captcha_id > -1)) {
                self::$captcha_id = self::tryCreateSession($captcha_id);

                if(self::$session->icon_requests >= 5) {
                    header('HTTP/1.1 403 Forbidden');
                    exit;
                }

                self::$session->icon_requests += 1;
                self::$session->save();

                if(in_array($hash, self::$session->hashes[2])) {
                    $icons_path = $_SESSION[self::ICON_CAPTCHA][self::CAPTCHA_ICON_PATH]; // Icons folder path

                    $icon_file = $icons_path . ((substr($icons_path, -1) === '/') ? '' : '/') . self::$session->theme . '/icon-' .
                        ((self::getCorrectIconHash() === $hash) ? self::$session->hashes[0] : self::$session->hashes[1]) . '.png';

                    if (is_file($icon_file)) {
                        $add_noise = (isset($_SESSION[self::ICON_CAPTCHA][self::CAPTCHA_NOISE])
                            && $_SESSION[self::ICON_CAPTCHA][self::CAPTCHA_NOISE]);

                        if($add_noise) {
                            $icon = imagecreatefrompng($icon_file);
                            $noise_color = imagecolorallocatealpha($icon, 0, 0, 0, 126);

                            for ($i = 0; $i < 5; $i++) {
                                $randX = ($i < 3) ? mt_rand(0, 2) : mt_rand(28, 30);
                                $randY = ($i < 3) ? mt_rand(0, 15) : mt_rand(16, 30);

                                imagesetpixel($icon, $randX, $randY, $noise_color);
                            }
                        }

                        header('Content-type: image/png');
                        header('Expires: 0');
                        header('Cache-Control: no-cache, no-store, must-revalidate');
                        header('Cache-Control: post-check=0, pre-check=0', false);
                        header('Pragma: no-cache');

                        if($add_noise && isset($icon)) {
                            imagepng($icon);
                            imagedestroy($icon);
                        } else {
                            readfile($icon_file);
                        }

                        exit;
                    }
                }
            }
        }

        private static function getCorrectIconHash() {
            self::tryCreateSession();

            return (isset(self::$captcha_id) && is_numeric(self::$captcha_id))
                ? self::$session->correct_hash : '';
        }

        private static function getImageHash($image = null) {
            self::tryCreateSession();

            return (!empty($image) && (isset(self::$captcha_id) && is_numeric(self::$captcha_id)))
                ? hash('tiger192,3', $image . hash('crc32b', uniqid('ic_', true))) : '';
        }

        private static function tryCreateSession($captchaId = -1, $theme = 'light') {
            if($captchaId > -1) {
                self::$captcha_id = $captchaId;
            }

            if(!isset(self::$session)) {
                self::$session = new CaptchaSession(self::$captcha_id, $theme);
            }

            return self::$captcha_id;
        }
    }
?>