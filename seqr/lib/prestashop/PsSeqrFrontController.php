<?php
/**
 * Created by IntelliJ IDEA.
 * User: kmanka
 * Date: 24/11/14
 * Time: 15:03
 */

abstract class PsSeqrFrontController {

    protected $smarty = null;
    protected $config = null;

    public function __construct() {

        global $smarty;
        $this->smarty = $smarty;

        $this->config = new PsConfig();
        $this->config->load();

        $this->assingSmartyDefault();
    }

    /**
     * @return string
     */
    protected function getShopUrl() {
        return Tools::getHttpHost(true, true) . __PS_BASE_URI__;
    }

    /**
     * @return string
     */
    protected function getModuleUrl() {
        return $this->getShopUrl() . "modules/seqr";
    }

    /**
     * @return string
     */
    protected function getModulePath() {
        return _PS_MODULE_DIR_ . "/seqr";
    }

    protected function assingSmartyDefault() {

        $this->smarty->assign(array(
            "shopUrl" => $this->getShopUrl(),
            "moduleUrl" => $this->getModuleUrl(),
            "modulePath" => $this->getModulePath()
        ));
    }

    protected function display($templateName) {

        $tplPath = $this->getTemplatePath($templateName);
        $this->smarty->display($tplPath);
    }

    /**
     * @param $templateName
     * @throws Exception
     * @return string
     */
    protected function getTemplatePath($templateName) {

        $path = $this->getModulePath() . "/" . $templateName;
        if (!file_exists($path)) throw new Exception("Template does not exist: " . $path);
        return $path;
    }

    protected function assignBreadcrumb() {

        $tplFile = $this->getTemplatePath("breadcrumb.tpl");
        $this->smarty->assign("breadcrumb", $tplFile);
    }

    protected function assignNavigation() {

        $tplFile = $this->getTemplatePath("navigation.tpl");
        $this->smarty->assign("navigation", $tplFile);
    }

    public function verifyUserLogged() {

        global $cookie;
        if (!$cookie->isLogged()) {
            Tools::redirect('authentication.php?back=order.php');
        }
    }

    public abstract function execute();


}