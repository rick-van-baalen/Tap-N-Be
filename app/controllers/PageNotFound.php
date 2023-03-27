<?php

class PageNotFound extends Controller {
    
    private $data;

    public function index() {
        $this->data['title'] = 'Pagina niet gevonden';
        $this->data['description'] = 'Deze pagina is niet gevonden.';
        $this->data['keywords'] = 'Pagina niet gevonden';

        $this->view('PageNotFound', $this->data);
    }

}