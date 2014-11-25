<?php

require_once(_PS_MODULE_DIR_ . '/seqr/lib/seqr_package.php');

if (!defined('_PS_VERSION_'))
    exit;

/**
 * The entry point of the SEQR payment module.
 */
class Seqr extends PaymentModule {

    private $config = null;

    public function __construct() {

        $this->name = 'seqr';
        $this->tab = 'Payment';
        $this->version = '1.1.0';
        $this->author = 'SEQR Team';
        $this->need_instance = 1;
        $this->is_configurable = 1;
        $this->ps_versions_compliancy = array('min' => '1.4', 'max' => "1.4.11");
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('SEQR');
        $this->description = $this->l('Accepts payments by SEQR.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall the SEQR module?');

        global $smarty;
        $this->smarty = $smarty;

        $this->loadConfig();
        $this->validateModuleSettings();
    }


    /**
     * Installs the SEQR module.
     * @return bool
     */
    public function install() {

        if (!parent::install()
            || !$this->registerHook('payment')
            || !$this->config->install()
        ) {
            return false;
        }
        return true;
    }

    /**
     * Uninstall the SEQR module.
     * @return bool
     */
    public function uninstall() {
        if (
            !$this->config->uninstall()
            || !parent::uninstall()
        ) {
            return false;
        }
        return true;
    }

    /**
     * Prints configuration page in the admin module.
     * @return string
     */
    public function getContent() {

        $output = null;

        if (Tools::isSubmit('submit')) {
            // Saving configuration settings

            $user = strval(Tools::getValue(SeqrConfig::SEQR_USER_ID));
            $terminalId = strval(Tools::getValue(SeqrConfig::SEQR_TERMINAL_ID));
            $terminalPass = strval(Tools::getValue(SeqrConfig::SEQR_TERMINAL_PASS));
            $wsdl = strval(Tools::getValue(SeqrConfig::SEQR_WSDL));
            $timeout = strval(Tools::getValue(SeqrConfig::SEQR_PAYMENT_TIMEOUT));

            $valid = true;
            $valid = $this->validateValue($user, $this->l('Invalid user id'), $output);
            $valid = $this->validateValue($terminalId, $this->l('Invalid terminal id'), $output) && $valid;
            $valid = $this->validateValue($terminalPass, $this->l('Invalid terminal password'), $output) && $valid;
            $valid = $this->validateValue($wsdl, $this->l('SEQR mode is not set'), $output) && $valid;
            $valid = $this->validateValue($timeout, $this->l('Payment timeout is not set'), $output) && $valid;

            if ($valid) {
                $newConfig = new PsConfig();
                $newConfig->populate(array(
                    SeqrConfig::SEQR_USER_ID => $user,
                    SeqrConfig::SEQR_TERMINAL_ID => $terminalId,
                    SeqrConfig::SEQR_TERMINAL_PASS => $terminalPass,
                    SeqrConfig::SEQR_WSDL => $wsdl,
                    SeqrConfig::SEQR_PAYMENT_TIMEOUT => $timeout
                ));
                $newConfig->save();
                $this->config = $newConfig;

                $output .= $this->displayConfirmation($this->l('Settings updated'));
            } else {
                $output .= $this->displayError($this->l("Please correct the form and try again"));
            }

        }
        return $output . $this->displayForm();
    }

    /**
     * Configuration form definition.
     * @return mixed
     */
    public function displayForm() {

        // Init Fields form array
        $formData = array(
            'userId' => array(
                'label' => $this->l('User id'),
                'name' => SeqrConfig::SEQR_USER_ID,
                'value' => $this->config->getUserId()
            ),
            'terminalId' => array(
                'label' => $this->l('Terminal id'),
                'name' => SeqrConfig::SEQR_TERMINAL_ID,
                'value' => $this->config->getTerminalId()
            ),
            'terminalPass' => array(
                'label' => $this->l('Terminal password'),
                'name' => SeqrConfig::SEQR_TERMINAL_PASS,
                'value' => $this->config->getTerminalPass()
            ),
            'timeout' => array(
                'label' => $this->l('Payment timeout (in seconds)'),
                'name' => SeqrConfig::SEQR_PAYMENT_TIMEOUT,
                'value' => $this->config->getTimeout()
            ),
            'wsdlUrl' => array(
                'label' => $this->l('SEQR WSDL url'),
                'name' => SeqrConfig::SEQR_WSDL,
                'value' => $this->config->getWsdl()
            )
        );

        $this->smarty->assign(array(
            'this_path' => $this->_path,
            'this_path_bw' => $this->_path,
            'moduleUrl' => $this->getModuleUrl(),
            'data' => $formData
        ));

        return $this->display(__FILE__, 'settings.tpl');
    }

    /**
     * Helper function used to validate configuration values provided by the user.
     * @param $value
     * @param $errorMessage
     * @param $output
     * @return bool
     */
    private function validateValue($value, $errorMessage, &$output) {

        $validation = !empty($value) && Validate::isGenericName($value);
        if (!$validation) {
            $output .= $this->displayError($this->l($errorMessage));
        }
        return $validation;
    }

    /**
     * Hook payment, displays SEQR payment option on payment selection page.
     * @param $params
     * @return array|mixed|string|void
     */
    public function hookPayment($params) {

        if (!$this->active) {
            return;
        }

        // Check if configuration is valid
        if (!$this->config->isValid()) {
            return;
        }

        $this->smarty->assign(array(
            'this_path' => $this->_path,
            'this_path_bw' => $this->_path,
            'moduleUrl' => $this->getModuleUrl()
        ));

        return $this->display(__FILE__, 'seqr_payment_option.tpl');
    }

    private function validateModuleSettings() {
        if (!$this->config->isValid()) {
            $this->warning = $this->l('The SEQR plugin must be configured in order to use this module correctly');
        }
    }

    /**
     * @return string
     */
    protected function getModuleUrl() {
        return Tools::getHttpHost(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name;
    }

    protected function loadConfig() {
        $this->config = new PsConfig();
        $this->config->load();
    }
}




