<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Diskalert extends CI_Controller
{
    public function index()
    {
        if (!is_cli()) {
          return;
        }

        $this->load->library('email');

        $subject = 'Running out of space on Block Storage Volume';
        $message = '<p>Block Storage Volume is almost out of disk space!</p>';

        $body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=' . strtolower(config_item('charset')) . '" />
            <title>' . html_escape($subject) . '</title>
            <style type="text/css">
                body {
                    font-family: Arial, Verdana, Helvetica, sans-serif;
                    font-size: 16px;
                }
            </style>
        </head>
        <body>
        ' . $message . '
        </body>
        </html>';

        $result = $this->email
            ->from('rafael@simpli-city.com')
            ->reply_to('rafael@simpli-city.com')
            ->to('info@thevertexlabs.com')
            ->subject($subject)
            ->message($body)
            ->send();

        var_dump($result);
        exit;
    }

}
