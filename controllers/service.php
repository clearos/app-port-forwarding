<?php

/**
 * Firewall port forwarding service controller.
 *
 * @category   apps
 * @package    port-forwarding
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011-2015 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/port_forwarding/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Firewall port forwarding controller.
 *
 * @category   apps
 * @package    port-forwarding
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011-2015 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/port_forwarding/
 */

class Service extends ClearOS_Controller
{
    /**
     * Port forwarding service.
     *
     * @return view
     */

    function index()
    {
        // Load libraries
        //---------------

        $this->load->library('port_forwarding/Port_Forwarding');
        $this->lang->load('port_forwarding');
        $this->lang->load('base');
        $this->lang->load('firewall');

        // Set validation rules
        //---------------------

        $this->form_validation->set_policy('service', 'port_forwarding/Port_Forwarding', 'validate_service', TRUE);
        $this->form_validation->set_policy('service_ip', 'port_forwarding/Port_Forwarding', 'validate_ip', TRUE);

        // Handle form submit
        //-------------------

        if ($this->form_validation->run()) {
            try {
                $this->port_forwarding->add_standard_service(
                    preg_replace('/\//', '_', $this->input->post('service')),
                    $this->input->post('service'),
                    $this->input->post('service_ip')
                );

                $this->page->set_status_added();
                redirect('/port_forwarding');
            } catch (Exception $e) {
                $this->page->view_exception($e);
                return;
            }
        }

        // Load the view data 
        //------------------- 

        $data['protocols'] = $this->port_forwarding->get_basic_protocols();
        $services = $this->port_forwarding->get_standard_service_list();

        // TODO: PPTP and IPsec are not supported - a hack below

        $data['services'] = array();

        foreach ($services as $service)
            if ($service !== 'IPsec')
                $data['services'][] = $service;

        // Load the views
        //---------------

        $this->page->view_form('port_forwarding/service', $data, lang('base_add_by') . ': ' . lang('firewall_service'));
    }
}
