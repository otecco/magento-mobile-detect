<?php
/*
 */

class Shopix_MobileDetect_Model_Observer extends Varien_Object
{
    var $set_header;
    var $set_header_mobile_only;
    var $run_code_desktop;
    var $run_code_mobile;

    public function __construct() {
        $this->set_header = 1;
        $this->set_header_mobile_only = 0;
        $this->run_code_desktop = Mage::getModel('core/store')->load(Mage::getStoreConfig('web/mobiledetect/desktop'))->getCode();
        $this->run_code_mobile = Mage::getModel('core/store')->load(Mage::getStoreConfig('web/mobiledetect/mobile'))->getCode();
    }

    /*
     * Visit default: autodetect mobile device and switch as needed
     * Switch to default (using ___store parameter): remember the selection
     * Visit mobile directly: reset autodetection
     */
    public function controllerFrontSendResponseBefore($observer) {
        $current = Mage::app()->getStore()->getCode();

        // Do not touch other store views.
        if (! in_array($current, array($this->run_code_desktop, $this->run_code_mobile)))
            return;

        // Configuration sanity checks.
        if (is_null($this->run_code_desktop) || is_null($this->run_code_desktop)
                || $this->run_code_desktop === $this->run_code_mobile)
            return;

        if ($this->set_header && ($current != $this->run_code_mobile || $this->set_header_mobile_only)) {
            Mage::app()->getFrontController()->getResponse()->setHeader("Vary", "User-Agent");
        }

        $session = Mage::getSingleton('core/session');

        // Changing store view to desktop, remember the preference.
        $___store = Mage::app()->getRequest()->getParam('___store', null);
        if (isset($___store) && ($___store == $this->run_code_desktop)) {
            $session->setRunCode($___store);
            return;
        }

        // Reset in mobile store view.
        if ($current == $this->run_code_mobile) {
            $session->getRunCode(true);
            return;
        }
            
        // Only do detection on the desktop store view.
        if ($current != $this->run_code_desktop) {
            return;
        }

        // ...and only when visited from a mobile device.
        if (! $this->isMobileBrowser($_SERVER['HTTP_USER_AGENT'])) {
            return;
        }

        // No saved preference.
        $run_code = $session->getRunCode();
        if (! isset($run_code))
            $run_code = $this->run_code_mobile;

        // Prevent redirection loop.
        if ($run_code == $current) {
            return;
        }

        $stores = Mage::app()->getStores(false, true);
        Mage::app()->getFrontController()->getResponse()->setRedirect($stores[$run_code]->getCurrentUrl(false), 301);
    }

    /**
     * From http://detectmobilebrowsers.com/download/php
     */
    function isMobileBrowser($useragent) {
        return preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|meego.+mobile|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4));
    }
}

