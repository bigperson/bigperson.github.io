<?php


class Tracking
{

    /**
     * @var string
     */
    private $russian_post_login = '';

    /**
     * @var string
     */
    private $russian_post_password = '';

    /**
     * @var string
     */
    private $lang = 'RUS';

    /**
     * @param array $tracks
     *
     * @return bool|string
     */
    public function getTicket($tracks)
    {
        $barcodes = $this->getBarcodes($tracks);

        $request = $this->getRequestBodyGetTiket($barcodes);

        $client = new SoapClient(
            "https://tracking.russianpost.ru/fc?wsdl",
            ['trace' => 1, 'soap_version' => SOAP_1_1]
        );

        $response = $client->__doRequest(
            $request,
            "https://tracking.russianpost.ru/fc",
            "getTicket",
            SOAP_1_1
        );

        $xml = simplexml_load_string($response);

        $xml->registerXPathNamespace('pos', 'http://fclient.russianpost.org/postserver');

        $result = $xml->xpath('//pos:ticketResponse');

        $ticket = (string)$result[0]->value;

        return $ticket;
    }

    /**
     * @param string $ticket
     *
     * @return array
     */
    public function getResponseByTicket($ticket)
    {
        $request = $this->getRequestBodyGetResponse($ticket);

        $client = new SoapClient("https://tracking.russianpost.ru/fc?wsdl", array('trace' => 1, 'soap_version' => SOAP_1_1));

        $response = $client->__doRequest(
            $request,
            "https://tracking.russianpost.ru/fc",
            "getResponseByTicket",
            SOAP_1_1
        );

        $xml = simplexml_load_string($response);

        $xml->registerXPathNamespace('nspost', 'http://fclient.russianpost.org/postserver');

        $res = [];

        $item = $xml->xpath('//nspost:answerByTicketResponse/nspost:Item');

        foreach ($xml->xpath('//nspost:answerByTicketResponse') as $response) {
            $response->registerXPathNamespace('nspost', 'http://fclient.russianpost.org');
            $items = $response->xpath('//nspost:Item');

            foreach ($items as $k => $item) {
                $barcode = (string)$item->attributes()->Barcode;

                $item->registerXPathNamespace('nspost', 'http://fclient.russianpost.org');
                $operations = $item->xpath('.//nspost:Operation');

                foreach ($operations as $n => $oper) {
                    $attr = $oper->attributes();
                    $res[$barcode][$n]['OperTypeID'] = (string)$attr->OperTypeID;
                    $res[$barcode][$n]['OperCtgID'] = (string)$attr->OperCtgID;
                    $res[$barcode][$n]['OperName'] = (string)$attr->OperName;
                    $res[$barcode][$n]['DateOper'] = (string)$attr->DateOper;
                    $res[$barcode][$n]['IndexOper'] = (string)$attr->IndexOper;
                }
            }

            break;
        }


        $finishedTracks = [];

        foreach ($res as $barcode => $item) {
            foreach ($item as $oper) {
                if ($oper['OperTypeID'] == 2) {
                    $finishedTracks[] = $barcode;
                    continue;
                }
            }
        }

        return $finishedTracks;
    }

    /**
     * @param $tracks
     *
     * @return string
     */
    private function getBarcodes($tracks)
    {
        $barcodes = "";

        foreach ($tracks as $track) {
            $barcodes .= "<fcl:Item Barcode=\"$track\"/>\n";
        }

        return $barcodes;
    }

    /**
     * @param string $barcodes
     *
     * @return string
     */
    private function getRequestBodyGetTiket($barcodes)
    {
        return '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:pos="http://fclient.russianpost.org/postserver" xmlns:fcl="http://fclient.russianpost.org">
              <soapenv:Header/>
              <soapenv:Body>
                 <pos:ticketRequest>
                    <request>
                       '.$barcodes.'           
                    </request>
                    <login>'.$this->russian_post_login.'</login>
                    <password>'.$this->russian_post_password.'</password>
                    <language>'.$this->lang.'</language>
                 </pos:ticketRequest>
              </soapenv:Body>
            </soapenv:Envelope>';
    }

    /**
     * @param $ticket
     *
     * @return string
     */
    private function getRequestBodyGetResponse($ticket)
    {
        return '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:pos="http://fclient.russianpost.org/postserver">
          <soapenv:Header/>
          <soapenv:Body>
             <pos:answerByTicketRequest>
                <ticket>'.$ticket.'</ticket>
                <login>'.$this->russian_post_login.'</login>
                <password>'.$this->russian_post_password.'</password>
             </pos:answerByTicketRequest>
          </soapenv:Body>
        </soapenv:Envelope>';
    }
}