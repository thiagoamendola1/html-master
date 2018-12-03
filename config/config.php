<?php
class Config
{
    //CONTÉM AS CONFIGURAÇÕES DA APLICAÇÃO
    private $token;
    private $url;
    private $gtoken;
    private $gurl;
    private $busIcon;
    private $stopIcon;

    public function __construct()
    {
        $str = file_get_contents('config/Access.json');
        $json = json_decode($str, true);
        $this->token = $json['otoken'];
        $this->url = $json['ourl'];
        $this->gtoken = $json['gtoken'];
        $this->gurl = $json['gurl'];
        $this->busIcon = $json['busIcon'];
        $this->stopIcon = $json['stopIcon'];

        //GAMBETA TEMPORARIA
       /* $this->token = "209bdb33469dbea33113600e6c6d3a719c6fa0f6df1aa728265a582f3a5f383b";
        $this->url = "http://api.olhovivo.sptrans.com.br/v2.1";
        $this->gtoken = "AIzaSyAmABWG72RYSfAfmHy6kUVzbhXYcqHR0Qg";
        $this->gurl = "https://maps.googleapis.com/maps/api/js?key=";
        $this->busIcon = "https://i.imgur.com/m9ROUKm.png";
        $this->stopIcon = "https://maps.google.com/mapfiles/kml/shapes/parking_lot_maps.png";*/
    }

    public function getOURL()
    {
        return $this->url;
    }
    public function getGURL()
    {
        return $this->gurl;
    }
    public function getOToken()
    {
        return $this->token;
    }
    public function getGToken()
    {
        return $this->gtoken;
    }
    public function getBusIcon()
    {
        return $this->busIcon;
    }
    public function getStopIcon()
    {
        return $this->stopIcon;
    }
}
