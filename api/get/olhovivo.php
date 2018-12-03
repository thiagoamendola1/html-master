<?php
require_once 'config/config.php';
require_once 'db/models/bus.php';
require_once 'db/models/parada.php';

//CONTÉM OS GETS DA API DA SPTRANS, E ALGUNS MÉTODOS SERVER-SIDE
class OlhoVivo
{
    private $url;
    private $token;
    private $cookie;

    public function __construct()
    {
        $config = new Config();
        $this->url = $config->getOURL();
        $this->token = $config->getOToken();
        $this->cookie = '/tmp/cookie.txt';
        $this->linhas = [];
    }

    public function validaChave()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . '/Login/Autenticar?token=' . $this->token);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: 0'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
        $result = curl_exec($ch);

        echo curl_error($ch);

        curl_close($ch);

    }

    public function getOnibus($nome)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . '/Linha/Buscar?termosBusca=' . $nome);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: 0'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
        $result = curl_exec($ch);

        echo curl_error($ch);
        curl_close($ch);
        return json_decode($result);
    }

    public function getParada($nome)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . '/Parada/Buscar?termosBusca=' . $nome);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: 0'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
        $result = curl_exec($ch);

        echo curl_error($ch);
        curl_close($ch);
        return $result;
    }

    public function getParadaPorCorredor()
    {
        $corredores = $this->getCorredor();
        $corredores = json_decode($corredores, true);
        $paradas = [];
        foreach ($corredores as $corr) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url . '/Parada/BuscarParadasPorCorredor?codigoCorredor=' . $corr['cc']);
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: 0'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
            $result = curl_exec($ch);

            $result = json_decode($result, true);

            foreach ($result as $parada) {
                $cp = $parada['cp'];
                $np = $parada['np'];
                $ed = $parada['ed'];
                $px = $parada['px'];
                $py = $parada['py'];
                array_push($paradas, new Parada($cp, $np, $ed, $py, $px));
            }

            echo curl_error($ch);
            curl_close($ch);
        }
        return $paradas;
    }


    public function getCorredor()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . '/Corredor');
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: 0'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
        $result = curl_exec($ch);

        echo curl_error($ch);
        curl_close($ch);
        return $result;
    }

    public function getPosAllOnibus()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . '/Posicao');
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: 0'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
        $result = curl_exec($ch);

        echo curl_error($ch);
        curl_close($ch);

        $result = json_decode($result, true);
        $allbus = $result['l'];
        $busarray = [];
        foreach ($allbus as $atualbus) {
            $id = $atualbus['cl'];
            $origem = $atualbus['lt1'];
            $destino = $atualbus['lt0'];
            $letreiro = $atualbus['c'];
            $bus = new Bus($id, $origem, $destino, $letreiro);
            foreach ($atualbus['vs'] as $position) {
                $bus->setPx($position['px']);
                $bus->setPy($position['py']);
            }
            array_push($busarray, $bus);
        }
        return $busarray;
    }
}
