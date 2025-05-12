<?php
require_once 'Menu.php';

// Get USSD parameters
$sessionId = $_POST['sessionId'] ?? '';
$phoneNumber = $_POST['phoneNumber'] ?? '';
$serviceCode = $_POST['serviceCode'] ?? '';
$text = $_POST['text'] ?? '';

// Initialize menu
$menu = new Menu($text, $sessionId, $phoneNumber);
$text = $menu->middleware($text);

if(empty($text)) {
    // First time user
    if(!$menu->isRegistered() && !$menu->isAgent()) {
        $menu->mainMenuUnregistered();
    } else {
        $menu->mainMenuRegistered();
    }
} else {
    $textArray = explode("*", $text);
    $firstOption = $textArray[0];

    if($menu->isAgent()) {
        // Agent specific menus
        switch($firstOption) {
            case 1:
                $menu->menuAgentApprove($textArray);
                break;
            case 2:
                $menu->menuCheckBalance($textArray);
                break;
            default:
                echo "END Invalid option";
        }
    } elseif(!$menu->isRegistered()) {
        // User registration
        switch($firstOption) {
            case 1:
                $menu->menuRegister($textArray);
                break;
            default:
                echo "END Invalid option";
        }
    } else {
        // Registered user menus
        switch($firstOption) {
            case 1:
                $menu->menuSendMoney($textArray);
                break;
            case 2:
                $menu->menuWithdrawMoney($textArray);
                break;
            case 3:
                $menu->menuCheckBalance($textArray);
                break;
            default:
                echo "END Invalid option";
        }
    }
}
?>