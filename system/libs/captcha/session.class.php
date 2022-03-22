<?php
    class CaptchaSession {
        const ICON_CAPTCHA = 'icon_captcha';
        const CAPTCHA_THEME = 'theme';
        const CAPTCHA_HASHES = 'hashes';
        const CAPTCHA_ICONS = 'icons';
        const CAPTCHA_LAST_CLICKED = 'last_clicked';
        const CAPTCHA_CORRECT_HASH = 'correct_hash';
        const CAPTCHA_COMPLETED = 'completed';

        public $id;
        public $hashes;
        public $icon_requests;
        public $theme;
        public $last_clicked;
        public $correct_hash;
        public $completed;

        public function __construct($id = 0, $theme = 'light') {
            $this->id = $id;
            $this->theme = $theme;
            $this->hashes = array();
            $this->icon_requests = 0;
            $this->last_clicked = -1;
            $this->correct_hash = '';
            $this->completed = false;

            $this->load();
        }

        public function clear() {
            $this->hashes = array();
            $this->icon_requests = -1;
            $this->last_clicked = 0;
        }

        public function load() {
            if(self::exists($this->id)) {
                if(isset($_SESSION[self::ICON_CAPTCHA][$this->id][self::CAPTCHA_THEME])) {
                    $this->theme = $_SESSION[self::ICON_CAPTCHA][$this->id][self::CAPTCHA_THEME];
                }

                if(isset($_SESSION[self::ICON_CAPTCHA][$this->id][self::CAPTCHA_HASHES])) {
                    $this->hashes = $_SESSION[self::ICON_CAPTCHA][$this->id][self::CAPTCHA_HASHES];
                }

                if(isset($_SESSION[self::ICON_CAPTCHA][$this->id][self::CAPTCHA_ICONS])) {
                    $this->icon_requests = $_SESSION[self::ICON_CAPTCHA][$this->id][self::CAPTCHA_ICONS];
                }

                if(isset($_SESSION[self::ICON_CAPTCHA][$this->id][self::CAPTCHA_LAST_CLICKED])) {
                    $this->last_clicked = $_SESSION[self::ICON_CAPTCHA][$this->id][self::CAPTCHA_LAST_CLICKED];
                }

                if(isset($_SESSION[self::ICON_CAPTCHA][$this->id][self::CAPTCHA_CORRECT_HASH])) {
                    $this->correct_hash = $_SESSION[self::ICON_CAPTCHA][$this->id][self::CAPTCHA_CORRECT_HASH];
                }

                if(isset($_SESSION[self::ICON_CAPTCHA][$this->id][self::CAPTCHA_COMPLETED])) {
                    $this->completed = $_SESSION[self::ICON_CAPTCHA][$this->id][self::CAPTCHA_COMPLETED];
                }
            }
        }

        public function save() {
            $data = array(
                self::CAPTCHA_HASHES 				=> $this->hashes,
                self::CAPTCHA_ICONS 				=> $this->icon_requests,
                self::CAPTCHA_THEME 				=> $this->theme,
                self::CAPTCHA_LAST_CLICKED	=> $this->last_clicked,
                self::CAPTCHA_CORRECT_HASH 	=> $this->correct_hash,
                self::CAPTCHA_COMPLETED		=> $this->completed
            );

            $_SESSION[self::ICON_CAPTCHA][$this->id] = $data;
        }

        public static function exists($id) {
            return isset($_SESSION[self::ICON_CAPTCHA][$id]);
        }
    }
?>