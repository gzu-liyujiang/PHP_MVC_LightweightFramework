<?php

class UnloginController extends BackendController
{

    public function main()
    {
        $this->template->display("Login.htm");
    }

}

