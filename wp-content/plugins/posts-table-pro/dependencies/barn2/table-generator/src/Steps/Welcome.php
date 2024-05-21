<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Steps;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Step;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Util;
/**
 * This step handles the license activation.
 */
class Welcome extends Step
{
    // Determines if the step should be displayed only in the wizard.
    const WIZARD_ONLY = \true;
    /**
     * Get things started.
     *
     * @param boolean|object $plugin
     */
    public function __construct($plugin = \false)
    {
        parent::__construct($plugin);
        $this->set_id('welcome');
        $this->set_name(__('Welcome','posts-table-pro' ));
        $this->set_title(__('Welcome to Posts Table Pro','posts-table-pro' ));
        $this->set_description(__('Create and display beautiful tables of your website content','posts-table-pro' ));
        $this->set_fields($this->get_fields_list());
    }
    /**
     * List of fields for this spte.
     *
     * @return array
     */
    public function get_fields_list()
    {
        $fields = [['type' => 'license', 'label' => __('License key','posts-table-pro' ), 'name' => 'license', 'value' => '']];
        return $fields;
    }
    /**
     * {@inheritdoc}
     */
    public function get_data($request)
    {
        if (!\method_exists($this->get_plugin(), 'get_license')) {
            return ['status' => '', 'exists' => \false, 'key' => '', 'status_help_text' => '', 'error_message' => '', 'free_plugin' => \true];
        }
        $license_handler = $this->get_plugin()->get_license();
        return $this->send_success_response(['values' => ['licenseDetails' => ['status' => $license_handler->get_status(), 'exists' => $license_handler->exists(), 'key' => $license_handler->get_license_key(), 'status_help_text' => $license_handler->get_status_help_text(), 'error_message' => $license_handler->get_error_message()]]]);
    }
    /**
     * {@inheritdoc}
     */
    public function save_data($request)
    {
        $values = $this->get_submitted_values($request);
        $license_details = isset($values['license']['details']) ? Util::clean($values['license']['details']) : \false;
        if (!$license_details) {
            return $this->send_error_response(['message' => __('No license details provided. Please enter a license.','posts-table-pro' )]);
        }
        $status = isset($license_details['status']) ? $license_details['status'] : \false;
        if ($status !== 'active') {
            return $this->send_error_response(['message' => __('Please validate your license.','posts-table-pro' )]);
        }
        return $this->send_success_response();
    }
}
