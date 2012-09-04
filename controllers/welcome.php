<?php
if ( ! defined("BASEPATH"))
{
    exit("No direct script access allowed");
}

class Welcome extends CI_Controller {
    public function index()
    {
        $this->load->library("static_loader");
        $config = $this->static_loader->set(
            "_channel_loading",
            "_channel_player",
        );
        echo $this->static_loader->load();
    }
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
