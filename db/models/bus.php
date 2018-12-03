<?php

    class Bus{
        private $id;
        public $origem;
        public $destino;
        public $letreiro;
        public $px;
        public $py;

        public function __construct($id, $origem, $destino, $letreiro){
            $this->id = $id;
            $this->origem = $origem;
            $this->destino = $destino;
            $this->letreiro = $letreiro;
            $this->px = [];
            $this->py = [];
        }

        public function setPx($px){
            array_push($this->px, $px);
        }
        public function setPy($py){
            array_push($this->py, $py);
        }

        public function getId(){
            return $this->id;
        }
    }