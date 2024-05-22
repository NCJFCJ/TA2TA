<?php

namespace IAWP\AJAX;

use IAWP\Utils\Singleton;
/** @internal */
class AJAX_Manager
{
    use Singleton;
    /** @var AJAX[] */
    private $instances = [];
    private function __construct()
    {
        $this->instances[] = new \IAWP\AJAX\Confirm_Cache_Cleared();
        $this->instances[] = new \IAWP\AJAX\Copy_Report();
        $this->instances[] = new \IAWP\AJAX\Create_Campaign();
        $this->instances[] = new \IAWP\AJAX\Create_Report();
        $this->instances[] = new \IAWP\AJAX\Delete_Campaign();
        $this->instances[] = new \IAWP\AJAX\Delete_Data();
        $this->instances[] = new \IAWP\AJAX\Delete_Report();
        $this->instances[] = new \IAWP\AJAX\Export_Campaigns();
        $this->instances[] = new \IAWP\AJAX\Export_Devices();
        $this->instances[] = new \IAWP\AJAX\Export_Geo();
        $this->instances[] = new \IAWP\AJAX\Export_Pages();
        $this->instances[] = new \IAWP\AJAX\Export_Referrers();
        $this->instances[] = new \IAWP\AJAX\Export_Reports();
        $this->instances[] = new \IAWP\AJAX\Filter();
        $this->instances[] = new \IAWP\AJAX\Import_Reports();
        $this->instances[] = new \IAWP\AJAX\Migration_Status();
        $this->instances[] = new \IAWP\AJAX\Preview_Email();
        $this->instances[] = new \IAWP\AJAX\Real_Time_Data();
        $this->instances[] = new \IAWP\AJAX\Rename_Report();
        $this->instances[] = new \IAWP\AJAX\Reset_Analytics();
        $this->instances[] = new \IAWP\AJAX\Save_Report();
        $this->instances[] = new \IAWP\AJAX\Set_Favorite_Report();
        $this->instances[] = new \IAWP\AJAX\Sort_Reports();
        $this->instances[] = new \IAWP\AJAX\Test_Email();
        $this->instances[] = new \IAWP\AJAX\Update_Capabilities();
        $this->instances[] = new \IAWP\AJAX\Update_User_Settings();
    }
    public function get_action_signatures() : array
    {
        $action_signatures = [];
        foreach ($this->instances as $instance) {
            $action_signatures = \array_merge($action_signatures, $instance->get_action_signature());
        }
        return $action_signatures;
    }
}
