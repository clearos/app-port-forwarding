<?php

/**
 * Firewall port forwarding port controller.
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

class Port extends ClearOS_Controller
{
    /**
     * Port forwarding port.
     *
     * @return view
     */

    function index()
    {
        $this->load->library('port_forwarding/Port_Forwarding');
        $this->lang->load('port_forwarding');
        $this->lang->load('base');
        $this->lang->load('firewall');

        // Set validation rules
        //---------------------

        $this->form_validation->set_policy('port_nickname', 'port_forwarding/Port_Forwarding', 'validate_name', TRUE);
        $this->form_validation->set_policy('port_protocol', 'port_forwarding/Port_Forwarding', 'validate_protocol', TRUE);
        $this->form_validation->set_policy('port_from', 'port_forwarding/Port_Forwarding', 'validate_single_port', TRUE);
        $this->form_validation->set_policy('port_to', 'port_forwarding/Port_Forwarding', 'validate_single_port', TRUE);
        $this->form_validation->set_policy('port_ip', 'port_forwarding/Port_Forwarding', 'validate_ip', TRUE);

        // Handle form submit
        //-------------------

        if ($this->form_validation->run()) {
            try {
                $this->port_forwarding->add_port(
                    $this->input->post('port_nickname'),
                    $this->input->post('port_protocol'),
                    $this->input->post('port_from'),
                    $this->input->post('port_to'),
                    $this->input->post('port_ip')
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

        // Load the view
        //--------------

        $this->page->view_form('port_forwarding/port', $data, lang('base_add_by') . ': ' . lang('firewall_port'));
    }
}
