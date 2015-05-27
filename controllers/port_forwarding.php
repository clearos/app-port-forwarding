<?php

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

class Port_Forwarding extends ClearOS_Controller
{
    /**
     * Port forwarding overview.
     *
     * @return view
     */

    function index()
    {
        $this->load->library('port_forwarding/Port_Forwarding');
        $this->load->library('network/Network');
        $this->lang->load('port_forwarding');

        // Load view data
        //---------------

        try {
            $data['ports'] = $this->port_forwarding->get_ports();
            $data['ranges'] = $this->port_forwarding->get_port_ranges();
            $data['pptp'] = $this->port_forwarding->get_pptp_server();
            $data['network_mode'] = $this->network->get_mode();
            $data['panic'] = $this->port_forwarding->is_panic();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load views
        //-----------

        $this->page->view_form('port_forwarding/summary', $data, lang('port_forwarding_app_name'));
    }

    /**
     * Delete port rule confirmation.
     *
     * @param string  $protocol  protocol
     * @param integer $from_port from port
     * @param integer $to_port   to port
     * @param string  $ip        IP address
     *
     * @return view
     */

    function delete($protocol, $from_port, $to_port, $ip)
    {
        $confirm_uri = '/app/port_forwarding/destroy/' . $protocol . '/' . $from_port . '/' . $to_port . '/' . $ip;
        $cancel_uri = '/app/port_forwarding';
        // FIXME: cleanup look and feel
        $items = array($protocol . ' ' . $from_port . ' > ' . $to_port . ' - ' . $ip);

        $this->page->view_confirm_delete($confirm_uri, $cancel_uri, $items);
    }

    /**
     * Delete port range rule confirmation.
     *
     * @param string  $protocol  protocol
     * @param integer $low_port  low port
     * @param integer $high_port high port
     * @param string  $ip        IP address
     *
     * @return view
     */

    function delete_range($protocol, $low_port, $high_port, $ip)
    {
        $confirm_uri = '/app/port_forwarding/destroy_range/' . $protocol . '/' . $low_port . '/' . $high_port . '/' . $ip;
        $cancel_uri = '/app/port_forwarding';
        // FIXME: cleanup look and feel
        $items = array($protocol . ' ' . $low_port . ':' . $high_port . ' - ' . $ip);

        $this->page->view_confirm_delete($confirm_uri, $cancel_uri, $items);
    }

    /**
     * Destroys port rule.
     *
     * @param string  $protocol  protocol
     * @param integer $from_port from port
     * @param integer $to_port   to port
     * @param string  $ip        IP address
     *
     * @return view
     */

    function destroy($protocol, $from_port, $to_port, $ip)
    {
        // Load libraries
        //---------------

        $this->load->library('port_forwarding/Port_Forwarding');

        // Handle form submit
        //-------------------

        try {
            $this->port_forwarding->delete_port($protocol, $from_port, $to_port, $ip);

            $this->page->set_status_deleted();
            redirect('/port_forwarding');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

    /**
     * Destroys port range rule.
     *
     * @param string  $protocol  protocol
     * @param integer $low_port  low port
     * @param integer $high_port high port
     * @param string  $ip        IP address
     *
     * @return view
     */

    function destroy_range($protocol, $low_port, $high_port, $ip)
    {
        // Load libraries
        //---------------

        $this->load->library('port_forwarding/Port_Forwarding');

        // Handle form submit
        //-------------------

        try {
            $this->port_forwarding->delete_port_range($protocol, $low_port, $high_port, $ip);

            $this->page->set_status_deleted();
            redirect('/port_forwarding');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

    /**
     * Disables port rule.
     *
     * @param string  $protocol  protocol
     * @param integer $from_port from port
     * @param integer $to_port   to port
     * @param string  $ip        IP address
     *
     * @return view
     */

    function disable($protocol, $from_port, $to_port, $ip)
    {
        try {
            $this->load->library('port_forwarding/Port_Forwarding');

            $this->port_forwarding->set_port_state(FALSE, $protocol, $from_port, $to_port, $ip);

            $this->page->set_status_disabled();
            redirect('/port_forwarding');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

    /**
     * Disables range rule.
     *
     * @param string  $protocol  protocol
     * @param integer $low_port  low port
     * @param integer $high_port high port
     * @param string  $ip        IP address
     *
     * @return view
     */

    function disable_range($protocol, $low_port, $high_port, $ip)
    {
        try {
            $this->load->library('port_forwarding/Port_Forwarding');

            $this->port_forwarding->set_port_range_state(FALSE, $protocol, $low_port, $high_port, $ip);

            $this->page->set_status_disabled();
            redirect('/port_forwarding');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

    /**
     * Enables port rule.
     *
     * @param string  $protocol  protocol
     * @param integer $from_port from port
     * @param integer $to_port   to port
     * @param string  $ip        IP address
     *
     * @return view
     */

    function enable($protocol, $from_port, $to_port, $ip)
    {
        try {
            $this->load->library('port_forwarding/Port_Forwarding');

            $this->port_forwarding->set_port_state(TRUE, $protocol, $from_port, $to_port, $ip);

            $this->page->set_status_enabled();
            redirect('/port_forwarding');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

    /**
     * Enables range rule.
     *
     * @param string  $protocol  protocol
     * @param integer $low_port  low port
     * @param integer $high_port high port
     * @param string  $ip        IP address
     *
     * @return view
     */

    function enable_range($protocol, $low_port, $high_port, $ip)
    {
        try {
            $this->load->library('port_forwarding/Port_Forwarding');

            $this->port_forwarding->set_port_range_state(TRUE, $protocol, $low_port, $high_port, $ip);

            $this->page->set_status_enabled();
            redirect('/port_forwarding');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }
}
