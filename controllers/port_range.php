<?php

/**
 * Firewall port forwarding port range controller.
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

class Port_Range extends ClearOS_Controller
{
    /**
     * Port forwarding overview.
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

        $this->form_validation->set_policy('range_nickname', 'port_forwarding/Port_Forwarding', 'validate_name', TRUE);
        $this->form_validation->set_policy('range_protocol', 'port_forwarding/Port_Forwarding', 'validate_protocol', TRUE);
        $this->form_validation->set_policy('range_start', 'port_forwarding/Port_Forwarding', 'validate_single_port', TRUE);
        $this->form_validation->set_policy('range_end', 'port_forwarding/Port_Forwarding', 'validate_single_port', TRUE);
        $this->form_validation->set_policy('range_ip', 'port_forwarding/Port_Forwarding', 'validate_ip', TRUE);

        // TODO - Firewall class needs fixing for validating port and port ranges
        // For now, since adding a range seems intuitive to have a separator, make sure
        // entry is just a single port.
        if ($this->input->post('submit') &&
            (!preg_match('/^\d+$/', $this->input->post('range_start')) ||
            !preg_match('/^\d+$/', $this->input->post('range_end'))
        )) {
            $this->page->set_message(lang('port_forwarding_port_range_warning'), 'warning');
            redirect('/port_forwarding/port_range');
        }
                

        // Handle form submit
        //-------------------

        if ($this->form_validation->run()) {
            try {
                $this->port_forwarding->add_port_range(
                    $this->input->post('range_nickname'),
                    $this->input->post('range_protocol'),
                    $this->input->post('range_start'),
                    $this->input->post('range_end'),
                    $this->input->post('range_ip')
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

        // Load the views
        //---------------

        $this->page->view_form('port_forwarding/port_range', $data, lang('base_add_by') . ': ' . lang('firewall_port_range'));
    }
}
