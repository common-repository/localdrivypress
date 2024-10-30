<?php

class LocaldrivyRestApiController extends WP_REST_Controller
{

  /**
   * Register the routes for the objects of the controller.
   */
    public function register_routes()
    {
        $version = '1';
        $namespace = 'lc/v' . $version;
        $base = 'cart';
        register_rest_route($namespace, '/' . $base, array(
      array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => array($this, 'lc_add_cart'),
        'args'                => array(),
        'permission_callback' => '__return_true',
      ), array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array($this, 'lc_get_cart'),
        'args'                => array(),
        'permission_callback' => '__return_true',
      )
    ));
        register_rest_route($namespace, '/' . $base."/valid", array(
      array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => array($this, 'validorder'),
        'args'                => array(),
        'permission_callback' => '__return_true',
      )
    ));
        register_rest_route($namespace, '/' . $base.'/(?P<id>\d+)', array(
       array(
        'methods'             => WP_REST_Server::DELETABLE,
        'callback'            => array($this, 'lc_delete_cart'),
        'args'                => array('id'),
        'permission_callback' => '__return_true',
      )
    ));
    }

    /**
     * Get a collection of items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function lc_add_cart($request)
    {
        $datasjson = json_decode($request->get_body(), true);
        $unit = "";
        $qty = 0;
        $pId = $datasjson['id'];

        if (array_key_exists('piece', $datasjson)) {
            $unit = 'piece';
            $qty = $datasjson['piece'];
        }
        if (array_key_exists('gr', $datasjson)) {
            $unit = 'gr';
            $qty = $datasjson['gr'];
        }
        if (array_key_exists('kg', $datasjson)) {
            $unit = 'gr';
            $qty = $qty + ($datasjson['kg'] * 1000);
        }
        $lcClient = new LocalDrivyApiClient();
        $responsetmp = $lcClient->addToCart($pId, $unit, $qty);
        $resp = json_decode($responsetmp, true);
        if ($resp['status']=='error') {
            return new WP_REST_Response(['status' => 'error','detail'=>$resp['details']], 400);
        }
        $data =  ['status' => 'success'];
        return new WP_REST_Response($data, 200);
    }

    /**
     * Get a collection of items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function lc_get_cart($request)
    {
        $lcClient = new LocalDrivyApiClient();
        $datas = json_decode($lcClient->getCart(), true);
        return new WP_REST_Response($datas, 200);
    }

    /**
    * Get a collection of items
    *
    * @param WP_REST_Request $request Full data about the request.
    * @return WP_Error|WP_REST_Response
    */
    public function lc_delete_cart($data)
    {
        $id = $data['id'] ;
        $lcClient = new LocalDrivyApiClient();
        $result = $lcClient->deleteCartLine($id);
        return new WP_REST_Response(['status'=>'ok'], 200);
    }

    /**
    * Get a collection of items
    *
    * @param WP_REST_Request $request Full data about the request.
    * @return WP_Error|WP_REST_Response
    */
    public function validorder()
    {
        $lcClient = new LocalDrivyApiClient();
        $response = $lcClient->validOrder();
        return new WP_REST_Response(json_decode($response), 200);
    }
}
