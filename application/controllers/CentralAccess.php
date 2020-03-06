<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 6/21/2019
 * Time: 1:07 PM
 */
include "Response.php";
include "Ballistics.php";
include "FlutterWavePayments.php";
include APPPATH . 'libraries/MtnMomoHelper.php';

//include APPPATH."models/CommonDatabaseOperations.php";

class CentralAccess
{
    private $genericResponse = null;
    private $ballisticHelper = null;
    private $flutterWavePayments = null;
    private $commonDboperations = null;
    private $mtnMomoOpenApi = null;

    public function __construct()
    {
    }

    public function getGenericResponse($code = null, $message = null, $data = null)
    {
        if ($this->genericResponse == null) {
            $this->setGenericResponse(new Response($code, $message, $data));
        }

        return $this->genericResponse;
    }

    /**
     * @param Response|null $genericResponse
     */
    public function setGenericResponse($genericResponse)
    {
        $this->genericResponse = $genericResponse;
    }

    /**
     * @return Ballistics|null
     */
    public function getBallisticHelper()
    {
        if ($this->ballisticHelper == null) {
            $this->setBallisticHelper(new Ballistics());
        }

        return $this->ballisticHelper;
    }

    /**
     * @param Ballistics|null $ballisticHelper
     */
    public function setBallisticHelper($ballisticHelper)
    {
        $this->ballisticHelper = $ballisticHelper;
    }

    /**
     * @return FlutterWavePayments|null
     */
    public function getFlutterWavePayments()
    {
        if ($this->flutterWavePayments == null) {
            $this->setFlutterWavePayments(new FlutterWavePayments());
        }
        return $this->flutterWavePayments;
    }

    /**
     * @param FlutterWavePayments|null $flutterWavePayments
     */
    public function setFlutterWavePayments($flutterWavePayments)
    {
        $this->flutterWavePayments = $flutterWavePayments;
    }

    /**
     * @return null
     */
    public function getMtnMomoOpenApi()
    {
        if ($this->mtnMomoOpenApi == null) {
            $this->setMtnMomoOpenApi(new MtnMomoHelper());
        }
        return $this->mtnMomoOpenApi;
    }

    /**
     * @param null $mtnMomoOpenApi
     */
    public function setMtnMomoOpenApi($mtnMomoOpenApi)
    {
        $this->mtnMomoOpenApi = $mtnMomoOpenApi;
    }

}
