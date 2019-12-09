<?php
/*
use Exception;
use SoapClient;
use SoapHeader;
*/
class UpsEasyTracking_Api
{
    private static $initiated = false;

    private static $keyaccess;
    private static $userid;
    private static $passwd;
    private static $mode;

    private static $client;

    public static $operation = "ProcessTrack";
    public static $wsdl = WPUPS_PLUGIN_DIR . "wsdls/Track.wsdl";

    /**
     * Set credentials and checks system operational mode
     *
     * @param string $keyaccess
     * @param string $userid
     * @param string $passwd
     * @param string $mode
     */
    public static function init()
    {
        if (!self::$initiated) {
            self::$keyaccess = esc_attr(get_option('_wpups_accesskey'));
            self::$userid = esc_attr(get_option('_wpups_username'));
            self::$passwd = esc_attr(get_option('_wpups_pass'));

            if (empty(self::$keyaccess) || empty(self::$userid) || empty(self::$passwd)) {
                echo "UPS web service credentials not defined.";
                exit();
            }

            self::$mode = esc_attr(get_option('_wpups_mode'));
            if (empty(self::$mode) || (self::$mode != "Test" && self::$mode != "Production")) {
                echo "Parameter Mode has to be either *Test* or *Production*";
                exit();
            }
        }
    }

    /**
     * Fetch activity of a shipment based on tracking number
     *
     * @param string $trackingNumber
     * @return obj Web Service Response
     */
    public static function track()
    {
        self::init();
        self::$initiated = true;

        $trackingNumber = $_REQUEST['trackingnumber'];
        self::$client = self::SOAPConnect();

        $request = array();
        $request = [
            "Request" => [
                "RequestOption" => 15,
                "TransactionReference" => ["CustomerContext" => "Description"]
            ],
            "InquiryNumber" => trim($trackingNumber),
            "TrackingOption" => "02"
        ];

        try {
            $resp = self::$client->__soapCall(self::$operation, array($request));
            echo self::loadView($resp);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    private static function loadView($resp)
    {

        if (is_object($resp)) {
            $html = '<table class="table table-striped table-inverse table-responsive">
                        <thead class="thead-inverse">
                            <tr>
                                <th colspan="7">Shipment Summary</th>
                            </tr>
                        </thead>
                        <tbody>
                                <tr>
                                    <td scope="row">Tracking:</td>
                                    <td>' . $resp->Shipment->InquiryNumber->Value . '</td>
                                    <td>Service:</td>
                                    <td>' . $resp->Shipment->Service->Description . '</td>
                                </tr>
                                <tr>
                                    <td scope="row">Reference:</td>
                                    <td colspan="3">' . $resp->Shipment->ReferenceNumber->Value . '</td>

                                </tr>
                                <tr>
                                    <td class="font-weight-bold" colspan="7">Shipment Activity</td>
                                </tr>';

            foreach ($resp->Shipment->Package->Activity as $activity) {
                $html .= '<tr>
                                                <td colspan="2">
                                                ' . date("M d, Y G:i", strtotime($activity->Date . ' ' . $activity->Time)) . '
                                                </td>
                                                <td colspan="5">
                                                ' . $activity->Status->Description . '
                                                </td>
                                            </tr>';
            }


            $html .= '</tbody>
                    </thead>
                    </table>';

            return $html;
        }

        return "Something went wrong.";
    }

    /**
     * Connect to webservice using SOAP and send credentials
     *
     * @return obj SOAP client handler
     */
    private static function SOAPConnect()
    {
        //Check if the credentials were defined and are properly loaded
        if (empty(self::$keyaccess) || empty(self::$userid) || empty(self::$passwd)) {
            throw new Exception("UPS credentials are empty.");
        }

        $mode = [
            'soap_version' => 'SOAP_1_1',
            'trace' => 1
        ];

        //Instantiate SoapClient
        $client = new SoapClient(self::$wsdl, $mode);

        //Set Soap endpoint
        $client->__setLocation(self::getEndPoint());

        //Header settings
        $headerCredentials = array();
        $headerCredentials['UsernameToken'] = ["Username" => self::$userid, "Password" => self::$passwd];
        $headerCredentials['ServiceAccessToken'] = ["AccessLicenseNumber" => self::$keyaccess];

        //Instantiate Soap Header Handler
        $header = new SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0', 'UPSSecurity', $headerCredentials);
        $client->__setSoapHeaders($header);

        return $client;
    }

    /**
     * Define correct web service URL
     *
     * @return string Web service URL
     */
    private static function getEndPoint()
    {
        switch (self::$mode) {
            case "Production":
                return "https://onlinetools.ups.com/webservices/Track";
                break;

            default:
                //Test enviroment by default
                return "https://wwwcie.ups.com/webservices/Track";
        }
    }
}
