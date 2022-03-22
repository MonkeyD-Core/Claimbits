<?php
    session_start();
    require('session.class.php');
    require('captcha.class.php');

    if((isset($_GET['hash']) && strlen($_GET['hash']) === 48) &&
        (isset($_GET['cid']) && is_numeric($_GET['cid'])) && !isAjaxRequest()) {
        CBCaptcha::getIconFromHash($_GET['hash'], $_GET['cid']);
        exit;
    }

    if(!empty($_POST) && isAjaxRequest()) {
        if(isset($_POST['rT']) && is_numeric($_POST['rT']) && isset($_POST['cID']) && is_numeric($_POST['cID'])) {
            switch((int)$_POST['rT']) {
                case 1:
                    $captcha_theme = (isset($_POST['tM']) && ($_POST['tM'] === 'light' || $_POST['tM'] === 'dark')) ? $_POST['tM'] : 'light';

                    header('Content-type: application/json');
                    exit(CBCaptcha::getCaptchaData($captcha_theme, $_POST['cID']));
                case 2:
                    if(CBCaptcha::setSelectedAnswer($_POST)) {
						header('HTTP/1.0 200 OK');
						exit;
					}
                    break;
                default:
                    break;
            }
        }
    }

    header('HTTP/1.1 400 Bad Request');
    exit;

    function isAjaxRequest() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }
?>