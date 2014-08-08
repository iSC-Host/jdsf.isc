<?php

/*
  ########################################################################################
  ## CUNITY(R) V1.0beta - An open source social network / "your private social network" ##
  ########################################################################################
  ##  Copyright (C) 2011 Smart In Media GmbH & Co. KG                                   ##
  ## CUNITY(R) is a registered trademark of Dr. Martin R. Weihrauch                     ##
  ##  http://www.cunity.net                                                             ##
  ##                                                                                    ##
  ########################################################################################

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as
  published by the Free Software Foundation, either version 3 of the
  License, or any later version.

  1. YOU MUST NOT CHANGE THE LICENSE FOR THE SOFTWARE OR ANY PARTS HEREOF! IT MUST REMAIN AGPL.
  2. YOU MUST NOT REMOVE THIS COPYRIGHT NOTES FROM ANY PARTS OF THIS SOFTWARE!
  3. NOTE THAT THIS SOFTWARE CONTAINS THIRD-PARTY-SOLUTIONS THAT MAY EVENTUALLY NOT FALL UNDER (A)GPL!
  4. PLEASE READ THE LICENSE OF THE CUNITY SOFTWARE CAREFULLY!

  You should have received a copy of the GNU Affero General Public License
  along with this program (under the folder LICENSE).
  If not, see <http://www.gnu.org/licenses/>.

  If your software can interact with users remotely through a computer network,
  you have to make sure that it provides a way for users to get its source.
  For example, if your program is a web application, its interface could display
  a "Source" link that leads users to an archive of the code. There are many ways
  you could offer source, and different solutions will be better for different programs;
  see section 13 of the GNU Affero General Public License for the specific requirements.

  #####################################################################################
 */

require_once 'phpmailer.class.php';

class Cunity_Mailer {

    // Properties
    private $to = '';
    private $subject = '';
    private $message = '';
    private $from = '';
    private $header = '';
    private $sender_name = '';
    private $receiver_name = '';
    // smtp
    private $host = "";
    private $username = "";
    private $password = "";
    private $smtp = "";
    private $auth = false;
    private $port = 0;
    private $mail_method = '';
    private $hasError = 0;
    private $errorMessage;
    // Options
    private $html = true;
    private $allow_name_addr = false; // allow 'User <mail@example.com>' addresses
    // Error-Handling
    private $errors = '';
    private $error_count = 0;
    private $html_output = true;
    private $display_errors = true;
    private $error_css = 'font-size: 0.8em; font-family: sans-serif;';
    // RegExp
    private $regex_email1 = '/^[A-Z0-9.%+-_]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i';
    // mail@example.com
    private $regex_email2 = '/^.+ <[A-Z0-9.%+-_]+@[A-Z0-9.-]+\.[A-Z]{2,6}>$/i';
    // User <mail@example.com>
    private $regex_subject = '/^.[^\n\r\f]+\z/';
    // disallow \n\r\f as whitespaces
    private $regex_message = '/.+/';

    // not empty || you may specify this expression

    function __construct(Cunity $cunity) {
        $this->cunity = $cunity;
    }

    // Error-Handling
    private function throw_error($error_string) {
        $this->error_count++;
        $this->errors .= '<ERROR - ' . sprintf('%02d', $this->error_count) . '> ' . $error_string . "; \n\r";
    }

    function get_error_count() {
        return $this->error_count;
    }

    function get_last_error() {
        $error_array = explode(" \n\r", $this->errors);
        return $error_array[count($error_array) - 1];
    }

    public function get_all_errors() {
        if (!empty($this->errors)) {
            if ($this->html_output)
                print '<p style="' . $this->error_css . '">' . (nl2br(htmlspecialchars($this->errors))) . '</p>';
            else
                print $this->errors;
        }
    }

    // Regular Expression Control Method
    private function check($type, $string) {
        // mail
        if ($type === 'mail') {
            if ($this->allow_name_addr) {
                if (preg_match($this->regex_email1, $string) === 1 || preg_match($this->regex_email2, $string) === 1)
                    return true;
                else
                    return false;
            }
            else {
                if (preg_match($this->regex_email1, $string) === 1)
                    return true;
                else
                    return false;
            }
        }
        // subject
        elseif ($type === 'subject') {
            if (preg_match($this->regex_subject, $string) === 1)
                return true;
            else
                return false;
        }
        // message
        elseif ($type === 'message') {
            if (preg_match($this->regex_message, $string) === 1)
                return true;
            else
                return false;
        }
    }

