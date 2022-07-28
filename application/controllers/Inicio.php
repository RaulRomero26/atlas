<?php
class Inicio extends Controller
{

    public $Catalogo;
    public $Remision;

    public function __construct()
    {
        $this->Catalogo = $this->model('Catalogo');
        $this->Remision = $this->model('Remision');
    }

    public function index()
    {

        if (!isset($_SESSION['userdata'])) {
            header("Location: ".base_url."Login");
            exit();
        }

        $data = [
            'titulo'    => 'Planeación | Inicio'
        ];
        
        $this->view('templates/header', $data);
        $this->view('system/inicio/inicioView', $data);
        $this->view('templates/footer', $data);
        
    }
}