    // Set Variables
    function to($paddress) {
        if (is_array($paddress)) {
            $this->to = ''; // reset
            foreach ($paddress as $key => $value) {
                if (!$this->check('mail', $value)) {
                    $this->throw_error('to() :: parameter expects an array of valid email-addresses');
                    return false;
                } else {
                    if ($this->to != '')
                        $this->to .= ', ' . $paddress[$key];
                    else
                        $this->to = $paddress[$key];
                }
            }
            return true;
        }
        else {
            if (true) {
                $this->to = $paddress;
                return true;
            } else {
                $this->throw_error('to() :: parameter expects a valid email-address');
                return false;
            }
        }
    }

    function from($paddress) {
        if ($this->check('mail', $paddress)) {
            $this->from = $paddress;
            return true;
        } else {
            $this->throw_error('from() :: parameter expects a valid email-address');
            return false;
        }
    }

    function subject($psubject) {
        $this->subject = ''; // reset

        if ($this->check('subject', $psubject)) {
            $this->subject = $psubject;
            return true;
        } else {
            $this->throw_error('subject() :: parameter expects a valid subject');
            return false;
        }
    }

    function message($pmessage) {
        if ($this->check('message', $pmessage)) {
            $this->message = $pmessage;
            return true;
        } else {
            $this->throw_error('message() :: parameter expects a valid message-body');
            return false;
        }
    }

    // Send
    function sendmail($sto, $receiver_name, $ssubject, $smessage, $sender = "", $sendername = "") {
        $mail = new PHPMailer(true);

        $body = $this->cunity->getConfig("email_header");
        $body = $body . '<p>' . $smessage . '</p>';
        $body = $body . $this->cunity->getConfig("email_footer");
        if ($this->cunity->getConfig("smtp_method") == "smtp") {
            $mail->IsSMTP(); // telling the class to use SMTP
        } elseif ($this->cunity->getConfig("smtp_method") == "sendMail") {
            $mail->IsSendmail();
        }

        try {

           // $mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)

            if ($this->cunity->getConfig("smtp_auth") != null)
                $mail->SMTPAuth = true;
            
            $mail->Host = $this->cunity->getConfig("smtp_host");

            if ($this->cunity->getConfig("smtp_port") != null) {
                $mail->Port = $this->cunity->getConfig("smtp_port");
            }
            $mail->Username = $this->cunity->getConfig("smtp_username"); // username
            $mail->Password = $this->cunity->getConfig("smtp_password"); // password

            if ($sender == "" && $sendername == "") {
                $mail->SetFrom($this->cunity->getConfig("smtp_sender_address"), $this->cunity->getConfig("smtp_sender_name"));
                $mail->AddReplyTo($this->cunity->getConfig("smtp_sender_address"), $this->cunity->getConfig("smtp_sender_name"));
            } elseif ($sender != "" && $sendername != "") {
                $mail->SetFrom($sender, $sendername);
                $mail->AddReplyTo($sender, $sendername);
            }
            $mail->Subject = $ssubject;

            $mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
            $mail->MsgHTML($body);

            $address = $sto;
            $mail->AddAddress($address, $receiver_name);

            $mail->Send();
            $this->setHasError(0, '');
            return true;
        } catch (phpmailerException $e) {            
            $this->setHasError(1, $e->errorMessage());            
            return true;
        }
    }

    function createheader() {
        $this->headers = array('From' => $this->from, 'To' => $this->to, 'Subject' => $this->subject);
    }

    function setHasError($hasError, $errorMessage) {
        $this->hasError = $hasError;
        $this->errorMessage = $errorMessage;
    }

    function getHasError() {
        return $this->hasError;
    }

    function getErrorMsg() {
        return $this->errorMessage;
    }

    // Destructor
    function __destruct() {
        
    }

}

?>